@extends('layouts.distributor.app')

@section('title', translate('Lista de Pedidos'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">{{ translate('Pedidos') }} <span class="badge badge-soft-dark ml-2">{{ $orders->total() }}</span></h1>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Card -->
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <h5>{{ translate('Lista de Pedidos') }}</h5>
                </div>
                <div class="col-lg-6">
                    <form action="{{ route('distributor.orders', ['status' => $status]) }}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control"
                                placeholder="{{ translate('Buscar por ID do Pedido') }}" aria-label="Search"
                                value="{{ request('search') }}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text"><i class="tio-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Header -->
        <div class="card-header">
            <div class="row justify-content-between align-items-center flex-grow-1">
                <div class="col-md-12 mb-3">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'all']) }}">
                                {{ translate('Todos') }} <span class="badge badge-soft-info">{{ $count_all ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'pending' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'pending']) }}">
                                {{ translate('Pendente') }} <span class="badge badge-soft-warning">{{ $counts['pending'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'confirmed' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'confirmed']) }}">
                                {{ translate('Confirmado') }} <span class="badge badge-soft-success">{{ $counts['confirmed'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'processing' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'processing']) }}">
                                {{ translate('Processando') }} <span class="badge badge-soft-primary">{{ $counts['processing'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'delivering' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'delivering']) }}">
                                {{ translate('Em Entrega') }} <span class="badge badge-soft-info">{{ $counts['delivering'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'delivered' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'delivered']) }}">
                                {{ translate('Entregue') }} <span class="badge badge-soft-success">{{ $counts['delivered'] ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'canceled' ? 'active' : '' }}" href="{{ route('distributor.orders', ['status' => 'canceled']) }}">
                                {{ translate('Cancelado') }} <span class="badge badge-soft-danger">{{ $counts['canceled'] ?? 0 }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End Header -->

        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('ID do Pedido') }}</th>
                        <th>{{ translate('Data') }}</th>
                        <th>{{ translate('Cliente') }}</th>
                        <th>{{ translate('Total') }}</th>
                        <th>{{ translate('Jornaleiro') }}</th>
                        <th>{{ translate('Status') }}</th>
                        <th>{{ translate('Ações') }}</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($orders as $key=>$order)
                    <tr>
                        <td>
                            <a href="{{ route('distributor.order.details', $order->id) }}">{{ $order->id }}</a>
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($order->created_at)) }}</td>
                        <td>
                            @if($order->vendor)
                                <a class="text-body text-capitalize" href="#">
                                    {{ $order->vendor->f_name }} {{ $order->vendor->l_name }}
                                </a>
                            @else
                                <span class="text-muted">{{ translate('Jornaleiro não encontrado') }}</span>
                            @endif
                        </td>
                        <td>{{ \App\CentralLogics\Helpers::format_currency($order->total_amount) }}</td>
                        <td>
                            @if($order['status']=='pending')
                                <span class="badge badge-soft-info">{{ translate('pendente') }}</span>
                            @elseif($order['status']=='confirmed')
                                <span class="badge badge-soft-success">{{ translate('confirmado') }}</span>
                            @elseif($order['status']=='processing')
                                <span class="badge badge-soft-warning">{{ translate('processando') }}</span>
                            @elseif($order['status']=='delivering')
                                <span class="badge badge-soft-primary">{{ translate('em entrega') }}</span>
                            @elseif($order['status']=='delivered')
                                <span class="badge badge-soft-success">{{ translate('entregue') }}</span>
                            @else
                                <span class="badge badge-soft-danger">{{ translate('cancelado') }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    <i class="tio-settings"></i>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="{{ route('distributor.order.details', $order->id) }}">
                                        <i class="tio-visible"></i> {{ translate('Ver Detalhes') }}
                                    </a>
                                    <a class="dropdown-item" target="_blank" href="#">
                                        <i class="tio-download"></i> {{ translate('Gerar PDF') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- End Table -->

        <!-- No Results -->
        @if(count($orders) == 0)
        <div class="text-center p-4">
            <img class="mb-3 w-120px" src="{{ asset('public/assets/admin/svg/illustrations/sorry.svg') }}" alt="Image">
            <p class="mb-0">{{ translate('Nenhum pedido encontrado') }}</p>
        </div>
        @endif
        <!-- End No Results -->

        <!-- Footer -->
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                <div class="col-sm-auto">
                    <div class="d-flex justify-content-center justify-content-sm-end">
                        <!-- Pagination -->
                        {!! $orders->links() !!}
                    </div>
                </div>
            </div>
            <!-- End Pagination -->
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
</div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
        // Implementação futura de funcionalidades JS
    });
</script>
@endpush
