<x-admin-guest-layout>
    <div class="reset_page mt-5">
        <h2>Successful!</h2>
        <form>
            <p class="mt-5 mb-5 content">You have successfully resetted your password. Log in using your new password to
                access our content!</p>
            <a href="{{ url('/admin/login') }}" class="btn btn-primary py-2 mt-4 w-100">Back to log In page</a>
        </form>
    </div>
</x-admin-guest-layout>
