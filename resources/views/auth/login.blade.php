@extends("layouts.main")

@section('hide-navbar', 'true')

@section("content")
<div class="flex flex-col items-center justify-center px-6 pt-8 mx-auto md:h-screen pt:mt-0 dark:bg-gray-900">
    <a href="" class="flex items-center justify-center mb-8 text-2xl font-semibold lg:mb-10 dark:text-white">
        <img src="{{ asset('assets/img/fav/myfav.png') }}" class="mr-4 h-16" alt="Product QR Stand Logo">
        <span>Product Information</span>  
    </a>
    <!-- Card -->
    <div class="w-full max-w-xl p-6 space-y-8 sm:p-8 bg-white rounded-lg shadow dark:bg-gray-800">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            Sign in to platform 
        </h2>
        <form action="{{ route('login') }}" method="POST" class="mt-8 space-y-6">
            @csrf
            <!-- <div>
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="name@company.com" required>
            </div> -->
            <div>
                <label for="username" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your Employee Code</label>
                <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="000-000999" required>
                @error('username')
                <span class="text-red-700">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div>
                <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" required>
                 @error('password')
                <span class="text-red-700">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input id="remember" aria-describedby="remember" name="remember" type="checkbox" class="w-4 h-4 border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-primary-300 dark:focus:ring-primary-600 dark:ring-offset-gray-800 dark:bg-gray-700 dark:border-gray-600" requireds>
                </div>
                <div class="ml-3 text-sm">
                <label for="remember" class="font-medium text-gray-900 dark:text-white">Remember me</label>
                </div>
                <!-- <a href="#" class="ml-auto text-sm text-primary-700 hover:underline dark:text-primary-500">Lost Password?</a> -->
            </div>
            <button type="submit" class="w-full px-5 py-3 text-base font-medium text-center text-white bg-primary-700 rounded-lg hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 sm:w-auto dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Login to your account</button>
            <!-- <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Not registered? <a class="text-primary-700 hover:underline dark:text-primary-500">Create account</a>
            </div> -->
        </form>

        <div id="pwa-install-panel" class="hidden border-t border-gray-200 pt-6 dark:border-gray-700">
            <div class="flex flex-col gap-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300">
                        <i class="fas fa-download"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Install Information</p>
                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Open the platform quickly from your device.</p>
                    </div>
                </div>
                <button id="pwa-install-button" type="button"
                    class="inline-flex items-center justify-center rounded-lg border border-primary-700 px-4 py-2 text-sm font-semibold text-primary-700 hover:bg-primary-50 focus:outline-none focus:ring-4 focus:ring-primary-200 dark:border-primary-400 dark:text-primary-300 dark:hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i>
                    Install
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (() => {
        const panel = document.getElementById('pwa-install-panel');
        const button = document.getElementById('pwa-install-button');
        const isInstalled = window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true;

        const syncInstallButton = () => {
            panel.classList.toggle('hidden', isInstalled || !window.pwaInstallPrompt);
        };

        window.addEventListener('pwa-install-available', syncInstallButton);
        window.addEventListener('pwa-install-complete', () => panel.classList.add('hidden'));

        button.addEventListener('click', async () => {
            const prompt = window.pwaInstallPrompt;

            if (!prompt) {
                return;
            }

            prompt.prompt();
            await prompt.userChoice;
            window.pwaInstallPrompt = null;
            panel.classList.add('hidden');
        });

        syncInstallButton();
    })();
</script>
@endsection
