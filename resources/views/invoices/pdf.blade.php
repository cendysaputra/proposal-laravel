<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->title }} - Invoice</title>
    <style>
        @php
            $s = isset($scale) ? floatval($scale) : 1;
        @endphp

        @page {
            margin: {{ 40 * $s }}px {{ 20 * $s }}px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: white;
        }

        /* Force sans-serif everywhere */
        * {
            font-family: Helvetica, Arial, sans-serif !important;
        }

        h1, h2, h3, h4, h5, h6, p, div, span, td, th {
            font-family: Helvetica, Arial, sans-serif !important;
        }

        .page-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: {{ 48 * $s }}px;
            min-height: 100vh;
        }

        /* Header */
        .logo {
            max-width: {{ 200 * $s }}px;
            height: auto;
            margin-bottom: {{ 16 * $s }}px;
        }

        /* Invoice Details */
        .invoice-details {
            margin-bottom: {{ 24 * $s }}px;
        }

        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-details td {
            vertical-align: bottom;
            width: 50%;
        }

        .invoice-details td.right {
            text-align: right;
        }

        .label-gray {
            font-size: {{ 14 * $s }}px;
            font-weight: 600;
            color: #6B7280;
            margin-bottom: {{ 8 * $s }}px;
        }

        .label-red {
            font-size: {{ 14 * $s }}px;
            font-weight: 600;
            color: #E11D48;
            margin-bottom: {{ 8 * $s }}px;
        }

        .value {
            font-size: {{ 14 * $s }}px;
            color: #111827;
            margin-bottom: {{ 16 * $s }}px;
            line-height: 1.5;
        }

        .value.mono {
            font-family: 'Courier New', monospace;
        }

        /* Items Table */
        .items-table-wrapper {
            margin-top: {{ 64 * $s }}px;
            margin-bottom: {{ 64 * $s }}px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead {
            background-color: #E11D48;
        }

        .items-table thead th {
            padding: {{ 14 * $s }}px {{ 16 * $s }}px;
            text-align: left;
            font-size: {{ 12 * $s }}px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .items-table tbody td {
            padding: {{ 10 * $s }}px {{ 16 * $s }}px;
            font-size: {{ 13 * $s }}px;
            color: #111827;
            border-bottom: 1px solid #F3F4F6;
        }

        .items-table tfoot {
            background-color: #FFC7D3;
        }

        .items-table tfoot td {
            padding: {{ 14 * $s }}px {{ 16 * $s }}px;
            font-size: {{ 13 * $s }}px;
            font-weight: bold;
            color: #111827;
        }

        /* Sections */
        .section {
            margin-top: {{ 64 * $s }}px;
            margin-bottom: {{ 40 * $s }}px;
        }

        .section.custom-item {
            margin-top: {{ 40 * $s }}px;
            margin-bottom: {{ 64 * $s }}px;
        }

        .section.payment {
            margin-top: {{ 64 * $s }}px;
            margin-bottom: {{ 64 * $s }}px;
        }

        .section h3 {
            font-size: {{ 17 * $s }}px;
            font-weight: 600;
            color: #111827;
            margin-bottom: {{ 16 * $s }}px;
        }

        .section p,
        .section div {
            font-size: {{ 13 * $s }}px;
            color: #374151;
            line-height: 1.6;
        }

        .section.payment p {
            color: #4B5563;
            margin-bottom: {{ 8 * $s }}px;
        }

        .section.payment .payment-info {
            font-size: {{ 15 * $s }}px;
            font-weight: 500;
            color: #111827;
        }

        /* Prepared By */
        .prepared-by {
            text-align: right;
            padding-bottom: {{ 64 * $s }}px;
            border-bottom: 1px solid #E5E7EB;
        }

        .prepared-by .label {
            font-size: {{ 13 * $s }}px;
            color: #4B5563;
            margin-bottom: {{ 16 * $s }}px;
        }

        .prepared-by .signature {
            max-height: {{ 80 * $s }}px;
            width: auto;
            margin-bottom: {{ 8 * $s }}px;
        }

        .prepared-by .name,
        .prepared-by .position {
            font-size: {{ 15 * $s }}px;
            font-weight: 600;
            color: #111827;
            margin-bottom: {{ 4 * $s }}px;
        }

        /* Footer */
        .footer {
            margin-top: {{ 40 * $s }}px;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            vertical-align: bottom;
            width: 50%;
        }

        .footer h3 {
            font-size: {{ 15 * $s }}px;
            font-weight: bold;
            color: #E11D48;
            margin-bottom: {{ 8 * $s }}px;
        }

        .footer p {
            font-size: {{ 13 * $s }}px;
            color: #374151;
            margin-bottom: {{ 16 * $s }}px;
            line-height: 1.5;
        }

        .footer .contacts {
            font-size: {{ 13 * $s }}px;
            color: #374151;
        }

        .footer .copyright {
            text-align: right;
            font-size: {{ 13 * $s }}px;
            color: #6B7280;
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Logo -->
        @if($invoice->brand)
            <img src="{{ public_path('images/' . $invoice->brand . '.png') }}" alt="Brand Logo" class="logo">
        @endif

        <!-- Invoice Details -->
        <div class="invoice-details">
            <table>
                <tr>
                    <td>
                        <div class="label-gray">Kepada Yth,</div>
                        <div class="value">{{ $invoice->client_info }}</div>
                    </td>
                    <td class="right">
                        <div class="label-red">Tanggal Invoice</div>
                        <div class="value">{{ $invoice->invoice_date->format('d F Y') }}</div>
                        <div class="label-red">Invoice Number</div>
                        <div class="value mono">{{ $invoice->number_invoice }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Items Table -->
        @if($invoice->item_details && is_array($invoice->item_details) && count($invoice->item_details) > 0)
            <div class="items-table-wrapper">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @foreach($invoice->item_details as $item)
                            @php
                                $amount = ($item['qty'] ?? 0) * ($item['price'] ?? 0);
                                $total += $amount;
                            @endphp
                            <tr>
                                <td>{{ $item['items'] ?? '-' }}</td>
                                <td>Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        <!-- Additional Info -->
        @if($invoice->additional_info)
            <div class="section">
                <h3>Additional Info</h3>
                <div>{!! nl2br(e($invoice->additional_info)) !!}</div>
            </div>
        @endif

        <!-- Custom Item Details -->
        @if($invoice->custom_item_details)
            <div class="section custom-item">
                <h3>Informasi Tambahan</h3>
                <div>{!! nl2br(e($invoice->custom_item_details)) !!}</div>
            </div>
        @endif

        <!-- Detail Pembayaran -->
        @if($invoice->detail_pembayaran)
            <div class="section payment">
                <p>Pembayaran invoice dapat dilakukan via Transfer Bank ke:</p>
                <div class="payment-info">{!! nl2br(e($invoice->detail_pembayaran)) !!}</div>
            </div>
        @endif

        <!-- Prepared By -->
        @if($invoice->prepared_by)
            <div class="prepared-by">
                <div class="label">Prepared by</div>
                @if(file_exists(public_path('images/signature.png')))
                    <img src="{{ public_path('images/signature.png') }}" alt="Signature" class="signature">
                @endif
                <div class="name">{{ $invoice->prepared_by }}</div>
                @if($invoice->prepared_position)
                    <div class="position">{{ $invoice->prepared_position }}</div>
                @endif
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <table>
                <tr>
                    <td>
                        <h3>Administrasi Digital</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore.</p>
                        <div class="contacts">0800 0000 0000 | 0800 0000 0000 | admin@domain.com</div>
                    </td>
                    <td>
                        <div class="copyright">Copyright &copy; 2026 Administrasi Digital | All Right Reserved</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
