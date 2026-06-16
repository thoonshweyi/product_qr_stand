<!DOCTYPE html>
<html lang="en" class="">
    <head>
        <!-- Application Name -->
        <title>{{ config('app.name') }}</title>

        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <!-- fav icon -->
        <link href="{{ asset('assets/img/fav/favicon.png') }}" rel="icon" type="image/png" sizes="16x16"/>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- fontawesome css1 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- toastr css1 js1 -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        
        <!-- custom css css1 -->
        <link href="{{ asset('assets/dist/css/style.css') }}" rel="stylesheet" type="text/css"/>
        
        <!-- Extra CSS -->
        @yield('css')

    </head>
    <body>
