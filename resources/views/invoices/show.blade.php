<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->title }} - Invoice</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
</head>
<body class="bg-[#F5F5F5]">
    <div class="min-h-screen py-12">
        <!-- Invoice Container -->
        <div class="max-w-300 mx-auto px-4 sm:px-6">
            <div class="bg-white shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="px-4 md:px-8 lg:px-12 pt-8 md:pt-8 lg:pt-12 pb-0">
                    <div class="flex justify-between items-start">
                        <!-- Brand Logo -->
                        <div>
                            @if($invoice->brand)
                                <img src="{{ asset('images/' . $invoice->brand . '.png') }}" alt="Brand Logo" class="h-16 object-contain w-60">
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div class="px-4 md:px-8 lg:px-12 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 items-end">
                        <!-- Left Column -->
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 mb-2">Kepada Yth,</h3>
                            <div class="text-gray-900 whitespace-pre-line">{{ $invoice->client_info }}</div>
                        </div>

                        <!-- Right Column -->
                        <div class="md:text-right">
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-[#00425880] mb-2">Tanggal Invoice</h3>
                                <p class="text-lg text-gray-900">{{ $invoice->invoice_date->format('d F Y') }}</p>
                            </div>

                            <div>
                                 <h3 class="text-sm font-semibold text-[#00425880] mb-2">Invoice Number</h3>
                                 <p class="text-lg font-mono text-gray-900 mb-4">{{ $invoice->number_invoice }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    @if($invoice->item_details && is_array($invoice->item_details) && count($invoice->item_details) > 0)
                        <div class="mt-16 mb-16">
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead class="bg-[#004258]">
                                        <tr>
                                            <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Items</th>
                                            <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider">Price</th>
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
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item['items'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900 text-left">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-[#FBCB7B]">
                                        <tr>
                                            <td class="px-4 py-4 text-sm font-bold text-gray-900 text-left">TOTAL</td>
                                            <td class="px-4 py-4 text-sm font-bold text-gray-900 text-left">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Custom Item Details -->
                    @if($invoice->custom_item_details)
                        <div class="mt-16 mb-16">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Info</h3>
                            <div class="prose max-w-none text-gray-700">
                                {!! \Illuminate\Support\Str::markdown($invoice->custom_item_details) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Additional Info -->
                    @if($invoice->additional_info)
                        <div class="mt-16 mb-16">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Tambahan</h3>
                            <div class="prose max-w-none text-gray-700">
                                {!! \Illuminate\Support\Str::markdown($invoice->additional_info) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Prepared By -->
                    @if($invoice->prepared_by)
                        <div class="mt-12 pt-8 border-t border-gray-200">
                            <p class="text-sm text-gray-600">Disiapkan oleh:</p>
                            <p class="text-lg font-semibold text-gray-900 mt-1">{{ $invoice->prepared_by }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
