<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Paskan Insurance') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ getImagePath('logo-icon.png', 'logo') }}">
    <script src="https://kit.fontawesome.com/21476ba9c1.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">


    {{-- <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css'> --}}
    {{-- <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'> --}}
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@fullcalendar/core@4.2.0/main.min.css'>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@4.3.0/main.min.css'>

    @include('admin.layouts.head-cdn') {{-- inlcude all head cdn and styles --}}

    <link rel="stylesheet" href="{{ loadAssets('date-range/daterangepicker.css') }}" />
    <link rel="stylesheet" href="{{ loadAssets('calender/calender.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles') {{-- // inlcude custom styles --}}

</head>

<body>

    <div class="wrapper">
        @include('admin.components.header')
        @include('admin.components.sidenav')

        <main class="page-content">
            @yield('content')
        </main>

        <div id="delete-confirm-popup">
            @include('admin.layouts.delete-confirm-popup')
        </div>

        <div class="overlay nav-toggle-icon"></div>
    </div>

    {{-- // load global scripts --}}
    @include('admin.layouts.scripts-cdn')

    <script src="{{ loadAssets('date-range/daterangepicker.js') }}"></script>

    @stack('scripts')

</body>

</html>
