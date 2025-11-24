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
                                @php
                                    $lang = app()->getLocale() ?? 'en';
                                    $translation = $insurance->translations->where('lang_code', $lang)->first();
                                    if (!$translation) {
                                        $translation = $insurance->translations->first();
                                    }
                                    $insuranceName = $translation->title ?? ($insurance->title ?? 'Unnamed');
                                @endphp
                                <option value="{{ $insurance->id }}">{{ $insuranceName }}</option>
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
                        // initializeFacebox();
                    },
                    error: function() {
                        alert('Something went wrong. Please try again.');
                    }
                });
            }
        });
    </script>
@endpush
