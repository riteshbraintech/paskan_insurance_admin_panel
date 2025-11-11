<x-admin-layout>
    <div class="dashboard_body">
        @include('admin.components.FlashMessage')
        <form action="{{ route('admin.updatePassword') }}" method="post">
            @csrf
            <div class="container">
                <div class="heading border-bottom pb-3 mt-4">
                    <h5>Change Password</h5>
                    <p class="text-muted mt-3">
                        Dashboard /
                        <span class="text-dark fw-semibold">Change Password</span>
                    </p>
                </div>
                <div class="row border-bottom">
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
                            <input type="password" class="form-control  @error('confirm_password') is-invalid @enderror" id="password_confirmation" placeholder="Enter confirm password" name="password_confirmation" value="" />
                            <label for="floatingInput"><span>Confirm Password</span></label>
                            @error('password_confirmation')
                                <span class="badge badge-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="form-floating text-secondary">
                            <button type="submit" class=" btn btn-primary">Update</button>
                            <button type="reset" class=" btn btn-warning">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>