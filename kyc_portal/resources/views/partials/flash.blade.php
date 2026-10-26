@if (session('status'))
    <div class="alert alert-success d-flex align-items-start gap-2" role="status" data-auto-dismiss>
        <x-icon name="check" :size="17" />
        <div>{{ session('status') }}</div>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
        <x-icon name="alert" :size="17" />
        <div>{{ session('warning') }}</div>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger d-flex align-items-start gap-2" role="alert">
        <x-icon name="alert" :size="17" />
        <div>{{ session('error') }}</div>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="d-flex align-items-start gap-2">
            <x-icon name="alert" :size="17" />
            <div>
                <strong class="d-block mb-1">Please check the form and try again.</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
