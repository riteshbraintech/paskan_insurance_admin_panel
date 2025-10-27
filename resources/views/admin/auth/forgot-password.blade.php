<x-admin-guest-layout>
    <div class="reset_page mt-5">
        <h2>Reset Password</h2>

        @include('admin.components.FlashMessage')

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-floating text-secondary mb-5 mt-5">
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control"
                    id="floatingInput" placeholder="Enter email">
                <label for="floatingInput"><span>Email used for the account</span></label>
            </div>
            <div class="checkbox d-flex justify-content-between mt-2 mb-3 remember-me">
                <div class="form-check text-secondary ms-3">
                    <input class="form-check-input" type="checkbox" id="autoSizingCheck">
                    <label class="form-check-label" for="autoSizingCheck">
                        Reset password using phone number instead
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary py-2 mt-4 w-100">Send link to reset my password</button>
        </form>
    </div>

</x-admin-guest-layout>
