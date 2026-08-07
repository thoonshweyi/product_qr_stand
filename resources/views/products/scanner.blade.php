@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-indigo-50 pb-16 pt-28 text-slate-900 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 dark:text-white">
    <main class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between gap-4">
            <a href="{{ route('products.catalog') }}" class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200 transition hover:text-[#073b78] dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Product Catalog
            </a>
            <span class="hidden text-sm font-medium text-slate-500 sm:block dark:text-slate-400">Secure camera scanning</span>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow-[0_24px_70px_-28px_rgba(15,56,110,0.35)] ring-1 ring-blue-100 dark:bg-slate-800 dark:shadow-black/30 dark:ring-slate-700">
            <div class="grid lg:grid-cols-[1.15fr_.85fr]">
                <section class="relative border-b border-blue-100 bg-gradient-to-br from-blue-50 via-white to-sky-50 p-4 sm:p-7 lg:border-b-0 lg:border-r dark:border-slate-700 dark:from-slate-900 dark:via-slate-900 dark:to-slate-950" aria-labelledby="scanner-title">
                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-300">Product finder</p>
                            <h1 id="scanner-title" class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl dark:text-white">Scan QR or Barcode</h1>
                        </div>
                        <div id="camera-indicator" class="flex items-center gap-2 rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200 dark:bg-white/10 dark:text-slate-300 dark:ring-white/10">
                            <span id="camera-dot" class="h-2 w-2 rounded-full bg-slate-500"></span>
                            <span id="camera-label">Camera off</span>
                        </div>
                    </div>

                    <div class="relative min-h-[320px] overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-inner ring-4 ring-white sm:min-h-[400px] dark:border-white/10 dark:bg-slate-900 dark:ring-slate-800">
                        <div id="qr-reader" class="h-full w-full"></div>
                        <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-b from-white to-blue-50 px-8 text-center dark:from-slate-900 dark:to-slate-950">
                            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-blue-100 text-blue-700 ring-1 ring-blue-200 dark:bg-blue-500/15 dark:text-blue-300 dark:ring-blue-400/30">
                                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 8V5.75a2 2 0 012-2H8m8 0h2.25a2 2 0 012 2V8m0 8v2.25a2 2 0 01-2 2H16m-8 0H5.75a2 2 0 01-2-2V16M8 8h3v3H8V8zm5 0h3v3h-3V8zm-5 5h3v3H8v-3zm5 0h1.5v1.5H16V16h-3v-3z"/></svg>
                            </div>
                            <p class="mt-5 font-semibold text-slate-900 dark:text-white">Camera is ready when you are</p>
                            <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500 dark:text-slate-400">Allow camera access, then place the product QR code or barcode inside the scanning frame.</p>
                        </div>
                    </div>

                    <div id="camera-zoom-controls" class="mt-4 hidden rounded-xl border border-blue-100 bg-white/80 px-4 py-3 dark:border-white/10 dark:bg-white/5">
                        <div id="camera-zoom-slider">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-magnifying-glass-plus text-sm text-blue-700 dark:text-blue-300"></i>
                                <label for="camera-zoom" class="text-xs font-semibold text-slate-600 dark:text-slate-300">Camera zoom</label>
                                <input id="camera-zoom" type="range" class="h-2 min-w-0 flex-1 cursor-pointer accent-blue-700">
                                <output id="camera-zoom-value" for="camera-zoom" class="w-10 text-right text-xs font-bold tabular-nums text-slate-700 dark:text-slate-200">1.0×</output>
                            </div>
                            <p class="mt-2 text-center text-xs text-slate-500 dark:text-slate-400">Increase zoom until the small barcode fills the scanning frame and looks sharp.</p>
                        </div>
                        <div id="camera-zoom-unsupported" class="hidden items-center justify-center gap-2 text-center text-xs font-medium text-slate-500 dark:text-slate-400">
                            <i class="fas fa-circle-info"></i>
                            This camera does not support zoom.
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap justify-center gap-3">
                        <button id="start-scanner" type="button" class="inline-flex min-w-[160px] items-center justify-center gap-2 rounded-xl bg-[#073b78] px-5 py-3 text-sm font-bold text-white shadow-lg shadow-blue-900/20 transition hover:-translate-y-0.5 hover:bg-[#0a4b91] focus:outline-none focus:ring-4 focus:ring-blue-400/30">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.55-2.275A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            Start camera
                        </button>
                        <button id="stop-scanner" type="button" disabled class="inline-flex min-w-[130px] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/20 dark:bg-white/10 dark:text-white dark:hover:bg-white/15">
                            <span class="h-3.5 w-3.5 rounded-sm bg-current"></span>
                            Stop
                        </button>
                    </div>
                </section>

                <aside class="flex flex-col bg-white p-6 sm:p-8 lg:p-10 dark:bg-slate-800">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#0a4b91] dark:text-blue-400">How it works</p>
                        <h2 class="mt-2 text-2xl font-bold">Find your product instantly</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">Scan the QR code or barcode printed on the product. We will verify its product code and open the matching catalog page.</p>
                    </div>

                    <div id="scan-status" class="mt-7 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/60" role="status" aria-live="polite">
                        <div class="flex gap-3">
                            <span id="status-icon" class="flex h-9 w-9 flex-none items-center justify-center rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            <div class="min-w-0">
                                <p id="status-title" class="font-bold">Ready to scan</p>
                                <p id="status-message" class="mt-1 text-sm leading-5 text-slate-500 dark:text-slate-400">Tap “Start camera” to begin.</p>
                            </div>
                        </div>
                    </div>

                    <form id="code-form" class="mt-7" novalidate>
                        <label for="product-code" class="mb-2 block text-sm font-bold">Product Code</label>
                        <div class="flex overflow-hidden rounded-xl border border-slate-300 bg-white focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-500/10 dark:border-slate-600 dark:bg-slate-900">
                            <input id="product-code" name="code" type="text" maxlength="2048" autocomplete="off" class="min-w-0 flex-1 border-0 bg-transparent px-4 py-3 text-sm focus:ring-0 dark:text-white" placeholder="Scan or enter product code">
                            <button id="find-product" type="submit" class="bg-[#073b78] px-4 text-sm font-bold text-white transition hover:bg-[#052e5e] disabled:cursor-wait disabled:opacity-60">Find</button>
                        </div>
                        <p class="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">You can also type the product code manually if camera access is unavailable.</p>
                    </form>

                    <div class="mt-auto pt-8 text-xs leading-5 text-slate-400">
                        Camera access requires your permission and a secure HTTPS connection.
                    </div>
                </aside>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const lookupUrl = @js(route('products.scan.lookup'));
        const csrfToken = @js(csrf_token());
        const reader = new Html5Qrcode('qr-reader', {
            formatsToSupport: [
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.ITF,
                Html5QrcodeSupportedFormats.CODABAR,
            ],
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true,
            },
        });
        const startButton = document.getElementById('start-scanner');
        const stopButton = document.getElementById('stop-scanner');
        const placeholder = document.getElementById('camera-placeholder');
        const codeForm = document.getElementById('code-form');
        const codeInput = document.getElementById('product-code');
        const findButton = document.getElementById('find-product');
        const statusBox = document.getElementById('scan-status');
        const statusTitle = document.getElementById('status-title');
        const statusMessage = document.getElementById('status-message');
        const cameraDot = document.getElementById('camera-dot');
        const cameraLabel = document.getElementById('camera-label');
        const zoomControls = document.getElementById('camera-zoom-controls');
        const zoomSlider = document.getElementById('camera-zoom-slider');
        const zoomUnsupported = document.getElementById('camera-zoom-unsupported');
        const zoomInput = document.getElementById('camera-zoom');
        const zoomValue = document.getElementById('camera-zoom-value');
        let isRunning = false;
        let isLookingUp = false;
        let zoomUpdateTimer = null;

        const setStatus = (type, title, message) => {
            const styles = {
                info: 'border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/60',
                success: 'border-emerald-200 bg-emerald-50 dark:border-emerald-800 dark:bg-emerald-950/30',
                error: 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950/30',
            };

            statusBox.className = `mt-7 rounded-2xl border p-4 ${styles[type]}`;
            statusTitle.textContent = title;
            statusMessage.textContent = message;
        };

        const setCameraState = (running) => {
            isRunning = running;
            startButton.disabled = running;
            stopButton.disabled = !running;
            placeholder.classList.toggle('hidden', running);
            cameraDot.className = `h-2 w-2 rounded-full ${running ? 'animate-pulse bg-emerald-400' : 'bg-slate-500'}`;
            cameraLabel.textContent = running ? 'Camera live' : 'Camera off';
        };

        const stopScanner = async () => {
            if (!isRunning) return;

            try {
                await reader.stop();
            } catch (error) {
                console.error('Unable to stop QR scanner.', error);
            } finally {
                setCameraState(false);
                zoomControls.classList.add('hidden');
            }
        };

        const applyCameraZoom = async (zoom) => {
            const value = Number(zoom);
            zoomValue.value = `${value.toFixed(1)}×`;

            try {
                await reader.applyVideoConstraints({ advanced: [{ zoom: value }] });
            } catch (error) {
                console.warn('Camera zoom is not available.', error);
            }
        };

        const configureCameraZoom = async () => {
            try {
                const capabilities = reader.getRunningTrackCapabilities();
                const zoom = capabilities?.zoom;

                if (!zoom || typeof zoom.min !== 'number' || typeof zoom.max !== 'number' || zoom.max <= zoom.min) {
                    zoomControls.classList.remove('hidden');
                    zoomSlider.classList.add('hidden');
                    zoomUnsupported.classList.remove('hidden');
                    zoomUnsupported.classList.add('flex');
                    return;
                }

                const automaticZoom = Math.min(
                    zoom.max,
                    Math.max(zoom.min, Math.min(2.5, zoom.min + ((zoom.max - zoom.min) * 0.45)))
                );

                zoomInput.min = zoom.min;
                zoomInput.max = zoom.max;
                zoomInput.step = zoom.step || 0.1;
                zoomInput.value = automaticZoom;
                zoomControls.classList.remove('hidden');
                zoomSlider.classList.remove('hidden');
                zoomUnsupported.classList.add('hidden');
                zoomUnsupported.classList.remove('flex');
                await applyCameraZoom(automaticZoom);
            } catch (error) {
                zoomControls.classList.remove('hidden');
                zoomSlider.classList.add('hidden');
                zoomUnsupported.classList.remove('hidden');
                zoomUnsupported.classList.add('flex');
                console.warn('Unable to read camera zoom capabilities.', error);
            }
        };

        zoomInput.addEventListener('input', () => {
            zoomValue.value = `${Number(zoomInput.value).toFixed(1)}×`;
            window.clearTimeout(zoomUpdateTimer);
            zoomUpdateTimer = window.setTimeout(() => applyCameraZoom(zoomInput.value), 80);
        });

        const lookupProduct = async (rawCode) => {
            const code = rawCode.trim();

            if (!code || isLookingUp) {
                if (!code) setStatus('error', 'Product code required', 'Scan a QR code or enter a product code first.');
                return;
            }

            isLookingUp = true;
            findButton.disabled = true;
            codeInput.value = code;
            setStatus('info', 'Checking product…', 'Please wait while we search the catalog.');

            try {
                const response = await fetch(lookupUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ code }),
                });
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Product not found.');
                }

                codeInput.value = data.product_code;
                setStatus('success', 'Product found', `${data.product_code} — Opening product details…`);
                window.setTimeout(() => window.location.assign(data.redirect_url), 650);
            } catch (error) {
                setStatus('error', 'Product not found', error.message);
                isLookingUp = false;
                findButton.disabled = false;

                Swal.fire({
                    icon: "error",
                    title: "Product Not Found!!",
                    text: `${error.message}`,
                });
            }
        };

        startButton.addEventListener('click', async () => {
            setStatus('info', 'Starting camera…', 'Please allow camera access when your browser asks.');

            try {
                await reader.start(
                    { facingMode: 'environment' },
                    {
                        fps: 20,
                        disableFlip: true,
                        videoConstraints: {
                            facingMode: { ideal: 'environment' },
                            width: { ideal: 1920 },
                            height: { ideal: 1080 },
                            focusMode: { ideal: 'continuous' },
                        },
                        qrbox: (viewfinderWidth, viewfinderHeight) => {
                            const width = Math.min(Math.floor(viewfinderWidth * 0.88), 360);
                            const height = Math.min(Math.floor(width * 0.5), Math.floor(viewfinderHeight * 0.6));

                            return { width, height };
                        },
                    },
                    async (decodedText) => {
                        if (isLookingUp) return;
                        const lookup = lookupProduct(decodedText);
                        await stopScanner();
                        await lookup;
                    },
                    () => {}
                );
                setCameraState(true);
                await configureCameraZoom();
                setStatus('info', 'Scanning…', 'Hold the code steady inside the frame. Use zoom for very small barcodes.');
            } catch (error) {
                setCameraState(false);
                setStatus('error', 'Camera unavailable', 'Check camera permission and make sure this page is opened over HTTPS. You can enter the code manually below.');
            }
        });

        stopButton.addEventListener('click', async () => {
            await stopScanner();
            setStatus('info', 'Scanner stopped', 'Tap “Start camera” when you want to scan again.');
        });

        codeForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            await stopScanner();
            lookupProduct(codeInput.value);
        });

        window.addEventListener('pagehide', () => {
            if (isRunning) reader.stop().catch(() => {});
        });
    });
</script>
@endsection
