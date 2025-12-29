<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Terkunci - {{ $invoice->title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <!-- Lock Card -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="from-black"></div>

                <div class="px-8 py-6">
                    <!-- Invoice Info -->
                    <div class="mb-6">
                        <h3 class="text-[24px] text-center font-semibold text-gray-700 mb-2">{{ $invoice->client_info }}</h3>
                        <p class="text-sm text-center text-gray-600 font-mono">{{ $invoice->number_invoice }}</p>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">
                                        {{ $errors->first('credentials') ?? $errors->first() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form action="{{ route('invoices.unlock', $invoice->slug) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
                                Username
                            </label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                value="{{ old('username') }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg transition-colors @error('username') @enderror"
                                placeholder="Masukkan username"
                                autofocus
                            >
                            @error('username')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Password
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg transition-colors @error('password') @enderror"
                                placeholder="Masukkan password"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button
                            type="submit"
                            class="cursor-pointer w-full flex justify-center items-center px-4 py-3 bg-[rgb(239,68,68)] hover:bg-black text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none">
                            Akses Invoice
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
