<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In QR Code - {{ $machine->ma_thiet_bi }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .ticket {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            border: 2px solid #000;
            width: 300px;
        }
        .qr-code {
            margin: 20px 0;
        }
        .machine-code {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .machine-name {
            font-size: 16px;
            color: #555;
            margin-bottom: 20px;
        }
        .btn-print {
            margin-top: 30px;
            padding: 10px 20px;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        @media print {
            body {
                background: white;
                height: auto;
                display: block;
            }
            .ticket {
                box-shadow: none;
                margin: 0 auto;
                page-break-inside: avoid;
            }
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="ticket">
        <div class="machine-code">{{ $machine->ma_thiet_bi }}</div>
        <div class="machine-name">{{ $machine->ten_thiet_bi }}</div>
        
        <div class="qr-code">
            {!! $qrCode !!}
        </div>

        <div style="font-size: 12px; color: #888;">Quét để xem hồ sơ máy</div>
    </div>

    <button onclick="window.print()" class="btn-print">In Tem QR</button>

</body>
</html>
