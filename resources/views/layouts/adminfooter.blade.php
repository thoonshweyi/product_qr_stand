        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
        <script src="{{ asset('assets/libs/jquery-3.6.0/jquery-3.6.0.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        @if(Session::has('success'))
            <script>toastr.success(@json(session()->get('success')), 'Successful')</script>
        @endif
        @if(session()->has('info'))
            <script>toastr.info(@json(session()->get('info')), 'Information')</script>
        @endif
        @if(session()->has('error'))
            <script>toastr.error(@json(session()->get('error')), 'Warning')</script>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <script>toastr.error(@json($error), 'Warning', {timeOut: 3000})</script>
            @endforeach
        @endif

        <script src="{{ asset('assets/libs/flowbite-admin/app.bundle.js') }}"></script>
        @yield('scripts')
    </body>
</html>
