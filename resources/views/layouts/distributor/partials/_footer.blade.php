<!-- Footer -->
<div class="footer">
    <div class="row justify-content-between align-items-center">
        <div class="col">
            <p class="font-size-sm mb-0">&copy; {{ date('Y') }} <a class="font-weight-bold" href="#">{{ config('app.name') }}</a>. {{ translate('messages.all_rights_reserved') }}.</p>
        </div>
        <div class="col-auto">
            <div class="d-flex justify-content-end">
                <!-- List Dot -->
                <ul class="list-inline list-separator">
                    <li class="list-inline-item">
                        <a class="list-separator-link" href="#">{{ translate('messages.support') }}</a>
                    </li>
                    <li class="list-inline-item">
                        <a class="list-separator-link" href="#">{{ translate('messages.license') }}</a>
                    </li>
                </ul>
                <!-- End List Dot -->
            </div>
        </div>
    </div>
</div>
<!-- End Footer -->