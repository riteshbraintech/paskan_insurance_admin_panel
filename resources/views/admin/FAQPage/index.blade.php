@extends('admin.layouts.app')
@section('content')
    <style>
        .rolesList>button {
            margin-left: 10px;
        }
    </style>

    <div id="flash-message-container">
        @include('admin.components.FlashMessage')
    </div>

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All FAQ</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a
                            href="{{ route('admin.categories.index') }}">FAQ Page</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All FAQ</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.faq.create') }}">
                    <a href="{{ route('admin.faq.create') }}" class="btn btn-primary">+ Add FAQ</a>
                </a>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="card-header">
                <div class="row g-2">
                    @include('admin.elements.perPage', ['datas' => $records])
                    @include('admin.elements.search')
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.FAQPage.table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public/admin/js/common.js') }}"></script>

    <script>
        function changeStatus(event, id) {
            let url = "{{ route('admin.faq.change.status') }}" + "/" + id;
            $.ajax({
                url: url,
                type: "post",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.status == "published") {
                        $(`.status-${id}`).html("Published");
                        $(`.status-${id}`).addClass("badge-success");
                        $(`.status-${id}`).removeClass("badge-danger");
                        // show flash message dynamically
                        showFlashMessage('success', response.message || 'Status changed successfully!');
                    } else {
                        $(`.status-${id}`).html("UnPublished");
                        $(`.status-${id}`).addClass("badge-danger");
                        $(`.status-${id}`).removeClass("badge-success");
                        // show flash message dynamically
                        showFlashMessage('success', response.message || 'Status changed successfully!');
                    }
                }
            });
        }
    </script>
@endpush
