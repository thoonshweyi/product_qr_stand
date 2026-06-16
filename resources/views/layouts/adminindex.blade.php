@include("layouts.adminheader")

     <div id="app">
          @include("layouts.adminnavbar")
          <div class="flex pt-16 overflow-hidden bg-gray-50 dark:bg-gray-900">

               <!-- Start Left Side Bar -->
               @include("layouts.adminleftsidebar")
               <!-- End Left Side Bar -->

               <div id="main-content" class="relative w-full h-full overflow-y-auto bg-gray-50 lg:ml-64 dark:bg-gray-900">
                    <main>
                         @yield('content')
                    </main>
                    <!-- Start Footer -->
                    <!-- End Footer -->
               </div>

          </div>
     </div>
         
@include("layouts.adminfooter")