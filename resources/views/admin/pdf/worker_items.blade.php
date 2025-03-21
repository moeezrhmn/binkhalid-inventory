<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Worker Items List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-info">
            <div>
                <strong>Worker Name:</strong> {{ $worker_name }}
            </div>
            <div>
                <strong>Date:</strong> {{ $date }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order No</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Color</th>
                <th>Size</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item->order->order_no }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->color }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html> 