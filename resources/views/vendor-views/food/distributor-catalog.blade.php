@extends('layouts.vendor.app')

@section('title', translate('Catálogo de Distribuidores'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-shop"></i></span>
                    {{ translate('Catálogo de Distribuidores') }}
                </h1>
                <p class="page-header-text">{{ translate('Selecione um distribuidor para ver seus produtos disponíveis') }}</p>
            </div>
        </div>
    </div>

    @if($distributorProducts->count() === 0)
        <div class="card mb-4">
            <div class="card-body d-flex align-items-center justify-content-center py-5">
                <img src="{{ asset('public/assets/admin/img/empty.png') }}" alt="vazio" style="width: 140px;">
                <div class="ml-3 text-left">
                    <h4 class="mb-1">{{ translate('Nenhum produto encontrado neste distribuidor') }}</h4>
                    <p class="text-muted mb-0">{{ translate('O distribuidor ainda não publicou produtos ativos. Tente novamente mais tarde.') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($distributorProducts->count() > 0)
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0 d-flex align-items-center">
                    {{ translate('Produtos Disponíveis') }}
                    <span class="badge badge-soft-primary ml-2">{{ $distributor->f_name }} {{ $distributor->l_name }}</span>
                </h4>
                <small class="text-muted">{{ translate('Habilite os produtos que deseja vender em sua loja') }}</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-thead-bordered table-nowrap align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ translate('Produto') }}</th>
                                <th>{{ translate('Categoria') }}</th>
                                <th class="text-right">{{ translate('Preço') }}</th>
                                <th class="text-center">{{ translate('Mín. por Compra') }}</th>
                                <th class="text-center">Meu Estoque</th>
                                <th class="text-center">Estoque Dist.</th>
                                <th class="text-center">{{ translate('Ativo') }}</th>
                                <th style="min-width: 260px;">{{ translate('Seu Preço de Venda') }}</th>
                                <th style="min-width: 200px;">{{ translate('Fazer Pedido') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($distributorProducts as $index => $product)
                            @php $isEnabled = in_array($product->id, $vendorProducts); @endphp
                            <tr class="product-row" data-product-id="{{ $product->id }}" data-pivot-id="{{ $pivot->id ?? '' }}">
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $product->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                             alt="{{ $product->name }}" 
                                             style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px;" 
                                             class="mr-3">
                                        <div>
                                            <div class="font-weight-semibold">{{ $product->name }}</div>
                                            @if(!empty($product->description))
                                                <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-soft-info">
                                        {{ $product->category ? ($product->category->name ?: $product->category->getOriginal('name') ?: 'N/A') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="text-right text-primary" style="font-size: 14px;">
                                    {{ \App\CentralLogics\Helpers::format_currency($product->price) }}
                                </td>
                                <td class="text-center">
                                    @php $pivot = isset($vendorPivotsMap) ? $vendorPivotsMap->get($product->id) : null; @endphp
                                    <span class="badge badge-soft-secondary">{{ $pivot->min_quantity ?? 1 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ ($pivot && $pivot->stock_quantity>0) ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ $pivot->stock_quantity ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-soft-info">{{ $product->item_stock ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <label class="toggle-switch toggle-switch-sm mb-0">
                                        <input type="checkbox" 
                                               class="toggle-switch-input product-toggle" 
                                               data-product-id="{{ $product->id }}" 
                                               {{ $isEnabled ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td style="min-width: 110px; max-width: 120px;">
                                    <div class="input-group input-group-sm">
                                        <input type="text"
                                               class="form-control form-control-sm sale-price-input"
                                               data-product-id="{{ $product->id }}"
                                               data-min-price="{{ $product->price }}"
                                               placeholder="{{ number_format($product->price, 2, ',', '.') }}"
                                               value="{{ isset($pivot) && isset($pivot->vendor_price) ? number_format($pivot->vendor_price, 2, ',', '.') : '' }}"
                                               style="font-size: 10px; padding: 2px 4px; width: 70px;"
                                               {{ $isEnabled ? '' : 'disabled' }}>
                                        <div class="input-group-append">
                                            <button class="btn btn-success btn-sm update-price-btn"
                                                    data-product-id="{{ $product->id }}"
                                                    type="button"
                                                    title="Salvar preço de venda"
                                                    data-toggle="tooltip"
                                                    style="padding: 4px 8px; font-size: 10px; min-width: 60px;"
                                                    {{ $isEnabled ? '' : 'disabled' }}>
                                                <i class="tio-save" style="font-size: 10px; margin-right: 3px;"></i>
                                                Salvar
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ translate('Mínimo') }}: {{ \App\CentralLogics\Helpers::format_currency($product->price) }}</small>
                                </td>
                                <td style="min-width: 200px;">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-outline-secondary btn-sm quantity-decrease"
                                                    data-product-id="{{ $product->id }}"
                                                    type="button"
                                                    title="Diminuir quantidade"
                                                    data-toggle="tooltip"
                                                    style="padding: 4px 6px; font-size: 12px; border-right: 0;"
                                                    {{ ($isEnabled && (($product->item_stock ?? 0) > 0)) ? '' : 'disabled' }}>
                                                <span style="font-weight: bold;">−</span>
                                            </button>
                                        </div>
                                        <input type="number"
                                               class="form-control form-control-sm quantity-input text-center"
                                               data-product-id="{{ $product->id }}"
                                               placeholder="Qtd"
                                               min="{{ $pivot->min_quantity ?? 1 }}"
                                               max="{{ ($pivot && $pivot->stock_quantity > 0) ? $pivot->stock_quantity : ($product->item_stock ?? 999) }}"
                                               value="{{ $pivot->min_quantity ?? 1 }}"
                                               style="font-size: 10px; padding: 2px 4px; width: 50px; border-left: 0; border-right: 0;"
                                               {{ ($isEnabled && (($product->item_stock ?? 0) > 0)) ? '' : 'disabled' }}>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary btn-sm quantity-increase"
                                                    data-product-id="{{ $product->id }}"
                                                    type="button"
                                                    title="Aumentar quantidade"
                                                    data-toggle="tooltip"
                                                    style="padding: 4px 6px; font-size: 12px; border-left: 0; border-right: 0;"
                                                    {{ ($isEnabled && (($product->item_stock ?? 0) > 0)) ? '' : 'disabled' }}>
                                                <span style="font-weight: bold;">+</span>
                                            </button>
                                            <button class="btn btn-success btn-sm add-to-cart-btn"
                                                    data-product-id="{{ $product->id }}"
                                                    data-pivot-id="{{ $pivot->id ?? '' }}"
                                                    type="button"
                                                    title="Adicionar ao carrinho"
                                                    data-toggle="tooltip"
                                                    style="padding: 4px 8px; font-size: 10px;"
                                                    {{ ($isEnabled && (($product->item_stock ?? 0) > 0)) ? '' : 'disabled' }}>
                                                <i class="tio-shopping-cart" style="font-size: 10px;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('public/assets/admin/img/no-data.png') }}" alt="No products" style="width: 150px;">
                <h4 class="mt-3">{{ translate('Nenhum produto disponível') }}</h4>
                <p class="text-muted">{{ translate('Este distribuidor ainda não possui produtos disponíveis.') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script_2')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
$(function(){
    if ($.fn.mask) {
        $('.sale-price-input').mask('#.##0,00', { reverse: true });
    }
    // Toggle produto (por linha)
    $('.product-toggle').on('change', function(){
        let productId = $(this).data('product-id');
        let isEnabled = $(this).is(':checked');
        let $row = $(this).closest('tr');
        let $input = $row.find('.sale-price-input');
        let $btn = $row.find('.update-price-btn');
        let $qtyInput = $row.find('.quantity-input');
        let $btnAdd = $row.find('.add-to-cart-btn');
        let $btnInc = $row.find('.quantity-increase');
        let $btnDec = $row.find('.quantity-decrease');
        let distributorStockText = $row.find('td:nth-child(6) .badge').text().trim();
        let distributorStock = parseInt(distributorStockText) || 0;
        
        if (isEnabled) {
            // Habilitar input e botão de preço
            $input.prop('disabled', false);
            $btn.prop('disabled', false);

            // Se houver preço no input, validar e enviar; caso contrário, enviar nulo (backend assume preço do distribuidor)
            let salePriceToSend = null;
            let raw = ($input.val() || '').toString().trim();
            if (raw) {
                // Converter máscara pt-BR para número (somente dígitos e divide por 100)
                let digits = raw.replace(/\D/g, '');
                if (digits) {
                    salePriceToSend = parseInt(digits, 10) / 100;
                }
                // Validar mínimo se houver preço informado
                if (salePriceToSend) {
                    let minPrice = parseFloat(($input.data('min-price') || '').toString());
                    if (minPrice && salePriceToSend < minPrice) {
                        toastr.error('{{ translate('O preço de venda deve ser maior ou igual ao preço do distribuidor') }}');
                        // Reverter toggle
                        $(this).prop('checked', false);
                        return;
                    }
                }
            }

            // Salvar estado habilitado imediatamente
            toggleProduct(productId, true, salePriceToSend, function(res){
                // Ativar controles de pedido quando existir pivot_id e houver estoque do distribuidor
                if (res && res.pivot_id) {
                    $row.attr('data-pivot-id', res.pivot_id).data('pivot-id', res.pivot_id);
                    $btnAdd.data('pivot-id', res.pivot_id).attr('data-pivot-id', res.pivot_id);
                    if (distributorStock > 0) {
                        $btnAdd.prop('disabled', false);
                        $qtyInput.prop('disabled', false);
                        $btnInc.prop('disabled', false);
                        $btnDec.prop('disabled', false);
                    }
                }
            });
        } else {
            $input.prop('disabled', true).val('');
            $btn.prop('disabled', true);
            // Desabilitar produto
            toggleProduct(productId, false, null, function(res){
                // Ao desabilitar com sucesso, bloquear controles de pedido
                $row.removeAttr('data-pivot-id').removeData('pivot-id');
                $btnAdd.removeData('pivot-id').attr('data-pivot-id','').prop('disabled', true);
                $qtyInput.prop('disabled', true);
                $btnInc.prop('disabled', true);
                $btnDec.prop('disabled', true);
            });
        }
    });
    
    // Atualizar preço
    $('.update-price-btn').on('click', function(){
        let productId = $(this).data('product-id');
        let $row = $(this).closest('tr');
        let $input = $row.find('.sale-price-input');
        // Converte máscara pt-BR para número (somente dígitos e divide por 100)
        let digits = ($input.val() || '').toString().replace(/\D/g,'');
        let salePrice = digits ? (parseInt(digits, 10) / 100) : NaN;
        
        if (!salePrice || salePrice <= 0) {
            toastr.error('{{ translate('Por favor, informe um preço válido') }}');
            return;
        }
        
        let minPrice = parseFloat(($input.data('min-price') || '').toString());
        if (minPrice && salePrice < minPrice) {
            toastr.error('{{ translate('O preço de venda deve ser maior ou igual ao preço do distribuidor') }}');
            return;
        }
        
        toggleProduct(productId, true, salePrice, function(res){
            // Ao habilitar com sucesso, se houver pivot_id, ativar os controles de pedido
            if (res && res.pivot_id) {
                let $btnAdd = $row.find('.add-to-cart-btn');
                let $qtyInput = $row.find('.quantity-input');
                let $btnInc = $row.find('.quantity-increase');
                let $btnDec = $row.find('.quantity-decrease');
                let distributorStockText = $row.find('td:nth-child(6) .badge').text().trim();
                let distributorStock = parseInt(distributorStockText) || 0;
                
                $row.attr('data-pivot-id', res.pivot_id).data('pivot-id', res.pivot_id);
                $btnAdd.data('pivot-id', res.pivot_id).attr('data-pivot-id', res.pivot_id);
                if (distributorStock > 0) {
                    $btnAdd.prop('disabled', false);
                    $qtyInput.prop('disabled', false);
                    $btnInc.prop('disabled', false);
                    $btnDec.prop('disabled', false);
                }
            }
            $row.find('.sale-price-input').val('');
        });
    });
    
    function toggleProduct(productId, enable, salePrice = null, onSuccessCb = null) {
        $.post({
            url: '{{ url('/') }}{{ route('vendor.food.toggle-distributor-product', [], false) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                _token: '{{ csrf_token() }}',
                distributor_product_id: productId,
                enable: enable ? 1 : 0,
                sale_price: salePrice
            },
            success: function(res){
                if(res.success){
                    toastr.success(res.message);
                    if (typeof onSuccessCb === 'function') {
                        try { onSuccessCb(res); } catch(e){}
                    }
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao processar solicitação') }}');
                    // Reverter toggle se houver erro
                    let $toggle = $(".product-toggle[data-product-id='"+productId+"']");
                    $toggle.prop('checked', !enable);
                }
            },
            error: function(xhr){
                let msg = '{{ translate('Erro ao processar solicitação') }}';
                if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                toastr.error(msg);
                // Reverter toggle se houver erro
                let $toggle = $(".product-toggle[data-product-id='"+productId+"']");
                $toggle.prop('checked', !enable);
            }
        });
    }
});

    // Adicionar ao carrinho
    $('.add-to-cart-btn').on('click', function(){
        let productId = $(this).data('product-id');
        let pivotId = $(this).data('pivot-id') || $(this).closest('tr').data('pivot-id');
        let $row = $(this).closest('tr');
        let $quantityInput = $row.find('.quantity-input');
        let quantity = parseInt($quantityInput.val()) || 1;
        // Checagem extra: se estoque do fornecedor (coluna Estoque Dist.) for 0, bloquear
        let distributorStockText = $row.find('td:nth-child(6) .badge').text().trim();
        let distributorStock = parseInt(distributorStockText) || 0;
        if (distributorStock <= 0) {
            toastr.error('{{ translate('Estoque do distribuidor indisponível') }}');
            return;
        }

        if (!pivotId) {
            toastr.error('{{ translate('Habilite o produto para poder adicionar ao carrinho') }}');
            return;
        }
        
        if (quantity <= 0) {
            toastr.error('{{ translate('Por favor, informe uma quantidade válida') }}');
            return;
        }
        
        let maxQuantity = parseInt($quantityInput.attr('max'));
        if (quantity > maxQuantity) {
            toastr.error('{{ translate('Quantidade não disponível em estoque') }}');
            return;
        }
        
        $.post({
            url: '{{ url('/') }}{{ route('vendor.distributor-products.add-to-cart', [], false) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                _token: '{{ csrf_token() }}',
                product_id: pivotId,
                quantity: quantity
            },
            success: function(res){
                if(res.success){
                    toastr.success(res.message);
                    updateCartCount();
                    // Resetar quantidade para 1
                    $quantityInput.val(1);
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao adicionar produto ao carrinho') }}');
                }
            },
            error: function(){
                toastr.error('{{ translate('Erro ao processar solicitação') }}');
            }
        });
    });
    
// Botões de incremento e decremento de quantidade (usando delegação de eventos)
    $(document).on('click', '.quantity-increase', function(){
        let productId = $(this).data('product-id');
        let $row = $(this).closest('tr');
        let $quantityInput = $row.find('.quantity-input');
        let currentQuantity = parseInt($quantityInput.val()) || 1;
        let maxQuantity = parseInt($quantityInput.attr('max'));
        
        if (currentQuantity < maxQuantity) {
            $quantityInput.val(currentQuantity + 1);
        } else {
            toastr.warning('{{ translate('Quantidade máxima atingida') }}');
        }
    });
    
    $(document).on('click', '.quantity-decrease', function(){
        let productId = $(this).data('product-id');
        let $row = $(this).closest('tr');
        let $quantityInput = $row.find('.quantity-input');
        let currentQuantity = parseInt($quantityInput.val()) || 1;
        let minQuantity = parseInt($quantityInput.attr('min'));
        
        if (currentQuantity > minQuantity) {
            $quantityInput.val(currentQuantity - 1);
        } else {
            toastr.warning('{{ translate('Quantidade mínima atingida') }}');
        }
    });
    
    // Função para atualizar contagem do carrinho
    function updateCartCount() {
        $.get('{{ url('/') }}{{ route('vendor.distributor-products.cart-count', [], false) }}')
            .done(function(data){
                let count = data.cart_count || 0;
                $('#header-distributor-cart-count').text(count);
                $('.cart-count').text(count);
            })
            .fail(function(){
                console.log('Erro ao atualizar contagem do carrinho');
            });
    }
    
    // Atualizar contagem do carrinho ao carregar a página
    updateCartCount();

    // Inicializar tooltips
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
