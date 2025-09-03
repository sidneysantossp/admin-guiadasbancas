@extends('layouts.distributor.app')

@section('title','Pedidos e Notas')

@push('css_or_js')
<style>
    .nav-tabs .nav-link.active { font-weight: 600; }
    .table thead th { font-size: 12px; text-transform: uppercase; color:#6c757d; }
    .table td { vertical-align: middle; }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header d-flex align-items-center justify-content-between">
        <div>
            <h1 class="page-header-title">Pedidos e Notas</h1>
        </div>
        <div>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}" class="btn btn-sm {{ ($view ?? 'list')==='list' ? 'btn-primary' : 'btn-outline-primary' }}">Lista</a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'kanban']) }}" class="btn btn-sm {{ ($view ?? 'list')==='kanban' ? 'btn-primary' : 'btn-outline-primary' }}">Kanban</a>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='all' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'all', 'view' => $view ?? 'list']) }}" role="tab">Todos</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='pending' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'pending', 'view' => $view ?? 'list']) }}" role="tab">Pendentes</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='confirmed' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'confirmed', 'view' => $view ?? 'list']) }}" role="tab">Confirmados</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='processing' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'processing', 'view' => $view ?? 'list']) }}" role="tab">Processamento</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='delivering' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'delivering', 'view' => $view ?? 'list']) }}" role="tab">Para Entrega</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='delivered' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'delivered', 'view' => $view ?? 'list']) }}" role="tab">Entregues</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link {{ ($status ?? 'all')==='canceled' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'canceled', 'view' => $view ?? 'list']) }}" role="tab">Cancelados</a>
        </li>
    </ul>

    @if(($view ?? 'list')==='list')
        <div class="card">
            <div class="card-header">
                <div class="row justify-content-between align-items-center flex-grow-1">
                    <div class="col-lg-4 mb-3 mb-lg-0">
                        <h5>Lista de Pedidos</h5>
                    </div>
                    <div class="col-lg-6">
                        <form action="{{ route('distributor.orders', ['status' => $status ?? 'all']) }}" method="GET">
                            <input type="hidden" name="view" value="list">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    placeholder="Buscar por ID do Pedido" aria-label="Search"
                                    value="{{ request('search') }}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text"><i class="tio-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-borderless mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Jornaleiro</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th>Itens</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach(($paginatedOrders ?? []) as $order)
                            <tr>
                                <td><a href="{{ route('distributor.order.details', $order->id) }}">#{{ $order->id }}</a></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="font-weight-bold">{{ $order->vendor->f_name ?? '' }} {{ $order->vendor->l_name ?? '' }}</span>
                                        <small class="text-muted">ID: {{ $order->vendor->id ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($order['status']=='pending')
                                        <span class="badge badge-soft-warning">Pendente</span>
                                    @elseif($order['status']=='confirmed')
                                        <span class="badge badge-soft-success">Confirmado</span>
                                    @elseif($order['status']=='processing')
                                        <span class="badge badge-soft-primary">Processando</span>
                                    @elseif($order['status']=='delivering')
                                        <span class="badge badge-soft-info">Em Entrega</span>
                                    @elseif($order['status']=='delivered')
                                        <span class="badge badge-soft-success">Entregue</span>
                                    @else
                                        <span class="badge badge-soft-danger">Cancelado</span>
                                    @endif
                                </td>
                                <td>R$ {{ number_format($order->total_amount ?? 0, 2, ',', '.') }}</td>
                                <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $order->items->count() ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('distributor.orders', ['status' => 'all','view'=>'kanban']) }}#{{ $order->id }}" class="btn btn-sm btn-outline-primary">Ver no Kanban</a>
                                    <a href="{{ route('distributor.order.details', $order->id) }}" class="btn btn-sm btn-outline-info">Detalhes</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if(isset($paginatedOrders) && $paginatedOrders->hasPages())
                <div class="card-footer">
                    <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                        <div class="col-sm-auto">
                            <div class="d-flex justify-content-center justify-content-sm-end">
                                {!! $paginatedOrders->links() !!}
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    @else
        @include('distributor.orders.kanban')
    @endif
</div>
@endsection