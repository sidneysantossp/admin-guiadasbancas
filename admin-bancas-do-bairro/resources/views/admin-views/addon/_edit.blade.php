


<form action="{{ route('admin.addon.update',[$addon['id']]) }}" method="post" class="d-flex flex-column h-100">
    @csrf
    <div>
        <div class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
            <h3 class="mb-0">{{ translate('Edit_Addon_Category') }}</h2>
                <button type="button"
                        class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                        aria-label="Close">&times;</button>
        </div>
        <div class="custom-offcanvas-body p-20">
            <div class="bg--secondary rounded p-20 mb-20">

                @if ($language)
                    <ul class="nav nav-tabs mb-4 border-0">
                        <li class="nav-item">
                            <a class="nav-link lang_link1 active" href="#"
                               id="default-link">{{ translate('messages.default') }}</a>
                        </li>
                        @foreach ($language as $lang)
                            <li class="nav-item">
                                <a class="nav-link lang_link1" href="#"
                                   id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="row">
                    <div class="col-12">
                        @if ($language)
                            <div class="form-group lang_form1" id="default-form1">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}
                                    ({{ translate('messages.default') }})
                                    <span class="form-label-secondary text-danger" data-toggle="tooltip"
                                          data-placement="right"
                                          data-original-title="{{ translate('messages.Required.') }}"> *
                                    </span>

                                </label>
                                <input type="text" name="name[]"
                                       value="{{ $addon?->getRawOriginal('name') }}" class="form-control"
                                       placeholder="{{ translate('messages.new_addon') }}" maxleng="255">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                            @foreach ($language as $key => $lang)
                                    <?php
                                    if (count($addon['translations'])) {
                                        $translate = [];
                                        foreach ($addon['translations'] as $t) {
                                            if ($t->locale == $lang && $t->key == 'name') {
                                                $translate[$lang]['name'] = $t->value;
                                            }
                                        }
                                    }
                                    ?>

                                <div class="form-group d-none lang_form1" id="{{ $lang }}-form1">
                                    <label class="input-label"
                                           for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}
                                        ({{ strtoupper($lang) }})
                                    </label>
                                    <input type="text" name="name[]" value="{{ $translate[$lang]['name'] ?? '' }}"
                                           class="form-control"
                                           placeholder="{{ translate('messages.Type_Category_Name') }}" maxlength="191">
                                </div>
                                <input type="hidden" name="lang[]" value="{{ $lang }}">
                            @endforeach
                        @else
                            <div class="form-group">
                                <label class="input-label"
                                       for="exampleFormControlInput1">{{ translate('messages.Category_Name') }}</label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="{{ translate('messages.new_addon') }}"
                                       value="{{ $addon?->getRawOriginal('name') }}" maxlength="191">
                            </div>
                            <input type="hidden" name="lang[]" value="default">
                        @endif

                    </div>

                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.restaurant')}}<span
                                    class="input-label-secondary"></span></label>
                            <select name="restaurant_id" id="restaurant_id" class="form-control  js-data-example-ajax"  data-placeholder="{{translate('messages.select_restaurant')}}" required oninvalid="this.setCustomValidity('{{translate('messages.please_select_restaurant')}}')">
                                @if($addon->restaurant)
                                    <option value="{{$addon->restaurant_id}}" selected="selected">{{$addon->restaurant->name}}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('messages.price')}}</label>
                            <input type="number" min="0" max="999999999999.99" step="0.01" name="price" value="{{$addon['price']}}" class="form-control" placeholder="200" required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="input-label"
                                   for="exampleFormControlInput1">{{ translate('messages.Stock_Type') }}
                            </label>
                            <select name="stock_type" id="edit_stock_type" class="form-control js-select2-custom">
                                <option  {{$addon['stock_type'] == 'unlimited' ? 'selected':'' }}  value="unlimited">{{ translate('messages.Unlimited_Stock') }}</option>
                                <option {{$addon['stock_type'] == 'limited' ? 'selected' : '' }} value="limited">{{ translate('messages.Limited_Stock')  }}</option>
                                <option {{$addon['stock_type'] == 'daily' ? 'selected' : '' }}  value="daily">{{ translate('messages.Daily_Stock')  }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 edit_hide_this">
                        <div class="form-group">
                            <label class="input-label" for="addon_stock">{{translate('messages.Addon_Stock')}}</label>
                            <input type="number" min="0" id="addon_stock" max="999999999999" name="addon_stock"   {{$addon['stock_type'] == 'unlimited' ? 'readonly':'' }} placeholder="{{$addon['stock_type'] == 'unlimited' ? translate('Unlimited') : translate('messages.Ex:_100')  }}"  value="{{$addon['stock_type'] == 'unlimited' ? '':$addon['addon_stock']  }}" class="form-control edit_stock_disable"  >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <span class="mb-2 d-block title-clr fw-normal">{{ translate('Category') }}</span>
                            <select name="category_id" required class="form-control js-select2-custom1"
                                    placeholder="Select Category">
                                <option selected disabled value=""> {{ translate('messages.select_category') }}
                                </option>
                                @foreach ($addonCategories as $category)
                                    <option {{ $category->id == $addon->addon_category_id ? 'selected' : '' }}
                                            value="{{ $category->id }}"> {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    @if ($productWiseTax)
                        <div class="col-12">
                            <div class="pickup-zone-tag">
                                <span class="mb-2 d-block title-clr fw-normal">{{ translate('Select Tax Rate') }}</span>
                                <select name="tax_ids[]" required id="" class="form-control multiple-select2"
                                        multiple="multiple" data-placeholder="{{translate('Type & Select Tax Rate')}}">
                                    @foreach ($taxVats as $taxVat)
                                        <option {{ in_array($taxVat->id, $taxVatIds) ? 'selected' : '' }}
                                                value="{{ $taxVat->id }}"> {{ $taxVat->name }}
                                            ({{ $taxVat->tax_rate }}%)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    <div class="align-items-center bg-white bottom-0 d-flex gap-3 justify-content-center mt-auto offcanvas-footer p-3 position-sticky">
        <button type="button" class="btn w-100 btn--secondary offcanvas-close h--40px">{{ translate('Cancel') }}</button>
        <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('Update') }}</button>
    </div>
</form>
