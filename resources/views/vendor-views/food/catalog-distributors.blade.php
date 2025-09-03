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

    @if($vendors->count() > 0)
        <div class="row">
            @foreach($vendors as $vendor)
                <div class="col-lg-3 col-md-4 mb-4">
                    <div class="card h-100 distributor-card" style="background-color:#ffffff; border:0; border-radius:12px;">
                    
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($vendor->image)
                                    <img src="{{ $vendor->image_full_url }}" 
                                         alt="{{ $vendor->f_name }} {{ $vendor->l_name }}" 
                                         class="rounded-circle" 
                                         style="width: 88px; height: 88px; object-fit: cover; border: 1px solid #e5e7eb;">
                                @else
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 88px; height: 88px; background: #f3f4f6; border: 1px solid #e5e7eb;">
                                        <i class="tio-shop" style="font-size: 2rem; color: #6b7280;"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <h5 class="card-title mb-3 text-center">{{ $vendor->f_name }} {{ $vendor->l_name }}</h5>
                            
                            <div class="mb-3">
                                <span class="badge badge-soft-primary-light">
                                    <i class="tio-checkmark-circle" style="margin-right: 5px;"></i>
                                    {{ $vendor->available_products_count ?? 0 }} {{ translate('produtos disponíveis') }}
                                </span>
                            </div>
                            
                            <a href="{{ route('vendor.food.distributor-catalog', $vendor->id) }}" 
                                class="btn btn-sidebar btn-block">
                                 <i class="tio-visible" style="margin-right: 8px;"></i> {{ translate('Ver Catálogo') }}
                             </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="{{ asset('public/assets/admin/img/no-data.png') }}" alt="No distributors" style="width: 150px;">
                <h4 class="mt-3">{{ translate('Nenhum distribuidor disponível') }}</h4>
                <p class="text-muted">{{ translate('Não há distribuidores com produtos disponíveis no momento.') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection

@push('script_2')
<script>
$(function(){
    // Sombra controlada apenas via CSS nesta página
});
</script>
@endpush

<style>
    .card.distributor-card {
        transition: box-shadow 0.2s ease, background-color 0.2s ease;
        border: 0 !important;
        border-radius: 12px;
        background-color: #ffffff !important; /* branco */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06) !important; /* sombra sutil padrão */
    }

    .card.distributor-card .card-body {
        background-color: transparent !important;
    }

    .card.distributor-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important; /* sombra mais forte no hover */
        background-color: #ffffff !important; /* mantém branco no hover */
    }

    .distributor-card .card-title {
        text-align: center !important;
        margin-left: auto;
        margin-right: auto;
        color: #111827;
        font-weight: 600;
        font-size: 1rem;
    }

    .badge-soft-primary-light {
        background-color: #FFFBEB !important; /* amarelo 50 */
        color: #CA8A04 !important; /* amarelo 600 */
        border: 1px solid #FDE68A !important; /* amarelo 200 */
        padding: 6px 10px;
        border-radius: 16px;
        font-size: 0.80rem;
        font-weight: 500;
    }

    /* Botão com a mesma cor da sidebar (apenas nesta página) */
    .btn.btn-sidebar {
        background-color: #334257 !important;
        border: 1px solid #334257 !important;
        color: #fff !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 0.375rem; /* 6px */
        text-decoration: none !important;
    }
    .btn.btn-sidebar i { margin-right: 8px; }
    
    .btn.btn-sidebar:hover,
    .btn.btn-sidebar:focus {
        background-color: #334257 !important;
        border-color: #334257 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(51, 66, 87, 0.2);
    }
    /* estados ativos */
    .btn.btn-sidebar:active,
    .btn.btn-sidebar.active,
    .show > .btn.btn-sidebar.dropdown-toggle,
    .btn.btn-sidebar:not(:disabled):not(.disabled):active,
    .btn.btn-sidebar:not(:disabled):not(.disabled).active {
        background-color: #2a3542 !important;
        border-color: #2a3542 !important;
        color: #fff !important;
        box-shadow: none !important;
    }
</style>