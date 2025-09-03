@extends('layouts.vendor.app')

@section('title', translate('Detalhes do Pedido ao Distribuidor'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-receipt-outlined"></i></span>
                    {{ translate('Detalhes do Pedido ao Distribuidor') }}
                </h1>
                <a href="{{ route('vendor.distributor-orders.index') }}" class="btn btn-soft-secondary mt-2">
                    <i class="tio-arrow-large-backward"></i> {{ translate('Voltar aos Pedidos') }}
                </a>
            </div>
            <div class="col-sm-auto">
                @if(in_array($order->status, ['pending','processing']))
                    <form action="{{ route('vendor.distributor-orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('{{ translate('Tem certeza que deseja cancelar este pedido?') }}');">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="tio-close"></i> {{ translate('Cancelar Pedido') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <h5 class="mb-1">{{ translate('Pedido') }}</h5>
                    <span class="d-block">#{{ $order->order_number }}</span>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-1">{{ translate('Distribuidor') }}</h5>
                    <span class="d-block">{{ $order->distributor->f_name }} {{ $order->distributor->l_name }}</span>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-1">{{ translate('Status') }}</h5>
                    <span class="badge badge-{{ $order->status == 'cancelled' ? 'danger' : ($order->status == 'completed' ? 'success' : 'info') }}">{{ $order->status_label ?? ucfirst($order->status) }}</span>
                </div>
                <div class="col-md-3">
                    <h5 class="mb-1">{{ translate('Total') }}</h5>
                    <span class="d-block text-primary h5 mb-0">{{ \App\CentralLogics\Helpers::format_currency($order->total_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline / Acompanhamento do Pedido -->
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">{{ translate('Acompanhamento do Pedido') }}</h4>
            <small class="text-muted">{{ translate('Última atualização:') }} {{ optional($order->updated_at)->format('d/m/Y H:i') }}</small>
        </div>
        <div class="card-body">
            @php
                $steps = [
                    ['key' => 'pending', 'label' => 'Pendente', 'icon' => 'tio-time'],
                    ['key' => 'confirmed', 'label' => 'Confirmado', 'icon' => 'tio-checkmark-circle'],
                    ['key' => 'processing', 'label' => 'Processando', 'icon' => 'tio-settings'],
                    ['key' => 'shipped', 'label' => 'Enviado', 'icon' => 'tio-truck'],
                    ['key' => 'delivered', 'label' => 'Entregue', 'icon' => 'tio-done'],
                ];
                $statusIndex = collect($steps)->pluck('key')->search($order->status);
            @endphp

            @if($order->status === 'cancelled')
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="tio-close-circle-outlined mr-2"></i>
                    <div>
                        <strong>{{ translate('Pedido cancelado') }}</strong>
                        <div class="small mb-0">{{ translate('Este pedido foi cancelado e não seguirá para as próximas etapas.') }}</div>
                    </div>
                </div>
            @endif

            <div class="timeline-steps">
                @foreach($steps as $index => $step)
                    @php
                        $state = $statusIndex !== false ? ($index < $statusIndex ? 'done' : ($index === $statusIndex ? 'current' : 'upcoming')) : 'upcoming';
                        if($order->status === 'cancelled') { $state = $index === 0 ? 'done' : 'blocked'; }
                    @endphp
                    <div class="timeline-step {{ $state }}">
                        <div class="timeline-dot">
                            <i class="{{ $step['icon'] }}"></i>
                        </div>
                        <div class="timeline-label">{{ translate($step['label']) }}</div>
                    </div>
                @endforeach
            </div>

            <style>
                .timeline-steps{display:flex;gap:1rem;align-items:center;justify-content:space-between;position:relative}
                .timeline-step{flex:1;position:relative;text-align:center}
                .timeline-step:before,.timeline-step:after{content:"";position:absolute;top:15px;height:2px;background:#e7eaf3;width:50%}
                .timeline-step:before{left:0}
                .timeline-step:after{right:0}
                .timeline-step:first-child:before{display:none}
                .timeline-step:last-child:after{display:none}
                .timeline-dot{width:32px;height:32px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin:0 auto;background:#e7eaf3;color:#677788}
                .timeline-label{margin-top:.5rem;font-size:.875rem;color:#677788}
                .timeline-step.done .timeline-dot{background:#daf5e8;color:#00a36c}
                .timeline-step.done:before{background:#daf5e8}
                .timeline-step.current .timeline-dot{background:#e1effe;color:#2f55d4}
                .timeline-step.current:before{background:#e1effe}
                .timeline-step.blocked .timeline-dot{background:#fdecea;color:#e63757}
                .timeline-step.blocked:before{background:#fdecea}
            </style>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <h4 class="card-title">{{ translate('Informações de Entrega') }}</h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <h6 class="mb-1">{{ translate('Endereço de Entrega') }}</h6>
                    <div>{{ $order->delivery_address ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-1">{{ translate('Data de Entrega') }}</h6>
                    <div>{{ $order->delivery_date ? \Carbon\Carbon::parse($order->delivery_date)->format('d/m/Y') : '-' }}</div>
                </div>
                <div class="col-md-12">
                    <h6 class="mb-1">{{ translate('Observações') }}</h6>
                    <div>{{ $order->notes ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ translate('Itens do Pedido') }}</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-borderless table-hover table-align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>{{ translate('Produto') }}</th>
                        <th class="text-center" style="width: 140px;">{{ translate('Quantidade') }}</th>
                        <th class="text-right" style="width: 160px;">{{ translate('Preço Unitário') }}</th>
                        <th class="text-right" style="width: 160px;">{{ translate('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->image ?? ($item->food->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg')) }}" alt="{{ $item->product_name ?? ($item->food->name ?? 'Produto') }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div class="ml-3">
                                        <strong>{{ $item->product_name ?? ($item->food->name ?? 'Produto') }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-right">{{ \App\CentralLogics\Helpers::format_currency($item->unit_price) }}</td>
                            <td class="text-right">{{ \App\CentralLogics\Helpers::format_currency($item->total_price ?? ($item->unit_price * $item->quantity)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">{{ translate('Total de Itens') }}</th>
                        <th class="text-right">{{ $order->total_items }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right h5 mb-0">{{ translate('Total do Pedido') }}</th>
                        <th class="text-right h5 mb-0 text-primary">{{ \App\CentralLogics\Helpers::format_currency($order->total_amount) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection