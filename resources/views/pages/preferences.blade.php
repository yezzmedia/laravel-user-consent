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
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 py-12">
        <div class="max-w-2xl mx-auto px-4 space-y-6">

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ $title ?? __('user-consent::messages.preferences_title') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $description ?? __('user-consent::messages.preferences_description') }}
                    </p>
                </div>

                @include('user-consent::pages._preferences-form')
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-900/20">
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
