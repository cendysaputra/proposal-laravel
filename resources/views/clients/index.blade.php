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
                                <div class="flex gap-2 mb-4">
                                    <!-- Progress -->
                                    <div class="flex-1 text-center p-2 bg-yellow-50 rounded-lg">
                                        <div class="text-xs text-yellow-700 mb-1">Progress</div>
                                        <div class="text-lg font-bold text-yellow-800">{{ $progressCount }}</div>
                                    </div>

                                    <!-- Proposal -->
                                    <div class="flex-1 text-center p-2 bg-purple-50 rounded-lg">
                                        <div class="text-xs text-purple-700 mb-1">Proposal</div>
                                        <div class="text-lg font-bold text-purple-800">{{ $proposalCount }}</div>
                                    </div>

                                    <!-- Mockup -->
                                    <div class="flex-1 text-center p-2 bg-indigo-50 rounded-lg">
                                        <div class="text-xs text-indigo-700 mb-1">Mockup</div>
                                        <div class="text-lg font-bold text-indigo-800">{{ $mockupCount }}</div>
                                    </div>

                                    <!-- Deal -->
                                    <div class="flex-1 text-center p-2 bg-green-50 rounded-lg">
                                        <div class="text-xs text-green-700 mb-1">Deal</div>
                                        <div class="text-lg font-bold text-green-800">{{ $dealCount }}</div>
                                    </div>

                                    <!-- Cancel -->
                                    <div class="flex-1 text-center p-2 bg-red-50 rounded-lg">
                                        <div class="text-xs text-red-700 mb-1">Cancel</div>
                                        <div class="text-lg font-bold text-red-800">{{ $cancelCount }}</div>
                                    </div>

                                    <!-- Total Klien -->
                                    <div class="flex-1 text-center p-2 bg-gray-50 rounded-lg">
                                        <div class="text-xs text-gray-600 mb-1">Total</div>
                                        <div class="text-lg font-bold text-gray-900">{{ $total }}</div>
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
