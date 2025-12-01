<style>
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 18px;
        color: #6c757d;
    }

    .toggle-password:hover {
        color: #000;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<x-admin-guest-layout>
    <div class="login_page mt-5">
        <div class="text-center">
            <img src="{{ loadAssets('images/bt_logo.png') }}" height="50" class="logo-icon" alt="logo icon">
            {{-- <h2 >Crazii Admin Log In</h2> --}}
        </div>
        @include('admin.components.FlashMessage')
        <form method="POST" action="{{ route('login') }}">
            <input type="hidden" id="firebase_token_generate" name="firebase_token" value="">
            @csrf
            <div class="form-floating text-secondary mb-5 mt-5">
                <input type="text" class="form-control @error('email') is-invalid @enderror" name="email"
                    value=" {{ old('email') }}" id="email" placeholder="name@example.com">
                <label for="email"><span>Email address</span></label>
                @error('email')
                    <span class="badge badge-danger">{{ $message }}</span>
                @enderror

            </div>
            {{-- <div class="form-floating text-secondary password_input">
                <input type="password" id="password_hide" class="form-control @error('password') is-invalid @enderror"
                    name="password" id="floatingPassword" placeholder="Password" required>
                <label for="floatingPassword" class="password"><span>Password</span></label>
                @error('password')
                    <span class="badge badge-danger">{{ $message }}</span>
                @enderror
            </div> --}}


            <div class="form-floating text-secondary password_input position-relative">

                <input type="password" id="password_hide" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder="Password" required>

                <label for="password_hide"><span>Password</span></label>

                {{-- Show / Hide Icon --}}
                <span class="toggle-password" id="showHidepas">
                    <i class="fa fa-eye-slash" id="toggleIcon"></i>
                </span>

                @error('password')
                    <span class="badge badge-danger">{{ $message }}</span>
                @enderror
            </div>


            <div class="checkbox d-flex justify-content-between mt-4 remember-me">
                <div class="form-check text-secondary ms-2">
                    <input class="form-check-input" type="checkbox" name="remember" id="autoSizingCheck">
                    <label class="form-check-label" for="autoSizingCheck">
                        Remember me
                    </label>
                </div>
                <a href="{{ route('password.request') }}">Forgot Password</a>
            </div>
            <button type="submit" class="btn btn-primary py-2 mt-4 w-100">Log In</button>
        </form>
    </div>

    @push('scripts')
        <script>
            // $("#showHidepas").on("click", function() {
            //     let input = $('#password_hide').prop('type');
            //     if (input == 'password') $('#password_hide').prop('type', 'text');
            //     else $('#password_hide').prop('type', 'password');
            // });
            $("#showHidepas").on("click", function() {
                let passwordField = $('#password_hide');
                let icon = $('#toggleIcon');

                if (passwordField.attr("type") === "password") {
                    passwordField.attr("type", "text");
                    icon.removeClass("fa-eye-slash").addClass("fa-eye");
                } else {
                    passwordField.attr("type", "password");
                    icon.removeClass("fa-eye").addClass("fa-eye-slash");
                }
            });
        </script>
    @endpush
</x-admin-guest-layout>
