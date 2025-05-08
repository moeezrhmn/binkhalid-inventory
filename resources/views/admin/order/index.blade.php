@extends('layouts.app')


@section('header-scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="{{asset('assets/js/jquery.min.js')}}"></script>
<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<style>
    .hidden{
        display: none;
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
@error('order_item')
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
        <span class="font-medium">{{ $message }}</span>
    </div>
@enderror
<style>
    .dataTables_wrapper .dataTables_length select{
        width: 60px;
    }
</style>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-2 ">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Orders</h1>
            <a href="{{ route('admin.order.create') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Add New Order
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <table id="order-table" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order No</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shipping Address</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- DataTable will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>




<div id="order-details-modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="overflow-y-auto hidden fixed inset-0 z-50 flex justify-center items-center w-full h-full py-10  bg-[#0000008f] bg-opacity-50" >
    <div class="relative p-4 w-full max-w-4xl max-h-full">
        <div class="relative bg-white rounded-lg shadow-lg dark:bg-gray-800">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Order Details</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="order-details-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-6 space-y-4" id="order-details-content">
                <!-- Dynamic content will be injected here -->
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="order-details-modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('footer-scripts')



<script>
    $(function() {
        $('#order-table').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 100,
            lengthMenu: [10, 25, 50, 100, 200, 500],
            ajax: "{{ route('admin.order.index') }}",
            columns: [
                { data: 'order_no', name: 'order_no', searchable: true },
                { data: 'customer', name: 'customer_name', searchable: true },
                { data: 'shipping_address', name: 'shipping_address', searchable: true },
                { data: 'description', name: 'description', searchable: true },
                { data: 'total', name: 'total', searchable: false },
                { data: 'status', name: 'status', searchable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            drawCallback: function() {
                // Apply Tailwind classes to DataTable elements
                $('#products-table_wrapper .dataTables_length select').addClass('mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md');
                $('#products-table_wrapper .dataTables_filter input').addClass('mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md');
                $('#products-table_wrapper .dataTables_info').addClass('text-sm text-gray-700 py-2');
                $('#products-table_wrapper .dataTables_paginate').addClass('relative z-0 inline-flex rounded-md shadow-sm -space-x-px my-2');
                $('#products-table_wrapper .dataTables_paginate .paginate_button').addClass('relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50');
                $('#products-table_wrapper .dataTables_paginate .paginate_button.current').addClass('z-10 bg-indigo-50 border-indigo-500 text-indigo-600');
                $('#products-table_wrapper .dataTables_paginate .paginate_button.disabled').addClass('opacity-50 cursor-not-allowed');

                // Initialize delete buttons
                $('.delete-btn').on('click', function() {
                    if (confirm('Are you sure you want to delete this product?')) {
                        const productId = $(this).data('id');
                        // Add your delete logic here
                        // Example:
                        // $.ajax({
                        //     url: `/admin/products/${productId}`,
                        //     type: 'DELETE',
                        //     data: {
                        //         "_token": "{{ csrf_token() }}",
                        //     },
                        //     success: function() {
                        //         $('#products-table').DataTable().ajax.reload();
                        //     }
                        // });
                    }
                });
            }
        });



        $(document).on('click', '.view-order', function(e) {
        e.preventDefault();
        $('#order-details-content').html('Loading...');
        const orderId = $(this).data('id');
        var url = `{{ route("admin.order.details", ":id") }}`.replace(':id', orderId);
        // Fetch order details via AJAX
        $.ajax({
                url:url ,
                type: 'GET',
                success: function(response) {

                    let orderDetailsHtml = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Order No:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.order_no}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Customer Name:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.customer_name}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Customer Email:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.customer_email}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Customer Phone:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.customer_phone}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Shipping Address:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.shipping_address}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Description:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.description || 'N/A'}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Subtotal:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.subtotal}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Total:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.total}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Status:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.status}</span>
                        </div>
                        <div>
                            <strong class="text-gray-700 dark:text-gray-300">Created At:</strong>
                            <span class="text-gray-900 dark:text-gray-100">${response.created_at}</span>
                        </div>
                    </div>
                `;

                // Add order items table if items exist
                if (response.order_items && response.order_items.length > 0) {
                    orderDetailsHtml += `
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Items</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Color/Size</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    `;

                    response.order_items.forEach(item => {
                        orderDetailsHtml += `
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${item.product.name}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${item.price}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${item.quantity}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${item.color || 'N/A'} / ${item.size || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">${item.description || 'N/A'}</td>
                            </tr>
                        `;
                    });

                    orderDetailsHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    console.log(response)
                    orderDetailsHtml += `
                        <p class="text-gray-500 dark:text-gray-400">No order items found.</p>
                    `;
                }

                // Inject HTML into modal
                $('#order-details-content').html(orderDetailsHtml);

                    // Show the modal
                    $('#order-details-modal').removeClass('hidden');
                },
                error: function() {
                    alert('Failed to load order details.');
                }
            });
        });

        // Close modal
        $('[data-modal-hide="order-details-modal"]').on('click', function() {
            $('#order-details-modal').addClass('hidden');
        });
    });
</script>


@endsection