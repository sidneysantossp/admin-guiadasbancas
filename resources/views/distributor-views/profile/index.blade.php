@extends('layouts.distributor.app')

@section('title',translate('messages.profile_settings'))

@push('css_or_js')

@endpush

@section('content')
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-end">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('messages.settings')}}</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{route('distributor.dashboard')}}">
                        <i class="tio-home mr-1"></i> {{translate('messages.dashboard')}}
                    </a>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-lg-3">
                <!-- Navbar -->
                <div class="navbar-vertical navbar-expand-lg mb-3 mb-lg-5">
                    <!-- Navbar Toggle -->
                    <button type="button" class="navbar-toggler btn btn-block btn-white mb-3"
                            aria-label="Toggle navigation" aria-expanded="false" aria-controls="navbarVerticalNavMenu"
                            data-toggle="collapse" data-target="#navbarVerticalNavMenu">
                <span class="d-flex justify-content-between align-items-center">
                  <span class="h5 mb-0">{{translate('messages.nav_menu')}}</span>

                  <span class="navbar-toggle-default">
                    <i class="tio-menu-hamburger"></i>
                  </span>

                  <span class="navbar-toggle-toggled">
                    <i class="tio-clear"></i>
                  </span>
                </span>
                    </button>
                    <!-- End Navbar Toggle -->

                    <div id="navbarVerticalNavMenu" class="collapse navbar-collapse">
                        <!-- Navbar Nav -->
                        <ul id="navbarSettings"
                            class="js-sticky-block js-scrollspy navbar-nav navbar-nav-lg nav-tabs card card-navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link active text-dark" href="javascript:" id="generalSection">
                                    <i class="tio-user-outlined nav-icon"></i> {{translate('messages.basic_information')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="javascript:" id="passwordSection">
                                    <i class="tio-lock-outlined nav-icon"></i> {{translate('messages.password')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="javascript:" id="paymentSection">
                                    <i class="tio-credit-card nav-icon"></i> Configurações de Pagamento
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="javascript:" id="deliverySection">
                                    <i class="tio-delivery nav-icon"></i> Configurações de Entrega
                                </a>
                            </li>
                        </ul>
                        <!-- End Navbar Nav -->
                    </div>
                </div>
                <!-- End Navbar -->
            </div>

            <div class="col-lg-9">
                <!-- Início do formulário principal -->
                <form action="{{env('APP_MODE')!='demo'?route('distributor.profile.update'):'javascript:'}}" method="post" enctype="multipart/form-data" id="distributor-settings-form">
                @csrf
                <!-- Card -->
                    <div class="card mb-3 mb-lg-5" id="generalDiv">
                        <!-- Profile Cover -->
                        <div class="profile-cover">
                            <div class="profile-cover-img-wrapper"></div>
                        </div>
                        <!-- End Profile Cover -->

                        <!-- Avatar -->
                        <label
                            class="avatar avatar-xxl avatar-circle avatar-border-lg avatar-uploader profile-cover-avatar"
                            for="avatarUploader">
                                 <img class="avatar-img" id="viewer"
                             onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                             src="{{ asset('storage/distributor') }}/{{ auth('distributor')->user()->image }}"
                             alt="Image Description">

                            <input type="file" class="js-file-attach avatar-uploader-input" id="avatarUploader"
                                   name="image" accept="image/*"
                                   data-hs-file-attach-options='{
                                      "textTarget": "#avatarUploadText",
                                      "mode": "image",
                                      "targetAttr": "src",
                                      "allowTypes": [".png", ".jpeg", ".jpg"]
                                   }'>

                            <span class="avatar-uploader-trigger">
                                <i class="tio-edit avatar-uploader-icon shadow-soft"></i>
                            </span>
                        </label>
                        <!-- End Avatar -->
                    </div>
                    <!-- End Card -->

                    <!-- Card -->
                    <div class="card mb-3 mb-lg-5">
                        <div class="card-header">
                            <h2 class="card-title h4"><i class="tio-info"></i> {{translate('messages.basic_information')}}</h2>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="firstNameLabel" class="col-sm-3 col-form-label input-label">{{translate('messages.full_name')}} <i
                                        class="tio-help-outlined text-body ml-1" data-toggle="tooltip"
                                        data-placement="top"
                                        title="{{translate('messages.Display_name')}}"></i></label>

                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="f_name" id="firstNameLabel"
                                               placeholder="{{translate('messages.first_name')}}" aria-label="{{translate('messages.first_name')}}"
                                               value="{{auth('distributor')->user()->f_name}}">
                                        <input type="text" class="form-control" name="l_name" id="lastNameLabel"
                                               placeholder="{{translate('messages.last_name')}}" aria-label="{{translate('messages.last_name')}}"
                                               value="{{auth('distributor')->user()->l_name}}">
                                    </div>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <!-- Form Group -->
                            <div class="row form-group">
                                <label for="phoneLabel" class="col-sm-3 col-form-label input-label">{{translate('messages.phone')}}</label>

                                <div class="col-sm-9">
                                    <input type="tel" class="js-masked-input form-control" name="phone" id="phoneLabel"
                                           placeholder="+x(xxx)xxx-xx-xx" aria-label="+(xxx)xx-xxx-xxxx"
                                           value="{{auth('distributor')->user()->phone}}"
                                           data-hs-mask-options='{
                                           "template": "+(880)00-000-00000"
                                         }'>
                                </div>
                            </div>
                            <!-- End Form Group -->

                            <div class="row form-group">
                                <label for="newEmailLabel" class="col-sm-3 col-form-label input-label">{{translate('messages.email')}}</label>

                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" id="newEmailLabel"
                                           value="{{auth('distributor')->user()->email}}"
                                           placeholder="{{translate('messages.enter_new_email_address')}}"
                                           aria-label="{{translate('messages.enter_new_email_address')}}">
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="row form-group">
                                <label for="addressLabel" class="col-sm-3 col-form-label input-label">Endereço</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="address" id="addressLabel"
                                           value="{{auth('distributor')->user()->address}}"
                                           placeholder="Digite seu endereço completo">
                                </div>
                            </div>

                            <!-- Cidade e Estado -->
                            <div class="row form-group">
                                <label class="col-sm-3 col-form-label input-label">Cidade / Estado</label>
                                <div class="col-sm-9">
                                    <div class="input-group input-group-sm-down-break">
                                        <input type="text" class="form-control" name="city" 
                                               placeholder="Cidade" value="{{auth('distributor')->user()->city}}">
                                        <input type="text" class="form-control" name="state" 
                                               placeholder="Estado" value="{{auth('distributor')->user()->state}}">
                                    </div>
                                </div>
                            </div>

                            <!-- CEP -->
                            <div class="row form-group">
                                <label for="zipCodeLabel" class="col-sm-3 col-form-label input-label">CEP</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="zip_code" id="zipCodeLabel"
                                           value="{{auth('distributor')->user()->zip_code}}"
                                           placeholder="00000-000">
                                </div>
                            </div>

                            <!-- Descrição -->
                            <div class="row form-group">
                                <label for="descriptionLabel" class="col-sm-3 col-form-label input-label">Descrição</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" name="description" id="descriptionLabel" rows="3"
                                              placeholder="Descreva sua empresa/negócio">{{auth('distributor')->user()->description}}</textarea>
                                </div>
                            </div>

                            <!-- End Form -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->

                    <!-- Botão Salvar Informações Básicas -->
                    <div class="d-flex justify-content-end" id="generalDiv_btn" style="display: none">
                        @if (env('APP_MODE')!='demo')
                        <button type="submit" class="btn btn-primary" id="saveGeneralBtn">Salvar Informações Básicas</button>
                        @else
                            <button type="button" class="btn btn-primary call-demo">{{translate('messages.Save_changes')}}</button>
                        @endif
                    </div>
                </form>
                <!-- Fim do formulário principal -->

                    <div id="passwordDiv" class="card mb-3 mb-lg-5" style="display: none">
                        <!-- Header -->
                        <div class="card-header">
                            <h4 class="card-title"><i class="tio-lock"></i> {{translate('messages.change_password')}}</h4>
                        </div>
                        <!-- End Header -->

                        <!-- Body -->
                        <div class="card-body">
                            <!-- Form -->
                            <form id="changePasswordForm" action="{{ route('distributor.profile.settings_password_update') }}" method="post"
                                  enctype="multipart/form-data">
                                  <!-- Usando URL absoluta para evitar problemas com nomes de rotas -->
                                @csrf
                                <!-- Form Group -->
                                <div class="row form-group">
                                    <label for="newPassword" class="col-sm-3 col-form-label input-label">{{translate('messages.new_password')}}</label>

                                    <div class="col-sm-9">
                                        <input type="password" class="js-pwstrength form-control" name="password"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"

                                               id="newPassword" placeholder="{{translate('messages.enter_new_password')}}"
                                               aria-label="{{translate('messages.enter_new_password')}}"
                                               data-hs-pwstrength-options='{
                                               "ui": {
                                                 "container": "#changePasswordForm",
                                                 "viewports": {
                                                   "progress": "#passwordStrengthProgress",
                                                   "verdict": "#passwordStrengthVerdict"
                                                 }
                                               }
                                             }' required>

                                        <p id="passwordStrengthVerdict" class="form-text mb-2"></p>

                                        <div id="passwordStrengthProgress"></div>
                                    </div>
                                </div>
                                <!-- End Form Group -->

                                <!-- Form Group -->
                                <div class="row form-group">
                                    <label for="confirmNewPasswordLabel" class="col-sm-3 col-form-label input-label">{{translate('messages.confirm_password')}}</label>

                                    <div class="col-sm-9">
                                        <div class="mb-3">
                                            <input type="password" class="form-control" name="confirm_password"
                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="{{ translate('messages.Must_contain_at_least_one_number_and_one_uppercase_and_lowercase_letter_and_symbol,_and_at_least_8_or_more_characters') }}"
                                            id="confirmNewPasswordLabel" placeholder="{{translate('messages.confirm_new_password')}}"
                                                   aria-label="{{translate('messages.confirm_new_password')}}" required>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Form Group -->

                                <div class="d-flex justify-content-end" id="passwordDiv_btn">
                                    @if (env('APP_MODE')!='demo')
                                    <button type="submit" class="btn btn-primary" id="savePasswordBtn">Salvar Nova Senha</button>
                                    @else
                                        <button type="button" class="btn btn-primary call-demo">{{translate('messages.Save_changes')}}</button>
                                    @endif

                                </div>
                            </form>
                            <!-- End Form -->
                        </div>
                        <!-- End Body -->
                    </div>
                    <!-- End Card -->

                    <!-- Seção de Configurações de Pagamento -->
                    <div id="paymentDiv" class="card mb-3 mb-lg-5" style="display: none">
                        <div class="card-header">
                            <h4 class="card-title"><i class="tio-credit-card"></i> Configurações de Pagamento</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('distributor.profile.payment-settings')}}" method="post">
                                @csrf
                                <!-- Chave PIX -->
                                <div class="row form-group">
                                    <label for="pixKeyLabel" class="col-sm-3 col-form-label input-label">Chave PIX</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="pix_key" id="pixKeyLabel"
                                               value="{{auth('distributor')->user()->pix_key}}"
                                               placeholder="Digite sua chave PIX (CPF, CNPJ, email ou telefone)">
                                    </div>
                                </div>

                                <!-- Nome do Titular -->
                                <div class="row form-group">
                                    <label for="accountHolderLabel" class="col-sm-3 col-form-label input-label">Nome do Titular</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="holder_name" id="accountHolderLabel"
                                               value="{{auth('distributor')->user()->holder_name}}"
                                               placeholder="Nome completo do titular da conta">
                                    </div>
                                </div>

                                <!-- Banco -->
                                <div class="row form-group">
                                    <label for="bankNameLabel" class="col-sm-3 col-form-label input-label">Banco</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" name="bank_name" id="bankNameLabel"
                                               value="{{auth('distributor')->user()->bank_name}}"
                                               placeholder="Nome do banco">
                                    </div>
                                </div>

                                <!-- Agência e Conta -->
                                <div class="row form-group">
                                    <label class="col-sm-3 col-form-label input-label">Agência / Conta</label>
                                    <div class="col-sm-9">
                                        <div class="input-group input-group-sm-down-break">
                                            <input type="text" class="form-control" name="branch" 
                                                    placeholder="Agência" value="{{auth('distributor')->user()->branch}}">
                                            <input type="text" class="form-control" name="account_no"
                                               placeholder="Conta" value="{{auth('distributor')->user()->account_no}}">
                                        </div>
                                    </div>
                                </div>

                                <!-- Botão Salvar -->
                                <div class="d-flex justify-content-end" id="paymentDiv_btn">
                                    @if (env('APP_MODE')!='demo')
                                    <button type="submit" class="btn btn-primary" id="savePaymentBtn">Salvar Configurações de Pagamento</button>
                                    @else
                                        <button type="button" class="btn btn-primary call-demo">{{translate('messages.Save_changes')}}</button>
                                    @endif
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Seção de Configurações de Entrega -->
                    <div id="deliveryDiv" class="card mb-3 mb-lg-5" style="display: none">
                        <div class="card-header">
                            <h4 class="card-title"><i class="tio-delivery"></i> Configurações de Entrega</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{route('distributor.profile.delivery-settings')}}" method="post">
                                @csrf
                                <!-- Tempo de Entrega -->
                                <div class="row form-group">
                                    <label for="deliveryTimeLabel" class="col-sm-3 col-form-label input-label">Tempo de Entrega (horas)</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="delivery_time" id="deliveryTimeLabel"
                                               value="{{auth('distributor')->user()->delivery_time}}"
                                               placeholder="Ex: 24" min="1" max="168">
                                        <small class="form-text text-muted">Tempo em horas para entrega dos produtos</small>
                                    </div>
                                </div>

                                <!-- Pedido Mínimo -->
                                <div class="row form-group">
                                    <label for="minimumOrderLabel" class="col-sm-3 col-form-label input-label">Pedido Mínimo (R$)</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="minimum_order" id="minimumOrderLabel"
                                               value="{{auth('distributor')->user()->minimum_order}}"
                                               placeholder="Ex: 50.00" step="0.01" min="0">
                                    </div>
                                </div>

                                <!-- Taxa de Entrega -->
                                <div class="row form-group">
                                    <label for="deliveryChargeLabel" class="col-sm-3 col-form-label input-label">Taxa de Entrega (R$)</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="delivery_charge" id="deliveryChargeLabel"
                                               value="{{auth('distributor')->user()->delivery_charge}}"
                                               placeholder="Ex: 10.00" step="0.01" min="0">
                                    </div>
                                </div>

                                <!-- Frete Grátis Acima de -->
                                <div class="row form-group">
                                    <label for="freeDeliveryLabel" class="col-sm-3 col-form-label input-label">Frete Grátis Acima de (R$)</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="free_delivery_over" id="freeDeliveryLabel"
                                               value="{{auth('distributor')->user()->free_delivery_over}}"
                                               placeholder="Ex: 100.00" step="0.01" min="0">
                                    </div>
                                </div>

                                <!-- Raio de Entrega -->
                                <div class="row form-group">
                                    <label for="deliveryRadiusLabel" class="col-sm-3 col-form-label input-label">Raio de Entrega (km)</label>
                                    <div class="col-sm-9">
                                        <input type="number" class="form-control" name="delivery_radius" id="deliveryRadiusLabel"
                                               value="{{auth('distributor')->user()->delivery_radius}}"
                                               placeholder="Ex: 15" step="0.1" min="0">
                                    </div>
                                </div>

                                <!-- Áreas de Entrega -->
                                <div class="row form-group">
                                    <label for="deliveryAreasLabel" class="col-sm-3 col-form-label input-label">Áreas de Entrega</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" name="delivery_areas" id="deliveryAreasLabel" rows="3"
                                                  placeholder="Liste os bairros ou áreas que você atende">{{auth('distributor')->user()->delivery_areas}}</textarea>
                                    </div>
                                </div>

                                <!-- Botão Salvar -->
                                <div class="d-flex justify-content-end" id="deliveryDiv_btn">
                                    @if (env('APP_MODE')!='demo')
                                    <button type="submit" class="btn btn-primary" id="saveDeliveryBtn">Salvar Configurações de Entrega</button>
                                    @else
                                        <button type="button" class="btn btn-primary call-demo">{{translate('messages.Save_changes')}}</button>
                                    @endif
                                </div>

                            </form>
                        </div>
                    </div>
                    
                <!-- Sticky Block End Point -->
                <div id="stickyBlockEndPoint"></div>
            </div>
        </div>
        <!-- End Row -->
    </div>
    <!-- End Content -->
@endsection

@push('script_2')
    <script>
        // Função para esconder todas as seções
        function hideAllSections() {
            $('#generalDiv').hide();
            $('#passwordDiv').hide();
            $('#paymentDiv').hide();
            $('#deliveryDiv').hide();
            $('#generalDiv_btn').hide();
            $('#passwordDiv_btn').hide();
            $('#paymentDiv_btn').hide();
            $('#deliveryDiv_btn').hide();
        }

        // Função para remover classe active de todos os links
        function removeActiveClass() {
            $('#generalSection').removeClass('active');
            $('#passwordSection').removeClass('active');
            $('#paymentSection').removeClass('active');
            $('#deliverySection').removeClass('active');
        }

        $('#generalSection').on('click', function (){
            hideAllSections();
            $('#generalDiv').show(600);
            $('#generalDiv_btn').show(600);
            removeActiveClass();
            $('#generalSection').addClass('active');
        })
        
        $('#passwordSection').on('click', function (){
            hideAllSections();
            $('#passwordDiv').show(600);
            $('#passwordDiv_btn').show(600);
            removeActiveClass();
            $('#passwordSection').addClass('active');
        })
        
        $('#paymentSection').on('click', function (){
            hideAllSections();
            $('#paymentDiv').show(600);
            $('#paymentDiv_btn').show(600);
            removeActiveClass();
            $('#paymentSection').addClass('active');
        })
        
        $('#deliverySection').on('click', function (){
            hideAllSections();
            $('#deliveryDiv').show(600);
            $('#deliveryDiv_btn').show(600);
            removeActiveClass();
            $('#deliverySection').addClass('active');
        })

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#avatarUploader").change(function () {
            readURL(this);
        });
        
        // Debug do botão salvar
        $('#saveGeneralBtn').on('click', function(e) {
            console.log('Botão clicado!');
            console.log('Formulário:', $(this).closest('form'));
            console.log('Action:', $(this).closest('form').attr('action'));
            console.log('Method:', $(this).closest('form').attr('method'));
        });
        
        // Mostrar seção ativa por padrão ao carregar a página
        $(document).ready(function() {
            $('#generalDiv').show();
            $('#generalDiv_btn').show();
        });
    </script>
@endpush
