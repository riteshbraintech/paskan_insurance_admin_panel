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
                            href="{{ route('admin.claiminsurance.index') }}">Claim Insurance</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Claim Insurances Faq</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.claiminsurance.create') }}">
                    <a href="{{ route('admin.claiminsurance.create') }}" class="btn btn-primary">+ Add Claim insurance</a>
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
                        <select id="insuranceFilter" name="insurance_id" class="form-select">
                            <option value="">-- Filter by Insurance --</option>
                            @foreach ($insurances as $insurance)
                                <option value="{{ $insurance->id }}" 
                                    {{ ($request->insurance_id ?? $insuranceID) == $insurance->id ? 'selected' : '' }}>
                                    {{ $insurance->translation->title ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.claiminsurance.table')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('public/admin/facebox/facebox.css') }}" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="{{ asset('public/admin/js/common.js') }}"></script>
    <script>
        function changeStatus(event, id) {
            let url = "{{ route('admin.claiminsurance.change.status') }}" + "/" + id;
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
    </script>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
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
            $('#insuranceFilter, #search-input').on('change keyup', function() {
                filterTable();
            });

            if ($('#insuranceFilter').val() !== "") {
                filterTable();
            }

            function filterTable(page = 1) {
                let insurance_id = $('#insuranceFilter').val();
                let search = $('#search-input').val();

                $.ajax({
                    url: "{{ route('admin.claiminsurance.filter') }}",
                    type: "GET",
                    data: {
                        insurance_id,
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
