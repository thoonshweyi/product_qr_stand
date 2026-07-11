<!doctype html>
<html lang="en" class="dark">
    <head>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Start Header -->
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <meta name="description" content="">
            <meta name="author" content="">
            <meta name="generator" content="Hugo">

            <title>Product QR Stand</title>

            <!-- Start Stylesheet -->
                <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
                <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"> -->
                <link rel="stylesheet" href="{{ asset('/assets/libs/flowbite-admin/app.css') }}">
            <!-- End Stylesheet -->
            <!-- Start Favicons -->
                <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
                <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
                <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
                <link rel="icon" type="image/png" href="/favicon.ico">
                <link rel="manifest" href="/site.webmanifest">
                <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
                <meta name="msapplication-TileColor" content="#ffffff">
                <meta name="theme-color" content="#ffffff">
            <!-- End Favicons -->
            <!-- {{-- partial "social" . --}} -->
            <!-- {{-- partial "analytics" . --}} -->

            <script>
                // On page load or when changing themes, best to add inline in `head` to avoid FOUC
                if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark')
                }
            </script>
        <!-- End Header -->




        <!-- Standard Practice: Import Library Files -->
        <!-- fontawesome css1 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- select2 css1 js1 -->
        <link href="{{ asset('assets/libs/select2/select2.min.css') }}" rel="stylesheet" />

        <!-- custom css css1 -->
        <link rel="stylesheet" href="{{ asset('/assets/dist/css/style.css') }}">
        @yield("css")

    </head>
    <body class="bg-gray-50 dark:bg-gray-800">


        <!-- Start Main -->
            <!-- navbar -->
            @include("layouts.partials.navbar-dashboard")

            <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">

                <!-- sidebar -->
                @include("layouts.partials.sidebar")
                
                <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
                    <main>

                    <!-- content -->
                    @yield("content")

                    </main>

                    @include("layouts.partials.footer-dashboard")
                </div>

            </div>

        <!-- End Main -->

        <!-- Start Scripts -->
            <script async defer src="https://buttons.github.io/buttons.js"></script>
            <!-- <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script> -->
            <script src="{{ asset('/assets/libs/flowbite-admin/app.bundle.js') }}"></script>
        <!-- End Script -->



        
        <!-- Standard Practice: Import Library Files -->
        <!-- jquery js1 -->
        <script src="{{asset('./assets/libs/jquery-3.6.0/jquery-3.6.0.min.js')}}" type="text/javascript"></script>
        
        <!-- select2 css1 js1 -->
        <script src="{{ asset('assets/libs/select2/select2.min.js') }}"></script>

        <!-- sweetalert2 js1 -->
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2@11.js') }}" type="text/javascript"></script>

        <!-- custom js js1 -->
        <script src="{{ asset('assets/dist/js/app.js') }}" type="text/javascript"></script>
        @yield("scripts")

    </body>
</html>