@extends('layouts.distributor.app')

@section('title', translate('Dashboard'))

@section('content')
<div class="content container-fluid dashboard-compact">
    <!-- Page Header -->
    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div class="page--header-title">
                <h1 class="page-header-title">{{translate('messages.welcome')}}, {{ $data['distributor']->f_name }}!</h1>
                <p class="page-header-text">{{translate('messages.distributor_dashboard_subtitle')}}</p>
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
                        {{translate('messages.order_statistics')}}
                    </h4>
                </div>
            </div>
            
            <div class="row g-2 mt-2">
                <div class="col-xl-3 col-sm-6">
                    <div class="resturant-card dashboard--card bg--2">
                        <h4 class="title">{{ $data['total_orders'] }}</h4>
                        <span class="subtitle">{{translate('messages.total_orders')}}</span>
                        <i class="fas fa-list-ul resturant-icon" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="resturant-card dashboard--card bg--3">
                        <h4 class="title">{{ $data['pending_orders'] }}</h4>
                        <span class="subtitle">{{translate('messages.pending_orders')}}</span>
                        <i class="fas fa-clock resturant-icon" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="resturant-card dashboard--card bg--5">
                        <h4 class="title">{{ $data['completed_orders'] }}</h4>
                        <span class="subtitle">{{translate('messages.completed_orders')}}</span>
                        <i class="fas fa-check resturant-icon" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="resturant-card dashboard--card bg--14">
                        <h4 class="title">{{ \App\CentralLogics\Helpers::format_currency($data['total_earnings']) }}</h4>
                        <span class="subtitle">Receita Total</span>
                        <i class="fas fa-wallet resturant-icon" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico: Pedidos por Período -->
    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
            <h4 class="card-header-title m-0 d-flex align-items-center">
                <i class="tio-chart-bar-1 mr-2"></i> Pedidos por período
            </h4>
            <div class="btn-group" role="group" aria-label="Filtro por período">
                <button type="button" class="btn btn-sm btn-outline-primary orders-range-btn" data-range="today">Hoje</button>
                <button type="button" class="btn btn-sm btn-outline-primary orders-range-btn" data-range="yesterday">Ontem</button>
                <button type="button" class="btn btn-sm btn-outline-primary orders-range-btn" data-range="last_7_days">Últimos 7 dias</button>
                <button type="button" class="btn btn-sm btn-outline-primary orders-range-btn" data-range="current_month">Mês atual</button>
                <button type="button" class="btn btn-sm btn-outline-primary orders-range-btn" data-range="last_90_days">90 dias</button>
            </div>
        </div>
        <div class="card-body">
            <div style="height: 360px;">
                <canvas id="ordersBarChartCanvas"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-xl-6">
            <!-- Top 10 Últimos pedidos por Jornaleiro -->
            <div class="card mb-3 h-100">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h4 class="card-header-title m-0 d-flex align-items-center">
                        <i class="tio-user-big-outlined mr-2"></i> Top 10 últimos pedidos por jornaleiro
                    </h4>
                </div>
                <div class="card-body">
                    <div id="latestByVendor" class="row g-3"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-6">
            <!-- Top 10 Produtos mais pedidos -->
            <div class="card mb-5 h-100">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h4 class="card-header-title m-0 d-flex align-items-center">
                        <i class="tio-premium-outlined mr-2"></i> Top 10 produtos mais pedidos
                    </h4>
                </div>
                <div class="card-body">
                    <div id="topProducts" class="row g-3"></div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@push('script')
<script>
(function(){
  const ENDPOINT = '{{ route('distributor.dashboard.orders_chart') }}';
  let chart;
  function setActive(range){
    document.querySelectorAll('.orders-range-btn').forEach(btn=>{
      const isActive = btn.dataset.range === range;
      btn.classList.toggle('btn-primary', isActive);
      btn.classList.toggle('btn-outline-primary', !isActive);
    });
  }
  async function fetchData(range){
    const res = await fetch(ENDPOINT+'?range='+encodeURIComponent(range), {headers:{'X-Requested-With':'XMLHttpRequest'}});
    if(!res.ok) throw new Error('Falha ao carregar dados');
    return res.json();
  }
  function render(labels, values){
    const ctx = document.getElementById('ordersBarChartCanvas').getContext('2d');
    if(chart) chart.destroy();
    chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Pedidos',
          data: values,
          backgroundColor: '#377dff',
          borderRadius: 6,
          maxBarThickness: 26
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, ticks: { precision: 0 } }
        },
        plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } }
      }
    });
  }
  async function load(range){
    try{
      setActive(range);
      const data = await fetchData(range);
      render(data.labels || [], data.data || []);
    }catch(err){
      console.error(err);
    }
  }
  function init(){
    document.querySelectorAll('.orders-range-btn').forEach(btn=>{
      btn.addEventListener('click', ()=> load(btn.dataset.range));
    });
    load('last_7_days');
  }
  function ensureChartJs(cb){
    if(window.Chart){ cb(); return; }
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
    s.onload = cb;
    document.head.appendChild(s);
  }
  document.addEventListener('DOMContentLoaded', ()=> ensureChartJs(init));
})();

// ======= Blocos "Top 10" =======
(function(){
  const latestByVendorContainer = document.getElementById('latestByVendor');
  const topProductsContainer = document.getElementById('topProducts');
  const latestUrl = '{{ route('distributor.dashboard.latest_orders_by_vendor') }}';
  const topProductsUrl = '{{ route('distributor.dashboard.top_products') }}';

  function cardVendor(item){
    const name = item.name || 'Jornaleiro';
    const when = item.last_order_at || '';
    const img = item.image ? item.image : 'https://via.placeholder.com/64x64?text=J';
    return `
      <div class="col-xl-6">
        <div class="d-flex align-items-center p-3 rounded border h-100" style="gap:12px;">
          <img src="${img}" alt="${name}" class="rounded" width="48" height="48" style="object-fit:cover;">
          <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0">${name}</h6>
              <span class="badge badge-soft-primary">${when}</span>
            </div>
          </div>
        </div>
      </div>`;
  }

  function cardProduct(item){
    const name = item.name || 'Produto';
    const qty = item.qty || 0;
    const img = item.image ? item.image : 'https://via.placeholder.com/80x80?text=P';
    return `
      <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="text-center p-3 border rounded h-100">
          <img src="${img}" alt="${name}" class="rounded mb-2" width="72" height="72" style="object-fit:cover;">
          <h6 class="mb-1">${name}</h6>
          <small class="text-muted">Quantidade: ${qty}</small>
        </div>
      </div>`;
  }

  async function loadLatestByVendor(){
    try{
      const res = await fetch(latestUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}});
      const data = await res.json();
      latestByVendorContainer.innerHTML = (data.data || []).map(cardVendor).join('') || '<div class="col-12 text-center text-muted">Sem dados</div>';
    }catch(e){ console.error(e); }
  }

  async function loadTopProducts(){
    try{
      const res = await fetch(topProductsUrl, {headers:{'X-Requested-With':'XMLHttpRequest'}});
      const data = await res.json();
      topProductsContainer.innerHTML = (data.data || []).map(cardProduct).join('') || '<div class="col-12 text-center text-muted">Sem dados</div>';
    }catch(e){ console.error(e); }
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    loadLatestByVendor();
    loadTopProducts();
  });
})();
</script>
@endpush