@extends('admin.layouts.app')
@section('content')
    <div class="dashboard_body">

        @include('admin.components.FlashMessage')

        <style scoped>
            .badge-danger {
                color: red;
            }
        </style>

        <div class="container-fluid">
            <div class="heading mt-4">
                <h5>Password</h5>
                <p class="text-muted mt-3">
                    Dashboard / <span class="text-dark fw-semibold">Update Password</span>
                </p>
            </div>
            <div class="profile_setup">
                <form action="{{ route('admin.updatePassword') }}" method="post" enctype="multipart/form-data">
					@csrf
					<input type="hidden" name="update_type" value="password">

					<div class="row">
						<div class="col-lg-4">
							<div class="form-floating text-secondary">
								<input type="password"
									class="form-control @error('old_password') is-invalid @enderror"
									id="floatingInput" placeholder="Enter password" name="old_password" />
								<label for="floatingInput"><span>Old Password</span></label>
								@error('old_password')
									<span class="badge badge-danger">{{ $message }}</span>
								@enderror
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-floating text-secondary">
								<input type="password"
									class="form-control @error('password') is-invalid @enderror"
									id="floatingInput" placeholder="Enter password" name="password" />
								<label for="floatingInput"><span>New Password</span></label>
								@error('password')
									<span class="badge badge-danger">{{ $message }}</span>
								@enderror
							</div>
						</div>

						<div class="col-lg-4">
							<div class="form-floating text-secondary">
								<input type="password"
									class="form-control @error('password_confirmation') is-invalid @enderror"
									id="password_confirmation" placeholder="Enter confirm password"
									name="password_confirmation" />
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
								<button type="submit" class="btn btn-primary">Change Password</button>
								<button type="reset" class="btn btn-warning">Reset</button>
							</div>
						</div>
					</div>
				</form>
            </div>

        </div>
    </div>
@endsection
