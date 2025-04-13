<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Worker Items List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
        }

        .header-info {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-info div {
            display: inline-block;
            width: 45%;
        }

        .items-wrapper {
            width: 100%;
            margin-top: 20px;
        }

        .column {
            width: 49%;
            margin-top: 20px;
            display: inline-block;
            border: 1px solid #000;
        }

        .col-1 {
            float: top;
            float: left;
        }

        .col-2 {
            float: top;
            float: right;
        }

        .item-block {
            /* width: 100%; */
            padding: 5px 10px;
            /* margin-bottom: 10px; */
            /* box-sizing: border-box; */
            font-size: 17px;
        }

        .col-top {
            background-color: lightgray;
            height: 25px;
            margin-bottom: 10px;
            width: 100%;
            border-bottom: 1px solid #000;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <div class="header-info">
        <div><strong>Worker Name:</strong> {{ $worker_name }}</div>
        <div><strong>Date:</strong> {{ $date }}</div>
        <div><strong>Title:</strong> _____________</div>
    </div>

    @php
    $itemsPerPage = 20; // 20 items per page (10 per column)
    $itemsPerColumn = 10; // 10 items per column
    @endphp

    @foreach($items->chunk($itemsPerPage) as $pageChunk)
    <div class="items-wrapper">
        @php
        $columns = $pageChunk->chunk($itemsPerColumn); // Split into columns

        @endphp
        <!-- First Column -->
        <div class="column col-1">
            <div class="col-top"></div>
            @if($columns->has(0))
            @foreach($columns[0] as $item)
            <div class="item-block">
                {{ $item->product->sku }} {{ $item->description }} <strong>Qty: {{ $item->quantity }}</strong>
            </div>
            @endforeach
            @endif
        </div>

        <!-- Second Column -->
        @if($columns->has(1))
        <div class="column col-2">
            <div class="col-top"></div>
            @foreach($columns[1] as $item)
            <div class="item-block">
                {{ $item->product->sku }} {{ $item->description }} <strong>Qty: {{ $item->quantity }}</strong>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    @if (!$loop->last)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>

</html>