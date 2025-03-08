@extends('layouts.app')
@section('header-scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<style>
    .item-subtotal:active{
        border: 0;
        outline: 0;
    }
</style>
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

<form action="{{ route('admin.order.store') }}" method="post">
    @csrf
    <div id="customer-hide-show-toggle">
        <div class="space-y-12">
            
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base/7 font-semibold text-gray-900">Customer Information</h2>
    
                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    
    
                    <div class="sm:col-span-3">
                        <label for="customer_name" class="block text-sm/6 font-medium text-gray-900">Customer Name <span class="text-red-600">*</span></label>
                        <div class="mt-2">
                            <input type="text" required name="customer_name" id="customer_name" placeholder="Abdul Rehman" value="{{ old('customer_name') }}" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                            
                        </div>
                    </div>
                    
    
                    <div class="sm:col-span-3">
                        <label for="customer_email" class="block text-sm/6 font-medium text-gray-900">Customer Email <span class="text-red-600">*</span></label>
                        <div class="mt-2">
                            <input type="text" required name="customer_email" id="customer_email" value="{{ old('customer_email') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                        </div>
                    </div>
    
    
                    <div class="sm:col-span-3">
                        <label for="customer_phone" class="block text-sm/6 font-medium text-gray-900">Customer Phone No <span class="text-red-600">*</span></label>
                        <div class="mt-2">
                            <input type="text" required name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                        </div>
                    </div>

                    <div class="sm:col-span-3">
                        <label for="status" class="block text-sm/6 font-medium text-gray-900">Order Status <span class="text-red-600">*</span></label>
                        <div class="mt-2">
                            <select name="status" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-100 sm:text-sm/6">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="completed">Completed</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="sm:col-span-3">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description <span class="text-red-600">*</span></label>
                        <textarea id="description" required name="description" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Street, City, Country">{{ isset($order) ? $order->description : old('description') }}</textarea>
                    </div>
    
    
                    
                    
    
                    <div class="sm:col-span-3">
                        <label for="shipping_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">shipping Adress: <span class="text-red-600">*</span></label>
                        <textarea id="shipping_address" required name="shipping_address" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Street, City, Country">{{ isset($order) ? $order->shipping_address : old('shipping_address') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <div id="order-hide-show-toggle">
        <h2 class="text-base/7 mt-6 font-semibold text-gray-900">Order Information</h2>

        <div class="space-y-12">
            <div class="p-6 bg-white border-b border-gray-200">
                <table id="items-table" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cancel</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">price</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- DataTable will populate this -->
                    </tbody>
                </table>
                <div class="flex flex-col justify-end align-bottom ml-auto" style="width: 100px; margin-left: auto; align-items: flex-end;">
                    <h2 class="text-base/4 mt-6 font-semibold text-black">Total <span class="text-red-600">*</span></h2>
                    <input type="text" readonly required id="grand-total" name="total" class="text-2">
                </div>
            </div>
        </div>

        <select id="product-dropdown" class="w-full">
            @foreach($products as $product)
            <option class="outer-main" data-id="{{$product->id}}" data-quantity="{{$product->quantity}}" data-price="{{$product->price}}" data-image="{{asset($product->image)}}" data-sku="{{$product->sku}}" data-desc="{{$product->description}}">{{$product->name}}</option>
            @endforeach
            
        </select>
        
    
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('admin.product.index') }}" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save
            </button>
        </div>
    </div>
</form>

@endsection
@section('footer-scripts')
{{-- <script>
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
</script> --}}

<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<script src="{{asset('assets/js/select2.min.js')}}"></script>


<script>
    $(document).ready(function() {
        var order_items = {};
        var orderIndex = 0;
    $('#product-dropdown').select2({
        templateResult: formatProduct,
        templateSelection: formatProductSelection
    });

    function formatProduct(product) {
        if (!product.id) {
            return product.text;
        }

        let image = $(product.element).data('image');
        let sku = $(product.element).data('sku');
        let desc = $(product.element).data('desc');
        let id = $(product.element).data('id');
        let price = $(product.element).data('price');
        let quantity = 1;
        
        return $(`
            <div class="flex items-center bg-white-100 p-2 space-x-3 select-item-option-inner">
                <img src="${image}" class="w-10 h-10 object-cover select-item-option-image rounded">
                <div>
                    <span class="text-gray-500 text-xs">${sku}</span><br>
                    <span class="font-semibold focus:text-black-900">${product.text}</span><br>
                    <span class="text-sm text-gray-600">${desc}</span>
                </div>
            </div>
        `);
    }

    function formatProductSelection(product) {
        return product.text;
    }

    // Listen for Select2 option selection
    $('#product-dropdown').on('select2:select', function(e) {
        let selectedData = e.params.data;
        let image = $(selectedData.element).data('image');
        let sku = $(selectedData.element).data('sku');
        let desc = $(selectedData.element).data('desc');
        let price = $(selectedData.element).data('price');
        let id = $(selectedData.element).data('id');
        let quantity = 1;

       


        

        // Append selected product to the table
        $('#items-table tbody').append(`
            <tr>
                <td class="px-6 py-3">
                    <button class="remove-btn px-3 py-1 rounded hover:bg-red-700">X</button>
                </td>
                <td><img src="${image}" class="w-10 h-10 rounded"></td>
                <td>
                    <small style="font-size: 11px; color: gray;">${sku}</small>
                    <div style="font-size: 1.3rem; font-weight: bold;">${selectedData.text}</div>
                    <div style="color: gray;">${desc}</div>
                </td>
                
                
                <td><input type="number" name="items[${orderIndex}][price]" class="border-0 outline-0 item-price" value="${price}" style="width: 100px; border: 0; outline: 0;"></td>
                <td><input type="number" name="items[${orderIndex}][quantity]" class="border-0 outline-0 item-quantity" value="1" min="1" style="width: 70px;"></td>
                <td style="width: 200px;"><textarea name="items[${orderIndex}][description]" class="border-0 outline-0 item-description" rows="3" style="border: 1px solid #71797E;"></textarea></td>
                <td style="width: 60px;">
                    <input type="text" class="item-subtotal" readonly name="items[${orderIndex}][subtotal]" value="${price}" style="width: 90px; display: block; margin: auto; border: 0;">
                </td>
                <input type="hidden" name="items[${orderIndex}][id]" value="${id}">
            </tr>
        `);
        orderIndex += 1;
        updateTotal();
    });
    $(document).on('input', '.item-quantity, .item-price', function() {
        let row = $(this).closest('tr');
        let price = parseFloat(row.find('.item-price').val()) || 0;
        let quantity = parseInt(row.find('.item-quantity').val()) || 1;

        let newSubTotal = (price * quantity).toFixed(2);
        row.find('.item-subtotal').val(""); // Update subtotal
        row.find('.item-subtotal').val(newSubTotal); // Update subtotal

        updateTotal(); // Recalculate grand total
    });

        let price = parseInt($(this).val()); // Get new quantity
        let quantity = $(this).closest('tr').find('.item-quantity').val(); // Get base price
        console.log(price);
        console.log(quantity);
        if (quantity < 1 || isNaN(quantity)) {
            quantity = 1; // Ensure quantity is at least 1
            quantity.val(1);
        }

        let newSubTotal = (price * quantity).toFixed(2); // Calculate new total
        $(this).closest('tr').find('.item-subtotal').val(`${newSubTotal}`); // Update price display
        updateTotal();
    
    // calculating the total
        function updateTotal() {
            let total = 0;
            $('.item-subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#grand-total').val(total.toFixed(2));
        }


    // Remove row when clicking the cancel button
    $(document).on('click', '.remove-btn', function() {
        $(this).closest('tr').remove();
        updateTotal();
    });

});
// 
    

</script>
<script>
    
</script>
@endsection