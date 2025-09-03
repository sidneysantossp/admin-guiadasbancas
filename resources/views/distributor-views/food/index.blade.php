@extends('layouts.distributor.app')

@section('title', 'Cadastro de Produto')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ dynamicAsset('public/assets/admin/css/tags-input.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> Cadastro de Produto</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <form action="javascript:" method="post" id="food_form" enctype="multipart/form-data">
            @csrf
            @php($default_lang = str_replace('_', '-', app()->getLocale()))
            
            <div class="row g-2">
                <!-- SEÇÃO 1: INFORMAÇÕES BÁSICAS -->
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title">
                                <span class="card-header-icon mr-2">
                                    <i class="tio-shopping-basket text-dark"></i>
                                </span>
                                <span>Informações Básicas do Produto</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="name">{{ translate('messages.name') }}<span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="Campo obrigatório"> *</span></label>
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Nome do produto" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="image">{{ translate('messages.image') }}</label>
                                        <input type="file" name="image" id="image" class="form-control" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-2 mb-0">
                                <label class="input-label" for="description">{{ translate('messages.short_description') }}</label>
                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Descrição breve do produto"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEÇÃO 2: CATEGORIA E SUBCATEGORIA -->
                <div class="col-lg-12">
                <div class="card shadow--card-2 border-0">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon mr-2">
                                <i class="tio-dashboard-outlined text-dark"></i>
                            </span>
                            <span>Informações de Categoria</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-6 col-lg-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="category_id">{{ translate('messages.category') }}<span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="Campo obrigatório"> *</span></label>
                                    <select name="category_id" id="category_id" class="form-control js-select2-custom" onchange="getRequest('{{url('/')}}/admin/food/get-categories?parent_id='+this.value,'sub-categories')" required>
                                        <option value="" selected disabled>{{ translate('messages.select_category') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category['id']}}" {{ old('category_id') == $category['id'] ? 'selected' : '' }}>{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="sub_category_id">{{ translate('messages.sub_category') }}</label>
                                    <select name="sub_category_id" id="sub_category_id" class="form-control js-select2-custom">
                                        <option value="" selected>{{ translate('messages.select_sub_category') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card shadow--card-2 border-0">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon mr-2">
                                <i class="tio-money text-dark"></i>
                            </span>
                            <span>Informações de Preço</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="price">{{ translate('messages.price') }}<span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="Campo obrigatório"> *</span></label>
                                    <input type="number" min="0" max="999999" step="0.01" placeholder="Ex: 100" class="form-control" name="price" id="price" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="discount">{{ translate('messages.discount') }}</label>
                                    <input type="number" min="0" max="999999" step="0.01" placeholder="Ex: 100" class="form-control" name="discount" id="discount">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="discount_type">{{ translate('messages.discount_type') }}</label>
                                    <select name="discount_type" id="discount_type" class="form-control js-select2-custom">
                                        <option value="percent">{{ translate('messages.percentage') }}</option>
                                        <option value="amount">{{ translate('messages.amount') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 4: VARIAÇÕES (OPCIONAL MAS PADRONIZADA) -->
            <div class="row g-2 mt-3">
                <div class="col-lg-12">
                    <div class="card shadow--card-2 border-0">
                        <div class="card-header">
                            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                <span class="card-header-icon mr-2"><i class="tio-layers"></i></span>
                                <span class="font-weight-bold">{{ translate('Variações do Produto') }}</span>
                                <span class="badge badge-soft-secondary ml-2">{{ translate('Opcional') }}</span>
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
                                                        <i class="tio-add"></i> {{ translate('Adicionar opção') }}
                                                    </button>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="variation-rows"></tbody>
                                    </table>
                                </div>

                                <div class="variation-tip mt-3">
                                    <i class="tio-info"></i>
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

            <!-- SEÇÃO 4: TAGS E DISPONIBILIDADE -->
            <div class="col-lg-12">
                <div class="card shadow--card-2 border-0">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon mr-2">
                                <i class="tio-label text-dark"></i>
                            </span>
                            <span>Informações Adicionais</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-8">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="tags">{{ translate('messages.tags') }}</label>
                                    <input type="text" class="form-control" name="tags" id="tags" placeholder="Digite as tags separadas por vírgula" data-role="tagsinput">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="maximum_cart_quantity">{{ translate('messages.maximum_cart_quantity') }}</label>
                                    <input type="number" name="maximum_cart_quantity" id="maximum_cart_quantity" class="form-control" min="0" placeholder="Ex: 5">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEÇÃO 5: DISPONIBILIDADE -->
            <div class="col-lg-12">
                <div class="card shadow--card-2 border-0">
                    <div class="card-header">
                        <h5 class="card-title">
                            <span class="card-header-icon mr-2"><i class="tio-checkmark-circle text-dark"></i></span>
                            <span>{{ translate('Disponibilidade do Produto') }}</span>
                        </h5>
                    </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-sm-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label">
                                            {{ translate('Disponibilidade') }}
                                            <span class="form-label-secondary text-danger" data-toggle="tooltip" data-placement="right" data-original-title="{{ translate('messages.Required.') }}"> * </span>
                                        </label>
                                        <select name="availability" id="availability-select" class="form-control js-select2-custom" required>
                                            <option value="">---{{ translate('messages.select') }}---</option>
                                            <option value="1">{{ translate('Disponível') }}</option>
                                            <option value="0">{{ translate('Indisponível') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="available_time_starts">{{ translate('messages.available_time_starts') }}</label>
                                        <input type="time" name="available_time_starts" id="available_time_starts" class="form-control" placeholder="Ex: 10:30">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="available_time_ends">{{ translate('messages.available_time_ends') }}</label>
                                        <input type="time" name="available_time_ends" id="available_time_ends" class="form-control" placeholder="Ex: 18:00">
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group mb-0">
                                        <div class="form-check mt-4">
                                            <input type="checkbox" name="always_available" id="always-available" class="form-check-input" value="1">
                                            <label class="form-check-label" for="always-available">
                                                {{ translate('Sempre Disponível') }} <small class="text-muted d-block">(ignora horários definidos acima)</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BOTÕES DE AÇÃO -->
                <div class="col-lg-12">
                    <div class="btn--container justify-content-end">
                        <a href="{{ route('distributor.food.list') }}" class="btn btn-secondary">{{ translate('messages.back') }}</a>
                        <button type="reset" id="reset_btn" class="btn btn-secondary">{{ translate('messages.reset') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('messages.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        // Alternância de abas de idioma (padrão admin)
        $(document).on('click', '.lang_link', function (e) {
            e.preventDefault();
            const id = $(this).attr('id');
            const lang = id.replace('-link', '');
            $('.lang_link').removeClass('active');
            $(this).addClass('active');
            $('.lang_form').addClass('d-none');
            $('#' + lang + '-form').removeClass('d-none');
        });

        // CATEGORIAS: carregar subcategorias
        $('#category_id').on('change', function() {
            var category_id = this.value;
            $('#sub_category_id').empty().append('<option value="">---{{ translate('messages.select') }}---</option>');
            
            if (category_id) {
                $.get("{{ route('distributor.food.get-categories') }}", {parent_id: category_id}, function(data) {
                    $('#sub_category_id').empty().append('<option value="">---{{ translate('messages.select') }}---</option>');
                    $.each(data, function(key, value) {
                        $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                });
            }
        });

        // ======== VARIAÇÕES - UI ========
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
                            <i class="tio-delete"></i> {{ translate('Remover') }}
                        </button>
                    </td>
                </tr>`;
            $('#variation-rows').append(tr);
            // aplica máscara de moeda
            $(`#${rowId} .variation-price`).mask('#.##0,00', { reverse: true });
        }

        $('#add-variation-row').on('click', function(){
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

        $('#product_form').on('submit', function (e) {
            e.preventDefault();
            
            // Se "sempre disponível" estiver marcado, limpa os campos de horário
            if ($('#always-available').is(':checked')) {
                $('input[name="available_time_starts"]').val('');
                $('input[name="available_time_ends"]').val('');
            }
            
            // Garante que o hidden de preço esteja atualizado (caso o usuário não tenha disparado input)
            (function ensureHiddenPrice(){
                let value = $('#price-input').val().replace(/\D/g, '');
                value = parseInt(value);
                if (isNaN(value)) value = 0;
                let realValue = value / 100; // ex.: "790" -> 7.90
                $('#price-input-hidden').val(realValue);
            })();
            
            // Monta payload de variações antes do submit
            buildVariationsPayload();
            
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{ route('distributor.food.store') }}',
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
                        toastr.success('{{ translate('messages.product_added_successfully') }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{ route('distributor.food.list') }}';
                        }, 2000);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('{{ translate('messages.something_went_wrong') }}');
                }
            });
        });

        // Máscara de real para o campo de valor
        $('#price-input').mask('#.##0,00', {
            reverse: true,
            translation: {
                '#': {pattern: /[0-9]/}
            }
        });
        // Formatação pt-BR no campo visível e atualização do hidden com valor numérico
        $(document).ready(function() {
            $('#price-input').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                value = parseInt(value);
                if (isNaN(value)) value = 0;
                let realValue = value / 100;
                // Atualiza hidden (valor para o backend)
                $('#price-input-hidden').val(realValue);
                // Atualiza exibição formatada
                $(this).val(realValue.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            });
        });

        // Controle do checkbox "sempre disponível"
        $('#always-available').change(function() {
            if ($(this).is(':checked')) {
                $('#time-availability-section').hide();
                $('#time-availability-section input').prop('required', false);
                $('#time-availability-section input').val(''); // Limpa os valores
            } else {
                $('#time-availability-section').show();
                // Não torna obrigatório pois os campos são opcionais
            }
        });

        // Image preview
        $('#image-input').change(function() {
            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.preview-image').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('.delete_image').click(function() {
            $('#image-input').val('');
            $('.preview-image').hide();
        });
    </script>
@endpush