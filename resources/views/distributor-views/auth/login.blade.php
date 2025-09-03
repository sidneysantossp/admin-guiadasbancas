<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Title -->
    <title>{{ translate('messages.distributor_login') }} | {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/admin/img/favicon.ico') }}">
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/vendor/icon-set/style.css') }}">
    
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/theme.minc619.css?v=1.0') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
</head>

<body class="d-flex align-items-center min-h-100">
    <!-- ========== MAIN CONTENT ========== -->
    <main id="content" role="main" class="flex-grow-1">
        <!-- Container -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-5 col-xl-4 d-none d-lg-flex justify-content-center align-items-center min-vh-lg-100 position-relative bg-dark" style="background-image: url({{ asset('public/assets/admin/img/1920x1080/img2.jpg') }});">
                    <div class="flex-grow-1 p-5">
                        <!-- Blockquote -->
                        <figure class="text-center">
                            <div class="mb-4">
                                @php($restaurant_logo = \App\Models\BusinessSetting::where(['key' => 'logo'])->first()->value)
                                <img class="avatar avatar-xl avatar-4by3" src="{{ asset('storage/app/public/business/' . $restaurant_logo) }}" alt="Logo" data-hs-theme-appearance="default">
                                <img class="avatar avatar-xl avatar-4by3" src="{{ asset('storage/app/public/business/' . $restaurant_logo) }}" alt="Logo" data-hs-theme-appearance="dark">
                            </div>
                            
                            <blockquote class="blockquote blockquote-light">{{ translate('messages.distributor_panel_welcome') }}</blockquote>
                            
                            <figcaption class="blockquote-footer blockquote-light">
                                {{ translate('messages.manage_your_distribution') }}
                                <cite title="Source Title">{{ config('app.name') }}</cite>
                            </figcaption>
                        </figure>
                        <!-- End Blockquote -->
                    </div>
                </div>
                
                <div class="col-lg-7 col-xl-8 d-flex justify-content-center align-items-center min-vh-lg-100">
                    <div class="flex-grow-1 mx-auto" style="max-width: 28rem;">
                        <!-- Heading -->
                        <div class="text-center mb-5 mb-md-7">
                            <h1 class="h2">{{ translate('messages.welcome_back') }}</h1>
                            <p>{{ translate('messages.sign_in_to_your_distributor_account') }}</p>
                        </div>
                        <!-- End Heading -->
                        
                        <!-- Form -->
                        <form class="js-validate" action="{{ route('distributor.auth.login.submit') }}" method="POST">
                            @csrf
                            
                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signinSrEmail">{{ translate('messages.your_email') }}</label>
                                
                                <input type="email" class="js-validate form-control form-control-lg @error('email') is-invalid @enderror" 
                                       name="email" id="signinSrEmail" placeholder="email@address.com" 
                                       aria-label="email@address.com" value="{{ old('email') }}" required 
                                       data-msg="Please enter a valid email address.">
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- End Form Group -->
                            
                            <!-- Form Group -->
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrPassword" tabindex="0">
                                    <span class="d-flex justify-content-between align-items-center">
                                        {{ translate('messages.password') }}
                                    </span>
                                </label>
                                
                                <div class="input-group input-group-merge">
                                    <input type="password" class="js-validate form-control form-control-lg @error('password') is-invalid @enderror" 
                                           name="password" id="signupSrPassword" placeholder="8+ characters required" 
                                           aria-label="8+ characters required" required 
                                           data-msg="Your password is invalid. Please try again.">
                                    
                                    <div class="input-group-append">
                                        <a class="input-group-text" href="javascript:;" data-toggle="password-field" data-target="#signupSrPassword">
                                            <i class="tio-visible-outlined"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <!-- End Form Group -->
                            
                            <!-- Checkbox -->
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="termsCheckbox" name="remember">
                                    <label class="custom-control-label text-muted" for="termsCheckbox">{{ translate('messages.remember_me') }}</label>
                                </div>
                            </div>
                            <!-- End Checkbox -->
                            
                            <button type="submit" class="btn btn-lg btn-block btn-primary">{{ translate('messages.sign_in') }}</button>
                        </form>
                        <!-- End Form -->
                    </div>
                </div>
            </div>
        </div>
        <!-- End Container -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->
    
    <!-- JS Global Compulsory  -->
    <script src="{{ asset('public/assets/admin/js/vendor.min.js') }}"></script>
    
    <!-- JS Implementing Plugins -->
    <script src="{{ asset('public/assets/admin/js/hs.toggle-password.js') }}"></script>
    
    <!-- JS Front -->
    <script src="{{ asset('public/assets/admin/js/theme.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/toastr.js') }}"></script>
    
    <!-- JS Plugins Init. -->
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF TOGGLE PASSWORD
            $('.js-toggle-password').each(function () {
                new HSTogglePassword(this).init()
            });
            
            // INITIALIZATION OF FORM VALIDATION
            $('.js-validate').each(function() {
                $.HSCore.components.HSValidation.init($(this));
            });
        });
    </script>
    
    <script>
        @if(session()->has('success'))
            toastr.success('{{ session('success') }}', {
                CloseButton: true,
                ProgressBar: true
            });
        @endif
        
        @if(session()->has('error'))
            toastr.error('{{ session('error') }}', {
                CloseButton: true,
                ProgressBar: true
            });
        @endif
        
        @if ($errors->any())
            @foreach($errors->all() as $error)
                toastr.error('{{ $error }}', {
                    CloseButton: true,
                    ProgressBar: true
                });
            @endforeach
        @endif
    </script>
</body>
</html>