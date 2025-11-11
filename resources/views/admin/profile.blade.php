@extends('admin.layouts.app')
@section('content')

    <div class="dashboard_body">

        @include('admin.components.FlashMessage')

        <style scoped>
            .badge-danger{
                color: red;
            }
        </style>
        
        <div class="container-fluid">
            <div class="heading mt-4">
                <h5>Profile</h5>
                <p class="text-muted mt-3">
                    Dashboard / <span class="text-dark fw-semibold">Profile</span>
                </p>
            </div>
			<div class="profile_setup">
				<form action="{{ route('admin.updateProfile') }}" method="post" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="update_type" value="profile">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="name" class="form-label">Full name</label>
							<input type="text" name="name" class="form-control" id="name" value="{{ old('name', $user->name) }}" placeholder="Enter Full Name">
							
							@if ($errors->has('name'))
								<div class="text-danger">{{ $errors->first('name') }}</div>
							@endif
						</div>
						<div class="col-md-4 mb-3">
							<label for="email" class="form-label">Email</label>
							<input type="email" readonly name="email" value="{{ old('email', $user->email) }}" class="form-control" id="email">
							
							@if ($errors->has('email'))
								<div class="text-danger">{{ $errors->first('email') }}</div>
							@endif
						</div>
						<div class="col-md-4 mb-3">
							<label for="user_name" class="form-label">Username</label>
							<input type="text" name="user_name" value="{{ old('user_name', $user->user_name) }}" class="form-control" id="user_name" readonly>
							
							@if ($errors->has('user_name'))
								<div class="text-danger">{{ $errors->first('user_name') }}</div>
							@endif
						</div>
						<div class="col-md-4 mb-3">
							<label for="date_of_birth" class="form-label">Date Of Birth</label>
							<input type="date" name="date_of_birth" class="form-control" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
							@if ($errors->has('date_of_birth'))
								<div class="text-danger">{{ $errors->first('date_of_birth') }}</div>
							@endif
						</div>
						<div class="col-md-4 mb-3">
							<label for="gender" class="form-label">Gender</label>
							<select class="form-select" name="gender" id="gender">
								<option selected disabled value="">Choose...</option>
								<option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male </option>
								<option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
							</select>
							@if ($errors->has('gender'))
								<div class="text-danger">{{ $errors->first('gender') }}</div>
							@endif
						</div>
						<div class="col-md-4 mb-3">
							<label for="phone" class="form-label">Contact Number</label>
							<input type="number" name="phone" class="form-control" id="phone" value="{{ old('phone', $user->phone) }}">
							
							@if ($errors->has('phone'))
								<div class="text-danger">{{ $errors->first('phone') }}</div>
							@endif
						</div>
						{{-- <div class="col-md-4 mb-3">
							<label for="address" class="form-label">Address</label>
							<input type="text" name="address" class="form-control" id="address" value="{{ old('address', $user->address) }}">
							@if ($errors->has('address'))
								<div class="text-danger">{{ $errors->first('address') }}</div>
							@endif
						</div> --}}
						{{-- <div class="col-md-4 mb-3">
							<label for="postcode" class="form-label">Postcode</label>
							<input type="text" name="postcode" class="form-control" id="postcode" value="{{ old('postcode', $user->postcode) }}">
							@if ($errors->has('postcode'))
								<div class="text-danger">{{ $errors->first('postcode') }}</div>
							@endif
						</div> --}}
						<div class="col-md-4 mb-3">
							<label for="image" class="form-label">Profile image</label>
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

@endsection