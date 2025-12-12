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
        {{-- <div class="breadcrumb-title pe-3">All Categories Fields</div> --}}
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><a
                            href="{{ route('admin.categoryformfield.index') }}">Category Form Field</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Category Field</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.categoryformfield.create') }}">
                    <a href="{{ route('admin.categoryformfield.create') }}" class="btn btn-primary">+ Add Category Form
                        Field</a>
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

                    <div class="col-md-3">
                        {{-- <select id="categoryFilter" name="category_id" class="form-select">
                            <option value="">-- Filter by Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryID == $category->id ? 'selected' : '' }}>
                                    {{ $category->translation->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select> --}}
                        <select id="categoryFilter" name="category_id" class="form-select">
                            <option value="">-- Filter by Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ ($request->category_id ?? $categoryID) == $category->id ? 'selected' : '' }}>
                                    {{ $category->translation->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>


                    </div>
                </div>
            </div>


            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.categoriesformfield.table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('public/admin/facebox/facebox.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="{{ asset('public/admin/js/common.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script>
        function changeStatus(event, id) {
            let url = "{{ route('admin.categoryformfield.change.status') }}" + "/" + id;
            $.ajax({
                url: url,
                type: "post",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // update badge
                        if (response.status === "Yes") {
                            $(`.status-${id}`).html("Yes")
                                .addClass("badge-success")
                                .removeClass("badge-danger");
                        } else {
                            $(`.status-${id}`).html("No")
                                .addClass("badge-danger")
                                .removeClass("badge-success");
                        }

                        // show flash message dynamically
                        showFlashMessage('success', response.message || 'Status changed successfully!');
                    } else {
                        showFlashMessage('error', response.message || 'Something went wrong.');
                    }
                },
                error: function() {
                    showFlashMessage('error', 'Server error occurred.');
                }
            });
        }

        function filterchangeStatus(event, id) {
            let url = "{{ route('admin.categoryformfield.filterchange.status') }}" + "/" + id;
            $.ajax({
                url: url,
                type: "post",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        // update badge
                        if (response.status === "Yes") {
                            $(`.filterstatus-${id}`).html("Yes")
                                .addClass("badge-success")
                                .removeClass("badge-danger");
                        } else {
                            $(`.filterstatus-${id}`).html("No")
                                .addClass("badge-danger")
                                .removeClass("badge-success");
                        }

                        // show flash message dynamically
                        showFlashMessage('success', response.message || 'Status changed successfully!');
                    } else {
                        showFlashMessage('error', response.message || 'Something went wrong.');
                    }
                },
                error: function() {
                    showFlashMessage('error', 'Server error occurred.');
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            initSortable();

            // Base URL for Facebox assets
            var baseUrl = "{{ asset('public/admin/facebox') }}";

            // Initialize Facebox
            function initializeFacebox() {
                $('a[rel*=facebox]').facebox({
                    loadingImage: baseUrl + '/loading.gif',
                    closeImage: baseUrl + '/closelabel.png'
                });
            }

            // initializeFacebox();
            $(document).on('ajaxComplete', initializeFacebox);

            // AJAX filter
            $('#categoryFilter, #search-input').on('change keyup', function() {
                filterTable();
            });

            if ($('#categoryFilter').val() !== "") {
                filterTable();
            }



            function filterTable(page = 1) {
                let category_id = $('#categoryFilter').val();
                let search = $('#search-input').val();

                $.ajax({
                    url: "{{ route('admin.categoryformfield.filter') }}",
                    type: "GET",
                    data: {
                        category_id,
                        search,
                        page
                    },
                    success: function(response) {
                        $('.load-table-data').html(response.html);
                        initSortable();
                        // initializeFacebox();
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                    }
                });
            }

            function initSortable() {
                $(".load-table-data table tbody").sortable({
                    placeholder: "ui-state-highlight",
                    cursor: "move",
                    update: function(event, ui) {
                        let order = [];

                        $(".load-table-data table tbody tr").each(function(index) {
                            order.push({
                                id: $(this).data('id'),
                                position: index + 1
                            });
                        });

                        $.ajax({
                            url: "{{ route('admin.categoryformfield.reorder') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                order: order
                            },
                            success: function(response) {
                                console.log('Order updated successfully');
                                filterTable();
                            },
                            error: function(xhr) {
                                alert('Sorting failed, please try again.');
                            }
                        });
                    }
                });
            }


        });
    </script>
@endpush
