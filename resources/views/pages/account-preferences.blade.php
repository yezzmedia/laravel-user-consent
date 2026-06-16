@component('account::pages.surface', [
    'pageTitle' => $title,
    'pageDescription' => $description,
    'profileSummary' => $profileSummary,
    'navigation' => $navigation,
])
    <div class="space-y-5">
        <x-account::page-header :title="$title" color="emerald">
            <x-slot:icon>
                <x-account::icon name="shield" class="h-5 w-5" />
            </x-slot:icon>
            <x-slot:meta>
                <p class="text-xs text-gray-400 dark:text-gray-500 leading-relaxed border-l-2 border-emerald-300 dark:border-emerald-700 pl-3">
                    {{ $description ?? __('user-consent::messages.preferences_hero_text') }}
                </p>
            </x-slot:meta>
        </x-account::page-header>

        <div class="h-0.5 bg-emerald-100 dark:bg-emerald-900/50"></div>

        @if(session('success'))
            <div class="border-l-2 border-emerald-400 bg-emerald-50 p-4 dark:border-emerald-500 dark:bg-emerald-900/20">
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        <x-account::account-card>
            @include('user-consent::pages._preferences-form')
        </x-account::account-card>
    </div>
@endcomponent
