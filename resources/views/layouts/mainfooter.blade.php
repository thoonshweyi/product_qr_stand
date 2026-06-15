        <!-- jquery js1 -->
         <script src="{{asset('./assets/libs/jquery-3.6.0/jquery-3.6.0.min.js')}}" type="text/javascript"></script>

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

            //
        </script>
    </body>
</html>
