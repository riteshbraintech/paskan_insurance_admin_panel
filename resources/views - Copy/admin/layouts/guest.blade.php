<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Crazii') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ getImagePath('logo-icon.png', 'logo') }}">

    @include('admin.layouts.head-cdn') {{-- inlcude all head cdn and styles --}}
    {{-- // for extra styles --}}
    @stack('styles')
    <style>
        .login_page {
            background: white;
            padding: 21px;
            box-shadow: 0px 0px 12px 0px #b7b7b7;
            border-radius: 10px;
            margin-top: 50% !important;
        }
    </style>
</head>

<body>
    <div class="dashboard">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4"></div>
                <div class="col-lg-3">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    {{-- load script cdn --}}
    @include('admin.layouts.scripts-cdn')

    <script>
        // success, info, warn
        function notify(msg, type) {
            $.notify(msg, type);
        }
    </script>
    {{-- load extra scripts --}}
    @stack('scripts')
</body>

</html>
