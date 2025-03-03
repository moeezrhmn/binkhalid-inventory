@extends('layouts.app')
@section('header-scripts')

@endsection
@section('content')

@if(session('message'))
<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
    <span class="font-medium">{{ session('message') }}</span>
</div>
@endif
@if(session('error'))

<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
    <span class="font-medium">{{ session('error') }}</span>
</div>
@endif

@if ($errors->any())
    <div class="flex p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
    <svg class="shrink-0 inline w-4 h-4 me-3 mt-[2px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
    </svg>
    <span class="sr-only">Danger</span>
    <div>
        <span class="font-medium">Validation errors:</span>
        <ul class="mt-1.5 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    </div>
@endif

<form action="{{ isset($product) ? route('admin.product.update', $product->id) : route('admin.product.store') }}" method="post" enctype="multipart/form-data">
    <div class="space-y-12">
        @csrf
        @if(isset($product))
        @method('PUT')
        @endif
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base/7 font-semibold text-gray-900">{{ isset($product) ? 'Edit Product' : 'Product Information' }}</h2>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="name" class="block text-sm/6 font-medium text-gray-900">Name</label>
                    <div class="mt-2">
                        <input type="text" name="name" id="name" value="{{ isset($product) ? $product->name : old('name') }}" autocomplete="given-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="sku" class="block text-sm/6 font-medium text-gray-900">SKU (code)</label>
                    <div class="mt-2">
                        <input type="text" name="sku" id="sku" placeholder="00001" value="{{ isset($product) ? $product->sku : old('sku') }}" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="color" class="block text-sm/6 font-medium text-gray-900">Color</label>
                    <div class="mt-2">
                        <input type="text" name="color" id="color" value="{{ isset($product) ? $product->color : old('color') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="size" class="block text-sm/6 font-medium text-gray-900">Size</label>
                    <div class="mt-2">
                        <input type="text" name="size" id="size" placeholder="Large" value="{{ isset($product) ? $product->size : old('size') }}" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <label for="price" class="block text-sm/6 font-medium text-gray-900">Price</label>
                    <div class="mt-2">
                        <input type="text" name="price" id="price" placeholder="Rs 200" value="{{ isset($product) ? $product->price : old('price') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                    </div>
                </div>

                <div class="sm:col-span-3">
                    <!-- Drag & Drop Area -->
                    <label id="drop-area" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-300 bg-white rounded-lg cursor-pointer hover:border-blue-500"
                        style="{{ isset($product) && $product->image ? 'background-image: url("' . asset($product->image) . '"); background-size: cover; background-position: center;' : '' }}">
                        <input type="file" id="file-input" name="image" class="hidden" />

                        @if(!isset($product) || !$product->image)
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16l-4-4m0 0l4-4m-4 4h16M3 12h18m-6 8v-16m-6 16v-16"></path>
                        </svg>

                        <p class="mt-2 text-sm text-gray-600">Drag & Drop or <span class="text-blue-500">Browse</span> Image</p>
                        <p class="mt-1 text-xs text-gray-400">Only JPG, PNG, GIF (Max 2MB)</p>
                        @endif
                    </label>
                </div>

                <div class="sm:col-span-3">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description:</label>
                    <textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Write your thoughts here...">{{ isset($product) ? $product->description : old('description') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-x-6">
        <a href="{{ route('admin.product.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
        <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            {{ isset($product) ? 'Update' : 'Save' }}
        </button>
    </div>
</form>

@endsection
@section('footer-scripts')
<script>
    const dropArea = document.getElementById("drop-area");
    const fileInput = document.getElementById("file-input");

    // Open file selector when clicking the drag area
    dropArea.addEventListener("click", () => {
        // fileInput.click();
    });

    // Handle file selection
    fileInput.addEventListener("change", handleFiles);

    dropArea.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropArea.classList.add("border-blue-500");
    });

    dropArea.addEventListener("dragleave", () => {
        dropArea.classList.remove("border-blue-500");
    });

    dropArea.addEventListener("drop", (e) => {
        e.preventDefault();
        dropArea.classList.remove("border-blue-500");
        fileInput.files = e.dataTransfer.files;
        handleFiles({
            target: {
                files: e.dataTransfer.files
            }
        });
    });

    function handleFiles(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                dropArea.style.backgroundImage = `url(${reader.result})`;
                dropArea.style.backgroundSize = "cover";
                dropArea.style.backgroundPosition = "center";
                // Hide the text and icon when an image is displayed
                dropArea.innerHTML = "";
            };
            reader.readAsDataURL(file);

            let fileInput = $("<input>")
                .attr("type", "file")
                .attr("name", "image")
                .prop("files", event.target.files) 
                .css("display", "none");
            $("form").append(fileInput);
        }
    }
</script>
@endsection