@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Role</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role Access</li>
                    <li class="breadcrumb-item active" aria-current="page">All Role</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <div class="btn-group">
                <a href="create-role.php">
                    <a href="{{ route('admin.role.create') }}" class="btn btn-primary">+ Add Role</a>
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-header py-3">
                <div class="row g-3">
                    @include('admin.elements.search')
                </div>
            </div>
            <div class="load-table-data">
                @include('admin.role.table', ['data' => []])
            </div>
        </div>
    </div>

@endsection