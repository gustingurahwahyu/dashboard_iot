<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Smart Inventory') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="relative min-h-screen overflow-hidden">
            <div class="absolute rounded-full opacity-50 pointer-events-none -top-24 right-10 h-80 w-80 bg-gradient-to-br from-emerald-200 via-teal-100 to-sky-200 blur-3xl"></div>
            <div class="absolute rounded-full opacity-50 pointer-events-none bottom-10 left-8 h-72 w-72 bg-gradient-to-br from-amber-100 via-rose-100 to-orange-200 blur-3xl"></div>

            <div class="relative flex flex-col items-center justify-center min-h-screen px-4 py-10">
                <div class="mb-8 text-center">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Smart Inventory</p>
                    <h1 class="mt-2 text-2xl font-semibold text-slate-900">RFID Control Dashboard</h1>
                    <p class="text-sm text-slate-500">Masuk untuk mengakses sistem inventaris.</p>
                </div>

                <div class="w-full max-w-md p-6 bg-white border shadow-sm rounded-3xl border-slate-200">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
