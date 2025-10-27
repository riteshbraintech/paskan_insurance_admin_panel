@extends('admin.layouts.app')
@section('content')

    <div class="dashboard_body">

        @include('admin.components.FlashMessage')

        <style scoped>
            .badge-danger{
                color: red;
            }
        </style>
        
        <div class="container">
            <div class="heading border-bottom pb-3 mt-4">
                <h5>Profile</h5>
                <p class="text-muted mt-3">
                    Dashboard / <span class="text-dark fw-semibold">Profile</span>
                </p>
            </div>

            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Profile</button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse {{ (empty(old('update_type')) || old('update_type')=='profile') ? 'show' : '' }}" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <form action="{{ route('admin.updateProfile') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="update_type" value="profile">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label mt-4">Full name</label>
                                        <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $user->name) }}" placeholder="Enter Full Name">
                                        
                                        @if ($errors->has('name'))
                                            <div class="text-danger">{{ $errors->first('name') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label mt-4">Email</label>
                                        <input type="email" readonly name="email" value="{{ old('email', $user->email) }}" class="form-control" id="email">
                                        
                                        @if ($errors->has('email'))
                                            <div class="text-danger">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="user_name" class="form-label mt-4">Username</label>
                                        <input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}" class="form-control" id="user_name" readonly>
                                        
                                        @if ($errors->has('user_name'))
                                            <div class="text-danger">{{ $errors->first('user_name') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_of_birth" class="form-label mt-4">Date Of Birth</label>
                                        <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                        @if ($errors->has('date_of_birth'))
                                            <div class="text-danger">{{ $errors->first('date_of_birth') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label mt-4">Gender</label>
                                        <select class="form-select" name="gender" id="gender">
                                            <option selected disabled value="">Choose...</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male </option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @if ($errors->has('gender'))
                                            <div class="text-danger">{{ $errors->first('gender') }}</div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label mt-4">Contact Number</label>
                                        <input type="number" name="phone" class="form-control" id="phone" value="{{ old('phone', $user->phone) }}">
                                        
                                        @if ($errors->has('phone'))
                                            <div class="text-danger">{{ $errors->first('phone') }}</div>
                                        @endif
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <label for="address" class="form-label mt-4">Address</label>
                                        <input type="text" name="address" class="form-control" id="address" value="{{ old('address', $user->address) }}">
                                        @if ($errors->has('address'))
                                            <div class="text-danger">{{ $errors->first('address') }}</div>
                                        @endif
                                    </div> --}}
                                    {{-- <div class="col-md-6">
                                        <label for="postcode" class="form-label mt-4">Postcode</label>
                                        <input type="text" name="postcode" class="form-control" id="postcode" value="{{ old('postcode', $user->postcode) }}">
                                        @if ($errors->has('postcode'))
                                            <div class="text-danger">{{ $errors->first('postcode') }}</div>
                                        @endif
                                    </div> --}}
                                    <div class="col-md-6">
                                        <label for="image" class="form-label mt-4">Profile image</label>
                                        <input type="file" name="image" class="form-control" id="image">
                                        @if (!empty(admin()->user()->image))
                                            <img src="{{ getImagePath(admin()->user()->image, 'profile') }}" alt="" width="54" height="54">
                                        @endif
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-3">
                                        <div class="form-floating text-secondary">
                                            <button type="submit" class=" btn btn-primary">Update</button>
                                            <button type="reset" class=" btn btn-warning">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Change Password</button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse {{ old('update_type')=='password' ? 'show' : '' }}" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <form action="{{ route('admin.updatePassword') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="update_type" value="password">

                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-floating text-secondary">
                                            <input type="password" class="form-control  @error('old_password') is-invalid @enderror" id="floatingInput" placeholder="Enter password" name="old_password" value="" />
                                            <label for="floatingInput"><span>Old Password</span></label>
                                            @error('old_password')
                                                <span class="badge badge-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating text-secondary">
                                            <input type="password" class="form-control  @error('password') is-invalid @enderror" id="floatingInput" placeholder="Enter password" name="password" value="" />
                                            <label for="floatingInput"><span>New Password</span></label>
                                            @error('password')
                                                <span class="badge badge-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-floating text-secondary">
                                            <input type="password" class="form-control  @error('password_confirmation') is-invalid @enderror" id="password_confirmation" placeholder="Enter confirm password" name="password_confirmation" value="" />
                                            <label for="floatingInput"><span>Confirm Password</span></label>
                                            @error('password_confirmation')
                                                <span class="badge badge-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-lg-3">
                                        <div class="form-floating text-secondary">
                                            <button type="submit" class=" btn btn-primary">Change Password</button>
                                            <button type="reset" class=" btn btn-warning">Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection