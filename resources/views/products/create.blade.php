@extends('layouts.dashboard')

@section('css')
    <style>[x-cloak] { display: none !important; }</style>
@endsection

@section('content')
<div
    x-data="productCreateForm(@js($sampleCategories), @js($sampleAttributes))"
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
                            <select x-model="form.category" name="category" id="category" required class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
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
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Product attributes</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose an existing attribute or type a new name to create it instantly.</p>
                        </div>
                        <span class="flex h-8 w-8 flex-none items-center justify-center rounded-full bg-primary-100 text-sm font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">2</span>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700/30">
                            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">Add product attribute</p>
                                    <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Value includes unit, for example 370W, 35 L/min or 7.5 mm.</p>
                                </div>
                                <span class="rounded-full bg-white px-2.5 py-1 text-xs font-semibold text-gray-600 ring-1 ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:ring-gray-700">
                                    <span x-text="attributeRows.length"></span> / <span x-text="maxAttributes"></span>
                                </span>
                            </div>

                            <div class="grid gap-3 lg:grid-cols-12">
                                <div class="lg:col-span-5">
                                    <label for="attribute_picker" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-300">Attribute</label>
                                    <select x-model="selectedAttributeName" @change="handleAttributePickerChange" id="attribute_picker" :disabled="hasReachedAttributeLimit" class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:disabled:bg-gray-800">
                                        <option value="">Choose attribute</option>
                                        <template x-for="attributeName in availableAttributes" :key="attributeName">
                                            <option :value="attributeName" :disabled="isAttributeSelected(attributeName)" x-text="attributeName"></option>
                                        </template>
                                        <option value="__create__">+ Create new attribute</option>
                                    </select>
                                </div>

                                <div class="lg:col-span-5">
                                    <label for="attribute_value" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-300">Value</label>
                                    <input x-model="selectedAttributeValue" @keydown.enter.prevent="addSelectedAttribute" type="text" id="attribute_value" :disabled="hasReachedAttributeLimit" class="block w-full rounded-lg border border-gray-300 bg-white p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:disabled:bg-gray-800" placeholder="e.g. 370W (0.5HP)">
                                </div>

                                <div class="flex items-end lg:col-span-2">
                                    <button type="button" @click="addSelectedAttribute" :disabled="!canAddSelectedAttribute" class="inline-flex h-[42px] w-full items-center justify-center rounded-lg bg-primary-700 px-3 text-sm font-semibold text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 disabled:cursor-not-allowed disabled:bg-gray-300 disabled:text-gray-500 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800 dark:disabled:bg-gray-700 dark:disabled:text-gray-400">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add
                                    </button>
                                </div>
                            </div>

                            <div x-show="creatingAttribute" x-transition class="mt-3 rounded-lg border border-primary-200 bg-white p-3 dark:border-primary-900 dark:bg-gray-800">
                                <label for="new_attribute_name" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-gray-300">New attribute name</label>
                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <input x-model="newAttributeName" @keydown.enter.prevent="createAttribute" type="text" id="new_attribute_name" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. Pipe Thickness">
                                    <div class="flex gap-2">
                                        <button type="button" @click="createAttribute" class="inline-flex items-center justify-center rounded-lg bg-primary-700 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-primary-800 dark:bg-primary-600 dark:hover:bg-primary-700">Create</button>
                                        <button type="button" @click="cancelCreateAttribute" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">Cancel</button>
                                    </div>
                                </div>
                            </div>

                            <p x-show="attributeError" x-text="attributeError" class="mt-2 text-xs font-medium text-red-600 dark:text-red-400"></p>
                            <p x-show="hasReachedAttributeLimit" class="mt-2 text-xs font-medium text-amber-700 dark:text-amber-400">Maximum 8 attributes can be added.</p>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                            <div class="hidden grid-cols-12 gap-3 border-b border-gray-200 bg-gray-50 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:border-gray-700 dark:bg-gray-700/30 dark:text-gray-400 sm:grid">
                                <span class="col-span-5">Attribute</span>
                                <span class="col-span-6">Value</span>
                                <span class="col-span-1 text-right">Action</span>
                            </div>

                            <div x-show="attributeRows.length === 0" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                No attributes added yet.
                            </div>

                            <template x-for="(attribute, index) in attributeRows" :key="attribute.id">
                                <div class="grid gap-2 border-t border-gray-100 px-4 py-3 first:border-t-0 dark:border-gray-700 sm:grid-cols-12 sm:items-center">
                                    <div class="sm:col-span-5">
                                        <label :for="'selected-attribute-name-' + attribute.id" class="sr-only">Attribute name</label>
                                        <select x-model="attribute.name" @change="attributeError = ''" :name="'attributes[' + index + '][name]'" :id="'selected-attribute-name-' + attribute.id" class="block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm font-semibold text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            <template x-for="attributeName in availableAttributes" :key="'row-' + attribute.id + '-' + attributeName">
                                                <option :value="attributeName" :disabled="isAttributeSelectedExcept(attributeName, attribute.id)" x-text="attributeName"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-6">
                                        <label :for="'selected-attribute-value-' + attribute.id" class="sr-only">Attribute value</label>
                                        <input x-model="attribute.value" type="text" :name="'attributes[' + index + '][value]'" :id="'selected-attribute-value-' + attribute.id" class="block w-full rounded-lg border border-gray-300 bg-white p-2 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="e.g. 370W (0.5HP)">
                                    </div>
                                    <div class="flex justify-end sm:col-span-1">
                                        <button type="button" @click="removeAttribute(attribute.id)" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/20 dark:hover:text-red-400" aria-label="Remove attribute">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
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
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Product images</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">PNG or JPG · up to 2 MB</span>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Main image</p>
                                <label for="main_image" class="group relative flex aspect-[16/10] cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                                    <img x-show="mainImagePreview" :src="mainImagePreview" class="h-full w-full object-cover" alt="Main product image preview">
                                    <div x-show="!mainImagePreview" class="p-4 text-center">
                                        <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-white text-gray-400 shadow-sm dark:bg-gray-800">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </span>
                                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Upload main image</p>
                                    </div>
                                    <span x-show="mainImagePreview" class="absolute bottom-2 right-2 rounded-md bg-gray-900/75 px-2 py-1 text-[11px] font-medium text-white">Change</span>
                                    <input @change="previewImage($event, 'main')" id="main_image" name="main_image" type="file" accept="image/png,image/jpeg" class="hidden">
                                </label>
                            </div>

                            <div>
                                <p class="mb-2 text-xs font-medium text-gray-600 dark:text-gray-300">Thumbnail</p>
                                <label for="thumbnail_image" class="group relative flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 transition hover:border-primary-500 hover:bg-primary-50 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:border-primary-500 dark:hover:bg-gray-700">
                                    <img x-show="thumbnailImagePreview" :src="thumbnailImagePreview" class="h-full w-full object-cover" alt="Thumbnail image preview">
                                    <div x-show="!thumbnailImagePreview" class="p-2 text-center">
                                        <svg class="mx-auto mb-1.5 h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                                        <p class="text-[11px] font-semibold leading-4 text-gray-700 dark:text-gray-200">Add thumbnail</p>
                                    </div>
                                    <span x-show="thumbnailImagePreview" class="absolute bottom-2 right-2 rounded-md bg-gray-900/75 px-2 py-1 text-[11px] font-medium text-white">Change</span>
                                    <input @change="previewImage($event, 'thumbnail')" id="thumbnail_image" name="thumbnail_image" type="file" accept="image/png,image/jpeg" class="hidden">
                                </label>
                            </div>
                        </div>

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
                            <div x-show="populatedAttributes.length === 0" class="py-4 text-center text-xs text-gray-500 dark:text-gray-400">
                                Added attributes will appear here.
                            </div>
                            <template x-for="attribute in populatedAttributes" :key="'preview-' + attribute.id">
                                <div class="flex items-center justify-between gap-4 py-2.5 text-sm">
                                    <dt class="text-gray-500 dark:text-gray-400" x-text="attribute.name"></dt>
                                    <dd class="text-right font-medium text-gray-900 dark:text-white" x-text="attribute.value || '—'"></dd>
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
    window.productCreateForm = function (categories, sampleAttributes) {
        return {
            categories,
            availableAttributes: [...sampleAttributes],
            attributeRows: [],
            maxAttributes: 8,
            nextAttributeId: 1,
            selectedAttributeName: '',
            selectedAttributeValue: '',
            creatingAttribute: false,
            newAttributeName: '',
            attributeError: '',
            mainImagePreview: '',
            thumbnailImagePreview: '',
            notification: '',
            notificationTimer: null,
            form: {
                productCode: '2000000602110',
                name: 'IM Dayuan Auto Pressure Pump ADGP370B 370W (0.5HP)',
                brand: 'IM Dayuan',
                model: 'ADGP-370B',
                country: 'China',
                status: 'Active',
                category: 'water-pump',
                websiteUrl: 'https://pro1globalhomecenter.com/product/2000000602110',
                description: 'ရေအားကောင်းစေရန် အသုံးပြုနိုင်ပါသည်။ အရည်အသွေးကောင်းမွန်သော ပစ္စည်းဖြစ်ပြီး အိမ်သုံးရေတင်စနစ်များအတွက် သင့်လျော်ပါသည်။'
            },
            get currentCategory() {
                return this.categories[this.form.category];
            },
            get populatedAttributes() {
                return this.attributeRows.filter((attribute) => attribute.name.trim() || attribute.value.trim());
            },
            get hasReachedAttributeLimit() {
                return this.attributeRows.length >= this.maxAttributes;
            },
            get canAddSelectedAttribute() {
                return !this.hasReachedAttributeLimit
                    && this.selectedAttributeName
                    && this.selectedAttributeName !== '__create__'
                    && !this.isAttributeSelected(this.selectedAttributeName);
            },
            normalizeAttributeName(name) {
                return (name || '').trim().replace(/\s+/g, ' ').toLocaleLowerCase();
            },
            isAttributeSelected(name) {
                return this.isAttributeSelectedExcept(name);
            },
            isAttributeSelectedExcept(name, currentId = null) {
                const normalizedName = this.normalizeAttributeName(name);
                return this.attributeRows.some((attribute) => {
                    return attribute.id !== currentId && this.normalizeAttributeName(attribute.name) === normalizedName;
                });
            },
            handleAttributePickerChange() {
                this.attributeError = '';

                if (this.selectedAttributeName === '__create__') {
                    this.creatingAttribute = true;
                    this.selectedAttributeName = '';
                    this.$nextTick(() => document.getElementById('new_attribute_name')?.focus());
                    return;
                }

                this.creatingAttribute = false;
            },
            createAttribute() {
                const name = this.newAttributeName.trim().replace(/\s+/g, ' ');
                this.attributeError = '';

                if (!name) {
                    this.attributeError = 'Enter an attribute name.';
                    return;
                }

                const existingName = this.availableAttributes.find((attributeName) => this.normalizeAttributeName(attributeName) === this.normalizeAttributeName(name));
                const attributeName = existingName || name;

                if (this.isAttributeSelected(attributeName)) {
                    this.attributeError = 'This attribute has already been added to this product.';
                    return;
                }

                if (!existingName) {
                    this.availableAttributes.push(attributeName);
                }

                this.selectedAttributeName = attributeName;
                this.newAttributeName = '';
                this.creatingAttribute = false;
                this.$nextTick(() => document.getElementById('attribute_value')?.focus());
                this.showDemoMessage(`"${attributeName}" is ready to add.`);
            },
            cancelCreateAttribute() {
                this.creatingAttribute = false;
                this.newAttributeName = '';
                this.attributeError = '';
            },
            addSelectedAttribute() {
                this.attributeError = '';

                if (this.hasReachedAttributeLimit) {
                    this.attributeError = 'Maximum 8 attributes can be added.';
                    return;
                }

                if (!this.selectedAttributeName) {
                    this.attributeError = 'Choose an attribute first.';
                    return;
                }

                if (this.isAttributeSelected(this.selectedAttributeName)) {
                    this.attributeError = 'This attribute has already been added to this product.';
                    return;
                }

                this.attributeRows.push({
                    id: this.nextAttributeId++,
                    name: this.selectedAttributeName,
                    value: this.selectedAttributeValue.trim()
                });
                this.selectedAttributeName = '';
                this.selectedAttributeValue = '';
                this.creatingAttribute = false;
                this.$nextTick(() => document.getElementById('attribute_picker')?.focus());
            },
            removeAttribute(id) {
                this.attributeRows = this.attributeRows.filter((attribute) => attribute.id !== id);
                this.attributeError = '';
            },
            previewImage(event, type) {
                const file = event.target.files && event.target.files[0];
                if (!file) {
                    if (type === 'main') this.mainImagePreview = '';
                    if (type === 'thumbnail') this.thumbnailImagePreview = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (loadEvent) => {
                    if (type === 'main') this.mainImagePreview = loadEvent.target.result;
                    if (type === 'thumbnail') this.thumbnailImagePreview = loadEvent.target.result;
                };
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
