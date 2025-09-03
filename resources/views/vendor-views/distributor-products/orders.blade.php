@extends('layouts.vendor.app')

@section('title', translate('Meus Pedidos'))

@push('css_or_js')
    <style>
        .order-status {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-confirmed { background-color: #dbeafe; color: #1e40af; }
        .status-processing { background-color: #e0e7ff; color: #3730a3; }
        .status-shipped { background-color: #f3e8ff; color: #6b21a8; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
        
        .order-timeline {
            position: relative;
            padding-left: 2rem;
        }
        .order-timeline::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 1rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -1.75rem;
            top: 0.25rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background: #9ca3af;
        }
        .timeline-item.active::before {
            background: #10b981;
        }
        .timeline-item.current::before {
            background: #3b82f6;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-receipt-outlined"></i></span>
                    {{ translate('Meus Pedidos') }}
                </h1>
                <p class="page-header-text">{{ translate('Acompanhe o status dos seus pedidos feitos aos distribuidores') }}</p>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('vendor.distributor-orders.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">{{ translate('Todos os Status') }}</option>
                            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>{{ translate('Pendente') }}</option>
                            <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>{{ translate('Confirmado') }}</option>
                            <option value="processing" {{ request('status')=='processing'?'selected':'' }}>{{ translate('Processando') }}</option>
                            <option value="shipped" {{ request('status')=='shipped'?'selected':'' }}>{{ translate('Enviado') }}</option>
                            <option value="delivered" {{ request('status')=='delivered'?'selected':'' }}>{{ translate('Entregue') }}</option>
                            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>{{ translate('Cancelado') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="tio-filter-list"></i> {{ translate('Filtrar') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('Pedido') }}</th>
                        <th>{{ translate('Distribuidor') }}</th>
                        <th>{{ translate('Itens') }}</th>
                        <th>{{ translate('Total') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->order_number }}</td>
                            <td>{{ $order->distributor->f_name }} {{ $order->distributor->l_name }}</td>
                            <td>{{ $order->total_items }}</td>
                            <td>{{ \App\CentralLogics\Helpers::format_currency($order->total_amount) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->status == 'cancelled' ? 'danger' : ($order->status == 'completed' ? 'success' : 'info') }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('vendor.distributor-orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="tio-visible"></i> {{ translate('Ver') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="{{ asset('public/assets/admin/img/no-data.png') }}" alt="No data" style="width: 120px;">
                                <h5 class="mt-3">{{ translate('Nenhum pedido encontrado') }}</h5>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="card-footer">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection