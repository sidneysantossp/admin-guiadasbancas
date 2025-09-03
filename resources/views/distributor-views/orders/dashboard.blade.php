@extends('layouts.distributor.app')

@section('title', translate('Dashboard de Pedidos'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="page--header-title">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-chart-bar-4"></i></span>
                    {{ translate('Dashboard de Pedidos') }}
                </h1>
                <p class="page-header-text">{{ translate('Gerencie seus pedidos e pagamentos') }}</p>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Stats Cards -->
    <div class="row g-2 mb-4">
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--2">
                <h4 class="title">{{ $stats['today_orders'] }}</h4>
                <span class="subtitle">{{ translate('Pedidos Hoje') }}</span>
                <i class="tio-today resturant-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--3">
                <h4 class="title">{{ $stats['week_orders'] }}</h4>
                <span class="subtitle">{{ translate('Esta Semana') }}</span>
                <i class="tio-calendar-week resturant-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--5">
                <h4 class="title">{{ $stats['month_orders'] }}</h4>
                <span class="subtitle">{{ translate('Este Mês') }}</span>
                <i class="tio-calendar-month resturant-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--14">
                <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($stats['total_revenue']) }}</h4>
                <span class="subtitle">{{ translate('Receita Total') }}</span>
                <i class="tio-wallet resturant-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--1">
                <h4 class="title">{{ $stats['pending_orders'] }}</h4>
                <span class="subtitle">{{ translate('Pendentes') }}</span>
                <i class="tio-clock resturant-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-sm-6">
            <div class="resturant-card dashboard--card bg--6">
                <h4 class="title">{{ $recentOrders->count() }}</h4>
                <span class="subtitle">{{ translate('Pedidos Recentes') }}</span>
                <i class="tio-receipt resturant-icon"></i>
            </div>
        </div>
    </div>

    <!-- Recent Orders with PIX QR Code -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-header-title">
                <i class="tio-receipt-outlined"></i>
                {{ translate('Pedidos Recentes') }}
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ translate('Pedido') }}</th>
                            <th>{{ translate('Jornaleiro') }}</th>
                            <th>{{ translate('Data') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th>{{ translate('Total') }}</th>
                            <th>{{ translate('Pagamento') }}</th>
                            <th>{{ translate('Ações') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('distributor.orders.show', $order->id) }}" class="text-dark">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="media align-items-center">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img" src="{{ $order->vendor->image_full_url ?? asset('public/assets/admin/img/160x160/img1.jpg') }}" alt="{{ $order->vendor->f_name }}">
                                    </div>
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{ $order->vendor->f_name }} {{ $order->vendor->l_name }}</h5>
                                        <span class="d-block font-size-sm text-body">{{ $order->vendor->phone }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm">{{ $order->created_at->format('d/m/Y') }}</span>
                                <span class="d-block font-size-sm text-muted">{{ $order->created_at->format('H:i') }}</span>
                            </td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge badge-soft-warning">{{ translate('Pendente') }}</span>
                                @elseif($order->status == 'confirmed')
                                    <span class="badge badge-soft-info">{{ translate('Confirmado') }}</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge badge-soft-primary">{{ translate('Processando') }}</span>
                                @elseif($order->status == 'delivered')
                                    <span class="badge badge-soft-success">{{ translate('Entregue') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ translate('Cancelado') }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="font-weight-bold">{{ \App\CentralLogics\Helpers::format_currency($order->total_amount) }}</span>
                            </td>
                            <td>
                                @if($order->payment_status == 'pending')
                                    <span class="badge badge-soft-warning">{{ translate('Pendente') }}</span>
                                @elseif($order->payment_status == 'paid')
                                    <span class="badge badge-soft-success">{{ translate('Pago') }}</span>
                                @else
                                    <span class="badge badge-soft-danger">{{ translate('Não Pago') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('distributor.orders.show', $order->id) }}" class="btn btn-sm btn-outline-info" title="{{ translate('Ver Detalhes') }}">
                                        <i class="tio-visible"></i>
                                    </a>
                                    @if($order->payment_status == 'pending')
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="generatePixQR({{ $order->id }}, '{{ $order->order_number }}', {{ $order->total_amount }})" 
                                            title="{{ translate('Gerar QR Code PIX') }}">
                                        <i class="tio-qr-code"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-center">
                                    <img class="mb-3" src="{{ asset('public/assets/admin/svg/illustrations/sorry.svg') }}" alt="Image Description" style="width: 7rem;">
                                    <p class="mb-0">{{ translate('Nenhum pedido encontrado') }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- PIX QR Code Modal -->
<div class="modal fade" id="pixQRModal" tabindex="-1" role="dialog" aria-labelledby="pixQRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="pixQRModalLabel">{{ translate('QR Code PIX - Pagamento') }}</h4>
                <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal" aria-label="Close">
                    <i class="tio-clear tio-lg"></i>
                </button>
            </div>
            <div class="modal-body text-center">
                <div id="pixQRContent">
                    <!-- QR Code will be generated here -->
                </div>
                <div class="mt-3">
                    <p class="text-muted">{{ translate('Escaneie o QR Code acima com o app do seu banco para efetuar o pagamento via PIX') }}</p>
                    <div class="alert alert-info">
                        <strong>{{ translate('Pedido:') }}</strong> <span id="pixOrderNumber"></span><br>
                        <strong>{{ translate('Valor:') }}</strong> <span id="pixAmount"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Fechar') }}</button>
                <button type="button" class="btn btn-primary" onclick="downloadQRCode()">{{ translate('Baixar QR Code') }}</button>
                <button type="button" class="btn btn-success" onclick="sendPixWhatsApp()">{{ translate('Enviar via WhatsApp') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
let currentOrderData = {};

function generatePixQR(orderId, orderNumber, amount) {
    currentOrderData = {
        orderId: orderId,
        orderNumber: orderNumber,
        amount: amount
    };
    
    // PIX payload (simplified - in production, use proper PIX format)
    const pixKey = '{{ auth('distributor')->user()->pix_key ?? auth('distributor')->user()->phone ?? '' }}';
    const merchantName = '{{ auth('distributor')->user()->f_name }} {{ auth('distributor')->user()->l_name }}';
    const merchantCity = '{{ auth('distributor')->user()->city ?? 'Cidade' }}';
    
    // Generate PIX payload string (EMV format)
    const pixPayload = generatePixPayload(pixKey, merchantName, merchantCity, amount, orderNumber);
    
    // Generate QR Code
    const qrContainer = document.getElementById('pixQRContent');
    qrContainer.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p class="mt-2">{{ translate('Gerando QR Code...') }}</p></div>';
    
    QRCode.toCanvas(pixPayload, { width: 300, margin: 2 }, function (error, canvas) {
        if (error) {
            qrContainer.innerHTML = '<div class="alert alert-danger">{{ translate('Erro ao gerar QR Code') }}</div>';
            return;
        }
        
        qrContainer.innerHTML = '';
        qrContainer.appendChild(canvas);
        
        // Update modal info
        document.getElementById('pixOrderNumber').textContent = orderNumber;
        document.getElementById('pixAmount').textContent = formatCurrency(amount);
        
        // Show modal
        $('#pixQRModal').modal('show');
    });
}

function generatePixPayload(pixKey, merchantName, merchantCity, amount, orderNumber) {
    // Simplified PIX payload generation
    // In production, use a proper PIX library
    const payload = `00020126580014BR.GOV.BCB.PIX0136${pixKey}52040000530398654${String(amount).padStart(10, '0')}5802BR59${merchantName.substring(0, 25).padEnd(25)}60${merchantCity.substring(0, 15).padEnd(15)}62${String(orderNumber).length.toString().padStart(2, '0')}${orderNumber}6304`;
    
    // Calculate CRC16 (simplified)
    const crc = calculateCRC16(payload);
    return payload + crc;
}

function calculateCRC16(data) {
    // Simplified CRC16 calculation
    // In production, use proper CRC16 implementation
    return '0000'; // Placeholder
}

function downloadQRCode() {
    const canvas = document.querySelector('#pixQRContent canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = `pix-qr-${currentOrderData.orderNumber}.png`;
        link.href = canvas.toDataURL();
        link.click();
    }
}

function sendPixWhatsApp() {
    const message = `🏪 *Pagamento PIX - Pedido ${currentOrderData.orderNumber}*\n\n` +
                   `💰 Valor: ${formatCurrency(currentOrderData.amount)}\n` +
                   `📱 Escaneie o QR Code para pagar via PIX\n\n` +
                   `Obrigado pela preferência!`;
    
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(amount);
}
</script>
@endpush