<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $client->judul }} - Data Clients</title>
    <link rel="icon" href="{{ asset('fav_icon.ico') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <h1 class="text-3xl font-bold text-gray-900">{{ $client->judul }}</h1>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @php
                $details = is_array($client->client_details) ? $client->client_details : [];
                $total = count($details);
                $dealCount = collect($details)->where('status', 'Deal')->count();
                $progressCount = collect($details)->where('status', 'Progress')->count();
                $cancelCount = collect($details)->where('status', 'Cancel')->count();
                $proposalCount = collect($details)->where('proposal', 'Yes')->count();
                $mockupCount = collect($details)->filter(fn($d) => !empty(trim($d['link_mockup'] ?? '')))->count();
            @endphp

            <!-- Statistics Overview -->
            <div class="flex gap-4 mb-8">
                <div class="flex-1 bg-white rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-gray-600 mb-1">Total Klien</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $total }}</div>
                </div>
                <div class="flex-1 bg-green-50 rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-green-700 mb-1">Deal</div>
                    <div class="text-2xl font-bold text-green-900">{{ $dealCount }}</div>
                    @if($total > 0)
                        <div class="text-xs text-green-600 mt-1">{{ round(($dealCount / $total) * 100, 1) }}%</div>
                    @endif
                </div>
                <div class="flex-1 bg-yellow-50 rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-yellow-700 mb-1">Progress</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ $progressCount }}</div>
                    @if($total > 0)
                        <div class="text-xs text-yellow-600 mt-1">{{ round(($progressCount / $total) * 100, 1) }}%</div>
                    @endif
                </div>
                <div class="flex-1 bg-red-50 rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-red-700 mb-1">Cancel</div>
                    <div class="text-2xl font-bold text-red-900">{{ $cancelCount }}</div>
                    @if($total > 0)
                        <div class="text-xs text-red-600 mt-1">{{ round(($cancelCount / $total) * 100, 1) }}%</div>
                    @endif
                </div>
                <div class="flex-1 bg-purple-50 rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-purple-700 mb-1">Proposal</div>
                    <div class="text-2xl font-bold text-purple-900">{{ $proposalCount }}</div>
                    @if($total > 0)
                        <div class="text-xs text-purple-600 mt-1">{{ round(($proposalCount / $total) * 100, 1) }}%</div>
                    @endif
                </div>
                <div class="flex-1 bg-indigo-50 rounded-lg shadow-sm p-4">
                    <div class="text-sm font-medium text-indigo-700 mb-1">Mockup</div>
                    <div class="text-2xl font-bold text-indigo-900">{{ $mockupCount }}</div>
                    @if($total > 0)
                        <div class="text-xs text-indigo-600 mt-1">{{ round(($mockupCount / $total) * 100, 1) }}%</div>
                    @endif
                </div>
            </div>

            <!-- Client Details Table -->
            @if(!empty($details))
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Detail Klien</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meeting Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mockup</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($details as $index => $detail)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail['company_name'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $detail['client_name'] ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if(!empty($detail['meeting_date']))
                                                {{ \Carbon\Carbon::parse($detail['meeting_date'])->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = $detail['status'] ?? 'Progress';
                                                $statusClasses = [
                                                    'Deal' => 'bg-green-100 text-green-800',
                                                    'Progress' => 'bg-yellow-100 text-yellow-800',
                                                    'Cancel' => 'bg-red-100 text-red-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if(($detail['proposal'] ?? 'No') === 'Yes')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    Yes
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    No
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(!empty(trim($detail['link_mockup'] ?? '')))
                                                <a href="{{ $detail['link_mockup'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline">
                                                    Lihat Mockup
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if(!empty($detail['notes']))
                                                <div class="max-w-xs truncate" title="{{ $detail['notes'] }}">
                                                    {{ $detail['notes'] }}
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada detail</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada detail klien yang tersimpan.</p>
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>
