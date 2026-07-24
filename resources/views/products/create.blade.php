@extends('layouts.dashboard')

@section('content')
<div id="product-create-page" class="min-h-screen">
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
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Add core product details, images and product specifications.</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex w-fit items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-gray-700">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to products
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="mx-auto mt-6 w-full max-w-screen-2xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300">
                <p class="font-semibold">Please check the form and try again.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="product-create-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
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

                    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-6">

                        <div class="lg:col-span-2">
                            <label for="product_code" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product code <span class="text-red-600">*</span></label>
                            <input type="search" name="product_code" id="product_code" value="{{ old('product_code') }}" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. 2000000602110">
                            <!-- <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">Keep leading zeros in the product code.</p> -->
                        </div>

                        <div class="lg:col-span-2">
                            <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-600">*</span></label>
                            <select name="status_id" id="status" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Choose status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}" @selected((string) old('status_id', $statuses->first()?->id) === (string) $status->id)>{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Name <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter product display name">
                        </div>

                        <div class="sm:col-span-2 lg:col-span-6">
                            <label for="product_name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Product Full Name <span class="text-red-600">*</span></label>
                            <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter a clear product name" readonly>
                        </div>

                        <div class="lg:col-span-3">
                            <label for="brand" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Brand <span class="text-red-600">*</span></label>
                            <input type="text" name="brand" id="brand" list="brand-options" value="{{ old('brand') }}" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Enter brand name" readonly>
                            <datalist id="brand-options">
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand }}"></option>
                                @endforeach
                            </datalist>
                        </div>

                        <div class="lg:col-span-3">
                            <label for="model" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Model <span class="text-red-600">*</span></label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. ADGP-370B">
                        </div>

                        <div class="lg:col-span-3">
                            <label for="category" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Category <span class="text-red-600">*</span></label>
                            <select name="category_id" id="category" required class="w-full block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white readonly">
                                <option value="">Choose category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected((string) old('category_id', $categories->first()?->id) === (string) $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <label for="country" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Country of origin <span class="text-red-600">*</span></label>
                            <select name="country_of_origin" id="country" required class="w-full block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">Choose country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected((string) old('country_of_origin', $country->first()?->id) === (string) $country->id)>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4 border-b border-gray-200 px-5 py-4 dark:border-gray-700 sm:px-6">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product specifications</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose an existing specification or type a new name to create it instantly.</p>
                        </div>
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">2</span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/30">
                            <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Add product specification</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Value includes unit, for example 370W, 35 L/min or 7.5 mm.</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="inline-flex rounded-lg border border-gray-200 bg-white p-0.5 dark:border-gray-700 dark:bg-gray-800">
                                        <button type="button" id="choose-specification-mode" class="rounded-md bg-primary-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition">Choose</button>
                                        <button type="button" id="new-specification-mode" class="rounded-md px-3 py-1.5 text-xs font-semibold text-gray-600 transition hover:text-gray-900 disabled:cursor-not-allowed disabled:text-gray-400 dark:text-gray-300 dark:hover:text-white dark:disabled:text-gray-600">New</button>
                                    </div>
                                    <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                        <span id="specification-count">0</span> / <span>8</span>
                                    </span>
                                </div>
                            </div>

                            <div class="grid gap-3 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,1.1fr)_154px] lg:items-end">
                                <div>
                                    <label for="specification_picker" id="specification-name-label" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-300">Specification</label>
                                    <select id="specification_picker" class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:disabled:bg-gray-800">
                                        <option value="">Choose specification</option>
                                    </select>
                                    <input type="text" id="new_specification_name" class="hidden w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:disabled:bg-gray-800" placeholder="e.g. Pipe Thickness">
                                </div>

                                <div>
                                    <label for="specification_value" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-300">Value <span class="text-red-600">*</span></label>
                                    <input type="text" id="specification_value" class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:disabled:bg-gray-800" placeholder="e.g. 370W (0.5HP)">
                                </div>

                                <div>
                                    <button type="button" id="add-specification" disabled class="inline-flex h-[42px] w-full items-center justify-center rounded-lg bg-primary-700 px-3 text-sm font-semibold text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span id="add-specification-label">Add</span>
                                    </button>
                                </div>
                            </div>

                            <p id="specification-error" class="mt-2 hidden text-xs font-medium text-red-600 dark:text-red-400"></p>
                            <p id="specification-limit" class="mt-2 hidden text-xs font-medium text-amber-700 dark:text-amber-400">Maximum 8 specifications can be added.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="hidden grid-cols-12 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:bg-gray-700/30 dark:text-gray-400 sm:grid">
                                <span class="col-span-5">Specification</span>
                                <span class="col-span-6">Value</span>
                                <span class="col-span-1 text-right">Action</span>
                            </div>

                            <div id="empty-specifications" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                No specifications added yet.
                            </div>
                            <div id="specification-rows"></div>
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
                                <span class="text-xs text-gray-500 dark:text-gray-400"><span id="description-count">0</span> / 2,000</span>
                            </div>
                            <textarea name="description" id="description" rows="6" maxlength="2000" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm leading-6 text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Describe benefits, usage and care instructions">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="website_url" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Website / QR link</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656-5.656l1-1m3-3 2-2a4 4 0 015.656 5.656l-1 1"/>
                                    </svg>
                                </div>
                                <input type="url" name="website_url" id="website_url" value="{{ old('website_url') }}" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 pl-10 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="https://example.com/product/...">
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
                        <span id="preview-status" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold"></span>
                    </div>

                    <div class="p-5">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Product images</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">PNG or JPG · up to 2 MB</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Main image <span class="text-red-600">*</span></p>
                                <label for="main_image" class="group relative flex aspect-[16/10] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                                    <img id="main-image-preview" class="hidden h-full w-full object-cover" alt="Main product image preview">
                                    <div id="main-image-placeholder" class="p-4 text-center">
                                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-400 shadow-sm dark:bg-gray-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Upload main image</p>
                                    </div>
                                    <span id="main-image-change" class="absolute bottom-2 right-2 hidden rounded-md bg-gray-900/75 px-2 py-1 text-[11px] font-medium text-white">Change</span>
                                    <input id="main_image" name="main_image" type="file" accept="image/png,image/jpeg" required class="hidden">
                                </label>
                            </div>

                            <div>
                                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Thumbnail</p>
                                <label for="thumbnail_image" class="group relative flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                                    <img id="thumbnail-image-preview" class="hidden h-full w-full object-cover" alt="Thumbnail image preview">
                                    <div id="thumbnail-image-placeholder" class="p-2 text-center">
                                        <svg class="mx-auto mb-1.5 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                        <p class="text-[11px] font-semibold leading-4 text-gray-700 dark:text-gray-200">Add thumbnail</p>
                                    </div>
                                    <span id="thumbnail-image-change" class="absolute bottom-2 right-2 hidden rounded-md bg-gray-900/75 px-2 py-1 text-[11px] font-medium text-white">Change</span>
                                    <input id="thumbnail_image" name="thumbnail_image" type="file" accept="image/png,image/jpeg" class="hidden">
                                </label>
                            </div>
                        </div>

                        <div class="mt-5">
                            <p id="preview-category" class="text-xs font-semibold uppercase tracking-wide text-primary-600 dark:text-primary-400"></p>
                            <h3 id="preview-name" class="mt-1.5 line-clamp-2 text-lg font-bold text-gray-900 dark:text-white"></h3>
                            <div class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                                <span id="preview-brand"></span>
                                <span class="h-1 w-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                                <span id="preview-model"></span>
                            </div>
                        </div>

                        <dl class="mt-5 divide-y divide-gray-100 rounded-lg border border-gray-200 px-4 dark:divide-gray-700 dark:border-gray-700">
                            <div id="empty-preview-specifications" class="py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                Added specifications will appear here.
                            </div>
                            <div id="preview-specifications"></div>
                        </dl>

                        <div class="mt-6 border-t border-gray-200 pt-5 dark:border-gray-700">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Product QR Code</h3>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">The QR code will be generated automatically after saving.</p>
                            </div>

                            <div class="mt-4 grid gap-5 sm:grid-cols-[9rem_minmax(0,1fr)] sm:items-center">
                                <div class="flex aspect-square w-36 items-center justify-center rounded-2xl border-2 border-dashed border-gray-200 bg-white p-3 dark:border-gray-600 dark:bg-gray-800">
                                    <div class="flex h-full w-full flex-col items-center justify-center text-center">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-700">
                                            <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h7v7H3V3zm2 2v3h3V5H5zm9-2h7v7h-7V3zm2 2v3h3V5h-3zM3 14h7v7H3v-7zm2 2v3h3v-3H5zm9-2h3v3h-3v-3zm4 0h3v7h-3v-3h-2v3h-2v-3h3v-2h1v-2z"/></svg>
                                        </span>
                                        <p class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400">Generated on save</p>
                                    </div>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
                                        <svg class="h-4 w-4 flex-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 010 5.656l-2 2a4 4 0 01-5.656-5.656l1-1m3-3l2-2a4 4 0 015.656 5.656l-1 1"/></svg>
                                        <p class="text-xs font-semibold uppercase tracking-wide">Destination</p>
                                    </div>
                                    <p class="mt-1.5 text-sm font-medium leading-5 text-gray-700 dark:text-gray-300">Product page URL will be assigned after the product is saved.</p>
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">The exact encoded URL will be stored with the product.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 border-t border-gray-200 pt-5 dark:border-gray-700">
                            <div class="mb-3">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Brand icon</h3>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG or JPG · up to 2 MB</p>
                            </div>
                            <label for="brand_icon" class="group relative flex h-28 w-28 cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                                <img id="brand-icon-image-preview" class="hidden h-full w-full object-contain p-2" alt="Brand icon preview">
                                <div id="brand-icon-image-placeholder" class="p-2 text-center">
                                    <svg class="mx-auto mb-1.5 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                    <p class="text-[11px] font-semibold leading-4 text-gray-700 dark:text-gray-200">Add brand icon</p>
                                </div>
                                <span id="brand-icon-image-change" class="absolute bottom-2 right-2 hidden rounded-md bg-gray-900/75 px-2 py-1 text-[11px] font-medium text-white">Change</span>
                                <input id="brand_icon" name="brand_icon" type="file" accept="image/png,image/jpeg" class="hidden">
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 bg-gray-50 px-5 py-4 dark:border-gray-700 dark:bg-gray-800">
                        <div class="flex flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                            <a href="{{ route('products.index') }}" class="inline-flex flex-1 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 dark:focus:ring-gray-700">Cancel</a>
                            <button type="submit" id="create-product-button" class="inline-flex flex-1 items-center justify-center rounded-lg bg-primary-700 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:opacity-60 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span id="create-product-button-label">Create product</span>
                            </button>
                        </div>
                        <p class="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">Product will be saved with its specifications and images.</p>

                        <div id="api-validation-errors" class="mx-auto mt-6 hidden w-full max-w-screen-2xl px-4s sm:px-6s lg:px-8s" role="alert">
                            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/20 dark:text-red-300">
                                <p class="font-semibold">Please check the form and try again.</p>
                                <ul id="api-validation-error-list" class="mt-2 list-disc space-y-1 pl-5"></ul>
                            </div>
                        </div>
                    </div>
                </section>

                 
            </aside>

        
        </div>
    </form>

</div>
@endsection

@section('scripts')
<script>
    // Using jQuery DOM same as weather forecast json
    $(document).ready(function() {

        const maxSpecifications = 8;
        const categories = @js($categories);
        const statuses = @js($statuses);
        let availableSpecifications = [...new Set(@js($specifications))];
        let nextId = 1;
        let entryMode = 'choose';
        let rows = (@js(old('specifications', [])) || [])
            .filter(row => String(row.name || '').trim() || String(row.value || '').trim())
            .map(row => ({ id: nextId++, name: row.name || '', value: row.value || '' }));

        rows.forEach(row => {
            if (row.name && !availableSpecifications.some(name => normalize(name) === normalize(row.name))) {
                availableSpecifications.push(row.name);
            }
        });

        const activeModeClasses = 'bg-primary-600 text-white shadow-sm';
        const inactiveModeClasses = 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white';

        function normalize(value) {
            return String(value || '').trim().replace(/\s+/g, ' ').toLocaleLowerCase();
        }

        function escapeHtml(value) {
            return $('<div>').text(value == null ? '' : String(value)).html()
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function isSelected(name, exceptId = null) {
            return rows.some(row => row.id !== exceptId && normalize(row.name) === normalize(name));
        }

        function entryName() {
            const value = entryMode === 'new' ? $('#new_specification_name').val() : $('#specification_picker').val();
            return String(value || '').trim().replace(/\s+/g, ' ');
        }

        function showError(message = '') {
            $('#specification-error').text(message).toggleClass('hidden', !message);
        }

        function renderPicker() {
            const selected = $('#specification_picker').val() || '';
            const options = ['<option value="">Choose specification</option>'];
            availableSpecifications.forEach(name => {
                options.push(`<option value="${escapeHtml(name)}" ${isSelected(name) ? 'disabled' : ''}>${escapeHtml(name)}</option>`);
            });
            $('#specification_picker').html(options.join('')).val(selected);
        }

        function initializeSpecificationSelect2() {
            const $picker = $('#specification_picker');

            if (!$picker.hasClass('select2-hidden-accessible')) {
                $picker.select2({
                    placeholder: 'Choose specification',
                });
            } else {
                $picker.trigger('change.select2');
            }

            $('.specification-row-name').each(function () {
                const $select = $(this);

                if (!$select.hasClass('select2-hidden-accessible')) {
                    $select.select2({
                        placeholder: 'Choose specification',
                    });
                }
            });
        }

        function renderRows() {
            const optionsFor = row => availableSpecifications.map(name =>
                `<option value="${escapeHtml(name)}" ${normalize(row.name) === normalize(name) ? 'selected' : ''} ${isSelected(name, row.id) ? 'disabled' : ''}>${escapeHtml(name)}</option>`
            ).join('');

            $('#specification-rows').html(rows.map((row, index) => `
                <div class="grid gap-2 border-t border-gray-100 px-4 py-3 first:border-t-0 dark:border-gray-700 sm:grid-cols-12 sm:items-center" data-row-id="${row.id}">
                    <div class="sm:col-span-5">
                        <label for="selected-specification-name-${row.id}" class="sr-only">Specification name</label>
                        <select name="specifications[${index}][name]" id="selected-specification-name-${row.id}" class="specification-row-name block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm font-semibold text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">${optionsFor(row)}</select>
                    </div>
                    <div class="sm:col-span-6">
                        <label for="selected-specification-value-${row.id}" class="sr-only">Specification value</label>
                        <input value="${escapeHtml(row.value)}" name="specifications[${index}][value]" id="selected-specification-value-${row.id}" required class="specification-row-value block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. 370W (0.5HP)">
                    </div>
                    <div class="flex justify-end sm:col-span-1">
                        <button type="button" class="remove-specification inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400" aria-label="Remove specification">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>`).join(''));

            const limitReached = rows.length >= maxSpecifications;
            $('#specification-count').text(rows.length);
            $('#empty-specifications').toggleClass('hidden', rows.length > 0);
            $('#specification-limit').toggleClass('hidden', !limitReached);
            $('#new-specification-mode, #specification_picker, #new_specification_name, #specification_value').prop('disabled', limitReached);
            renderPicker();
            initializeSpecificationSelect2();
            renderPreviewSpecifications();
            updateAddButton();
        }

        function renderPreviewSpecifications() {
            const populated = rows.filter(row => normalize(row.name) || String(row.value || '').trim());
            $('#empty-preview-specifications').toggleClass('hidden', populated.length > 0);
            $('#preview-specifications').html(populated.map(row => `
                <div class="flex items-center justify-between gap-4 py-2.5 text-sm">
                    <dt class="text-gray-500 dark:text-gray-400">${escapeHtml(row.name)}</dt>
                    <dd class="text-right font-medium text-gray-900 dark:text-white">${escapeHtml(row.value || '—')}</dd>
                </div>`).join(''));
        }

        function setEntryMode(mode) {
            if (mode === 'new' && rows.length >= maxSpecifications) return;
            entryMode = mode;
            showError();
            const isNew = mode === 'new';
            $('#specification_picker').toggleClass('hidden', isNew);
            $('#specification_picker').next('.select2-container').toggle(!isNew);
            $('#new_specification_name').toggleClass('hidden', !isNew).toggleClass('block', isNew);
            $('#specification-name-label').text(isNew ? 'New specification name' : 'Specification');
            $('#add-specification-label').text(isNew ? 'Create & add' : 'Add');
            $('#choose-specification-mode').toggleClass(activeModeClasses, !isNew).toggleClass(inactiveModeClasses, isNew);
            $('#new-specification-mode').toggleClass(activeModeClasses, isNew).toggleClass(inactiveModeClasses, !isNew);
            if (isNew) $('#specification_picker').val(''); else $('#new_specification_name').val('');
            if (isNew) {
                $('#new_specification_name').trigger('focus');
            } else {
                $('#specification_picker').next('.select2-container').find('.select2-selection').trigger('focus');
            }
            updateAddButton();
        }

        function updateAddButton() {
            const name = entryName();
            $('#add-specification').prop('disabled', rows.length >= maxSpecifications || !name || isSelected(name));
        }

        function addSpecification() {
            showError();
            if (rows.length >= maxSpecifications) return showError('Maximum 8 specifications can be added.');
            const requestedName = entryName();
            if (!requestedName) return showError(entryMode === 'new' ? 'Enter a specification name.' : 'Choose a specification first.');
            const existingName = availableSpecifications.find(name => normalize(name) === normalize(requestedName));
            const name = existingName || requestedName;
            if (isSelected(name)) return showError('This specification has already been added to this product.');
            if (!existingName) availableSpecifications.push(name);
            rows.push({ id: nextId++, name, value: String($('#specification_value').val() || '').trim() });
            $('#specification_picker, #new_specification_name, #specification_value').val('');
            setEntryMode('choose');
            renderRows();
        }

        function updateProductPreview() {
            const category = categories.find(item => String(item.id) === String($('#category').val()));
            const status = statuses.find(item => String(item.id) === String($('#status').val()));
            const statusName = status ? status.name : '';
            $('#preview-category').text(category ? category.name : 'No category selected');
            $('#preview-name').text($('#name').val() || 'Untitled product');
            $('#preview-brand').text($('#brand').val());
            $('#preview-model').text($('#model').val());
            $('#description-count').text($('#description').val().length);
            $('#preview-status').text(statusName || 'No status')
                .toggleClass('bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300', statusName.toLowerCase() === 'active')
                .toggleClass('bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300', statusName.toLowerCase() !== 'active');
        }

        function previewImage(input, type) {
            const file = input.files && input.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = event => {
                $(`#${type}-image-preview`).attr('src', event.target.result).removeClass('hidden');
                $(`#${type}-image-placeholder`).addClass('hidden');
                $(`#${type}-image-change`).removeClass('hidden');
            };
            reader.readAsDataURL(file);
        }

        function displayValidationErrors(errors) {
            const messages = Object.values(errors).flat();
            $('#api-validation-error-list').html(messages.map(message => `<li>${escapeHtml(message)}</li>`).join(''));
            $('#api-validation-errors').toggleClass('hidden', messages.length === 0);
            if (messages.length) {
                document.getElementById('api-validation-errors').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function clientValidationErrors() {
            const errors = {};
            const requiredFields = {
                product_code: 'Product code is required.',
                status: 'Status is required.',
                name: 'Name is required.',
                product_name: 'Product full name is required.',
                brand: 'Brand is required.',
                model: 'Model is required.',
                category: 'Category is required.',
                country: 'Country of origin is required.'
            };

            Object.entries(requiredFields).forEach(([id, message]) => {
                if (!String($(`#${id}`).val() || '').trim()) errors[id] = [message];
            });

            const mainImage = document.getElementById('main_image').files[0];
            if (!mainImage) errors.main_image = ['Main image is required.'];

            if (!rows.length) {
                errors.specifications = ['At least one product specification is required.'];
            } else {
                rows.forEach((row, index) => {
                    if (!String(row.name || '').trim()) errors[`specifications.${index}.name`] = [`Specification ${index + 1} name is required.`];
                    if (!String(row.value || '').trim()) errors[`specifications.${index}.value`] = [`Specification '${row.name}' value is required.`];
                });
            }

            return errors;
        }

        $('#choose-specification-mode').on('click', () => setEntryMode('choose'));
        $('#new-specification-mode').on('click', () => setEntryMode('new'));
        $('#add-specification').on('click', addSpecification);
        $('#specification_picker, #new_specification_name, #specification_value').on('input change', updateAddButton).on('keydown', event => {
            if (event.key === 'Enter') { event.preventDefault(); addSpecification(); }
        });
        $('#specification-rows').on('input', '.specification-row-value', function () {
            rows.find(row => row.id === Number($(this).closest('[data-row-id]').data('row-id'))).value = $(this).val();
            renderPreviewSpecifications();
        }).on('change', '.specification-row-name', function () {
            const row = rows.find(item => item.id === Number($(this).closest('[data-row-id]').data('row-id')));
            if (isSelected($(this).val(), row.id)) return showError('This specification has already been added to this product.');
            row.name = $(this).val(); showError(); renderRows();
        }).on('click', '.remove-specification', function () {
            const id = Number($(this).closest('[data-row-id]').data('row-id'));
            rows = rows.filter(row => row.id !== id); showError(); renderRows();
        });
        $('#name, #brand, #model, #category, #status, #website_url, #description').on('input change', updateProductPreview);
        $('#main_image').on('change', function () { previewImage(this, 'main'); });
        $('#thumbnail_image').on('change', function () { previewImage(this, 'thumbnail'); });
        $('#brand_icon').on('change', function () { previewImage(this, 'brand-icon'); });

        renderRows();
        updateProductPreview();



        $('#category').select2({
            placeholder: 'Choose a Category',
        });
        $('#category').on('select2:opening', function (event) {
            event.preventDefault();
        });
        $('#country').select2({
            placeholder: 'Choose a Category',
        });
        
        $('#product_code').on('blur', async () => {
            console.log("hay");
            var product_code = $('#product_code').val();

            await $.ajax({
                url:"{{url('/productsearch')}}",
                method:"GET",
                data:{"product_code":product_code},
                beforeSend:function(){
                    $(".loader").addClass("show");
                },
                success:function(response){
                    console.log(response); // {status: 'scuccess', data: Array(2)}

                    var data = response.data;

                    // console.log(data);

                    $("#product_name").val(data.product_name).trigger('input');
                    $("#brand").val(data.brand).trigger('input');
                    
                    $("#category option").filter(function () {
                        return $(this).text().trim() === data.maincategory;
                    }).prop("selected", true);
                    $("#category").trigger('change');

                },
                // error:function(response){
                //      console.log(response);
                // },
                complete:function(){
                    console.log("complete:");
                    $(".loader").removeClass("show");
                }
            });
        });



        // Start Product Save
            let isSubmitting = false;
            $('#product-create-form').on('submit', function (event) {
                event.preventDefault();
                showError();

                const errors = clientValidationErrors();
                if (Object.keys(errors).length) {
                    displayValidationErrors(errors);
                    return;  
                }

                displayValidationErrors({});
                // $.ajax({
                //     url: this.action,
                //     method: 'POST',
                //     data: new FormData(this),
                //     processData: false,
                //     contentType: false,
                //     headers: { 'Accept': 'application/json' }
                // }).done(response => {
                //     window.location.href = response.redirect;
                // }).fail(xhr => {
                //     const response = xhr.responseJSON || {};
                //     displayValidationErrors(response.errors || { request: [response.message || 'Unable to save the product. Please try again.'] });
                //     $button.prop('disabled', false);
                //     $('#create-product-button-label').text('Create product');
                // });

                Swal.fire({
                    icon: "question",
                    title: "Are you sure to save product?",
                    // text: ``,
                    showCancelButton: true,
                }).then((result) => {
                    if(result.isConfirmed)
                    {
                        isSubmitting = true;                            
                        $(".fullloader").removeClass("hidden");
                        // Swal.disableButtons();

                        $('#create-product-button').prop('disabled', true);
                        $('#create-product-button-label').text('Saving...');

                        console.log('submit');
                        const form = document.getElementById('product-create-form');
                        const formData = new FormData(form);

                        $.ajax({
                            url: form.action,
                            type:"POST",
                            dataType: "json",
                            data:formData,
                            processData: false,
                            contentType: false,
                            success:async function(response){
                                console.log(response);

                                const data = response;

                                if(data.success){
                                    Swal.fire({
                                        icon: "success",
                                        title: "Product saved successfully!",
                                        text: data.message,
                                    });
                                    

                                    // setTimeout(() => {                                            
                                    //     window.location.href="{{ route('products.index') }}";
                                    // }, 3000);
                                    window.location.href="{{ route('products.index') }}";


                                }else{
                                    Swal.fire({
                                        icon: "error",
                                        title: "Product Save Error!!",
                                        text: `${data.message}`,
                                    });

                                    isSubmitting = false;
                                    $(".fullloader").addClass("hidden");
                                    $('#create-product-button').prop('disabled', false);
                                    $('#create-product-button-label').text('Create product');

                                }
                            },
                            error:function(response){
                                console.log("Error: ",response);

                                Swal.fire({
                                    icon: "error",
                                    title: "Product Save Error!!",
                                    text: "Something went wrong while saving product.",
                                });

                                isSubmitting = false;
                                $(".fullloader").addClass("hidden");
                                $('#create-product-button').prop('disabled', false);
                                $('#create-product-button-label').text('Create product');

                            },
                        
                        });

                    }
                })
            });

        // End Product Save

    });

</script>
@endsection



<!-- Note -->
<!-- ဒီနှစ်ခုက jQuery AJAX ကို FormData ပို့တဲ့အခါ မဖြစ်မနေထည့်ရတဲ့ configuration ပါ။
processData: false,
contentType: false,
processData: false
jQuery က ပုံမှန်အားဖြင့် ပို့မယ့် data ကို query string ပြောင်းပါတယ်။
ဥပမာ:
data: {
    name: 'COTTO',
    model: 'ABC'
}
ကို:
name=COTTO&model=ABC
အဖြစ် ပြောင်းပါတယ်။
ဒါပေမဲ့ FormData ထဲမှာ image/file တွေပါနိုင်လို့ query string အဖြစ်ပြောင်းလို့မရပါဘူး။
processData: false
ဆိုတာ:
FormData ကို query string မပြောင်းဘဲ မူလအတိုင်းပို့ပါ။

လို့ jQuery ကိုပြောတာပါ။
မထည့်ရင် ဒီ error ဖြစ်ပါတယ်:
TypeError: Illegal invocation
contentType: false
File upload request မှာ browser က ဒီလို header တည်ဆောက်ရပါတယ်:
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary...
boundary က text data နဲ့ file data ကို ခွဲဖို့ browser က အလိုအလျောက်ထည့်ပေးတဲ့ value ပါ။
contentType: false
ဆိုတာ:
Content-Type ကို jQuery က ကိုယ်တိုင်မသတ်မှတ်ဘဲ browser ကို သတ်မှတ်ခိုင်းပါ။

လို့ဆိုလိုပါတယ်။
multipart/form-data ကို ကိုယ်တိုင်ရေးတာလည်း မမှန်ပါ:
// မသုံးရ
contentType: 'multipart/form-data'
ဒီလိုရေးရင် လိုအပ်တဲ့ boundary မပါနိုင်လို့ server က file ကို ဖတ်မရနိုင်ပါဘူး။
အပြည့်အစုံ
const form = document.getElementById('product-create-form');
const formData = new FormData(form);

$.ajax({
    url: form.action,
    type: 'POST',
    data: formData,

    processData: false, // FormData ကို query string မပြောင်းရန်
    contentType: false, // Browser ကို multipart header သတ်မှတ်ခိုင်းရန်

    success: function (response) {
        console.log(response);
    }
});
အတိုချုပ်:
processData: false
→ FormData ကို မပြောင်းနဲ့

contentType: false
→ File upload header ကို browser က သတ်မှတ်ပါစေ -->
