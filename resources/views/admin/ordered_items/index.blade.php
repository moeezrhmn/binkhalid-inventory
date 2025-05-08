@extends('layouts.app')

@section('header-scripts')

<link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

@endsection

@section('content')
@if(session('message'))
<div class="p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50" role="alert">
    <span class="font-medium">{{ session('message') }}</span>
</div>
@endif
@if(session('error'))
<div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
    <span class="font-medium">{{ session('error') }}</span>
</div>
@endif
<style>
    .dataTables_wrapper .dataTables_length select{
        min-width: 60px;
    }
</style>
<div class="bg-white shadow rounded-lg p-6">
    <h4 class="text-lg font-semibold mb-4">Ordered Items</h4>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <form class="mt-1 flex rounded-md shadow-sm" action="{{ route('admin.generate.worker.pdf') }}" method="post">
                    @csrf
                    <input type="hidden" name="selected_items" id="selected_items">
                    <input type="text" name="worker_name" required id="worker_name" class="flex-1 min-w-0 block px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter worker name">
                    <button type="submit" id="generate_pdf" disabled class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    Generate File
                </button>
            </form>
        </div>
    </div>



    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
            <select id="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="canceled">Canceled</option>
            </select>
        </div>
        <div>
            <label for="product_ids" class="block text-sm font-medium text-gray-700">Product</label>
            <select id="product_ids" multiple class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <option value="">All</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
            <input type="text" id="color" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Enter color">
        </div>
        <div>
            <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
            <div class="flex space-x-2">
                <input type="date" id="date_from" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                <input type="date" id="date_to" class="w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
        </div>
    </div>
    <table id="orderedItemsTable" class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"> <input type="checkbox" id="select_all"> </th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order No</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Status</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
            </tr>
        </thead>
    </table>
</div>

@endsection



@section('footer-scripts')
<script>
    var ordered_product_ids = []
    $(document).ready(function() {

        let selectedIds = [];
        let selectAll = false;
        let totalRecords = 0;

        let table = $('#orderedItemsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 200,
            lengthMenu: [[10, 50, 100, 200, 500, -1], [10, 50, 100, 200, 500, "All"]],
            ajax: {
                url: "{{ route('admin.order.ordered_items') }}",
                data: function(d) {
                    d.status = $('#status').val();
                    d.product_ids = $('#product_ids').val();
                    d.color = $('#color').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [
                { data: 'checkbox', name: 'checkbox', orderable: false, searchable: false },
                { data: 'order_no', name: 'order_no' },
                { data: 'customer_name', name: 'customer_name' },
                { data: 'product_name', name: 'product_name' },
                { data: 'quantity', name: 'quantity' },
                { data: 'price', name: 'price' },
                { data: 'color', name: 'color' },
                { data: 'status', name: 'status' },
                { data: 'assigned', name: 'assigned' },
                { data: 'created_at', name: 'created_at'}
            ],
            drawCallback: function(settings) {
                // Update total records from response
                ordered_product_ids = settings.json.ordered_product_ids || [];
                totalRecords = settings.json.totalRecords || 0;

                // Re-apply checkbox states
                $('.checkbox').each(function() {
                    const id = $(this).data('order-item-id');
                    $(this).prop('checked', selectAll || selectedIds.includes(id));
                });

                // Update selected count display
                updateSelectedCount();
            }
        });

        // Filter change handler
        $('#status, #product_ids, #color, #date_from, #date_to').change(function() {
            selectedIds = []; // Reset selections on filter change
            selectAll = false;
            $('#select_all').prop('checked', false);
            table.ajax.reload();
        });

        // $(document).on('click', '#generate_pdf', function(e) {
        //     e.preventDefault();
        //     if (selectedIds.length === 0 && !selectAll) {
        //         alert('Please select at least one item or check "Select All".');
        //         return;
        //     }
        //     updateSelectedItems();
        //     $(this).closest('form').submit();
        // });

        // Enable/disable generate button and update selected count
        function updateGenerateButton() {
            console.log('update gen btn', selectAll)
            const checkedBoxes = selectAll ? ordered_product_ids.length : selectedIds.length;
            $('#generate_pdf').prop('disabled', checkedBoxes === 0);
            updateSelectedCount();
        }

        // Update hidden input with selected IDs or select-all flag
        function updateSelectedItems() {
            console.log(selectAll)
            if (selectAll) {
                $('#selected_items').val(JSON.stringify(ordered_product_ids)); 
            } else {
                $('#selected_items').val(JSON.stringify(selectedIds));
            }
        }

        // Update selected count display
        function updateSelectedCount() {
            const count = selectAll ? ordered_product_ids.length : selectedIds.length;
            // $('#selected_count').text(`Selected: ${count}`);
        }

        // Handle individual checkbox changes
        $(document).on('change', '.checkbox', function() {
            const id = $(this).data('order-item-id');
            if ($(this).is(':checked')) {
                if (!selectedIds.includes(id)) {
                    selectedIds.push(id);
                }
            } else {
                selectedIds = selectedIds.filter(item => item !== id);
                selectAll = false; // Unchecking any box disables select-all
                $('#select_all').prop('checked', false);
            }
            updateSelectedItems();
            updateGenerateButton();
        });

        // Handle select all checkbox
        $(document).on('change', '#select_all', function() {
            selectAll = $(this).is(':checked');
            console.log('select all in event ', selectAll)
            if (selectAll) {
                selectedIds = []; // Clear individual selections
                $('.checkbox').prop('checked', true);
            } else {
                $('.checkbox').prop('checked', false);
            }
            updateSelectedItems();
            updateGenerateButton();
        });
    });
</script>
@endsection
