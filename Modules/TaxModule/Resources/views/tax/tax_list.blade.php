@extends('layouts.admin.app')

@section('title', translate('messages.Tax_Setup'))


    @section('taxModule')
    active
    @endsection
    @section('taxModuleDisplay')
    block
    @endsection
    @section('tax_setup')
    show active
    @endsection

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')

    <div class="content container-fluid">
        <h3 class="mb-20">{{ translate('All Taxes') }}</h3>
        <div class="mt-5">

            @if ($taxVatdatas)
                <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-20">
                    <h4 class="mb-0">{{ translate('List of Taxes') }}

                        <span class="badge badge-soft-dark ml-2" id="itemCount">{{ $taxVats->count() }}</span>
                    </h4>
                    <div class="search--button-wrapper justify-content-end">
                        <form class="search-form min--260">
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control h--40px"
                                    placeholder="{{ translate('messages.Ex: Tax') }}"
                                    value="{{ request()?->search ?? null }}"
                                    aria-label="{{ translate('messages.search') }}">

                                <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                            </div>
                        </form>


                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle h--40px" href="javascript:;"
                                data-hs-unfold-options='{
                            "target": "#usersExportDropdown", "type": "css-animation" }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}
                            </a>
                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('messages.download_options') }}</span>
                                <a id="export-excel" class="dropdown-item"
                                    href="{{ route('taxvat.export', ['type' => 'excel', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ dynamicAsset('Modules/TaxModule/public/assets/admin/img/excel.svg') }}"
                                        alt="Image Description">
                                    {{ translate('messages.excel') }}
                                </a>
                                <a id="export-csv" class="dropdown-item"
                                    href="{{ route('taxvat.export', ['type' => 'csv', request()->getQueryString()]) }}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ dynamicAsset('Modules/TaxModule/public/assets/admin/img/placeholder-csv-format.svg') }}"
                                        alt="Image Description">
                                    .{{ translate('messages.csv') }}
                                </a>
                            </div>
                        </div>
                        {{-- <button type="button"
                            class="btn btn--primary btn-outline-primary">{{ translate('messages.import') }}</button> --}}
                        <button type="button" class="btn btn--primary offcanvas-trigger"
                            data-target="#offcanvas__customBtn">{{ translate('messages.create_tax') }}</button>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table id="datatable"
                        class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table fz--14px">
                        <thead class="thead-light">
                            <tr>
                                <th class="border-0">{{ translate('sl') }}</th>
                                <th class="border-0">{{ translate('messages.tax_name') }}</th>
                                <th class="border-0">{{ translate('messages.tax_rate') }}</th>
                                <th class="border-0 text-end">{{ translate('messages.status') }}</th>
                                <th class="border-0 text-center">{{ translate('messages.action') }}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">

                            @foreach ($taxVats as $key => $taxVat)
                                <tr>
                                    <td>{{ $key + $taxVats->firstItem() }}</td>
                                    <td>
                                        {{ $taxVat->name }}
                                    </td>
                                    <td>
                                        {{ $taxVat->tax_rate }}%
                                    </td>
                                    <td>
                                        <label
                                            class="toggle-switch ml-auto confirmStatus justify-content-end toggle-switch-sm"
                                            data-url="{{ route('taxvat.status', $taxVat->id) }}"
                                            data-id="{{ $taxVat->id }}" data-is_active="{{ $taxVat->is_active }}"
                                            data-on_title="{{ translate('messages.Turn On The Status?') }}"
                                            data-off_title="{{ translate('messages.Turn Off The Status?') }}"
                                            data-on_message= "{{ translate('do you want to turn on the VAT status from your system. It will  effect on tax calculation & report') }}"
                                            data-off_message= "{{ translate('do you want to turn off the VAT status from your system. It will  effect on tax calculation & report') }}"
                                            for="status_{{ $taxVat->id }}">
                                            <input type="checkbox" {{ $taxVat->is_active == 1 ? 'checked' : '' }}
                                                class="toggle-switch-input" id="status_{{ $taxVat->id }}">
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td class="">
                                        <a class="btn btn-sm mx-auto text-end action-btn info--outline text--info info-hover offcanvas-trigger get_data"
                                            data-target="#editTaxData" data-id="{{ $taxVat->id }}"
                                            data-name="{{ $taxVat->name }}" data-tax_rate="{{ $taxVat->tax_rate }}"
                                            data-is_active="{{ $taxVat->is_active }}"
                                            data-action="{{ route('taxvat.update', $taxVat->id) }}" href="#0">
                                            <i class="tio-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                        @if(count($taxVats) === 0)
                        <div class="empty--data">
                            <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                            <h5>
                                {{translate('no_data_found')}}
                            </h5>
                        </div>
                        @endif
                </div>
                <!-- End Table -->
            @else
                <div class="bg--F6F6F6 tax-error__table w-100 h225-vh py-5">
                    <div class="max-349 text-center mx-auto my-5">
                        <img src="{{ dynamicAsset('Modules/TaxModule/public/assets/admin/img/tax-error.png') }}" alt="img"
                            class="mb-20">
                        <h4 class="mb-2">{{ translate('Currently you don’t have any Tax') }}</h4>
                        <p class="mb-20">
                            {{ translate('In this page you see all the Tax you added. Please create new tax to collect tax') }}
                        </p>
                        <div class="d-flex align-items-center justify-content-center gap-md-3 gap-2">

                            {{-- <button type="button"
                                class="btn btn--primary btn-outline-primary">{{ translate('messages.import') }}</button> --}}
                            <button type="button" class="btn btn--primary offcanvas-trigger"
                                data-target="#offcanvas__customBtn">{{ translate('messages.create_tax') }}</button>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <div id="offcanvas__customBtn" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div>
            <form action="{{ route('taxvat.store') }}" method="post">
                @method('POST')
                @csrf
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <h3 class="mb-0">{{ translate('messages.create_tax') }}</h2>
                        <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;</button>
                </div>
                <div class="custom-offcanvas-body p-20">

                    <div class="bg--secondary rounded p-20 mb-20">
                        <div class="mb-15">
                            <h4 class="mb-0">{{ translate('Availability') }}</h4>
                            <p class="fz-12px">
                                {{ translate('If you turn off this status your tax calculation will effect.') }}</p>
                        </div>
                        <label
                            class="border d-flex align-items-center bg-white-n justify-content-between rounded p-10px px-3">
                            {{ translate('Status') }}
                            <div class="toggle-switch ml-auto justify-content-end toggle-switch-sm" for="status">
                                <input type="checkbox" name="status" value="1"
                                    {{ old('status') == 1 ? 'checked' : '' }} class="toggle-switch-input" id="status">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </div>
                        </label>
                    </div>
                    <div class="bg--secondary rounded p-20 mb-20">
                        <div class="form-group">
                            <label class="mb-2 fz--14px d-block">{{ translate('messages.tax_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control h--45px border-0 pl-unset" required
                                placeholder="Type tax name" value="{{ old('name') }}" maxlength="50">
                        </div>
                        <div class="form-group mb-0">
                            <label class="mb-2 fz--14px d-block">{{ translate('messages.tax_rate') }} <span class="text-danger">*</span></label>
                            <div class="custom-group-btn border">
                                <div class="flex-sm-grow-1">
                                    <input type="number" value="{{ old('tax_rate') }}" required name="tax_rate"
                                        min="0.001" step="0.001" max="100"
                                        class="form-control h--45px border-0 pl-unset" placeholder="Ex: 5">
                                </div>
                                <!-- <select name="" class="custom-select h--45px w-auto">
                                    <option value="1">%</option>
                                    <option value="2">/</option>
                                </select> -->
                                <div class="flex-shrink-0">
                                    <span class="input-group-text ltr border-0"> % </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
            <button type="reset" class="btn w-100 btn--secondary h--40px">{{ translate('messages.reset') }}</button>
            <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('messages.Submit') }}</button>
        </div>
        </form>
    </div>


    <div id="editTaxData" class="custom-offcanvas d-flex flex-column justify-content-between">
        <div>
            <form action="" method="post">
                @method('PUT')
                @csrf
                <div
                    class="custom-offcanvas-header bg--secondary d-flex justify-content-between align-items-center px-3 py-3">
                    <h3 class="mb-0">{{ translate('messages.edit_tax') }}</h2>
                        <button type="button"
                            class="btn-close w-25px h-25px border rounded-circle d-center bg--secondary text-dark offcanvas-close fz-15px p-0"
                            aria-label="Close">&times;</button>
                </div>
                <div class="custom-offcanvas-body p-20">
                    <div class="bg--secondary rounded p-20 mb-20">
                        <div class="mb-15">
                            <h4 class="mb-0">{{ translate('Availability') }}</h4>
                            <p class="fz-12px">
                                {{ translate('If you turn off this status your tax calculation will effect.') }}</p>
                        </div>
                        <label
                            class="border d-flex align-items-center bg-white-n justify-content-between rounded p-10px px-3">
                            {{ translate('Status') }}
                            <div class="toggle-switch ml-auto justify-content-end toggle-switch-sm" for="status">
                                <input type="checkbox" class="toggle-switch-input" name="status" value="1"
                                    id="tax_status">
                                <span class="toggle-switch-label">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </div>
                        </label>
                    </div>
                    <div class="bg--secondary rounded p-20 mb-20">
                        <div class="form-group">
                            <label class="mb-2 fz--14px d-block">{{ translate('messages.tax_name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" readonly class="form-control h--45px border-0 pl-unset"
                                placeholder="VAT" id="tax_name">
                        </div>
                        <div class="form-group mb-0">
                            <label class="mb-2 fz--14px d-block">{{ translate('messages.tax_rate') }} <span class="text-danger">*</span></label>
                            <div class="custom-group-btn border">
                                <div class="flex-sm-grow-1">
                                    <input type="number" name="tax_rate" id="tax_rate" required name="tax_rate"
                                        min="0.001" step="0.001" max="100"
                                        class="form-control h--45px border-0 pl-unset" placeholder="10">
                                </div>
                                 <div class="flex-shrink-0">
                                    <span class="input-group-text ltr border-0"> % </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex p-15 rounded gap-2 bg-opacity-warning-10">
                        <svg width="25" height="25" viewBox="0 0 15 15" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_14019_1949)">
                                <path
                                    d="M7.6001 14.8162C8.98457 14.8162 10.3379 14.4056 11.4891 13.6365C12.6402 12.8673 13.5374 11.774 14.0673 10.495C14.5971 9.21587 14.7357 7.8084 14.4656 6.45053C14.1955 5.09267 13.5288 3.84539 12.5498 2.86642C11.5709 1.88745 10.3236 1.22076 8.96573 0.950668C7.60786 0.680572 6.2004 0.819195 4.92131 1.34901C3.64223 1.87882 2.54898 2.77603 1.77981 3.92717C1.01064 5.07832 0.600098 6.4317 0.600098 7.81617C0.602105 9.67207 1.34025 11.4514 2.65257 12.7637C3.96489 14.076 5.7442 14.8142 7.6001 14.8162ZM7.6001 3.73283C7.77316 3.73283 7.94233 3.78415 8.08622 3.8803C8.23011 3.97644 8.34227 4.1131 8.40849 4.27298C8.47472 4.43287 8.49205 4.6088 8.45829 4.77854C8.42452 4.94827 8.34119 5.10418 8.21882 5.22655C8.09645 5.34892 7.94054 5.43226 7.7708 5.46602C7.60107 5.49978 7.42514 5.48245 7.26525 5.41623C7.10536 5.35 6.96871 5.23785 6.87256 5.09396C6.77642 4.95006 6.7251 4.78089 6.7251 4.60783C6.7251 4.37577 6.81729 4.15321 6.98138 3.98911C7.14547 3.82502 7.36803 3.73283 7.6001 3.73283ZM7.01676 6.6495H7.6001C7.90952 6.6495 8.20626 6.77242 8.42506 6.99121C8.64385 7.21 8.76676 7.50675 8.76676 7.81617V11.3162C8.76676 11.4709 8.70531 11.6192 8.59591 11.7286C8.48651 11.838 8.33814 11.8995 8.18343 11.8995C8.02872 11.8995 7.88035 11.838 7.77095 11.7286C7.66156 11.6192 7.6001 11.4709 7.6001 11.3162V7.81617H7.01676C6.86206 7.81617 6.71368 7.75471 6.60429 7.64531C6.49489 7.53592 6.43343 7.38754 6.43343 7.23283C6.43343 7.07812 6.49489 6.92975 6.60429 6.82035C6.71368 6.71096 6.86206 6.6495 7.01676 6.6495Z"
                                    fill="#FFBB38" />
                            </g>
                            <defs>
                                <clipPath id="clip0_14019_1949">
                                    <rect width="14" height="14" fill="white"
                                        transform="translate(0.600098 0.816162)" />
                                </clipPath>
                            </defs>
                        </svg>
                        <p class="fz-12px mb-0">
                            {{ translate('Recheck your changes & make sure before update. When you change it will effect on all related') }}
                            <span class="fz-12px font-semibold title-clr">{{ translate('Tax Calculation.') }}</span>
                        </p>
                    </div>
                </div>
        </div>
        <div class="offcanvas-footer p-3 d-flex align-items-center justify-content-center gap-3">
            <button type="button" class="btn w-100 btn--secondary h--40px reset">{{ translate('Reset') }}</button>
            <button type="submit" class="btn w-100 btn--primary h--40px">{{ translate('Update') }}</button>
        </div>

        </form>
    </div>


    <div id="offcanvasOverlay" class="offcanvas-overlay"></div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ dynamicAsset('Modules/TaxModule/public/assets/admin/img/status-ons.png') }}" class="mb-20"
                        alt="">
                    <h3 class="title-clr mb-2" id="confirmationTitle"></h3>
                    <p class="fz--14px" id="confirmationMessage"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0 gap-2">
                    <button type="button" class="btn min-w-120px btn--secondary"
                        data-dismiss="modal">{{ translate('No') }}</button>
                    <button type="button" id="seturl" data-url=""
                        class="btn min-w-120px btn--primary">{{ translate('Yes') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ dynamicAsset('Modules/TaxModule/public/assets/js/admin/toastr_notification.js') }}"></script>
    <script src="{{ dynamicAsset('Modules/TaxModule/public/assets/js/admin/taxvat.js') }}"></script>
    <script src="{{ dynamicAsset('public/assets/admin/js/offcanvas.js') }}"></script>
@endpush
