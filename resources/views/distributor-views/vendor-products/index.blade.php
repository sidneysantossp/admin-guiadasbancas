@extends('layouts.distributor.app')

@section('title', translate('Gerenciar Estoque de Jornaleiros'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-shopping-cart"></i></span>
                    {{ translate('Gerenciar Estoque de Jornaleiros') }}
                </h1>
                <p class="page-header-text">{{ translate('Gerencie o estoque dos produtos habilitados pelos jornaleiros') }}</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('distributor.vendor-products.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <select name="vendor_id" class="form-control">
                            <option value="">{{ translate('Todos os Jornaleiros') }}</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->f_name }} {{ $vendor->l_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="{{ translate('Buscar produto...') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}>
                            <label class="form-check-label">{{ translate('Estoque baixo') }}</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">{{ translate('Filtrar') }}</button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success" onclick="bulkUpdateStock()">{{ translate('Atualizar em Lote') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($products->count() === 0)
        <div class="card mb-4">
            <div class="card-body d-flex align-items-center justify-content-center py-5">
                <img src="{{ asset('public/assets/admin/img/empty.png') }}" alt="vazio" style="width: 140px;">
                <div class="ml-3 text-left">
                    <h4 class="mb-1">{{ translate('Nenhum produto encontrado') }}</h4>
                    <p class="text-muted mb-0">{{ translate('Nenhum jornaleiro habilitou produtos ainda.') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($products->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-title">{{ translate('Produtos Habilitados pelos Jornaleiros') }}</h5>
            </div>
            <div class="card-body p-0">
                <form id="bulk-update-form">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>{{ translate('Produto') }}</th>
                                    <th>{{ translate('Jornaleiro') }}</th>
                                    <th>{{ translate('Preço Distribuidor') }}</th>
                                    <th>{{ translate('Preço Jornaleiro') }}</th>
                                    <th>{{ translate('Estoque Atual') }}</th>
                                    <th>{{ translate('Novo Estoque') }}</th>
                                    <th>{{ translate('Ações') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                                        </td>
                                        <td>
                                            <div class="media align-items-center">
                                                <img class="avatar avatar-lg mr-3" src="{{ $product->food->image_full_url }}" alt="{{ $product->food->name }}">
                                                <div class="media-body">
                                                    <h5 class="text-hover-primary mb-0">{{ $product->food->name }}</h5>
                                                    <span class="d-block font-size-sm text-body">{{ $product->food->category->name ?? '' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="d-block h5 mb-0">{{ $product->vendor->f_name }} {{ $product->vendor->l_name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-info">R$ {{ number_format($product->food->price, 2, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-soft-success">R$ {{ number_format($product->vendor_price ?? $product->food->price, 2, ',', '.') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->stock_quantity <= 10 ? 'badge-soft-danger' : 'badge-soft-primary' }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control stock-input" 
                                                   data-product-id="{{ $product->id }}" 
                                                   value="{{ $product->stock_quantity }}" 
                                                   min="0" 
                                                   style="width: 80px;">
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    onclick="updateSingleStock({{ $product->id }})">
                                                {{ translate('Atualizar') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@push('script_2')
<script>
    // Selecionar todos os checkboxes
    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', $(this).prop('checked'));
    });

    // Atualizar estoque individual
    function updateSingleStock(productId) {
        let stockQuantity = $(`.stock-input[data-product-id="${productId}"]`).val();
        
        $.post({
            url: '{{ route('distributor.vendor-products.update-stock') }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                _token: '{{ csrf_token() }}',
                product_id: productId,
                stock_quantity: stockQuantity
            },
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao atualizar estoque') }}');
                }
            },
            error: function() {
                toastr.error('{{ translate('Erro ao processar solicitação') }}');
            }
        });
    }

    // Atualizar estoque em lote
    function bulkUpdateStock() {
        let selectedProducts = [];
        
        $('.product-checkbox:checked').each(function() {
            let productId = $(this).val();
            let stockQuantity = $(`.stock-input[data-product-id="${productId}"]`).val();
            
            selectedProducts.push({
                id: productId,
                stock_quantity: stockQuantity
            });
        });
        
        if (selectedProducts.length === 0) {
            toastr.warning('{{ translate('Selecione pelo menos um produto') }}');
            return;
        }
        
        $.post({
            url: '{{ route('distributor.vendor-products.bulk-update-stock') }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                _token: '{{ csrf_token() }}',
                products: selectedProducts
            },
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao atualizar estoque') }}');
                }
            },
            error: function() {
                toastr.error('{{ translate('Erro ao processar solicitação') }}');
            }
        });
    }
</script>
@endpush