@extends('layouts.vendor.app')

@section('title','Update Food')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{dynamicAsset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-edit"></i> {{translate('messages.food_update')}}</h1>
        </div>

        <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-body pb-0">
                            @php
                                $languageSetting = \App\Models\BusinessSetting::where('key', 'language')->first();
                                $language = $languageSetting->value ?? null;
                                $langs = collect(
                                    is_string($language)
                                        ? (json_decode($language, true) ?: [])
                                        : (is_array($language) ? $language : [])
                                )
                                ->filter(function ($l) { return is_string($l) && $l !== ''; })
                                ->values()
                                ->all();
                                $default_lang = str_replace('_', '-', app()->getLocale());
                                if (!isset($langs) || !is_array($langs)) { $langs = []; }
                            @endphp
                            @if (!empty($langs))
                                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                    <ul class="nav nav-tabs mb-4">
                                        <li class="nav-item">
                                            <a class="nav-link lang_link active"
                                                href="#"
                                                id="default-link">{{ translate('Default') }}</a>
                                        </li>
                                        @foreach (($langs ?? []) as $lang)
                                            <li class="nav-item">
                                                <a class="nav-link lang_link "
                                                    href="#"
                                                    id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="card-body">
                            <div class="lang_form"  id="default-form">
                                <div class="form-group">
                                    <label class="input-label" for="default_name">{{ translate('messages.name') }} ({{ translate('Default') }})</label>
                                    <input type="text" name="name[]" id="default_name" class="form-control" value="{{$product?->getRawOriginal('name')}}" required placeholder="{{ translate('messages.new_food') }}">
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.short_description') }} ({{ translate('Default') }})</label>
                                    <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{{ $product?->getRawOriginal('description') ?? '' }}</textarea>
                                </div>
                            </div>
                            @if (!empty($langs))
                                @foreach (($langs ?? []) as $lang)
                                    @php
                                        $__translations = $product['translations'] ?? [];
                                        $translate = [];
                                        if (!empty($__translations) && is_iterable($__translations)) {
                                            foreach ($__translations as $t) {
                                                if ($t->locale == $lang && $t->key == 'name') {
                                                    $translate[$lang]['name'] = $t->value;
                                                }
                                                if ($t->locale == $lang && $t->key == 'description') {
                                                    $translate[$lang]['description'] = $t->value;
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="d-none lang_form" id="{{ $lang }}-form">
                                        <div class="form-group">
                                            <label class="input-label" for="{{ $lang }}_name">{{ translate('messages.name') }} ({{ strtoupper($lang) }})</label>
                                            <input type="text" name="name[]" id="{{ $lang }}_name" class="form-control" value="{{ $translate[$lang]['name'] ?? '' }}" placeholder="{{ translate('messages.new_food') }}">
                                        </div>
                                        <input type="hidden" name="lang[]" value="{{ $lang }}">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.short_description') }} ({{ strtoupper($lang) }})</label>
                                            <textarea type="text" name="description[]" class="form-control ckeditor min-height-154px">{{ $translate[$lang]['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0 h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <span>{{ translate('Food_Image') }} <small class="text-danger">({{ translate('messages.Ratio_200x200') }})</small></span>
                            </h5>
                            <div class="form-group mb-0 h-100 d-flex flex-column align-items-center justify-content-center">
                                <label>
                                    <div id="image-viewer-section" class="my-auto text-center">
                                        <img class="initial-52 object--cover border--dashed" id="viewer"
                                        src="{{ $product?->image_full_url ?? dynamicAsset('/public/assets/admin/img/100x100/food-default-image.png') }}"
                                        alt="image">
                                        <input type="file" name="image" id="customFileEg1" class="d-none" accept="image/*">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dashboard-outlined"></i></span>
                                <span>{{ translate('messages.food_details') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{ translate('messages.category') }}<span class="input-label-secondary">*</span></label>
                                        <select name="category_id" id="category-id" class="form-control js-select2-custom" required>
                                            <option value="" selected disabled>{{ translate('messages.select_category') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ (isset($product_category) && !empty($product_category) && isset($product_category[0]) && isset($product_category[0]->id) && ($category->id == $product_category[0]->id)) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{ translate('messages.sub_category') }}<span class="input-label-secondary"></span></label>
                                        <select name="sub_category_id" id="sub-categories" class="form-control js-select2-custom">
                                            <option value="">---{{ translate('messages.select') }}---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.price') }}<span class="input-label-secondary">*</span></label>
                                        <input type="number" min="0" max="999999999999.99" step="0.01" value="{{ $product['price'] }}" name="price" class="form-control" placeholder="{{ translate('messages.Ex:') }} 100" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.discount_type') }}</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent" {{ $product['discount_type'] == 'percent' ? 'selected' : '' }}>{{ translate('messages.percentage') }}</option>
                                            <option value="amount" {{ $product['discount_type'] == 'amount' ? 'selected' : '' }}>{{ translate('messages.amount') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.discount') }}</label>
                                        <input type="number" min="0" value="{{ $product['discount'] }}" max="999999999999.99" step="0.01" name="discount" class="form-control" placeholder="{{ translate('messages.Ex:') }} 100">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.food_type') }}</label>
                                        <select required name="veg" class="form-control js-select2-custom">
                                            <option value="0" {{ $product['veg'] == 0 ? 'selected' : '' }}>{{ translate('messages.non_veg') }}</option>
                                            <option value="1" {{ $product['veg'] == 1 ? 'selected' : '' }}>{{ translate('messages.veg') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2 mt-2">
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dashboard-outlined"></i></span>
                                <span>{{ translate('messages.addon') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $decoded = $product->add_ons ?? ($product['add_ons'] ?? '[]');
                                if ($decoded === null || $decoded === '' || $decoded === 'null') { 
                                    $decoded = '[]'; 
                                }
                                $selectedAddons = json_decode($decoded, true);
                                if (!is_array($selectedAddons)) { 
                                    $selectedAddons = []; 
                                }
                            @endphp
                            <label class="input-label" for="addon_select">{{ translate('Select Add-on') }}</label>
                            <select name="addon_ids[]" id="addon_select" class="form-control h--45px js-select2-custom" multiple="multiple">
                                @foreach(\App\Models\AddOn::where('restaurant_id', \App\CentralLogics\Helpers::get_restaurant_id())->orderBy('name')->get() as $addon)
                                    <option value="{{ $addon->id }}" {{ (!empty($selectedAddons) && in_array($addon->id, $selectedAddons, true)) ? 'selected' : '' }}>
                                        {{ $addon->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-date-range"></i></span>
                                <span>{{ translate('messages.Availability') }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.available_time_starts') }}</label>
                                        <input type="time" name="available_time_starts" class="form-control" id="available_time_starts" value="{{ $product['available_time_starts'] }}" placeholder="{{ translate('messages.Ex :') }} 10:30 am" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.available_time_ends') }}</label>
                                        <input type="time" name="available_time_ends" class="form-control" value="{{ $product['available_time_ends'] }}" id="available_time_ends" placeholder="5:45 pm" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn--primary">{{ translate('messages.update') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script>
        // Fixed getRequest function to handle responses properly
        function getRequest(route, element_id) {
            $.get({
                url: route,
                success: function (data) {
                    // Handle both JSON and HTML responses
                    if (typeof data === 'object' && data.options) {
                        $('#' + element_id).empty().append(data.options);
                    } else if (typeof data === 'string') {
                        $('#' + element_id).empty().append(data);
                    } else {
                        console.log('Unexpected data format:', data);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error loading categories:', error);
                }
            });
        }

        $('#product_form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('vendor.food.update',[$product['id']])}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('{{translate('messages.product_updated_successfully')}}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('vendor.food.list')}}';
                        }, 2000);
                    }
                }
            });
        });

        $('.lang_link').click(function(e){
            e.preventDefault();
            $('.lang_link').removeClass('active');
            $(this).addClass('active');
            var form_id = this.id;
            var lang = form_id.substring(0, form_id.length - 5);
            $('.lang_form').addClass('d-none');
            $('#'+lang+'-form').removeClass('d-none');
            if(lang == 'default') {
                $(".from_part_2").removeClass("d-none");
            } else {
                $(".from_part_2").addClass("d-none");
            }
        });

        // Category change handler
        $('#category-id').on('change', function() {
            let category_id = $(this).val();
            if (category_id) {
                getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + category_id, 'sub-categories');
            }
        });

        // Initialize category loading on page load
        $(document).ready(function () {
            setTimeout(function () {
                let category = $("#category-id").val();
                @php
                    $subCategoryId = (isset($product_category) && is_iterable($product_category) && count($product_category) >= 2 && isset($product_category[1]) && isset($product_category[1]->id)) ? $product_category[1]->id : '';
                @endphp
                let sub_category = '{{ $subCategoryId }}';
                if (category) {
                    getRequest('{{url('/')}}/restaurant-panel/food/get-categories?parent_id=' + category + '&sub_category=' + sub_category, 'sub-categories');
                }
            }, 1000);
        });
    </script>
@endpush
