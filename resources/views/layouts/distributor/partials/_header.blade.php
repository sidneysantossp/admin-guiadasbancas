<header id="header" class="navbar navbar-expand-lg navbar-fixed navbar-height navbar-flush navbar-container navbar-bordered">
    <div class="navbar-nav-wrap">
        <div class="navbar-brand-wrapper">
            <!-- Logo -->
            @php($restaurant_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value)
            <a class="navbar-brand" href="{{ route('distributor.dashboard') }}" aria-label="Front">
                <img class="navbar-brand-logo" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}'" 
                     src="{{ asset('storage/app/public/business/' . $restaurant_logo) }}" alt="Logo">
            </a>
            <!-- End Logo -->
        </div>

        <div class="navbar-nav-wrap-content-left">
            <!-- Navbar Vertical Toggle -->
            <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                <i class="tio-clear tio-lg"></i>
            </button>
            <!-- End Navbar Vertical Toggle -->
        </div>

        <div class="navbar-nav-wrap-content-right">
            <!-- Navbar -->
            <ul class="navbar-nav align-items-center flex-row">
                <li class="nav-item d-none d-sm-inline-block">
                    <!-- Notification -->
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker btn btn-icon btn-ghost-secondary rounded-circle" href="javascript:;" 
                           data-hs-unfold-options='{
                             "target": "#notificationDropdown",
                             "type": "css-animation"
                           }'>
                            <i class="tio-notifications-on-outlined"></i>
                            <span class="btn-status btn-sm-status btn-status-danger"></span>
                        </a>

                        <div id="notificationDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu" style="width: 25rem;">
                            <!-- Header -->
                            <div class="card-header">
                                <span class="card-title h4">Notificações</span>
                            </div>
                            <!-- End Header -->

                            <!-- Body -->
                            <div class="card-body-height">
                                <div class="tab-content" id="notificationTabContent">
                                    <div class="tab-pane fade show active" id="notificationNavOne" role="tabpanel">
                                        <ul class="list-group list-group-flush navbar-card-list-group">
                                            <li class="list-group-item custom-list-item">
                                                <div class="row gx-2">
                                                    <div class="col-auto">
                                                        <div class="avatar avatar-sm avatar-circle">
                                                            <img class="avatar-img" src="{{ asset('public/assets/admin/img/160x160/img3.jpg') }}" alt="Image Description">
                                                        </div>
                                                    </div>
                                                    <div class="col ml-n3">
                                                        <span class="card-title h5">Sem Novas Notificações</span>
                                                        <p class="card-text font-size-sm">Tudo Certo</p>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- End Body -->
                        </div>
                    </div>
                    <!-- End Notification -->
                </li>

                <li class="nav-item">
                    <!-- Account -->
                    <div class="hs-unfold">
                        <a class="js-hs-unfold-invoker navbar-dropdown-account-wrapper" href="javascript:;" 
                           data-hs-unfold-options='{
                             "target": "#accountNavbarDropdown",
                             "type": "css-animation"
                           }'>
                            <div class="avatar avatar-sm avatar-circle">
                                <img class="avatar-img" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'" 
                                     src="{{ asset('storage/app/public/distributor/' . auth('distributor')->user()->image) }}" alt="Image Description">
                                <span class="avatar-status avatar-sm-status avatar-status-success"></span>
                            </div>
                        </a>

                        <div id="accountNavbarDropdown" class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right navbar-dropdown-menu navbar-dropdown-account" style="width: 16rem;">
                            <div class="dropdown-item-text">
                                <div class="media align-items-center">
                                    <div class="avatar avatar-sm avatar-circle mr-2">
                                        <img class="avatar-img" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img1.jpg') }}'" 
                                             src="{{ asset('storage/app/public/distributor/' . auth('distributor')->user()->image) }}" alt="Image Description">
                                    </div>
                                    <div class="media-body">
                                        <span class="card-title h5">{{ auth('distributor')->user()->f_name }} {{ auth('distributor')->user()->l_name }}</span>
                                        <span class="card-text">{{ auth('distributor')->user()->email }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown-divider"></div>

                            <a id="distributorAccountProfileLink" data-link="profile" class="dropdown-item" href="{{ route('distributor.profile.view') }}">
                                <span class="text-truncate pr-2" title="Meu Perfil">Meu Perfil</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <a id="distributorAccountLogoutLink" data-link="logout" class="dropdown-item" href="javascript:" onclick="Swal.fire({
                                title: '{{ translate('messages.do_you_want_to_logout') }}',
                                showDenyButton: true,
                                showCancelButton: true,
                                confirmButtonColor: '#FC6A57',
                                cancelButtonColor: '#363636',
                                confirmButtonText: '{{ translate('messages.yes') }}',
                                denyButtonText: '{{ translate('messages.dont_logout') }}',
                                }).then((result) => {
                                if (result.value) {
                                location.href='{{ route('distributor.auth.logout') }}';
                                } else{
                                Swal.fire('{{ translate('messages.canceled') }}', '', 'info')
                                }
                                })">
                                <span class="text-truncate pr-2" title="Sair">Sair</span>
                            </a>
                        </div>
                    </div>
                    <!-- End Account -->
                </li>
            </ul>
            <!-- End Navbar -->
        </div>
    </div>
</header>