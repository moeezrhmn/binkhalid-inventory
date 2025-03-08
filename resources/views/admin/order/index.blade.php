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
@endsection


@section('footer-scripts')



<script>
    $(function() {
        $('#order-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.order.index') }}",
            columns: [{
                    data: 'order_no',
                    name: 'order_no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'customer',
                    name: 'customer'
                },
                {
                    data: 'shipping_address',
                    name: 'shipping_address'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'total',
                    name: 'total'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
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
    });
</script>


@endsection