@extends('layouts.distributor.app')

@section('title', translate('messages.food_list'))

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{ translate('Gestão de Estoque') }}</h1>
                </div>
                <div class="col-sm-auto">
                    <a class="btn btn-primary bg-primary text-white border-0 shadow-sm" href="{{ route('distributor.food.index') }}">
                        <i class="tio-add-circle"></i>
                        {{ translate('messages.add_new_food') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header py-2 border-0">
                <div class="search--button-wrapper">
                    <h5 class="card-title">{{ translate('Gestão de Estoque') }} <span class="badge badge-soft-dark ml-2" id="itemCount">{{ $foods->total() }}</span></h5>
                    <form class="search-form">
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{ translate('messages.search_by_name') }}" aria-label="{{ translate('messages.search_here') }}" value="{{ request()?->search ?? '' }}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                </div>
            </div>
            <!-- End Header -->

            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table" data-hs-datatables-options='{
                     "order": [],
                     "orderCellsTop": true,
                     "paging": false,
                     "searching": false
                   }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{ translate('messages.sl') }}</th>
                            <th class="border-0">{{ translate('messages.name') }}</th>
                            <th class="border-0">{{ translate('messages.category') }}</th>
                            <th class="border-0">{{ translate('messages.price') }}</th>
                            <th class="border-0">Estoque Atual</th>
                            <th class="border-0">Última venda</th>
                            <th class="border-0">Inativo/Ativo</th>
                            <th class="text-center border-0">{{ translate('messages.action') }}</th>
                        </tr>
                    </thead>

                    <tbody id="table-div">
                        @foreach($foods as $key => $food)
                            <tr>
                                <td>{{ $key + $foods->firstItem() }}</td>
                                <td>
                                    <a class="media align-items-center" href="{{ route('distributor.food.edit', [$food['id']]) }}">
                                        <img class="avatar avatar-lg mr-3" src="{{ $food['image_full_url'] }}" alt="{{ $food->name }} image">
                                        <div class="media-body">
                                            <h5 class="text-hover-primary mb-0">{{ Str::limit($food['name'], 20, '...') }}</h5>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{ Str::limit($food?->category?->name ?? translate('messages.category_deleted'), 20, '...') }}
                                    </span>
                                </td>
                                <td>R$ {{ number_format($food['price'], 2, ',', '.') }}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        @if($food->stock_type == 'unlimited')
                                            <span class="badge badge-soft-success">Ilimitado</span>
                                        @else
                                            {{ $food->item_stock ?? 0 }} unidades
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @php($last = $food->last_sale_at ?? null)
                                    <span class="d-block font-size-sm text-body">
                                        {{ $last ? \Carbon\Carbon::parse($last)->format('d/m/Y H:i') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="availabilityCheckbox{{ $food->id }}">
                                        <input type="checkbox" onclick="updateStatus({{ $food->id }}, {{ $food->status ? 0 : 1 }})" class="toggle-switch-input" id="availabilityCheckbox{{ $food->id }}" {{ $food->status ? 'checked' : '' }}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="text-center">
                                    <div class="btn--container justify-content-center d-flex align-items-center">
                                        <a class="btn action-btn btn--primary btn-outline-primary d-flex align-items-center justify-content-center" href="{{ route('distributor.food.edit', [$food['id']]) }}" title="{{ translate('messages.edit') }}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="btn action-btn btn--danger btn-outline-danger d-flex align-items-center justify-content-center" href="javascript:" onclick="form_alert('food-{{ $food['id'] }}','{{ translate('messages.Want_to_delete_this_item') }}')" title="{{ translate('messages.delete') }}">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{ route('distributor.food.delete', [$food['id']]) }}" method="post" id="food-{{ $food['id'] }}">
                                            @csrf @method('delete')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(count($foods) === 0)
                    <div class="empty--data">
                        <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="public">
                        <h5>
                            {{ translate('no_data_found') }}
                        </h5>
                    </div>
                @endif
            </div>
            <!-- End Table -->

            <!-- Footer -->
            <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $foods->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>
    function updateStatus(id, status) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $.post({
            url: '{{ route('distributor.food.status') }}',
            data: {
                id: id,
                status: status
            },
            success: function (data) {
                toastr.success('Disponibilidade atualizada com sucesso!');
                location.reload();
            },
            error: function () {
                toastr.error('Erro ao atualizar disponibilidade!');
            }
        });
    }
</script>
@endpush

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });

        function form_alert(id, message) {
            Swal.fire({
                title: '{{ translate('messages.are_you_sure') }}',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#FC6A57',
                cancelButtonText: '{{ translate('messages.no') }}',
                confirmButtonText: '{{ translate('messages.yes') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#' + id).submit()
                }
            })
        }
    </script>
@endpush