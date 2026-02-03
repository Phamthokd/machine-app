<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In QR Tổ {{ $department->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .print-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 columns for A4 */
            gap: 15px;
            max-width: 210mm; /* A4 width */
            margin: 0 auto;
            background: white;
            padding: 10mm;
            min-height: 297mm; /* Minimum A4 height */
        }
        .ticket {
            background: white;
            padding: 10px;
            border: 1px dashed #ccc;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 180px; /* Fixed height for consistency */
        }
        .machine-code {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .machine-name {
            font-size: 11px;
            color: #555;
            margin-bottom: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            max-width: 100%;
            height: 25px;
        }
        .qr-code svg {
            width: 80px;
            height: 80px;
        }
        .header-info {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 30px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .print-container {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                padding: 0;
                margin: 0;
                width: 100%;
                box-shadow: none;
            }
            .ticket {
                border: 1px solid #ddd; /* Solid border for printing */
                break-inside: avoid;
            }
            .btn-print, .header-info {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="header-info">
        <h3>Danh sách QR Code - Tổ: {{ $department->name }} ({{ count($machines) }} máy)</h3>
        <button onclick="window.print()" class="btn-print">In Tất Cả</button>
    </div>

    <div class="print-container">
        @foreach($machines as $machine)
        <div class="ticket">
            <div class="machine-code">{{ $machine->ma_thiet_bi }}</div>
            <div class="machine-name">{{ $machine->ten_thiet_bi }}</div>
            <div class="qr-code">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->generate($machine->ma_thiet_bi) !!}
            </div>
        </div>
        @endforeach
    </div>

</body>
</html>
