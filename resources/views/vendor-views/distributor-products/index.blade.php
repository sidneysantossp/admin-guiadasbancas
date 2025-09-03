@extends('layouts.vendor.app')

@section('title', translate('Produtos dos Distribuidores'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-shopping-basket"></i></span>
                    {{ translate('Produtos dos Distribuidores') }}
                </h1>
                <p class="page-header-text">{{ translate('Encontre produtos de distribuidores para seu negócio') }}</p>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-primary" href="{{ route('vendor.distributor-products.cart') }}">
                    <i class="tio-shopping-cart"></i> 
                    {{ translate('Carrinho') }}
                    <span class="badge badge-light ml-1" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('vendor.distributor-products.index') }}">
                <div class="row g-3">
                    <div class="col-lg-3">
                        <select name="distributor_id" class="form-control">
                            <option value="">{{ translate('Todos os Distribuidores') }}</option>
                            @foreach($distributors as $distributor)
                                <option value="{{ $distributor->id }}" {{ request('distributor_id') == $distributor->id ? 'selected' : '' }}>
                                    {{ $distributor->f_name }} {{ $distributor->l_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="in_stock" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="in_stock">
                                {{ translate('Apenas com estoque') }}
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <input type="text" name="search" class="form-control" placeholder="{{ translate('Buscar produto...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="tio-search"></i> {{ translate('Filtrar') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Grid -->
    @if($products->count() > 0)
        <div class="row">
            @foreach($products as $product)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 product-card">
                        <div class="card-img-top position-relative">
                            <img src="{{ $product->food->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}" 
                                 alt="{{ $product->food->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            
                            <!-- Stock Badge -->
                            @if($product->stock_quantity > 0)
                                <span class="badge badge-success position-absolute" style="top: 10px; right: 10px;">
                                    {{ translate('Em estoque') }}: {{ $product->stock_quantity }}
                                </span>
                            @else
                                <span class="badge badge-danger position-absolute" style="top: 10px; right: 10px;">
                                    {{ translate('Sem estoque') }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->food->name }}</h5>
                            
                            <div class="mb-2">
                                <small class="text-muted">{{ translate('Distribuidor') }}:</small>
                                <strong>{{ $product->distributor->f_name }} {{ $product->distributor->l_name }}</strong>
                            </div>
                            
                            <div class="mb-2">
                                <small class="text-muted">{{ translate('Categoria') }}:</small>
                                {{ $product->food->category->name ?? 'N/A' }}
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="h5 text-primary mb-0">
                                            {{ \App\CentralLogics\Helpers::format_currency($product->final_price) }}
                                        </span>
                                        @if($product->margin_percentage > 0)
                                            <br><small class="text-success">{{ translate('Margem') }}: {{ $product->margin_percentage }}%</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">
                                        {{ translate('Mín') }}: {{ $product->min_quantity }}
                                    </small>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                @if($product->stock_quantity > 0)
                                    <div class="input-group mb-2">
                                        <input type="number" class="form-control quantity-input" 
                                               min="{{ $product->min_quantity }}" 
                                               max="{{ $product->stock_quantity }}" 
                                               value="{{ $product->min_quantity }}"
                                               data-product-id="{{ $product->id }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('un') }}</span>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block add-to-cart" 
                                            data-product-id="{{ $product->id }}">
                                        <i class="tio-shopping-cart"></i> {{ translate('Adicionar ao Carrinho') }}
                                    </button>
                                @else
                                    <button class="btn btn-secondary btn-block" disabled>
                                        <i class="tio-shopping-cart-off"></i> {{ translate('Sem Estoque') }}
                                    </button>
                                @endif
                                
                                <a href="{{ route('vendor.distributor-products.show', $product->id) }}" 
                                   class="btn btn-outline-primary btn-block mt-2">
                                    <i class="tio-visible"></i> {{ translate('Ver Detalhes') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('public/assets/admin/img/no-data.png') }}" alt="No products" style="width: 150px;">
                <h4 class="mt-3">{{ translate('Nenhum produto encontrado') }}</h4>
                <p class="text-muted">{{ translate('Não há produtos disponíveis dos distribuidores no momento') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script_2')
<script>
    $(document).ready(function() {
        // Update cart count on page load
        updateCartCount();
        
        // Add to cart functionality
        $('.add-to-cart').on('click', function() {
            let productId = $(this).data('product-id');
            let quantity = $(`.quantity-input[data-product-id="${productId}"]`).val();
            
            $.post({
                url: '{{ route('vendor.distributor-products.add-to-cart') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    quantity: quantity
                },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        updateCartCount();
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function() {
                    toastr.error('{{ translate('Erro ao adicionar produto ao carrinho') }}');
                }
            });
        });
        
        // Update quantity validation
        $('.quantity-input').on('input', function() {
            let min = parseInt($(this).attr('min'));
            let max = parseInt($(this).attr('max'));
            let value = parseInt($(this).val());
            
            if (value < min) {
                $(this).val(min);
            } else if (value > max) {
                $(this).val(max);
                toastr.warning('{{ translate('Quantidade máxima disponível') }}: ' + max);
            }
        });
    });
    
    function updateCartCount() {
        $.get("{{ route('vendor.distributor-products.cart-count') }}")
            .done(function (data) {
                var count = data.cart_count || 0;
                $('.cart-count').text(count);
                // Also update header badge if present
                var headerBadge = $('#header-distributor-cart-count');
                if (headerBadge.length) {
                    headerBadge.text(count);
                }
            })
            .fail(function () {
                // Fallback: keep current count if request fails
                console.warn('Falha ao buscar quantidade do carrinho do distribuidor');
            });
    }
</script>

<style>
.product-card {
    transition: transform 0.2s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.1);
}

.card-img-top {
    transition: transform 0.3s;
}

.product-card:hover .card-img-top {
    transform: scale(1.05);
}
</style>
@endpush
