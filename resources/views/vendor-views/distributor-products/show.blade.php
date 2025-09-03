@extends('layouts.vendor.app')

@section('title', translate('Detalhes do Produto do Distribuidor'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-sm mb-2 mb-sm-0">
                <h1 class="page-header-title">
                    <span class="page-header-icon"><i class="tio-visible"></i></span>
                    {{ translate('Detalhes do Produto do Distribuidor') }}
                </h1>
                <a href="{{ route('vendor.distributor-products.index') }}" class="btn btn-soft-secondary mt-2">
                    <i class="tio-arrow-large-backward"></i> {{ translate('Voltar ao Catálogo') }}
                </a>
            </div>
            <div class="col-sm-auto">
                <a class="btn btn-primary" href="{{ url('/') }}{{ route('vendor.distributor-products.cart', [], false) }}">
                    <i class="tio-shopping-cart"></i> {{ translate('Carrinho') }}
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="row no-gutters">
            <div class="col-lg-5">
                <img src="{{ $product->food->image_full_url ?? asset('public/assets/admin/img/160x160/img2.jpg') }}" alt="{{ $product->food->name }}" class="w-100 h-100" style="object-fit: cover; min-height: 320px;">
            </div>
            <div class="col-lg-7">
                <div class="card-body">
                    <h3 class="mb-2">{{ $product->food->name }}</h3>
                    <p class="mb-1 text-muted">{{ translate('Distribuidor') }}: <strong>{{ $product->distributor->f_name }} {{ $product->distributor->l_name }}</strong></p>
                    <p class="mb-1 text-muted">{{ translate('Categoria') }}: {{ $product->food->category->name ?? 'N/A' }}</p>
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-4">
                            <div class="h4 text-primary mb-0">{{ \App\CentralLogics\Helpers::format_currency($product->final_price) }}</div>
                            @if($product->margin_percentage > 0)
                                <small class="text-success">{{ translate('Margem') }}: {{ $product->margin_percentage }}%</small>
                            @endif
                        </div>
                        <div>
                            @if($product->stock_quantity > 0)
                                <span class="badge badge-success">{{ translate('Em estoque') }}: {{ $product->stock_quantity }}</span>
                            @else
                                <span class="badge badge-danger">{{ translate('Sem estoque') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ translate('Quantidade mínima') }}:</small>
                        <strong>{{ $product->min_quantity }}</strong>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <div class="input-group">
                                <input type="number" id="quantity" class="form-control" min="{{ $product->min_quantity }}" max="{{ $product->stock_quantity }}" value="{{ min(max($product->min_quantity,1), max($product->stock_quantity, $product->min_quantity)) }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ translate('un') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            @if($product->stock_quantity > 0)
                                <button id="addToCartBtn" class="btn btn-primary btn-block">
                                    <i class="tio-shopping-cart"></i> {{ translate('Adicionar ao Carrinho') }}
                                </button>
                            @else
                                <button class="btn btn-secondary btn-block" disabled>
                                    <i class="tio-shopping-cart-off"></i> {{ translate('Sem Estoque') }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script>
$(function(){
    $('#quantity').on('input', function(){
        let min = parseInt($(this).attr('min'));
        let max = parseInt($(this).attr('max'));
        let val = parseInt($(this).val());
        if (isNaN(val) || val < min) $(this).val(min);
        if (val > max) { $(this).val(max); toastr.warning('{{ translate('Quantidade máxima disponível') }}: ' + max); }
    });

    $('#addToCartBtn').on('click', function(){
        $.post({
            url: '{{ url('/') }}{{ route('vendor.distributor-products.add-to-cart', [], false) }}',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: {
                _token: '{{ csrf_token() }}',
                product_id: {{ $product->id }},
                quantity: $('#quantity').val()
            },
            success: function(res){
                if(res.success){
                    toastr.success(res.message);
                } else {
                    toastr.error(res.message || '{{ translate('Erro ao adicionar ao carrinho') }}');
                }
            },
            error: function(){ toastr.error('{{ translate('Erro ao adicionar ao carrinho') }}'); }
        });
    });
});
</script>
@endpush
