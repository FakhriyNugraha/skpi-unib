<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKPI - Cetak Banyak</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:ital,wght@0,400;0,700;1,400&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background-color: #fff;
        }

        body {
            position: relative;
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background-color: #fff;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 400px;
            height: auto;
            pointer-events: none;
        }

        @media print {
            .watermark {
                position: fixed;
                top: 50vh;
                left: 50vw;
                transform: translate(-50%, -50%);
                opacity: 0.1;
                z-index: -1;
                width: 400px;
                height: auto;
                pointer-events: none;
            }
        }

        .container {
            width: 21cm;
            margin: 0 auto;
            padding: 1.5cm;
        }

        .logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .logo img {
            height: 120px;
            width: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 8px;
        }

        .header h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .header h2 {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .title {
            text-align: center;
            margin-bottom: 18px;
        }

        .title h3 {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .title h4 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .title .number {
            font-size: 10.5pt;
            margin-bottom: 6px;
        }

        .title p {
            font-size: 10pt;
            margin-bottom: 3px;
        }

        .item-italic {
            font-style: italic;
        }

        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            border-bottom: 1.5px solid #000;
            padding-bottom: 2px;
        }

        .section-title span {
            font-weight: normal;
            font-style: italic;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-label {
            font-weight: bold;
        }

        .item-value {
            margin-left: 5px;
        }

        .two-column {
            display: flex;
            justify-content: space-between;
        }

        .column {
            width: 48%;
        }

        .learning-outcomes {
            margin-top: 10px;
        }

        .learning-category {
            margin-bottom: 10px;
        }

        .learning-category-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .learning-list {
            list-style-type: lower-alpha;
            margin-left: 20px;
        }

        .learning-list li {
            margin-bottom: 5px;
        }

        .additional-info {
            margin-top: 15px;
        }

        .additional-list {
            list-style-type: lower-alpha;
            margin-left: 20px;
        }

        .additional-list li {
            margin-bottom: 5px;
        }

        .signature {
            text-align: right;
            margin-top: 30px;
        }

        .signature p {
            margin-bottom: 5px;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 50px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 9pt;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .container {
                width: 100%;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none;
            }

            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <!-- Tombol Cetak -->
    <div class="no-print" style="position: fixed; top: 20px; right: 30px; z-index: 900;">
        <button onclick="window.print()" style="background-color: #1e3a8a; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: Arial, sans-serif; font-size: 14px; font-weight: 500; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); transition: all 0.2s ease-in-out;">
            Cetak Semua SKPI
        </button>
    </div>

    @foreach($skpis as $index => $skpi)
        @include('skpi._print-body', ['skpi' => $skpi])

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>