@extends('layouts.vendor.app')

@section('title', translate('Carrinho de Distribuidores'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-shopping-cart"></i></span>
                    {{ translate('Carrinho de Compras (Distribuidores)') }}
                </h1>
                <a href="{{ route('vendor.distributor-products.index') }}" class="btn btn-soft-secondary mt-2">
                    <i class="tio-arrow-large-backward"></i> {{ translate('Voltar ao Catálogo') }}
                </a>
            </div>
        </div>
    </div>

    @if(count($cartByDistributor) > 0)
        @foreach($cartByDistributor as $distributorId => $group)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        {{ translate('Distribuidor') }}: {{ $group['distributor']->f_name }} {{ $group['distributor']->l_name }}
                    </h4>
                    <div>
                        <span class="h5 mb-0 text-primary">{{ \App\CentralLogics\Helpers::format_currency($group['total']) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ translate('Produto') }}</th>
                                    <th style="width: 180px;">{{ translate('Quantidade') }}</th>
                                    <th>{{ translate('Preço Unitário') }}</th>
                                    <th>{{ translate('Total') }}</th>
                                    <th style="width: 60px;">{{ translate('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group['items'] as $item)
                                    <tr data-cart-key="{{ $item['cart_key'] }}" data-unit-price="{{ $item['unit_price'] }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item['image'] ?? asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="{{ $item['product_name'] }}" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                                <div class="ml-3">
                                                    <strong>{{ $item['product_name'] }}</strong><br>
                                                    <small class="text-muted">{{ translate('Mín') }}: {{ $item['min_quantity'] }} | {{ translate('Estoque') }}: {{ $item['stock_quantity'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <input type="number" class="form-control form-control-sm quantity-input" min="{{ $item['min_quantity'] }}" max="{{ $item['stock_quantity'] }}" value="{{ $item['quantity'] }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-sm btn-outline-secondary update-qty" type="button">{{ translate('Atualizar') }}</button>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \App\CentralLogics\Helpers::format_currency($item['unit_price']) }}</td>
                                        <td class="item-total">{{ \App\CentralLogics\Helpers::format_currency($item['total_price']) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger remove-item" type="button">
                                                <i class="tio-delete"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form method="POST" action="{{ url('/') }}{{ route('vendor.distributor-products.checkout', [], false) }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="distributor_id" value="{{ $distributorId }}">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">{{ translate('Dados de Entrega') }}</h6>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="auto-fill-{{ $distributorId }}">
                                        <i class="tio-refresh"></i> {{ translate('Preencher com meus dados') }}
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="input-label">{{ translate('Endereço de Entrega') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="delivery_address" 
                                       placeholder="{{ translate('Informe o endereço de entrega') }}" 
                                       value="{{ auth('vendor')->user()->restaurants->first()->address ?? '' }}" required>
                                <small class="text-muted">{{ translate('Endereço onde os produtos devem ser entregues') }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="input-label">{{ translate('Data de Entrega') }}</label>
                                <input type="date" class="form-control" name="delivery_date" 
                                       min="{{ now()->addDay()->toDateString() }}" 
                                       value="{{ now()->addDays(2)->toDateString() }}">
                                <small class="text-muted">{{ translate('Data desejada para entrega') }}</small>
                            </div>
                            <div class="col-md-3">
                                <label class="input-label">&nbsp;</label>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="tio-checkmark-circle"></i> {{ translate('Finalizar Pedido') }}
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="input-label">{{ translate('Observações do Pedido') }}</label>
                                <textarea class="form-control" name="notes" rows="2" 
                                          placeholder="{{ translate('Alguma observação especial para este pedido?') }}"></textarea>
                                <small class="text-muted">{{ translate('Informações adicionais sobre horário de entrega, localização específica, etc.') }}</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="card">
            <div class="card-body d-flex justify-content-between">
                <h4 class="mb-0">{{ translate('Total Geral') }}</h4>
                <h4 class="mb-0 text-primary">{{ \App\CentralLogics\Helpers::format_currency($totalAmount) }}</h4>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('public/assets/admin/img/no-data.png') }}" alt="No items" style="width: 150px;">
                <h4 class="mt-3">{{ translate('Seu carrinho está vazio') }}</h4>
                <a href="{{ route('vendor.distributor-products.index') }}" class="btn btn-primary mt-3">
                    <i class="tio-shopping-basket"></i> {{ translate('Ir ao Catálogo') }}
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script_2')
<script>
function formatCurrency(val){
    try { return new Intl.NumberFormat(undefined, {style: 'currency', currency: '{{ config('currency', 'BRL') }}'}).format(val); }
    catch(e){ return (parseFloat(val)||0).toFixed(2); }
}

function updateCartCount(){
    $.get("{{ url('/') }}{{ route('vendor.distributor-products.cart-count', [], false) }}")
        .done(function(res){
            var count = res.cart_count || 0;
            $('#header-distributor-cart-count').text(count);
            $('.cart-count').text(count);
        });
}

$(document).ready(function(){
    updateCartCount();

    // Auto-fill functionality
    $('[id^="auto-fill-"]').on('click', function(){
        let $form = $(this).closest('form');
        let vendorData = {
            address: '{{ auth('vendor')->user()->restaurants->first()->address ?? '' }}',
            phone: '{{ auth('vendor')->user()->phone ?? '' }}',
            email: '{{ auth('vendor')->user()->email ?? '' }}',
            name: '{{ auth('vendor')->user()->f_name ?? '' }} {{ auth('vendor')->user()->l_name ?? '' }}'
        };
        $form.find('input[name="delivery_address"]').val(vendorData.address);
        let deliveryDate = new Date();
        deliveryDate.setDate(deliveryDate.getDate() + 2);
        $form.find('input[name="delivery_date"]').val(deliveryDate.toISOString().split('T')[0]);
        let defaultNote = `Contato: ${vendorData.phone} - ${vendorData.email}`;
        $form.find('textarea[name="notes"]').val(defaultNote);
        toastr.success("{{ translate('Dados preenchidos automaticamente!') }}");
    });

    $('.update-qty').on('click', function(){
        let $row = $(this).closest('tr');
        let cartKey = $row.data('cart-key');
        let qty = parseInt($row.find('.quantity-input').val());
        $.post({
            url: '{{ url('/') }}{{ route('vendor.distributor-products.update-cart', [], false) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {_token:'{{ csrf_token() }}', cart_key: cartKey, quantity: qty},
            success: function(res){
                if(res.success){
                    toastr.success(res.message);
                    let unit = parseFloat($row.data('unit-price'));
                    $row.find('.item-total').text(formatCurrency(unit * qty));
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao atualizar carrinho') }}');
                }
            },
            error: function(){ toastr.error('{{ translate('Erro ao atualizar carrinho') }}'); }
        });
    });

    $('.remove-item').on('click', function(){
        let $row = $(this).closest('tr');
        let cartKey = $row.data('cart-key');
        $.post({
            url: '{{ url('/') }}{{ route('vendor.distributor-products.remove-from-cart', [], false) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {_token:'{{ csrf_token() }}', cart_key: cartKey},
            success: function(res){
                if(res.success){ toastr.success(res.message); $row.remove(); }
                else { toastr.error(res.message || '{{ translate('Erro ao remover item') }}'); }
            },
            error: function(){ toastr.error('{{ translate('Erro ao remover item') }}'); }
        });
    });

    // After any ajax call to distributor-products endpoints, refresh counter
    $(document).ajaxSuccess(function(event, xhr, settings){
        try{
            if(settings && settings.url && settings.url.indexOf('vendor/distributor-products') !== -1){
                var data = xhr.responseJSON || {};
                if(typeof data.cart_count !== 'undefined'){
                    $('#header-distributor-cart-count').text(data.cart_count);
                    $('.cart-count').text(data.cart_count);
                } else {
                    updateCartCount();
                }
            }
        }catch(e){ /* silent */ }
    });
});
</script>
@endpush
