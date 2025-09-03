@extends('layouts.distributor.app')

@section('title','Atualizar Produto')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{dynamicAsset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <style>
        /* Destaque visual para a seção de variações */
        .variations-highlight {
            border: 1px solid #cfe2ff;
            background: #f8fbff;
            border-radius: 8px;
            padding: 16px;
        }
        .variation-builder .table thead th { white-space: nowrap; }
        .variation-builder .remove-row-btn { color: #c03221; }
        .variation-builder .add-row-btn { color: #0a7cff; }
        .variation-tip { font-size: 12px; color: #6b7785; }
        .badge-optional { font-size: 11px; background: #e7f1ff; color: #0052cc; }
        
        /* Estilos personalizados para botões */
        .btn--container {
            padding: 25px 0;
            gap: 15px;
        }
        .btn--container .btn {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            margin: 0 8px;
        }
        .btn--reset {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: white !important;
        }
        .btn--reset:hover {
            background-color: #5a6268 !important;
            border-color: #545b62 !important;
        }
        .btn--primary {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: white !important;
        }
        .btn--primary:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
    </style>
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title"><i class="tio-edit text-dark"></i> Atualizar produto</h1>
        </div>

        <form action="javascript:" method="post" id="product_form" enctype="multipart/form-data">
            @csrf
            <div class="row g-2">
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-body pb-0">
                            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                                <ul class="nav nav-tabs mb-4">
                                    <li class="nav-item">
                                        <a class="nav-link lang_link active"
                                            href="#"
                                            id="default-link">Português</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="lang_form"  id="default-form">
                                <div class="form-group">
                                    <label class="input-label" for="default_name">{{translate('messages.name')}} ({{translate('messages.default')}})</label>
                                    <input type="text" name="name[]" id="default_name" class="form-control" placeholder="{{translate('messages.new_food_name')}}" value="{{$product['name']}}" required>
                                </div>
                                <input type="hidden" name="lang[]" value="default">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('messages.short_description')}} ({{translate('messages.default')}})</label>
                                    <textarea name="description[]" class="form-control" rows="3" placeholder="{{translate('messages.short_description')}}">{{$product['description']}}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dashboard-outlined text-dark"></i></span>
                                <span>Imagem do Produto</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="d-flex justify-content-center">
                                    <div class="upload-file">
                                        <input type="file" name="image" id="customFileEg1" class="upload-file__input" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                        <div class="upload-file__img">
                                            <img src="{{ $product->image ? asset('storage/app/public/product/'.$product->image) : asset('public/assets/admin/img/900x400/img1.jpg') }}" alt="Imagem do produto">
                                        </div>
                                        <div class="upload-file__edit">
                                            <i class="tio-edit text-dark"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-2">
                <div class="col-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2"><i class="tio-dashboard-outlined text-dark"></i></span>
                                <span>{{translate('messages.food_details')}}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.category')}}<span class="input-label-secondary">*</span></label>
                                        <select name="category_id" id="category-id" class="form-control js-select2-custom" onchange="getRequest('{{url('/')}}/admin/food/get-categories?parent_id='+this.value,'sub-categories')">
                                            <option value="" selected disabled>{{translate('messages.select_category')}}</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}" {{ (isset($product_category[0]) && $product_category[0]->id==$category['id']) ? 'selected' : '' }}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_category')}} (1° nível)<span class="input-label-secondary"></span></label>
                                        <select name="sub_category_id" id="sub-categories" class="form-control js-select2-custom" onchange="getRequest('{{url('/')}}/admin/food/get-categories?parent_id='+this.value,'sub-sub-categories')">
                                            @if(isset($product_category[1]))
                                                <option value="{{$product_category[1]->id}}" selected>{{$product_category[1]->name}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('messages.sub_sub_category')}} (2° nível)<span class="input-label-secondary"></span></label>
                                        <select name="sub_sub_category_id" id="sub-sub-categories" class="form-control js-select2-custom">
                                            @if(isset($product_category[2]))
                                                <option value="{{$product_category[2]->id}}" selected>{{$product_category[2]->name}}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>

                            @php($attrs = $product['attributes'] ? json_decode($product['attributes'], true) : [])
                            @php($product_type = $attrs['product_type'] ?? '')

                            <!-- Todos os campos de preço, desconto e estoque em uma única linha -->
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="input-label" for="price-input">{{translate('messages.price')}}<span class="input-label-secondary">*</span></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">R$</span>
                                            </div>
                                            <input type="text" id="price-input" value="{{number_format($product['price'], 2, ',', '.')}}" class="form-control" placeholder="100,00" required>
                                            <input type="hidden" name="price" id="price-input-hidden" value="{{$product['price']}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount_type')}}</label>
                                        <select name="discount_type" class="form-control js-select2-custom">
                                            <option value="percent" {{$product['discount_type']=='percent'?'selected':''}}>{{translate('messages.percentage')}}</option>
                                            <option value="amount" {{$product['discount_type']=='amount'?'selected':''}}>{{translate('messages.amount')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.discount')}}</label>
                                        <input type="number" min="0" value="{{$product['discount']}}" max="999999999999.99" step="0.01" name="discount" class="form-control" placeholder="100">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label font-weight-bold">{{ translate('Tipo de Unidade') }} *</label>
                                        @php($product_type = isset($product_type) ? $product_type : (data_get(json_decode($product['attributes'] ?? '[]', true), 'product_type', '')))
                                        <select name="product_type" class="form-control js-select2-custom" required>
                                            <option value="">---{{ translate('messages.select') }}---</option>
                                            <option value="un" {{ $product_type=='un' ? 'selected' : '' }}>{{ translate('UN') }}</option>
                                            <option value="pct" {{ $product_type=='pct' ? 'selected' : '' }}>{{ translate('PCT') }}</option>
                                            <option value="kg" {{ $product_type=='kg' ? 'selected' : '' }}>{{ translate('KG') }}</option>
                                            <option value="lt" {{ $product_type=='lt' ? 'selected' : '' }}>{{ translate('LT') }}</option>
                                            <option value="m" {{ $product_type=='m' ? 'selected' : '' }}>{{ translate('M') }}</option>
                                            <option value="cx" {{ $product_type=='cx' ? 'selected' : '' }}>{{ translate('CX') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="input-label font-weight-bold">{{ translate('Quantidade em Estoque') }} *</label>
                                        <input type="number" name="quantity" class="form-control" min="1" step="1" value="{{$product['item_stock']}}" placeholder="{{ translate('Digite a quantidade') }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.available_time_starts')}}</label>
                                        <input type="time" value="{{$product['available_time_starts']}}" name="available_time_starts" class="form-control" placeholder="{{translate('messages.Ex:')}} 10:30">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('messages.available_time_ends')}}</label>
                                        <input type="time" value="{{$product['available_time_ends']}}" name="available_time_ends" class="form-control" placeholder="{{translate('messages.Ex:')}} 19:30">
                                    </div>
                                </div>
                            </div>

                            <!-- Campos ocultos para manter compatibilidade -->
                            <input type="hidden" name="veg" value="{{$product['veg']}}">
                            <input type="hidden" name="stock_type" value="{{$product['stock_type']}}">
                            <input type="hidden" name="maximum_cart_quantity" value="{{$product['maximum_cart_quantity']}}">

                            <!-- Seção de Variações (Edit) -->
                            <div class="row g-2 mt-3">
                                <div class="col-lg-12">
                                    <div class="card shadow--card-2 border-0">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                                <span class="card-header-icon mr-2"><i class="tio-layers text-dark"></i></span>
                                                <span class="font-weight-bold">{{ translate('Variações do Produto') }}</span>
                                                <span class="badge badge-optional ml-2">{{ translate('Opcional') }}</span>
                                            </h5>
                                        </div>
                                        <div class="card-body variations-highlight">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <div class="font-weight-bold mb-1">{{ translate('Habilitar variações?') }}</div>
                                                    <div class="variation-tip">{{ translate('Use variações para vender tamanhos, cores ou outras opções com preços diferentes') }}</div>
                                                </div>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="enable-variations">
                                                    <label class="custom-control-label" for="enable-variations">{{ translate('Ativar') }}</label>
                                                </div>
                                            </div>

                                            <div id="variation-builder" class="variation-builder" style="display: none;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="input-label font-weight-bold">{{ translate('Nome do Atributo') }}</label>
                                                            <input type="text" id="variation-attribute-name" class="form-control" placeholder="Ex.: Tamanho, Cor, Sabor">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-borderless align-middle mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:40%">{{ translate('Opção') }}</th>
                                                                <th style="width:40%">{{ translate('Acréscimo no Preço (R$)') }}</th>
                                                                <th style="width:20%" class="text-right">
                                                                    <button type="button" class="btn btn-sm btn-light add-row-btn" id="add-variation-row">
                                                                        <i class="tio-add text-dark"></i> {{ translate('Adicionar opção') }}
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="variation-rows"></tbody>
                                                    </table>
                                                </div>

                                                <div class="variation-tip mt-3">
                                                    <i class="tio-info text-dark"></i>
                                                    {{ translate('O preço final será calculado como Preço de Venda + Acréscimo escolhido') }}
                                                </div>
                                            </div>

                                            <!-- Hidden inputs para backend -->
                                            <input type="hidden" name="choice_options" id="choice_options">
                                            <input type="hidden" name="variations" id="variations">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Fim da seção de variações -->

                            <div class="row">
                                <div class="col-12">
                                    <div class="btn--container justify-content-end">
                                        <button type="reset" id="reset_btn" class="btn btn--reset">{{translate('messages.reset')}}</button>
                                        <button type="submit" class="btn btn--primary">{{translate('messages.update')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script>
        function getRequest(route, element_id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + element_id).empty().append(data.options);
                },
            });
        }

        function stock_type_change(val) {
            if (val == 'unlimited') {
                $('#stock_input').hide();
            } else {
                $('#stock_input').show();
            }
        }

        // ======== VARIAÇÕES - UI (Edit) ========
        function addVariationRow(option = '', price = '') {
            var rowId = 'row-' + Date.now() + '-' + Math.floor(Math.random()*1000);
            var tr = `
                <tr id="${rowId}">
                    <td>
                        <input type="text" class="form-control variation-option" placeholder="Ex.: P, M, G" value="${option}">
                    </td>
                    <td>
                        <input type="text" class="form-control variation-price" placeholder="0,00" value="${price}">
                    </td>
                    <td class="text-right">
                        <button type="button" class="btn btn-sm btn-link remove-row-btn" data-row="${rowId}">
                            <i class="tio-delete text-dark"></i> {{ translate('Remover') }}
                        </button>
                    </td>
                </tr>`;
            $('#variation-rows').append(tr);
            // aplica máscara de moeda
            $(`#${rowId} .variation-price`).mask('#.##0,00', { reverse: true });
        }

        $(document).on('click', '#add-variation-row', function(){
            addVariationRow();
        });

        $(document).on('click', '.remove-row-btn', function(){
            var row = $(this).data('row');
            $('#' + row).remove();
        });

        $('#enable-variations').on('change', function(){
            if($(this).is(':checked')){
                $('#variation-builder').slideDown(150);
                if($('#variation-rows tr').length === 0){
                    addVariationRow();
                }
            } else {
                $('#variation-builder').slideUp(150);
                $('#variation-rows').empty();
                $('#variation-attribute-name').val('');
                $('#choice_options').val('');
                $('#variations').val('');
            }
        });

        function buildVariationsPayload(){
            var enabled = $('#enable-variations').is(':checked');
            if(!enabled){
                $('#choice_options').val('');
                $('#variations').val('');
                return;
            }
            var attrName = ($('#variation-attribute-name').val() || '').trim();
            var options = [];
            var variations = [];

            $('#variation-rows tr').each(function(){
                var opt = $(this).find('.variation-option').val().trim();
                var priceMasked = $(this).find('.variation-price').val();
                if(opt){
                    options.push(opt);
                    var price = 0;
                    if(priceMasked){
                        price = parseFloat(priceMasked.replace(/\./g,'').replace(',', '.')) || 0;
                    }
                    variations.push({ type: opt, price: price });
                }
            });

            if(attrName && options.length){
                $('#choice_options').val(JSON.stringify([{ name: attrName, options: options }]));
                $('#variations').val(JSON.stringify(variations));
            } else {
                $('#choice_options').val('');
                $('#variations').val('');
            }
        }

        // Pré-preencher variações existentes
        $(document).ready(function(){
            try {
                var existingChoiceOptions = @json($product['choice_options'] ? json_decode($product['choice_options'], true) : []);
                var existingVariations = @json($product['variations'] ? json_decode($product['variations'], true) : []);
            } catch (e) {
                var existingChoiceOptions = [];
                var existingVariations = [];
            }
            if (existingChoiceOptions && existingChoiceOptions.length) {
                $('#enable-variations').prop('checked', true);
                $('#variation-builder').show();
                var attrName = existingChoiceOptions[0].name || '';
                $('#variation-attribute-name').val(attrName);
                (existingVariations || []).forEach(function(v){
                    var priceStr = (parseFloat(v.price || 0)).toFixed(2).replace('.', ',');
                    addVariationRow(v.type || '', priceStr);
                });
                buildVariationsPayload();
            }
        });

        $('#product_form').on('submit', function (e) {
            e.preventDefault();
            // Monta payload de variações antes do submit
            buildVariationsPayload();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('distributor.food.update',[$product['id']])}}',
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
                            location.href = '{{route('distributor.food.list')}}';
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('{{translate('messages.something_went_wrong')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });

        $('.lang_link').click(function(e){
            e.preventDefault();
            $('.lang_link').removeClass('active');
            $(this).addClass('active');
            var form_id = this.id;
            var lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $('.lang_form').addClass('d-none');
            $('#'+lang+'-form').removeClass('d-none');
            if(lang == 'default')
            {
                $(".from_part_2").removeClass("d-none");
            }
            else
            {
                $(".from_part_2").addClass("d-none");
            }
        });

        $(document).ready(function(){
            $('#category-id').select2({
                ajax: {
                    url: '{{route('admin.category.get-all')}}',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            all:true,
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });
        // Máscara para formato monetário brasileiro (R$)
        $(document).ready(function() {
            $('#price-input').on('input', function(e) {
                // Remove caracteres não numéricos
                let value = $(this).val().replace(/\D/g, '');
                
                // Converte para centavos
                value = parseInt(value);
                if (isNaN(value)) {
                    value = 0;
                }
                
                // Calcula valor em reais (divide por 100 para converter centavos em reais)
                let realValue = value / 100;
                
                // Atualiza o campo hidden com o valor para o servidor (sem formatação)
                $('#price-input-hidden').val(realValue);
                
                // Formata para exibição no campo visível
                $(this).val(realValue.toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            });
        });
    </script>
@endpush