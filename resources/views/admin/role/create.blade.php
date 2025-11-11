@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Role</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.role.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role Access</li>
                    <li class="breadcrumb-item active" aria-current="page">Create Role</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('admin.role.store') }}" method="post">
        @csrf
        <div class="row">
            <div class="col-xl-9 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div style="padding: 0 0 10px 0">
                            <h6 class="mb-0 text-uppercase">Role Name</h6>
                        </div>
                        <input class="form-control" name="role_name" type="text" value="{{ old('role_name') }}" aria-label="default input example" />
                        @if ($errors->has('role_name'))
                            <div class="text-danger">{{ $errors->first('role_name') }}</div>
                        @endif

                        <div class="row row-cols-auto g-3 mt-2">
                            <div class="col">
                                <button type="submit" class="btn btn-success px-5 radius-30">Create</button>
                            </div>

                            <div class="col">
                                <a href="{{route('admin.role')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
    </script>
@endpush
