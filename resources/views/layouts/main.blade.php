<!doctype html>
<html lang="en" class="">
    <head>
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
                if (localStorage.getItem('color-theme') === 'dark') {
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
        <!-- <link rel="stylesheet" href="{{ asset('/assets/dist/css/style.css') }}"> -->
        @yield("css")

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 dark:bg-gray-800">

        @hasSection('hide-navbar')
        @else
            @include("layouts.navbar")
        @endif
      


        <!-- Start Main -->
        <main class="bg-gray-50 dark:bg-gray-900">
            <!-- content -->
            @yield("content")
        </main>
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



























{{-- 
how flowbite layout work?
explain how wthe full dashboard page become by combining partials.


This project is a Hugo static site, not a React/Vue app. The full dashboard page is assembled by Hugo from:
a base HTML wrapper
a dashboard layout
shared partials
the actual page content
The main chain is:
content/_index.html
   says: layout: dashboard
        |
        v
layouts/_default/dashboard.html
   fills the "main" block
        |
        v
layouts/_default/baseof.html
   provides full <!doctype html>, <head>, <body>, scripts
In [content/_index.html (line 1)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/content/_index.html:1), the front matter says:
layout: dashboard
footer: true
That tells Hugo: “render this page using layouts/_default/dashboard.html.”
Then [layouts/_default/dashboard.html (line 1)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/layouts/_default/dashboard.html:1) defines the dashboard body:
{{ partial "navbar-dashboard" . }}
<div class="flex pt-16 ...">
  {{ partial "sidebar" . }}

  <div id="main-content" class="... lg:ml-64 ...">
    <main>
      {{ .Content }}
    </main>
    {{ if .Params.footer }} {{ partial "footer-dashboard" . }} {{ end }}
  </div>
</div>
So visually:
navbar-dashboard partial
------------------------------------------------
sidebar partial | content/_index.html content
                | footer-dashboard partial
The key line is:
{{ .Content }}
That is where Hugo injects the HTML from [content/_index.html (line 7)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/content/_index.html:7), meaning all the dashboard widgets, cards, charts, tables, etc.
The outer page shell comes from [layouts/_default/baseof.html (line 1)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/layouts/_default/baseof.html:1):
<html>
  <head>
    {{ partial "header" . }}
  </head>
  <body>
    {{ partial "skippy" . }}

    {{ block "main" . }}
    {{ end }}

    {{ partial "scripts" . }}
  </body>
</html>
dashboard.html provides the main block. baseof.html provides the document around it.
So the final homepage is basically:
<!doctype html>
<html>
  <head>
    header partial
  </head>
  <body>
    skippy partial

    navbar-dashboard partial
    dashboard flex wrapper
      sidebar partial
      main content from content/_index.html
      footer-dashboard partial

    scripts partial
  </body>
</html>
The CSS/JS are also partial-driven. [layouts/partials/header.html (line 15)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/layouts/partials/header.html:15) includes stylesheet/favicons/social/analytics. [layouts/partials/scripts.html (line 3)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/layouts/partials/scripts.html:3) loads app.bundle.js.
That bundle starts at [src/index.js (line 1)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/src/index.js:1), which imports:
import './style.css';
import 'flowbite/dist/flowbite.js';
import './sidebar';
import './charts';
import './dark-mode';
So Flowbite behavior like dropdowns, collapses, sidebar toggles, dark mode, and charts becomes active after the HTML is assembled. The partials create the structure; Tailwind classes style it; Flowbite JS makes the interactive attributes like data-dropdown-toggle and data-collapse-toggle work.



=> baseof come from which page?



baseof.html does not come from a content page directly. It is Hugo’s base template.
For this dashboard page, the flow is:
content/_index.html
  layout: dashboard
        ↓
layouts/_default/dashboard.html
  defines {{ define "main" }}
        ↓
layouts/_default/baseof.html
  wraps everything with <html>, <head>, <body>
So baseof.html is automatically used by Hugo as the outer shell when a layout like dashboard.html defines a block such as:
{{ define "main" }}
...
{{ end }}
In your repo, the file is:
[layouts/_default/baseof.html (line 1)](/home/pro1/Documents/presentation/portals/productqrstand/flowbite-admin-dashboard/layouts/_default/baseof.html:1)
Think of it like:
baseof.html = master page / outer frame
dashboard.html = dashboard layout inside that frame
content/_index.html = actual dashboard widgets/content 

--}}
