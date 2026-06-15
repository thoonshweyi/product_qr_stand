


        {{-- 
        <!-- Start Right Navbar -->
        <div class="right-panels">
            <form action="" method="">
                <input type="text" name="usersearch" id="usersearch" class="form-control form-control-sm rounded-0 mb-2" placeholder="Search...."/>
            </form>
            <ul id="onoffusers" class="list-group list-group-flush">
                @foreach($onlineusers as $onlineuser)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">{{ $onlineuser->name }}</div>
                            <div class="small">{{  \Carbon\Carbon::parse($onlineuser->last_active)->format("m-d-Y h:m:s a") }}</div>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-circle fa-xs"></i>
                        </div>
                    </li>
                @endforeach
            </ul>


        </div>
        <!-- End Right Navbar -->

        <!-- START MODAL AREA -->
            <!-- Start Quicksearch Modal -->
            <div id="quicksearchmodal" class="modal fade">
                <div class="modal-dialog modal-dialog-center">
                    <div class="modal-content rounded-0">
                        <div class="modal-header">
                            <h6 class="modal-title">Result</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <!-- <li><a href=""></a></li> -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Quicksearch Modal -->
        <!-- END MODAL AREA -->
        --}}

        <!-- bootstrap css1 js1 -->
        <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> -->
        <!-- jquery js1 -->
        <!-- <script src="https://code.jquery.com/jquery-3.6.3.min.js" type="text/javascript"></script> -->
         <script src="{{asset('./assets/libs/jquery-3.6.0/jquery-3.6.0.min.js')}}" type="text/javascript"></script>
        <!-- Google Chart -->
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <!-- Chartjs js1 -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- toastr css1 js1 -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" type="text/javascript"></script>

            @if(Session::has("success"))
                <script>toastr.success('{{ session()->get("success") }}', 'Successful')</script>
            @endif

            @if(session()->has("info"))
                <script>toastr.info('{{ session()->get("info") }}', 'Information')</script>
            @endif

            @if(session()->has("error"))
                <script>toastr.error('{{ session()->get("error") }}', 'Inconceivable')</script>
            @endif

            @if($errors)
                @foreach($errors->all() as $error)
                    <script>toastr.error('{{$error}}', 'Warning!',{timeOut:3000})</script>
                @endforeach
            @endif
        

        
        <!-- custom js js1 -->
        <script src="{{ asset('assets/dist/js/app.js') }}" type="text/javascript"></script>
        <!-- Extra js -->
        @yield('scripts')

        <script type="text/javascript">

        </script>
    </body>
</html>
