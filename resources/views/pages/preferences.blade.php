<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('user-consent::messages.preferences_title') }}</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950">
    <div class="min-h-screen py-12">
        <div class="max-w-2xl mx-auto px-4 space-y-6">

            <div class="border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="relative">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <div class="pl-6 pr-6 pt-6 pb-5">
                        <div class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </span>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $title ?? __('user-consent::messages.preferences_title') }}
                                </h1>
                                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500 leading-relaxed border-l-2 border-indigo-200 dark:border-indigo-800 pl-3">
                                    {{ $description ?? __('user-consent::messages.preferences_hero_text') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @include('user-consent::pages._preferences-form')
            </div>

            @if(session('success'))
                <div class="border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-900/20">
                    <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
                </div>
            @endif

            <div class="text-center">
                <a href="/" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition">
                    &larr; {{ __('user-consent::messages.back_to_site') }}
                </a>
            </div>

        </div>
    </div>
</body>
</html>
