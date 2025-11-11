@extends('admin.layouts.app')
@section('content')

    <style>
        .rolesList>button {
            margin-left: 10px;
        }
    </style>

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Staff</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('admin.staff.list') }}">Staff Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">All Staff</li>
                </ol>
            </nav>
        </div>

        <div class="ms-auto">
            <div class="btn-group">
                <a href="{{ route('admin.staff.create') }}">
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">+ Add Staff</a>
                </a>
            </div>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <div class="row g-2">
                    @include('admin.elements.search')
                    @if (in_array(admin()->user()->role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                        <div class="col-lg-7 d-flex justify-content-end rolesList">
                            @if (!empty($role))
                                <button type="button" class="btn btn-outline-success px-2 btn-sm radius-30 roleBTN active" id="filterbtn-show_all" onclick="filterRole(event,'','filterbtn-show_all')">Show All</button>
                               
                                <button type="button" class="btn btn-outline-success px-2 btn-sm radius-30 roleBTN" id="filterbtn-active" onclick="filterRole(event,'','filterbtn-active','active')">Active</button>

                                @foreach ($role as $rl)
                                    <button type="button" class="btn btn-outline-success px-2 btn-sm radius-30 roleBTN" id="filterbtn-{{$rl->id}}" onclick="filterRole(event, '{{ $rl->role_slug }}','filterbtn-{{$rl->id}}')">({{ $rl->total_user_count }}) {{ $rl->role_name }}</button>
                                @endforeach
                            @endif
                        </div>
                    @endif
                    
                    @include('admin.elements.perPage', ['datas' => $staffList])
                </div>
            </div>

            <div class="card-body">
                <div class="load-table-data">
                    @include('admin.staff.table')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('admin.staff.js.staff-js')
@endpush