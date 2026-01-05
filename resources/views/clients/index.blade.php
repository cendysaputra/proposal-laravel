<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Clients</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Data Clients</h1>
                <p class="mt-1 text-sm text-gray-600">Daftar data klien yang masuk</p>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if($clients->isEmpty())
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data klien</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada data klien yang tersedia.</p>
                </div>
            @else
                <!-- Client Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($clients as $client)
                        @php
                            $details = is_array($client->client_details) ? $client->client_details : [];
                            $total = count($details);
                            $dealCount = collect($details)->where('status', 'Deal')->count();
                            $progressCount = collect($details)->where('status', 'Progress')->count();
                            $cancelCount = collect($details)->where('status', 'Cancel')->count();
                            $proposalCount = collect($details)->where('proposal', 'Yes')->count();
                            $mockupCount = collect($details)->filter(fn($d) => !empty(trim($d['link_mockup'] ?? '')))->count();
                        @endphp

                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="p-6">
                                <!-- Title -->
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $client->judul }}
                                    </h3>
                                </div>

                                <!-- Statistics -->
                                <div class="space-y-3 mb-4">
                                    <!-- Total Clients -->
                                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-700">Total Klien</span>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-sm font-semibold text-gray-900">
                                            {{ $total }}
                                        </span>
                                    </div>

                                    <!-- Status Breakdown -->
                                    <div class="grid grid-cols-2 gap-2">
                                        <!-- Deal -->
                                        <div class="flex items-center justify-between p-2 bg-green-50 rounded-lg">
                                            <span class="text-xs font-medium text-green-700">Deal</span>
                                            <span class="text-sm font-bold text-green-800">{{ $dealCount }}</span>
                                        </div>

                                        <!-- Progress -->
                                        <div class="flex items-center justify-between p-2 bg-yellow-50 rounded-lg">
                                            <span class="text-xs font-medium text-yellow-700">Progress</span>
                                            <span class="text-sm font-bold text-yellow-800">{{ $progressCount }}</span>
                                        </div>

                                        <!-- Cancel -->
                                        <div class="flex items-center justify-between p-2 bg-red-50 rounded-lg">
                                            <span class="text-xs font-medium text-red-700">Cancel</span>
                                            <span class="text-sm font-bold text-red-800">{{ $cancelCount }}</span>
                                        </div>

                                        <!-- Proposal -->
                                        <div class="flex items-center justify-between p-2 bg-purple-50 rounded-lg">
                                            <span class="text-xs font-medium text-purple-700">Proposal</span>
                                            <span class="text-sm font-bold text-purple-800">{{ $proposalCount }}</span>
                                        </div>
                                    </div>

                                    <!-- Additional Info -->
                                    <div class="flex items-center text-xs text-gray-600 pt-2">
                                        <svg class="w-4 h-4 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ $mockupCount }} Mockup</span>
                                    </div>
                                </div>

                                <!-- View Button -->
                                <a href="{{ route('clients.show', $client->slug) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $clients->links() }}
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
