<?php

namespace App\Http\Controllers\Vendor;

use App\Exports\VendorTaxExport;
use App\Exports\VendorWiseTaxExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Maatwebsite\Excel\Facades\Excel;
use App\CentralLogics\Helpers;
use Modules\TaxModule\Entities\OrderTax;

class VendorTaxReportController extends Controller
{

    public function vendorTax(Request $request)
    {

        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');
        $key = explode(' ', $request['search']);
        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();


        $restaurant = Helpers::get_restaurant_data();

        $vendortaxData =   $this->getVendortaxData($restaurant->id, $startDate, $endDate, $key);
        $summary =   $vendortaxData['summary'];
        $orders = $vendortaxData['orders'];

        $totalOrders = $summary->total_orders;
        $totalOrderAmount = $summary->total_order_amount;
        $totalTax = $summary->total_tax;
        $taxSummary = $vendortaxData['taxSummary'];
        $orders = $orders->paginate(config('default_pagination'))
            ->withQueryString();

        return view('vendor-views.report.tax-report.vendor-tax-detail-report', compact('totalOrders', 'totalOrderAmount', 'totalTax', 'restaurant', 'orders', 'startDate', 'endDate', 'taxSummary'));
    }

    public function vendorTaxExport(Request $request)
    {
        $dateRange = $request->dates ?? now()->subDays(6)->format('m/d/Y') . ' - ' . now()->format('m/d/Y');
        $key = explode(' ', $request['search']);
        list($startDate, $endDate) = explode(' - ', $dateRange);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startDate));
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endDate));
        $startDate = $startDate->startOfDay();
        $endDate = $endDate->endOfDay();


        $restaurant = Helpers::get_restaurant_data();

        $vendortaxData =   $this->getVendortaxData($restaurant->id, $startDate, $endDate, $key, true);
        $summary =   $vendortaxData['summary'];
        $orders = $vendortaxData['orders'];

        $orders = $orders->cursor();
        $startDate = Carbon::parse($startDate)->format('d M, Y');
        $endDate = Carbon::parse($endDate)->format('d M, Y');

        $data = [
            'orders' => $orders,
            'search' => $request->search ?? null,
            'from' => $startDate,
            'to' => $endDate,
            'summary' => $summary
        ];

        if ($request->export_type == 'excel') {
            return Excel::download(new VendorTaxExport($data), $restaurant->name . 's TaxExport.xlsx');
        } else if ($request->export_type == 'csv') {
            return Excel::download(new VendorTaxExport($data),  $restaurant->name . 's TaxExport.csv');
        }
    }

    private function getVendortaxData($restaurant_id, $startDate, $endDate, $search, $export = false)
    {
        $summary = DB::table('orders')
            ->where('total_tax_amount' , '>', 0)
            ->where('restaurant_id', $restaurant_id)
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                foreach ($search as $value) {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            }))
            ->selectRaw('COUNT(*) as total_orders, SUM(order_amount) as total_order_amount, SUM(total_tax_amount) as total_tax')
            ->first();

        $orders = Order::with([
            'orderTaxes' => function (MorphMany $query) {
                $query->where('order_type', Order::class)
                    ->select('id', 'order_id', 'tax_name', 'tax_amount','tax_on','tax_type');
            }
        ])
            ->where('total_tax_amount' , '>', 0)
            ->where('restaurant_id', $restaurant_id)
            ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                foreach ($search as $value) {
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            }))
            ->whereIn('order_status', ['delivered', 'refund_requested', 'refund_request_canceled'])
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('created_at', [$startDate, $endDate]))
            ->select(['id', 'order_amount', 'total_tax_amount', 'order_type', 'created_at', 'order_status', 'payment_status'])
            ->latest('created_at');

        if(!$export){
            $taxSummary = DB::table('order_taxes')
                ->select('tax_name', DB::raw('SUM(tax_amount) as total_tax'))
                ->where('order_type', Order::class)
                ->when(count($search), fn($q) => $q->where(function ($q) use ($search) {
                    foreach ($search as $value) {
                        $q->orWhere('order_id', 'like', "%{$value}%");
                    }
                }))
                ->whereIn('order_id', $orders->pluck('id')->toArray())
                ->where('store_id', $restaurant_id)
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('tax_name')
                ->get();

        }
        return ['summary' => $summary, 'orders' => $orders, 'taxSummary' => $taxSummary??[]];
    }
}
