@extends('admin.layouts.app')
@section('content')

    @push('styles')
    @endpush
    
    <style>
        .input-group-text {
            align-items: left;
            padding: 0px;
            font-size: 13px;
            font-weight: 100;
            text-align: left;
        }
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Project</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.project.index') }}">Project</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Project</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.project.create') }}">
                    <a href="{{ route('admin.project.create') }}" class="btn btn-primary">+ Add Project</a>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3 px-0">
                <div class="row g-2">
                    @include('admin.elements.search')
                    <div class="col-lg-2">
                        <select onchange="loadData(event);" class="form-select status-filter" name="status" id="">
                            <option selected value="">All Projects</option>
                            <option value="live">Live</option>
                            <option value="dev">Dev</option>
                        </select>
                    </div>
                    @include('admin.elements.perPage', ['datas' => $projects])
                </div>
            </div>

            <div class="card-body p-1 mt-2">           
                <div class="load-table-data">
                    @include('admin.project.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function loadData(event) {
            let statusFilter = '';
            statusFilter = $('.status-filter').val() == undefined ? '' : $('.status-filter').val();
            extraSearchItem = "statusFilter=" + statusFilter ;
            ajaxTableData();
        }
    </script>
@endpush
