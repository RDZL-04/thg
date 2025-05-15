<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/font/fonts.googleapis.css') }}">
        <link rel="stylesheet" href="{{ asset('css/font/fontawesome.css') }}">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-thg/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-thg/css/bootstrap-select.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/bootstrap-thg/css/bootstrap4-toggle.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-thg/css/datatables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-thg/css/jquery.dataTables.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-thg/css/toastr.min.css') }}">	
        <!-- summernote css/js -->
        <link rel="stylesheet" href="{{ asset('css/summernote-thg/summernote-bs4.min.css') }}">
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ asset('css/bootstrap-thg/js/alpine.js') }}" defer></script>
        <script src="{{ asset('css/bootstrap-thg/js/jquery-3.5.1.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/popper.min.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/bootstrap-select.min.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/bootstrap4-toggle.min.js') }}"></script>
        <script src="{{ asset('css/bootstrap-thg/js/sweetalert.min.js') }}"></script>
        <script src="{{ asset('css/summernote-thg/summernote-bs4.min.js') }}"></script>
        <style>
            .fas{
                color: black;
            }
            .fas:hover{
                color: blue;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-dropdown')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
