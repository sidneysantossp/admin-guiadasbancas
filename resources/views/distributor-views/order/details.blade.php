@extends('layouts.distributor.app')

@section('title', translate('Detalhes do Pedido'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header d-print-none">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-no-gutter">
                        <li class="breadcrumb-item">
                            <a class="breadcrumb-link" href="{{ route('distributor.orders', ['status' => 'all']) }}">
                                {{ translate('Pedidos') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ translate('Detalhes do Pedido') }}</li>
                    </ol>
                </nav>

                <div class="d-sm-flex align-items-sm-center">
                    <h1 class="page-header-title mb-0">{{ translate('Pedido') }} #{{ $order['id'] }}</h1>

                    @if($order['payment_status'] == 'paid')
                        <span class="badge badge-soft-success ml-sm-3">
                            <span class="legend-indicator bg-success"></span>{{ translate('Pago') }}
                        </span>
                    @else
                        <span class="badge badge-soft-danger ml-sm-3">
                            <span class="legend-indicator bg-danger"></span>{{ translate('Não Pago') }}
                        </span>
                    @endif

                    <div class="ml-2 ml-sm-3">
                        <span class="d-none d-sm-inline-block mr-2">{{ translate('Status do Pedido') }}:</span>

                        @if($order['status'] == 'pending')
                            <span class="badge badge-soft-warning ml-1 ml-sm-0">
                                <span class="legend-indicator bg-warning"></span>{{ translate('Pendente') }}
                            </span>
                        @elseif($order['status'] == 'confirmed')
                            <span class="badge badge-soft-success ml-1 ml-sm-0">
                                <span class="legend-indicator bg-success"></span>{{ translate('Confirmado') }}
                            </span>
                        @elseif($order['status'] == 'processing')
                            <span class="badge badge-soft-primary ml-1 ml-sm-0">
                                <span class="legend-indicator bg-primary"></span>{{ translate('Processando') }}
                            </span>
                        @elseif($order['status'] == 'delivering')
                            <span class="badge badge-soft-info ml-1 ml-sm-0">
                                <span class="legend-indicator bg-info"></span>{{ translate('Em Entrega') }}
                            </span>
                        @elseif($order['status'] == 'delivered')
                            <span class="badge badge-soft-success ml-1 ml-sm-0">
                                <span class="legend-indicator bg-success"></span>{{ translate('Entregue') }}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-1 ml-sm-0">
                                <span class="legend-indicator bg-danger"></span>{{ translate('Cancelado') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-auto">
                <div class="d-flex">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1" 
                       href="{{ route('distributor.order.details', [$order['id']-1]) }}" 
                       data-toggle="tooltip" data-placement="top" title="{{ translate('Pedido Anterior') }}">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle" 
                       href="{{ route('distributor.order.details', [$order['id']+1]) }}" 
                       data-toggle="tooltip" data-placement="top" title="{{ translate('Próximo Pedido') }}">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <div class="row" id="printableArea">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <!-- Card -->
            <div class="card mb-3 mb-lg-5">
                <!-- Header -->
                <div class="card-header d-print-none">
                    <h5 class="card-header-title">{{ translate('Detalhes do Pedido') }}</h5>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-ghost-secondary" type="button" data-toggle="dropdown">
                            {{ translate('Status') }}: 
                            @if($order['status'] == 'pending')
                                <span class="badge badge-soft-warning ml-1 ml-sm-0">
                                    {{ translate('Pendente') }}
                                </span>
                            @elseif($order['status'] == 'confirmed')
                                <span class="badge badge-soft-success ml-1 ml-sm-0">
                                    {{ translate('Confirmado') }}
                                </span>
                            @elseif($order['status'] == 'processing')
                                <span class="badge badge-soft-primary ml-1 ml-sm-0">
                                    {{ translate('Processando') }}
                                </span>
                            @elseif($order['status'] == 'delivering')
                                <span class="badge badge-soft-info ml-1 ml-sm-0">
                                    {{ translate('Em Entrega') }}
                                </span>
                            @elseif($order['status'] == 'delivered')
                                <span class="badge badge-soft-success ml-1 ml-sm-0">
                                    {{ translate('Entregue') }}
                                </span>
                            @else
                                <span class="badge badge-soft-danger ml-1 ml-sm-0">
                                    {{ translate('Cancelado') }}
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu">
                            <form action="{{ route('distributor.order.update_status') }}" method="post">
                                @csrf
                                <input type="hidden" name="id" value="{{ $order['id'] }}">
                                <button class="dropdown-item" type="submit" name="status" value="pending">{{ translate('Pendente') }}</button>
                                <button class="dropdown-item" type="submit" name="status" value="confirmed">{{ translate('Confirmado') }}</button>
                                <button class="dropdown-item" type="submit" name="status" value="processing">{{ translate('Processando') }}</button>
                                <button class="dropdown-item" type="submit" name="status" value="delivering">{{ translate('Em Entrega') }}</button>
                                <button class="dropdown-item" type="submit" name="status" value="delivered">{{ translate('Entregue') }}</button>
                                <button class="dropdown-item" type="submit" name="status" value="canceled">{{ translate('Cancelado') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="media">
                                <div class="avatar avatar-xl mr-3">
                                    <img class="img-fluid"
                                         src="{{ asset('public/assets/admin/img/160x160/img1.jpg') }}"
                                         alt="Image Description">
                                </div>

                                <div class="media-body">
                                    <div class="text-dark">
                                        @if($order->vendor)
                                            <span class="font-weight-bold">{{ $order->vendor['f_name'] . ' ' . $order->vendor['l_name'] }}</span>
                                        @else
                                            <span class="font-weight-bold">{{ translate('Jornaleiro não disponível') }}</span>
                                        @endif
                                    </div>
                                    <div class="text-body">
                                        @if($order->vendor)
                                            <span>{{ $order->vendor['email'] }}</span>
                                        @endif
                                    </div>
                                    <div class="text-body">
                                        @if($order->vendor)
                                            <span>{{ $order->vendor['phone'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="d-flex justify-content-sm-end">
                                <div class="text-right">
                                    <h5>{{ translate('Data do Pedido') }}</h5>
                                    <span>{{ date('d/m/Y H:i:s', strtotime($order['created_at'])) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <h5>{{ translate('Informações do Jornaleiro') }}</h5>

                            @if($order->vendor)
                                <span class="d-block">{{ $order->vendor->name }}</span>
                                @if($order->vendor->address)
                                    <span class="d-block">{{ $order->vendor->address }}</span>
                                @endif
                            @else
                                <span class="d-block">{{ translate('Jornaleiro não disponível') }}</span>
                            @endif
                        </div>

                        <div class="col-sm-6">
                            <div class="text-sm-right">
                                <h5>{{ translate('Detalhes da Entrega') }}</h5>
                                <span class="d-block">{{ translate('Data do pedido') }}: {{ date('d/m/Y H:i', strtotime($order->created_at)) }}</span>
                                @if($order->delivery_address)
                                    <span class="d-block">{{ $order->delivery_address }}</span>
                                @endif
                                @if($order->delivery_date)
                                    <span class="d-block">{{ translate('Data de entrega') }}: {{ date('d/m/Y', strtotime($order->delivery_date)) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>{{ translate('Itens do Pedido') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('Produto') }}</th>
                                    <th>{{ translate('Preço') }}</th>
                                    <th>{{ translate('Qtd') }}</th>
                                    <th class="text-right">{{ translate('Total') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="media">
                                            <div class="avatar avatar-xl mr-3">
                                                <img class="img-fluid"
                                                    src="{{ $item->food->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                                    onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'"
                                                    alt="Image Description">
                                            </div>
                                            <div class="media-body">
                                                <h5 class="text-hover-primary mb-0">{{ $item->product_name ?? ($item->food->name ?? translate('Produto')) }}</h5>
                                                <small>{{ Str::limit($item->food->description ?? '', 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ \App\CentralLogics\Helpers::format_currency($item['unit_price']) }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td class="text-right">{{ \App\CentralLogics\Helpers::format_currency($item['unit_price'] * $item['quantity']) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <div class="row justify-content-md-end mb-3">
                        <div class="col-md-6">
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <span>{{ translate('Sub-total') }}:</span>
                                </div>
                                <div class="col-6 text-right">
                                    <span>{{ \App\CentralLogics\Helpers::format_currency((($order['total_amount'] ?? 0) > 0 ? $order['total_amount'] : ($order->items->sum('total_price') ?? 0))) }}</span>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <span>{{ translate('Taxa de Entrega') }}:</span>
                                </div>
                                <div class="col-6 text-right">
                                    <span>{{ \App\CentralLogics\Helpers::format_currency($order['delivery_fee'] ?? 0) }}</span>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <span>{{ translate('Impostos') }}:</span>
                                </div>
                                <div class="col-6 text-right">
                                    <span>{{ \App\CentralLogics\Helpers::format_currency($order['tax_amount'] ?? 0) }}</span>
                                </div>
                            </div>
                            <hr>
                            <div class="row justify-content-between">
                                <div class="col-6">
                                    <span class="font-weight-bold">{{ translate('Total') }}:</span>
                                </div>
                                <div class="col-6 text-right">
                                    <span class="font-weight-bold">{{ \App\CentralLogics\Helpers::format_currency((($order['total_amount'] ?? 0) > 0 ? $order['total_amount'] : ($order->items->sum('total_price') ?? 0)) + ($order['delivery_fee'] ?? 0) + ($order['tax_amount'] ?? 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>

        <div class="col-lg-4">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">{{ translate('Status do Pedido') }}</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="timeline-one">
                        @foreach($order['statusHistory'] ?? [] as $status)
                            <div class="timeline-one__item">
                                <span class="timeline-one__icon"></span>
                                <div class="timeline-one__content">
                                    <span class="d-block font-weight-bold">{{ $status['status'] }}</span>
                                    <span>{{ date('d/m/Y H:i', strtotime($status['created_at'])) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->

            <!-- Card -->
            <div class="card mt-3">
                <!-- Header -->
                <div class="card-header">
                    <h4 class="card-header-title">{{ translate('Informações de Pagamento') }}</h4>
                </div>
                <!-- End Header -->

                <!-- Body -->
                <div class="card-body">
                    <div class="payment-information">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ translate('Método de Pagamento') }}:</span>
                            <span>{{ translate($order['payment_method']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ translate('Status do Pagamento') }}:</span>
                            <span>
                                @if($order['payment_status'] == 'paid')
                                    <span class="badge badge-soft-success">{{ translate('Pago') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ translate('Não Pago') }}</span>
                                @endif
                            </span>
                        </div>
                        @if($order['transaction_reference'])
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ translate('Referência') }}:</span>
                                <span>{{ $order['transaction_reference'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- End Body -->
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
        // Implementação futura de funcionalidades JS
    });
</script>
@endpush
