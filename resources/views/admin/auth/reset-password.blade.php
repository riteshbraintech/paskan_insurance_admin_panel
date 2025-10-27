<x-admin-guest-layout>
    <div class="reset_page mt-5">
        <h2>Reset Password</h2>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

            <div class="form-floating text-secondary mt-5 mb-5">
                <img src="{{ loadAssets('image/RemoveRedEyeFilled.png')}}" class="i-icon">
                <input type="password" class="form-control" id="floatingPassword" name="password"
                    placeholder="Enter new password">
                <label for="floatingPassword" class="password"><span>New Password</span></label>
                @error('password')
                    <span class="badge badge-danger">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-floating text-secondary mt-5 mb-5">
                <img src="{{ loadAssets('image/RemoveRedEyeFilled.png')}}" class="i-icon">
                <input type="password" class="form-control" id="floatingPassword" placeholder="Enter confirm password"
                    name="password_confirmation">
                <label for="floatingPassword" class="password"><span>Confirm Password</span></label>
                @error('password')
                    <span class="badge badge-danger">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary py-2 mt-5 w-100">Reset Password</button>
        </form>
    </div>
</x-admin-guest-layout>
