<x-admin-guest-layout>
    <div class="reset_page mt-5">
        <h2>Reset Password</h2>
        <form>
            <p class="mt-5">A link to reset your password has been sent to <strong>{{ $email }}</strong></p>
            <p class="mb-4">Please check your email and spam folder for our email.</p>
            <a href="{{ url('admin/login') }}" class="btn btn-primary py-2 mt-4 w-100">Back to log In page</a>
        </form>
    </div>
</x-admin-guest-layout>
