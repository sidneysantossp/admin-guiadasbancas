<?php

namespace App\Http\Controllers\Distributor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:distributor');
    }

    // GET /distributor/food/bulk
    public function form()
    {
        $distributor = Auth::guard('distributor')->user();
        $categories = Category::where('position', 0)->select('id', 'name')->get();
        return view('distributor-views.food.bulk', compact('categories'));
    }

    // GET /distributor/food/bulk/template?format=csv|json|xml
    public function template(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));
        $headers = ['name', 'description', 'category_id', 'price', 'maximum_cart_quantity', 'image_url'];

        if ($format === 'json') {
            $sample = [
                [
                    'name' => 'Produto Exemplo 1',
                    'description' => 'Descrição do produto 1',
                    'category_id' => 1,
                    'price' => '9.90',
                    'maximum_cart_quantity' => 5,
                    'image_url' => 'https://exemplo.com/imagens/produto1.png',
                ],
                [
                    'name' => 'Produto Exemplo 2',
                    'description' => 'Descrição do produto 2',
                    'category_id' => 2,
                    'price' => '19.50',
                    'maximum_cart_quantity' => 2,
                    'image_url' => 'https://exemplo.com/imagens/produto2.png',
                ],
            ];
            return Response::make(json_encode($sample, JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="bulk_products_template.json"',
            ]);
        }

        if ($format === 'xml') {
            $xml = new \SimpleXMLElement('<products/>' );
            $p1 = $xml->addChild('product');
            foreach ($headers as $h) { $p1->addChild($h, ''); }
            $p1->name = 'Produto Exemplo 1';
            $p1->description = 'Descrição do produto 1';
            $p1->category_id = '1';
            $p1->price = '9.90';
            $p1->maximum_cart_quantity = '5';
            $p1->image_url = 'https://exemplo.com/imagens/produto1.png';

            $p2 = $xml->addChild('product');
            foreach ($headers as $h) { $p2->addChild($h, ''); }
            $p2->name = 'Produto Exemplo 2';
            $p2->description = 'Descrição do produto 2';
            $p2->category_id = '2';
            $p2->price = '19.50';
            $p2->maximum_cart_quantity = '2';
            $p2->image_url = 'https://exemplo.com/imagens/produto2.png';

            return Response::make($xml->asXML(), 200, [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="bulk_products_template.xml"',
            ]);
        }

        // default CSV
        $out = implode(',', $headers) . "\n";
        $out .= "Produto Exemplo 1,Descrição do produto 1,1,9.90,5,https://exemplo.com/imagens/produto1.png\n";
        $out .= "Produto Exemplo 2,Descrição do produto 2,2,19.50,2,https://exemplo.com/imagens/produto2.png\n";
        return Response::make($out, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bulk_products_template.csv"',
        ]);
    }

    // GET /distributor/food/export?format=csv|json|xml
    public function export(Request $request)
    {
        $distributor = Auth::guard('distributor')->user();
        $format = strtolower($request->get('format', 'csv'));
        $foods = Food::where('vendor_id', $distributor->id)
            ->select('name', 'description', 'category_id', 'price', 'maximum_cart_quantity', 'image')
            ->get();

        if ($format === 'json') {
            return Response::make($foods->toJson(JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="products_export.json"',
            ]);
        }

        if ($format === 'xml') {
            $xml = new \SimpleXMLElement('<products/>' );
            foreach ($foods as $f) {
                $p = $xml->addChild('product');
                $p->addChild('name', htmlspecialchars($f->name));
                $p->addChild('description', htmlspecialchars($f->description ?? ''));
                $p->addChild('category_id', (string) $f->category_id);
                $p->addChild('price', number_format((float)$f->price, 2, '.', ''));
                $p->addChild('maximum_cart_quantity', (string) ($f->maximum_cart_quantity ?? 1));
                $imgUrl = $f->image ? asset('storage/product/'.$f->image) : '';
                $p->addChild('image_url', $imgUrl);
            }
            return Response::make($xml->asXML(), 200, [
                'Content-Type' => 'application/xml',
                'Content-Disposition' => 'attachment; filename="products_export.xml"',
            ]);
        }

        // CSV
        $headers = ['name', 'description', 'category_id', 'price', 'maximum_cart_quantity', 'image_url'];
        $out = implode(',', $headers) . "\n";
        foreach ($foods as $f) {
            $row = [
                self::escapeCsv($f->name),
                self::escapeCsv($f->description ?? ''),
                $f->category_id,
                number_format((float)$f->price, 2, '.', ''),
                $f->maximum_cart_quantity ?? 1,
                $f->image ? asset('storage/product/'.$f->image) : '',
            ];
            $out .= implode(',', $row) . "\n";
        }
        return Response::make($out, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products_export.csv"',
        ]);
    }

    // POST /distributor/food/bulk/upload
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'format' => 'required|in:csv,json,xml',
            'default_category_id' => 'nullable|integer',
        ]);

        $distributor = Auth::guard('distributor')->user();
        $format = strtolower($request->get('format'));

        $content = file_get_contents($request->file('file')->getRealPath());
        $rows = [];
        try {
            if ($format === 'json') {
                $rows = json_decode($content, true);
                if (!is_array($rows)) throw new \Exception('JSON inválido');
            } elseif ($format === 'xml') {
                $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
                if (!$xml) throw new \Exception('XML inválido');
                foreach ($xml->product as $p) {
                    $rows[] = [
                        'name' => (string) ($p->name ?? ''),
                        'description' => (string) ($p->description ?? ''),
                        'category_id' => (string) ($p->category_id ?? ''),
                        'price' => (string) ($p->price ?? ''),
                        'maximum_cart_quantity' => (string) ($p->maximum_cart_quantity ?? ''),
                        'image_url' => (string) ($p->image_url ?? ''),
                    ];
                }
            } else { // csv
                $lines = preg_split('/\r\n|\r|\n/', trim($content));
                if (count($lines) < 2) throw new \Exception('CSV vazio');
                $headers = str_getcsv(array_shift($lines));
                foreach ($lines as $line) {
                    if (trim($line) === '') continue;
                    $data = str_getcsv($line);
                    $row = [];
                    foreach ($headers as $i => $h) {
                        $row[$h] = $data[$i] ?? null;
                    }
                    $rows[] = $row;
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Falha ao ler arquivo: ' . $e->getMessage()]);
        }

        $created = 0; $failed = 0; $errors = [];
        $errorRows = [];
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $name = trim((string)($row['name'] ?? ''));
                $description = (string)($row['description'] ?? '');
                $categoryId = (int)($row['category_id'] ?? 0);
                if (!$categoryId && $request->filled('default_category_id')) {
                    $categoryId = (int) $request->get('default_category_id');
                }
                $priceStr = trim((string)($row['price'] ?? ''));
                // Normaliza preço em formato BR: 1.234,56 -> 1234.56
                if ($priceStr !== '') {
                    $priceStr = str_replace('.', '', $priceStr);
                    $priceStr = str_replace(',', '.', $priceStr);
                }
                $price = is_numeric($priceStr) ? (float)$priceStr : null;
                $maxQty = (int)($row['maximum_cart_quantity'] ?? 1);
                $imageUrl = trim((string)($row['image_url'] ?? ''));

                // Validações obrigatórias
                if (!$name || !$categoryId || $price === null) {
                    $failed++;
                    $reason = 'Campos obrigatórios ausentes (name, category_id, price).';
                    $errors[] = 'Linha '.($index+1).': '.$reason;
                    $errorRows[] = [($index+1), $name ?: '-', $reason];
                    continue;
                }

                // Verifica se categoria existe
                if (!Category::find($categoryId)) {
                    $failed++;
                    $reason = 'category_id inválido (categoria não encontrada)';
                    $errors[] = 'Linha '.($index+1).': '.$reason;
                    $errorRows[] = [($index+1), $name, $reason];
                    continue;
                }

                $food = new Food();
                $food->name = $name;
                $food->description = $description;
                $food->category_id = $categoryId;
                $food->price = $price;
                $food->maximum_cart_quantity = $maxQty ?: 1;
                $food->vendor_id = $distributor->id;
                $food->restaurant_id = $distributor->id;
                $food->veg = 0;
                $food->status = 1;
                $food->discount = 0;
                $food->discount_type = 'amount';
                $food->stock_type = 'unlimited';
                $food->item_stock = 0;
                $food->is_halal = 0;
                $food->category_ids = json_encode([[ 'id' => $categoryId, 'position' => 1 ]]);
                $food->choice_options = json_encode([]);
                $food->variations = json_encode([]);
                $food->attributes = json_encode([]);
                $food->add_ons = json_encode([]);
                // Tenta baixar imagem se URL fornecida
                if ($imageUrl && filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                    try {
                        $imgData = @file_get_contents($imageUrl);
                        if ($imgData !== false) {
                            $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                            $filename = Str::random(20).'.'.$ext;
                            Storage::disk('public')->put('product/'.$filename, $imgData);
                            $food->image = $filename;
                        }
                    } catch (\Exception $e) {
                        // Ignora erro de imagem no import, registra aviso
                        $errors[] = 'Linha '.($index+1).': falha ao baixar image_url.';
                    }
                }

                $food->save();
                $created++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['file' => 'Erro ao importar: ' . $e->getMessage()]);
        }

        if ($failed > 0) {
            // Gera CSV de erros para download
            $csv = "linha,nome,erro\n";
            foreach ($errorRows as $er) {
                $csv .= self::escapeCsv($er[0]).','.self::escapeCsv($er[1]).','.self::escapeCsv($er[2])."\n";
            }
            try {
                if (!Storage::disk('public')->exists('tmp')) {
                    Storage::disk('public')->makeDirectory('tmp');
                }
                $filename = 'tmp/import_report_'.date('Ymd_His').'.csv';
                Storage::disk('public')->put($filename, $csv);
                $url = asset('storage/'.$filename);
                return back()->with('warning', "Importação concluída com avisos. Criados: {$created}, Falhas: {$failed}.\nBaixe o relatório de erros: {$url}");
            } catch (\Exception $e) {
                // Se falhar ao salvar o CSV, retorna mensagens em texto
                return back()->with('warning', "Importação concluída com avisos. Criados: {$created}, Falhas: {$failed}.\n".implode("\n", $errors));
            }
        }
        return back()->with('success', "Importação concluída. Produtos criados: {$created}.");
    }

    private static function escapeCsv($value)
    {
        $value = (string)$value;
        if (str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n")) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }
        return $value;
    }

    // GET /distributor/food/categories/export?format=csv|json
    public function categories(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));
        $cats = Category::select('id', 'name', 'parent_id', 'position')->orderBy('position')->orderBy('name')->get();

        if ($format === 'json') {
            return Response::make($cats->toJson(JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="categories_export.json"',
            ]);
        }

        // CSV default
        $out = "id,name,parent_id,position\n";
        foreach ($cats as $c) {
            $out .= implode(',', [
                self::escapeCsv($c->id),
                self::escapeCsv($c->name),
                self::escapeCsv($c->parent_id),
                self::escapeCsv($c->position),
            ]) . "\n";
        }
        return Response::make($out, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="categories_export.csv"',
        ]);
    }
}
