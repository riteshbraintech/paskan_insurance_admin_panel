@extends('admin.layouts.app')
@section('content')

    @include('admin.components.FlashMessage')

    @php
        $role_id = admin()->user()->role_id;
    @endphp

    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Edit Staff</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.staff.list') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.staff.list')}}">Staff Account</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Staff</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form action="{{ route('admin.staff.update', ['id' => $staffInfo->id]) }}" method="post" enctype="multipart/form-data" class="row g-3 needs-validation">
                            @csrf
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full name<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ $staffInfo->name }}" required placeholder="Enter Full Name">
                                
                                @if ($errors->has('name'))
                                    <div class="text-danger">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                <input type="email" name="email" value="{{ $staffInfo->email }}" class="form-control" id="email" required>
                               
                                @if ($errors->has('email'))
                                    <div class="text-danger">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="user_name" class="form-label">Username<span class="text-danger">*</span></label>
                                <input type="text" name="user_name" value="{{ $staffInfo->user_name }}" class="form-control" id="user_name" >
                                
                                @if ($errors->has('user_name'))
                                    <div class="text-danger">{{ $errors->first('user_name') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">Date Of Birth</label>
                                <input type="date" name="date_of_birth" value="{{ $staffInfo->date_of_birth }}" class="form-control" id="date_of_birth">
                                @if ($errors->has('date_of_birth'))
                                    <div class="text-danger">{{ $errors->first('date_of_birth') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                                <select class="form-select" name="gender" id="gender" required>
                                    <option disabled value="">Choose...</option>
                                    <option value="male" {{ $staffInfo->gender == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ $staffInfo->gender == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="transgender" {{ $staffInfo->gender == 'transgender' ? 'selected' : '' }}>Transgender</option>
                                </select>
                                @if ($errors->has('gender'))
                                    <div class="text-danger">{{ $errors->first('gender') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Contact Number</label>
                                <div class="d-flex align-items-center contact_number">
                                    <select class="form-control" name="dial_code">
                                        <option value="91" {{ $staffInfo->dial_code == 91 ? 'selected' : '' }}>+91</option>
                                        <option value="1" {{ $staffInfo->dial_code == 1 ? 'selected' : '' }}>+1</option>
                                        <option value="86" {{ $staffInfo->dial_code == 86 ? 'selected' : '' }}>+86</option>
                                    </select>
                                    <input type="number" class="form-control" name="phone" id="phone" value="{{ $staffInfo->phone }}">
                                </div>
                                
                                @if ($errors->has('phone'))
                                    <div class="text-danger">{{ $errors->first('phone') }}</div>
                                @endif
                            </div>

                           @if(in_array($role_id,[\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                                <div class="col-md-6">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" name="password" value="" class="form-control" id="password" >
                                    
                                    @if ($errors->has('password'))
                                        <div class="text-danger">{{ $errors->first('password') }}</div>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation" value="" class="form-control" id="password_confirmation" >
                                    
                                    @if ($errors->has('password_confirmation'))
                                        <div class="text-danger">{{ $errors->first('password_confirmation') }}</div>
                                    @endif
                                </div>
                           @endif

                            <div class="col-md-6">
                                <label for="image" class="form-label">Profile Image</label>
                                <input type="file" name="image" class="form-control" id="image">
                                @if ($staffInfo->image)
                                    <img src="{{ getImagePath($staffInfo->image, 'staff') }}" height="50" width="100">
                                @endif
                                @if ($errors->has('image'))
                                    <div class="text-danger">{{ $errors->first('image') }}</div>
                                @endif

                            </div>

                            @if (in_array($role_id, [\App\Models\Role::ADMIN, \App\Models\Role::SUPERADMIN]))
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label" disabled="disabled">Role<span class="text-danger">*</span></label>
                                    <select class="form-select role_id_select" name="role_id" id="role_id">
                                        <option selected disabled value="">Select a Role</option>
                                        @if (!empty($role))
                                            @foreach ($role as $rol)
                                                <option value="{{ $rol->role_slug }}" {{ old('role_id', $staffInfo->role_id) == $rol->role_slug ? 'selected' : 'Subadmin' }}>{{ $rol->role_name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('role_id'))
                                        <div class="text-danger">{{ $errors->first('role_id') }}</div>
                                    @endif
                                </div>


                                <div class="col-md-6 manager-div" style="display: {{ $staffInfo->role_id == \App\Models\Role::STAFF ? 'block':'none'}};">
                                    <label for="created_by" class="form-label" disabled="disabled">Manager</label>
                                    <select class="form-select created_by" name="created_by" id="created_by">
                                        <option selected value="">Select a Manager</option>
                                        @if (!blank($manager))
                                            @foreach ($manager as $rol)
                                                <option value="{{ $rol->id }}" {{ old('created_by', $staffInfo->created_by) == $rol->id ? 'selected' : '' }}>{{ $rol->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if ($errors->has('created_by'))
                                        <div class="text-danger">{{ $errors->first('created_by') }}</div>
                                    @endif
                                </div>
                            @endif

                            <div class="row qtrly_target_container" style="display:none">
                                <div class="col-md-6">
                                    <label for="qtrly_target" class="form-label">Qtrly Target($)<span class="text-danger">*</span></label>
                                    <input type="text" name="qtrly_target" class="form-control qtrly_target" id="qtrly_target" value="{{ old('qtrly_target',$staffInfo->qtrly_target) }}" placeholder="Enter your target">
                                    @if ($errors->has('qtrly_target'))
                                        <div class="text-danger">{{ $errors->first('qtrly_target') }}</div>
                                    @endif
    
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="minimum_accepted_qtrly_target" class="form-label">Minimum Accepted Qtrly Target($)<span class="text-danger">*</span></label>
                                    <input type="text" name="minimum_accepted_qtrly_target" class="form-control minimum_accepted_qtrly_target" id="minimum_accepted_qtrly_target" value="{{ old('minimum_accepted_qtrly_target',$staffInfo->minimum_accepted_qtrly_target) }}" placeholder="Enter your target">
                                    @if ($errors->has('minimum_accepted_qtrly_target'))
                                        <div class="text-danger">{{ $errors->first('minimum_accepted_qtrly_target') }}</div>
                                    @endif
                                </div>
                            </div>

                            @if ($role_id == \App\Models\Role::SUPERADMIN)
                                <div class="col-md-6">
                                    <label for="is_test" class="form-label">Is Test</label>
                                    <select class="form-select" name="is_test" id="is_test">
                                        <option value="0" {{$staffInfo->is_test == '0' ? 'selected' : ''}} >No </option>
                                        <option value="1" {{$staffInfo->is_test == '1' ? 'selected' : ''}}>Yes </option>   
                                    </select>
                                    @if ($errors->has('is_test'))
                                        <div class="text-danger">{{ $errors->first('is_test') }}</div>
                                    @endif
                                </div>
                            @endif

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" name="description" id="description" placeholder="">{{ $staffInfo->description }}</textarea>
                                    <div class="invalid-feedback">Please enter a message in the textarea.</div>
                                </div>
                            </div>

                            @if ($staffInfo->status == 'inactive')
                                <div>
                                    <input type="checkbox" id="check" name="check" value="{{ old('check', $staffInfo->check) == 1 ? 'checked' : '' }}"  {{ $staffInfo->check == 1 ? 'checked' : ''}}>
                                    <label for="check">For DropDown Show</label>
                                </div>
                            @endif
                            
                            <div class="col-12">
                                <div class="row row-cols-auto g-3">
                                    <div class="col">
                                        <button type="submit" class="btn btn-success px-5 radius-30">Update</button>
                                    </div>

                                    <div class="col">
                                        <a href="{{route('admin.staff.list')}}" class="btn btn-outline-success px-5 radius-30">Back</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @include('admin.staff.js.staff-js')
@endpush