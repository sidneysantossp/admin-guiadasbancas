@extends('layouts.distributor.app')

@section('title', translate('Reports'))

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="page--header-title">
                <h1 class="page-header-title">{{translate('messages.reports')}}</h1>
                <p class="page-header-text">{{translate('messages.distributor_reports_subtitle')}}</p>
            </div>
        </div>
    </div>
    <!-- End Page Header -->

    <!-- Stats -->
    <div class="card mb-3">
        <div class="card-body pt-0">
            <div class="d-flex flex-wrap justify-content-between statistics--title-area">
                <div class="statistics--title pr-sm-3">
                    <h4 class="m-0 mr-1">
                        {{translate('messages.business_statistics')}}
                    </h4>
                </div>
            </div>
            
            <div class="row g-2 mt-2">
                <div class="col-xl-4 col-sm-6">
                    <div class="resturant-card dashboard--card bg--2">
                        <h4 class="title">{{ $total_orders }}</h4>
                        <span class="subtitle">{{translate('messages.total_orders')}}</span>
                        <i class="fas fa-chart-line resturant-icon" style="font-size: 2.5rem; color: #377dff;"></i>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6">
                    <div class="resturant-card dashboard--card bg--3">
                        <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($total_revenue) }}</h4>
                        <span class="subtitle">{{translate('messages.total_revenue')}}</span>
                        <i class="fas fa-dollar-sign resturant-icon" style="font-size: 2.5rem; color: #28a745;"></i>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6">
                    <div class="resturant-card dashboard--card bg--5">
                        <h4 class="title">{{ $total_products }}</h4>
                        <span class="subtitle">{{translate('messages.total_products')}}</span>
                        <i class="fas fa-box resturant-icon" style="font-size: 2.5rem; color: #ffc107;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">
                        <i class="fas fa-chart-bar mr-2"></i>
                        {{translate('messages.order_reports')}}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{translate('messages.view_detailed_order_reports')}}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        {{translate('messages.view_reports')}}
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">
                        <i class="fas fa-chart-pie mr-2"></i>
                        {{translate('messages.product_reports')}}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{translate('messages.view_product_performance_reports')}}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        {{translate('messages.view_reports')}}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        {{translate('messages.revenue_reports')}}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{translate('messages.view_revenue_and_earnings_reports')}}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        {{translate('messages.view_reports')}}
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        {{translate('messages.daily_reports')}}
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{translate('messages.view_daily_business_reports')}}</p>
                    <a href="#" class="btn btn-primary btn-sm">
                        {{translate('messages.view_reports')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection