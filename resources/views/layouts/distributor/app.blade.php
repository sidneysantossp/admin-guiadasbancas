<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ session()->get('direction') ?? 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title') | {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/admin/img/favicon.ico') }}">
    
    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/vendor.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/vendor/icon-set/style.css') }}">
    
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/theme.minc619.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/toastr.css') }}">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('public/assets/admin/css/bootstrap.min.css') }}">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        .main {
            padding: 8px 20px 20px !important;
        }
        /* Reduce gap caused by fixed navbar */
        .navbar-fixed ~ .main {
            padding-top: 0.75rem !important;
        }
        
        /* Vertical sidebar (fixed) */
        .navbar-vertical-aside {
              width: 250px;
              position: fixed;
              left: 0;
              top: 0;
              height: 100vh;
              z-index: 1000;
              background-color: #ffffff !important;
              border-right: 1px solid #e7eaf3;
              box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
          }
          
          .navbar-vertical-container,
          .navbar-vertical-footer-offset,
          .navbar-vertical-content {
              background-color: #334257 !important;
          }
          
          .navbar-vertical-aside .navbar-nav {
               background-color: #334257 !important;
               padding: 0;
           }
           
           .navbar-brand {
               padding: 1rem;
               background-color: #ffffff !important;
               border-bottom: 1px solid #e7eaf3;
               display: flex;
               align-items: center;
               justify-content: center;
           }
           
           .navbar-brand-logo {
               max-height: 40px;
               width: auto;
           }
           
           .navbar-vertical-aside-toggle {
               position: absolute;
               top: 1rem;
               right: 1rem;
               background-color: transparent;
               border: none;
               color: #677788;
           }
        
        .content {
            padding: 10px 20px 20px;
        }
        .content.compact, .content.dashboard-compact {
            padding-top: 8px !important;
        }
        
        /* Offset layout to the right of the fixed vertical sidebar */
        @media (min-width: 992px) {
            body.has-navbar-vertical-aside #content.main,
            .navbar-vertical-aside ~ #content.main,
            body.has-navbar-vertical-aside .main,
            .navbar-vertical-aside ~ .main {
                margin-left: 250px !important;
            }
        }
        
        .card {
            border: 1px solid #e7eaf3;
            border-radius: 0.375rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .page-header {
            margin-bottom: 0.5rem !important;
        }
        
        .page-header-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0 0 0.25rem !important;
        }
        .page-header-title.compact { margin-bottom: 0.25rem !important; }
        
        .page-header-text {
            color: #677788;
            margin-bottom: 0;
        }
        
        .card-header-icon {
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 0.375rem;
        }
        
        .card-header-icon i {
            font-size: 1.5rem;
            color: #377dff;
        }
        
        .card-subtitle {
            color: #677788;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .card-title {
             color: #1e2022;
             font-weight: 600;
         }
         
         /* Sidebar responsive behavior */
         @media (max-width: 991.98px) {
             .navbar-vertical-aside {
                 transform: translateX(-100%);
                 transition: transform 0.3s ease;
             }
             
             .navbar-vertical-aside.show {
                 transform: translateX(0);
             }
             
-            .main {
-                margin-left: 0;
-            }
+            .main {
+                margin-left: 0 !important;
+            }
         }
         
         /* Mini mode styles */
         .navbar-vertical-aside-mini-mode .navbar-vertical-aside {
             width: 80px;
         }
         
-        .navbar-vertical-aside-mini-mode .main {
-            margin-left: 80px;
-        }
+        .navbar-vertical-aside-mini-mode #content.main,
+        .navbar-vertical-aside-mini-mode .main {
+            margin-left: 80px !important;
+        }
         
         .navbar-vertical-aside-mini-mode .navbar-vertical-aside-mini-mode-hidden-elements {
             display: none;
         }
         
         /* Submenu styles */
         .js-navbar-vertical-aside-submenu {
             display: none;
             padding-left: 1rem;
         }
         
         .show .js-navbar-vertical-aside-submenu {
             display: block;
         }
         
         /* Navigation styles */
         .navbar-nav .nav-link {
             color: #677788 !important;
             padding: 0.75rem 1rem;
             border-radius: 0.375rem;
             margin-bottom: 0.25rem;
         }
         
         .navbar-nav .nav-link:hover,
         .navbar-nav .nav-link.active {
             background-color: #f8f9fa !important;
             color: #334257 !important;
         }
         
         /* Ensure submenu items also follow the same pattern */
         .navbar-nav .nav-link .nav-link {
             color: #677788 !important;
         }
         
         .navbar-nav .nav-link .nav-link:hover,
         .navbar-nav .nav-link .nav-link.active {
             background-color: #f8f9fa !important;
             color: #334257 !important;
         }
         
         .navbar-nav .nav-link i {
             margin-right: 0.75rem;
             width: 1.25rem;
             text-align: center;
         }
         /* Ensure header dropdowns appear above content */
         #header {
             position: relative;
             z-index: 1100;
             overflow: visible;
         }
         .navbar-nav-wrap,
         .navbar-nav-wrap-content-right {
             overflow: visible;
         }
         .navbar-dropdown-menu {
             z-index: 99999; /* ensure above all */
             position: fixed; /* avoid overflow clipping */
             background: #fff;
             border: 1px solid rgba(0,0,0,.1);
             box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
             min-width: 16rem;
             min-height: 120px;
             display: none;
             visibility: hidden;
             opacity: 0;
          }
         .navbar-dropdown-menu.show {
-         .navbar-dropdown-menu.show {
+         .navbar-dropdown-menu.show {
              display: block !important;
              visibility: visible !important;
              opacity: 1 !important;
          }
-         
-         }
+         
         }
         /* Ensure items are clickable */
         .navbar-dropdown-menu .dropdown-item { pointer-events: auto; cursor: pointer; }
         /* End dropdown styling */

         /* FORM/UI STANDARDIZATION */
         /* Neutralize colored headers to match platform standard */
         .card > .card-header.bg-primary,
         .card > .card-header.bg-success,
         .card > .card-header.bg-warning,
         .card > .card-header.bg-info,
         .card > .card-header.text-white,
         .card > .card-header.text-dark {
            background-color: #ffffff !important;
            color: #334257 !important;
         }

         /* Default card header appearance */
         .card > .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e7eaf3;
            padding: 1rem 1.25rem;
         }

         /* Title inside headers */
         .card > .card-header .card-header-title,
         .card > .card-header .card-title {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
            color: #334257;
         }

         /* Icon in section headers */
         .card-header-icon {
            color: #677788;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.25rem;
         }

         /* Button container spacing */
         .btn--container { gap: .5rem; display: flex; flex-wrap: wrap; }
     </style>
    
    @stack('css_or_js')
</head>

<body class="footer-offset has-navbar-vertical-aside navbar-vertical-aside-show-xl">
    <script src="{{ asset('public/assets/admin/js/hs.theme-appearance.js') }}"></script>
    
    <!-- ========== HEADER ========== -->
    @include('layouts.distributor.partials._header')
    <!-- ========== END HEADER ========== -->
    
    <!-- ========== MAIN CONTENT ========== -->
    <!-- Navbar Vertical -->
    @include('layouts.distributor.partials._sidebar')
    
    <main id="content" role="main" class="main pointer-event">
        <!-- Content -->
        @yield('content')
        <!-- End Content -->
        
        <!-- Footer -->
        @include('layouts.distributor.partials._footer')
        <!-- End Footer -->
    </main>
    <!-- ========== END MAIN CONTENT ========== -->
    
    <!-- JS Global Compulsory  -->
    <script src="{{ asset('public/assets/admin/js/vendor.min.js') }}"></script>
    
    <!-- jQuery Fallback (sync) -->
    <script>
        if (typeof window.jQuery === 'undefined') {
            document.write('\x3Cscript src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuziv4y/faTqgFtohetphbbj0=" crossorigin="anonymous">\x3C/script>');
        }
    </script>
    
    <!-- JS Implementing Plugins -->
    <script src="{{ asset('public/assets/admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/select2.min.js') }}"></script>
    
    <!-- JS Front -->
    <script src="{{ asset('public/assets/admin/js/theme.min.js') }}"></script>
    <script src="{{ asset('public/assets/admin/js/toastr.js') }}"></script>
    
    <!-- JS Plugins Init. -->
    <script>
        $(document).ready(function () {
            // Debug logs
            try {
                console.log('[Distributor Header] jQuery ready');
                console.log('[Distributor Header] account invoker count:', $('.navbar-dropdown-account-wrapper').length);
                console.log('[Distributor Header] account menu exists:', $('#accountNavbarDropdown').length);
            } catch (e) {}
            // Ensure dropdowns start hidden
            $('#accountNavbarDropdown, #notificationDropdown').removeClass('show').hide().css({visibility:'visible'});
            // Sidebar toggle functionality
            $('.js-navbar-vertical-aside-toggle-invoker').on('click', function() {
                $('.navbar-vertical-aside').toggleClass('navbar-vertical-aside-mini-mode');
                $('body').toggleClass('navbar-vertical-aside-mini-mode');
            });
            
            // Submenu toggle
            $('.js-navbar-vertical-aside-menu-link').on('click', function(e) {
                var $this = $(this);
                var $parent = $this.parent();
                var $submenu = $parent.find('.js-navbar-vertical-aside-submenu');
                
                if ($submenu.length > 0 && $this.attr('href') === 'javascript:') {
                    e.preventDefault();
                    $submenu.slideToggle();
                    $parent.toggleClass('show');
                }
            });

            // Init HS Unfold (dropdowns: notifications/account)
            try {
                if (window.HSCore && $.HSCore && $.HSCore.components && $.HSCore.components.HSUnfold) {
                    $.HSCore.components.HSUnfold.init($('[data-hs-unfold-options]'));
                } else {
                    // Fallback simple toggle
                    $('.js-hs-unfold-invoker').on('click', function (e) {
                        e.preventDefault();
                        var opts = $(this).attr('data-hs-unfold-options');
                        var target = null;
                        try { target = JSON.parse(opts).target; } catch (err) {}
                        if (target) {
                            var $t = $(target);
                            $t.toggleClass('show');
                            $t.toggle();
                        }
                    });
                }
            } catch (e) {
                // no-op
            }

            // Always ensure toggles work (explicit handlers)
            function positionDropdown($inv, $menu){
                // Ensure menu has measurable size
                if (!$menu.data('__movedToBody')) {
                    $menu.appendTo('body');
                    $menu.data('__movedToBody', true);
                }
                var wasHidden = !$menu.is(':visible');
                if (wasHidden) $menu.css({display:'block', visibility:'hidden'});
                var invOff = $inv.offset();
                var invH = $inv.outerHeight();
                var invW = $inv.outerWidth();
                var menuW = $menu.outerWidth();
                if (!menuW || menuW === 0) {
                    var cssW = parseFloat($menu.css('width'));
                    menuW = cssW && !isNaN(cssW) ? cssW : 256; // fallback 16rem
                }
                var scrollY = $(window).scrollTop() || 0;
                var scrollX = $(window).scrollLeft() || 0;
                // Convert document coords to viewport coords for position: fixed
                var left = (invOff.left - scrollX) + invW - menuW;
                if (left < 8) left = 8;
                var top = (invOff.top - scrollY) + invH + 8;
                $menu.css({left:left, top:top});
                if (wasHidden) $menu.css({visibility:'visible', display:'none'});
                try { console.log('[Distributor Header] positioned dropdown bbox', {left, top, menuW}); } catch(e){}
            }

            function hideMenu($menu){ $menu.removeClass('show').css('display','none'); }
            function showMenu($inv, $menu){
                positionDropdown($inv, $menu);
                $menu.addClass('show').css('display','block');
            }
            function toggleMenu($inv, $menu){
                if ($menu.hasClass('show')) {
                    hideMenu($menu);
                } else {
                    // close the other menu first
                    if ($menu.attr('id') === 'accountNavbarDropdown') hideMenu($('#notificationDropdown'));
                    if ($menu.attr('id') === 'notificationDropdown') hideMenu($('#accountNavbarDropdown'));
                    showMenu($inv, $menu);
                }
            }

            function closeAll(){ hideMenu($('#accountNavbarDropdown')); hideMenu($('#notificationDropdown')); }

            // Detach hs-unfold plugin handlers for these specific invokers to avoid double-handling
            $('[data-hs-unfold-options*="#accountNavbarDropdown"]').off('click');
            $('[data-hs-unfold-options*="#notificationDropdown"]').off('click');

            // Account: direct + delegated (covers re-renders)
            $('.navbar-dropdown-account-wrapper').off('click.__acc').on('click.__acc', function(e){
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                toggleMenu($(this), $('#accountNavbarDropdown'));
            });
            $(document).off('click.__accDeleg').on('click.__accDeleg', '[data-hs-unfold-options*="#accountNavbarDropdown"], .navbar-dropdown-account-wrapper', function(e){
                if (e.isPropagationStopped && e.isPropagationStopped()) return;
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                toggleMenu($(this), $('#accountNavbarDropdown'));
            });

            $('[data-hs-unfold-options*="#notificationDropdown"]').off('click.__noti').on('click.__noti', function(e){
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) e.stopImmediatePropagation();
                toggleMenu($(this), $('#notificationDropdown'));
            });

            // Close dropdowns on outside click
            $(document).off('click.__outside').on('click.__outside', function(e){
                var $t = $(e.target);
                var insideAcc = $t.closest('#accountNavbarDropdown').length || $t.closest('.navbar-dropdown-account-wrapper').length;
                var insideNoti = $t.closest('#notificationDropdown').length || $t.closest('[data-hs-unfold-options*="#notificationDropdown"]').length;
                if (!insideAcc) hideMenu($('#accountNavbarDropdown'));
                if (!insideNoti) hideMenu($('#notificationDropdown'));
            });

            // Close on Esc
            $(document).off('keydown.__esc').on('keydown.__esc', function(e){
                if (e.key === 'Escape' || e.keyCode === 27) {
                    hideMenu($('#accountNavbarDropdown'));
                    hideMenu($('#notificationDropdown'));
                }
            });

            // Native capture-phase outside close for maximum robustness
            (function(){
                function isInside(target){
                    var el = target;
                    var matches = function(node, sel){
                        return node.nodeType === 1 && (node.matches ? node.matches(sel) : node.msMatchesSelector && node.msMatchesSelector(sel));
                    };
                    while (el && el !== document) {
                        if (
                            matches(el, '#accountNavbarDropdown') ||
                            matches(el, '#notificationDropdown') ||
                            matches(el, '.navbar-dropdown-account-wrapper') ||
                            matches(el, '[data-hs-unfold-options*="#notificationDropdown"]')
                        ) return true;
                        el = el.parentNode;
                    }
                    return false;
                }
                function onAny(e){
                    var t = e.target;
                    if (!isInside(t)) {
                        hideMenu($('#accountNavbarDropdown'));
                        hideMenu($('#notificationDropdown'));
                    }
                }
                try {
                    document.addEventListener('click', onAny, true);
                    // mousedown removed to avoid pre-closing before link click default
                    document.addEventListener('touchstart', onAny, {passive:true, capture:true});
                } catch(_){ /* no-op */ }
            })();

            // Initialize tooltips if available
            if (typeof $.fn.tooltip !== 'undefined') {
                $('[data-toggle="tooltip"]').tooltip();
            }

            // Ensure links inside account menu work reliably
            $(document)
              .off('click.__accLink')
              .on('click.__accLink', '#accountNavbarDropdown .dropdown-item, #distributorAccountProfileLink, #distributorAccountLogoutLink', function(e){
                  // Do not treat as outside; let default navigate or onclick handle
                  e.stopPropagation();
              });

            $('#distributorAccountProfileLink').off('click.__go').on('click.__go', function(e){
                // Allow default navigation; some browsers might need explicit location change if menu closes simultaneously
                var href = this.getAttribute('href');
                if (href && href !== 'javascript:' && href !== '#') {
                    // Close menus then navigate
                    hideMenu($('#accountNavbarDropdown'));
                    hideMenu($('#notificationDropdown'));
                    // Avoid preventing default; just ensure propagation is stopped
                    setTimeout(function(){ window.location.href = href; }, 0);
                }
            });
        });
    </script>
    
    @stack('script_2')
    @stack('script')
    
    {!! Toastr::message() !!}
    
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