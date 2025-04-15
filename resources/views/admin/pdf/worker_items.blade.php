<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Worker Items List</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 20px;
        }
        .header {
            margin-bottom: 30px;
        }
        .header-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .table-wrapper {
            display: inline-block;
            width: 40%;
            vertical-align: top;
            margin-right: 9%;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-info">
            <div><strong>Worker Name:</strong> {{ $worker_name }}</div>
            <div><strong>Date:</strong> {{ $date }}</div>
        </div>
    </div>

    @php
        $chunkSize = 50;
        $chunkedItems = $items->chunk($chunkSize);
    @endphp

    @foreach($chunkedItems as $index => $chunk)
        @if($chunk->isNotEmpty())
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Color/Size</th>
                        <th>Description</th>
                        <th>Quantity</th>
                        <th>Order</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($chunk as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->color }} {{ $item->size ? ' / ' . $item->size : '' }}</td>
                        <td>{{ $item->description ?? 'N/A' }}</td>
                        <td>{{ $item->quantity ?? '0' }}</td>
                        <td>{{ $item->order->order_no ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($index % 2 == 1 && !$loop->last)
            <div style="clear: both;"></div> <!-- Clear inline-block floats -->
            <div style="page-break-after: always;"></div>
        @endif
        @endif
    @endforeach
</body>
</html>