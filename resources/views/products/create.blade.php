@extends('layouts.dashboard')

@section('css')
    <style>[x-cloak] { display: none !important; }</style>
@endsection

@section('content')
<div
    x-data="productCreateForm(@js($sampleCategories))"
    class="min-h-screen"
>
    <div class="border-b border-gray-200 bg-white px-4 py-5 dark:border-gray-700 dark:bg-gray-800 sm:px-6">
        <nav class="mb-4 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboards.index') }}" class="inline-flex items-center text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white">
                        <svg class="mr-2.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 011-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <a href="{{ route('products.index') }}" class="ml-1 text-gray-700 hover:text-primary-600 dark:text-gray-300 dark:hover:text-white md:ml-2">Products</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="ml-1 text-gray-500 dark:text-gray-400 md:ml-2">Create</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <div class="mb-2 flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create a new product</h1>
                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">Sample data</span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Add core product details and category-specific specifications for the QR stand.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to products
            </a>
        </div>
    </div>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="showDemoMessage('Product creation is ready for backend integration.')">
        @csrf
        <div class="mx-auto grid w-full max-w-screen-2xl gap-6 p-4 sm:p-6 lg:grid-cols-12 lg:p-8">
            <div class="space-y-6 lg:col-span-8">
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Basic information</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fields marked with an asterisk are required.</p>
                        </div>
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">1</span>
                    </div>

                    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6">
                        <div>
                            <label for="product_code" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product code <span class="text-red-600">*</span></label>
                            <input x-model="form.productCode" type="text" name="product_code" id="product_code" value="2000000602110" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. 2000000602110">
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Keep leading zeros in the product code.</p>
                        </div>

                        <div>
                            <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-600">*</span></label>
                            <select x-model="form.status" name="status" id="status" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach ($sampleStatuses as $status)
                                    <option value="{{ $status }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product name <span class="text-red-600">*</span></label>
                            <input x-model="form.name" type="text" name="name" id="name" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter a clear product name">
                        </div>

                        <div>
                            <label for="brand" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Brand <span class="text-red-600">*</span></label>
                            <select x-model="form.brand" name="brand" id="brand" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach ($sampleBrands as $brand)
                                    <option value="{{ $brand }}">{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="model" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Model</label>
                            <input x-model="form.model" type="text" name="model" id="model" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. ADGP-370B">
                        </div>

                        <div>
                            <label for="category" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Category <span class="text-red-600">*</span></label>
                            <select x-model="selectedCategory" @change="changeCategory" name="category" id="category" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                @foreach ($sampleCategories as $key => $category)
                                    <option value="{{ $key }}">{{ $category['group'] }} / {{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="country" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Country of origin</label>
                            <select x-model="form.country" name="country" id="country" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option>China</option>
                                <option>Thailand</option>
                                <option>Vietnam</option>
                                <option>Myanmar</option>
                            </select>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product specifications</h2>
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300" x-text="currentCategory.group + ' / ' + currentCategory.name"></span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Specification fields adapt to the selected category.</p>
                        </div>
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">2</span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="mb-5 flex rounded-lg border border-blue-200 bg-blue-50 p-3.5 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-900/20 dark:text-blue-300">
                            <svg class="mr-3 mt-0.5 h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-3a1 1 0 11-2 0 1 1 0 012 0zm-2 3a1 1 0 000 2h.01v2.01a1 1 0 102 0V11a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Units are stored separately so values remain searchable and comparable.
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <template x-for="(attribute, index) in attributes" :key="selectedCategory + '-' + attribute.key">
                                <div>
                                    <label :for="'attribute-' + attribute.key" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white" x-text="attribute.label"></label>
                                    <div class="flex">
                                        <template x-if="attribute.type !== 'select'">
                                            <input x-model="attribute.value" :type="attribute.type" :name="'attributes[' + attribute.key + ']'" :id="'attribute-' + attribute.key" :placeholder="attribute.placeholder || ''" step="any" class="block min-w-0 flex-1 rounded-l-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:z-10 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" :class="attribute.unit ? 'rounded-r-none' : 'rounded-r-lg'">
                                        </template>
                                        <template x-if="attribute.type === 'select'">
                                            <select x-model="attribute.value" :name="'attributes[' + attribute.key + ']'" :id="'attribute-' + attribute.key" class="block min-w-0 flex-1 rounded-l-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:z-10 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" :class="attribute.unit ? 'rounded-r-none' : 'rounded-r-lg'">
                                                <template x-for="option in attribute.options" :key="option">
                                                    <option :value="option" x-text="option"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <span x-show="attribute.unit" class="inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-100 px-3 text-sm font-medium text-gray-600 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-200" x-text="attribute.unit"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Description & publishing</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add content displayed when customers scan the QR code.</p>
                        </div>
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">3</span>
                    </div>

                    <div class="grid gap-5 p-5 sm:p-6">
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="description" class="block text-sm font-medium text-gray-900 dark:text-white">Product description</label>
                                <span class="text-xs text-gray-500 dark:text-gray-400"><span x-text="form.description.length"></span> / 2,000</span>
                            </div>
                            <textarea x-model="form.description" name="description" id="description" rows="6" maxlength="2000" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm leading-6 text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Describe benefits, usage and care instructions"></textarea>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="batch_no" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Batch number</label>
                                <input x-model="form.batch" type="text" name="batch_no" id="batch_no" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. B1">
                            </div>
                            <div>
                                <label for="website_url" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Website / QR link</label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656-5.656l1-1m3-3 2-2a4 4 0 015.656 5.656l-1 1"/>
                                        </svg>
                                    </div>
                                    <input x-model="form.websiteUrl" type="url" name="website_url" id="website_url" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="https://example.com/product/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <aside class="space-y-6 lg:col-span-4">
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:sticky lg:top-20">
                    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product preview</h2>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Customer-facing QR card</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold" :class="form.status === 'Active' ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300'" x-text="form.status"></span>
                    </div>

                    <div class="p-5">
                        <label for="product_image" class="group relative flex aspect-[16/9] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                            <img x-show="imagePreview" :src="imagePreview" class="h-full w-full object-cover" alt="Product image preview">
                            <div x-show="!imagePreview" class="p-5 text-center">
                                <span class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-full bg-white text-gray-400 shadow-sm dark:bg-gray-800">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2 1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">Upload product image</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG or JPG · up to 2 MB</p>
                            </div>
                            <span x-show="imagePreview" class="absolute bottom-3 right-3 rounded-lg bg-gray-900/75 px-3 py-1.5 text-xs font-medium text-white">Change image</span>
                            <input @change="previewImage" id="product_image" name="image" type="file" accept="image/png,image/jpeg" class="hidden">
                        </label>

                        <div class="mt-5">
                            <p class="text-xs font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400" x-text="currentCategory.group + ' · ' + currentCategory.name"></p>
                            <h3 class="mt-1.5 line-clamp-2 text-lg font-bold text-gray-900 dark:text-white" x-text="form.name || 'Untitled product'"></h3>
                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <span x-text="form.brand"></span>
                                <span class="h-1 w-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                <span x-text="form.model"></span>
                            </div>
                        </div>

                        <dl class="mt-5 divide-y divide-gray-100 rounded-lg border border-gray-200 px-4 dark:divide-gray-700 dark:border-gray-700">
                            <template x-for="attribute in attributes.slice(0, 4)" :key="'preview-' + attribute.key">
                                <div class="flex items-center justify-between gap-4 py-2.5 text-sm">
                                    <dt class="text-gray-500 dark:text-gray-400" x-text="attribute.label"></dt>
                                    <dd class="text-right font-medium text-gray-900 dark:text-white"><span x-text="attribute.value || '—'"></span><span x-show="attribute.value && attribute.unit" class="ml-1 text-gray-500 dark:text-gray-400" x-text="attribute.unit"></span></dd>
                                </div>
                            </template>
                        </dl>

                        <div class="mt-5 flex items-center gap-3 rounded-lg bg-gray-50 p-3 dark:bg-gray-700/50">
                            <div class="flex h-11 w-11 flex-none items-center justify-center rounded-lg bg-white text-gray-800 shadow-sm dark:bg-gray-800 dark:text-white">
                                <svg class="h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm9-2h3v3h-3v-3zm4 0h3v7h-3v-3h-2v3h-2v-3h3v-2h1v-2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">QR destination</p>
                                <p class="truncate text-xs text-gray-500 dark:text-gray-400" x-text="form.websiteUrl || 'Link will be generated after creation'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                            <button type="button" @click="form.status = 'Draft'; showDemoMessage('Sample draft saved locally for preview.')" class="inline-flex flex-1 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700">Save draft</button>
                            <button type="submit" class="inline-flex flex-1 items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create product
                            </button>
                        </div>
                        <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">Prototype only — no data will be saved.</p>
                    </div>
                </section>
            </aside>
        </div>
    </form>

    <div x-cloak x-show="notification" x-transition.opacity.duration.200ms class="fixed bottom-5 right-5 z-50 max-w-sm rounded-lg border border-green-200 bg-white p-4 text-sm text-gray-700 shadow-lg dark:border-green-900 dark:bg-gray-800 dark:text-gray-200" role="status">
        <div class="flex items-start gap-3">
            <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </span>
            <div>
                <p class="font-semibold text-gray-900 dark:text-white">Design prototype</p>
                <p class="mt-0.5" x-text="notification"></p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    window.productCreateForm = function (categories) {
        return {
            categories,
            selectedCategory: 'water-pump',
            attributes: [],
            imagePreview: '',
            notification: '',
            notificationTimer: null,
            form: {
                productCode: '2000000602110',
                name: 'IM Dayuan Auto Pressure Pump ADGP370B 370W (0.5HP)',
                brand: 'IM Dayuan',
                model: 'ADGP-370B',
                country: 'China',
                status: 'Active',
                batch: 'B1',
                websiteUrl: 'https://pro1globalhomecenter.com/product/2000000602110',
                description: 'ရေအားကောင်းစေရန် အသုံးပြုနိုင်ပါသည်။ အရည်အသွေးကောင်းမွန်သော ပစ္စည်းဖြစ်ပြီး အိမ်သုံးရေတင်စနစ်များအတွက် သင့်လျော်ပါသည်။'
            },
            get currentCategory() {
                return this.categories[this.selectedCategory];
            },
            init() {
                this.loadAttributes();
            },
            changeCategory() {
                this.loadAttributes();
            },
            loadAttributes() {
                this.attributes = JSON.parse(JSON.stringify(this.currentCategory.attributes));
            },
            previewImage(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) {
                    this.imagePreview = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (loadEvent) => this.imagePreview = loadEvent.target.result;
                reader.readAsDataURL(file);
            },
            showDemoMessage(message) {
                this.notification = message;
                window.clearTimeout(this.notificationTimer);
                this.notificationTimer = window.setTimeout(() => this.notification = '', 3500);
            }
        };
    };
</script>
@endsection
