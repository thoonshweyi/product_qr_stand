@include('layouts.adminheader')

<div id="app">
    @include('layouts.adminnavbar')

    <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">
        @include('layouts.adminleftsidebar')

        <div id="main-content" class="relative w-full min-h-screen overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
            <main>
                @yield('content')
            </main>
        </div>
    </div>
</div>

@include('layouts.adminfooter')
