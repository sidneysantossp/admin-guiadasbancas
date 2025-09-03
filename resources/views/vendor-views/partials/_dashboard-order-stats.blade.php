<div class="col-xl-3 col-sm-6">
    <a class="resturant-card dashboard--card bg--2" href="{{route('vendor.order.list',['confirmed'])}}">
        <h4 class="title">{{$data['confirmed']}}</h4>
        <span class="subtitle">{{translate('messages.confirmed')}}</span>
        <i class="resturant-icon tio-checkmark-circle" style="font-size:34px"></i>
    </a>
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--3" href="{{route('vendor.order.list',['cooking'])}}">
        <h4 class="title">{{$data['cooking']}}</h4>
        <span class="subtitle">{{translate('messages.cooking')}}</span>
        <i class="resturant-icon tio-shopping-basket" style="font-size:34px"></i>
    </a>
    <!-- End Card -->
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--5" href="{{route('vendor.order.list',['ready_for_delivery'])}}">
        <h4 class="title">{{$data['ready_for_delivery']}}</h4>
        <span class="subtitle">{{translate('messages.ready_for_delivery')}}</span>
        <i class="resturant-icon tio-shopping-cart" style="font-size:34px"></i>
    </a>
    <!-- End Card -->
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--14" href="{{route('vendor.order.list',['food_on_the_way'])}}">
        <h4 class="title">{{$data['food_on_the_way']}}</h4>
        <span class="subtitle">{{translate('messages.food_on_the_way')}}</span>
        <i class="resturant-icon tio-truck" style="font-size:34px"></i>
    </a>
    <!-- End Card -->
</div>

<div class="col-12">
    <div class="row g-2 mt-2">
        <div class="col-sm-6 col-lg-3">
            <a href="{{route('vendor.order.list',['delivered'])}}" class="order--card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <i class="tio-done mr-2"></i>
                        <span>{{translate('messages.delivered')}}</span>
                    </h6>
                    <span class="card-title h3">
                        {{$data['delivered']}}
                    </span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="{{route('vendor.order.list',['refunded'])}}" class="order--card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <i class="tio-money mr-2"></i>
                        <span>{{translate('messages.refunded')}}</span>
                    </h6>
                    <span
                        class="card-title h3">{{$data['refunded']}}</span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="{{route('vendor.order.list',['scheduled'])}}" class="order--card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <i class="tio-calendar mr-2"></i>
                        <span>{{translate('messages.scheduled')}}</span>
                    </h6>
                    <span
                        class="card-title h3">{{$data['scheduled']}}</span>
                </div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="{{route('vendor.order.list',['all'])}}" class="order--card h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                        <i class="tio-shop mr-2"></i>
                        <span>{{translate('messages.all')}}</span>
                    </h6>
                    <span
                        class="card-title h3">{{$data['all']}}</span>
                </div>
            </a>
        </div>
    </div>
</div>
