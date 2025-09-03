<aside class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered">
    <div class="navbar-vertical-container">
        <div class="navbar-vertical-footer-offset">
            <!-- Logo -->
            @php($restaurant_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value)
            <a class="navbar-brand" href="{{ route('distributor.dashboard') }}" aria-label="Front">
                <img class="navbar-brand-logo" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'" 
                     src="{{ asset('storage/app/public/business/' . $restaurant_logo) }}" alt="Logo">
            </a>
            <!-- End Logo -->

            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                <i class="tio-clear tio-lg"></i>
            </button>
            <!-- End Navbar Vertical Toggle -->

            <!-- Content -->
            <div class="navbar-vertical-content">
                <ul class="navbar-nav navbar-nav-lg nav-tabs">
                    <!-- Dashboard -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('distributor') ? 'show' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('distributor.dashboard') }}">
                            <i class="tio-home-vs-1-outlined nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.dashboard') }}</span>
                        </a>
                    </li>
                    <!-- End Dashboard -->


                    <!-- Orders -->
                    <li class="nav-item">
                        <small class="nav-subtitle">{{ translate('messages.order_management') }}</small>
                        <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                    </li>

                    <li class="navbar-vertical-aside-has-menu {{ Request::is('distributor/orders*') ? 'show' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                            <i class="tio-shopping-cart nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.orders') }}</span>
                        </a>
                        <ul class="js-navbar-vertical-aside-submenu nav nav-sub {{ Request::is('distributor/orders*') ? 'd-block' : '' }}">
                            <li class="nav-item {{ Request::is('distributor/orders/list/all') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'all']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.all_orders') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('distributor/orders/list/pending') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'pending']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.pending') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('distributor/orders/list/confirmed') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'confirmed']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.confirmed') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('distributor/orders/list/processing') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'processing']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.processing') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('distributor/orders/list/delivered') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'delivered']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.delivered') }}</span>
                                </a>
                            </li>
                            <li class="nav-item {{ Request::is('distributor/orders/list/canceled') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('distributor.orders', ['status' => 'canceled']) }}">
                                    <span class="tio-circle nav-indicator-icon"></span>
                                    <span class="text-truncate">{{ translate('messages.canceled') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- End Orders -->

                    <!-- Food Management -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('distributor/food*') ? 'show' : '' }}">
            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" data-hs-unfold-options='{
                "target": "#navbarVerticalMenuFood",
                "type": "accordion"
            }'>
                <i class="tio-shopping-basket-outlined nav-icon"></i>
                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                    {{ translate('messages.food_management') }}
                </span>
            </a>
            <ul id="navbarVerticalMenuFood" class="js-navbar-vertical-aside-submenu nav nav-sub {{ Request::is('distributor/food*') ? 'd-block' : '' }}">
                <li class="nav-item {{ Request::is('distributor/food') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('distributor.food.index') }}">
                        <span class="tio-circle nav-indicator-icon"></span>
                        <span class="text-truncate">{{ translate('messages.add_new') }}</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('distributor/food/list') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('distributor.food.list') }}">
                        <span class="tio-circle nav-indicator-icon"></span>
                        <span class="text-truncate">{{ translate('Estoque') }}</span>
                    </a>
                </li>
                <li class="nav-item {{ Request::is('distributor/food/bulk') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('distributor.food.bulk.form') }}">
                        <span class="tio-circle nav-indicator-icon"></span>
                        <span class="text-truncate">{{ translate('Importar/Exportar') }}</span>
                    </a>
                </li>
            </ul>
        </li>

                    <!-- Vendor Products Management -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('distributor/vendor-products*') ? 'show' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('distributor.vendor-products.index') }}">
                            <i class="tio-shop nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('Estoque Jornaleiros') }}</span>
                        </a>
                    </li>

                    <!-- Profile Settings -->
                    <li class="navbar-vertical-aside-has-menu {{ Request::is('distributor/profile*') ? 'show' : '' }}">
                        <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{ route('distributor.profile.view') }}">
                            <i class="tio-user nav-icon"></i>
                            <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{ translate('messages.profile_settings') }}</span>
                        </a>
                    </li>
                    <!-- End Profile Settings -->
                    
                </ul>
            </div>
            <!-- End Content -->
        </div>
    </div>
</aside>