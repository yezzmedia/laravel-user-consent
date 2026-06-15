@component('account::pages.surface', [
    'pageTitle' => $title,
    'pageDescription' => $description,
    'profileSummary' => $profileSummary,
    'navigation' => $navigation,
])
    <div class="space-y-6">
        <x-account::page-header :title="$title" :subtitle="$description" color="emerald">
            <x-slot:icon>
                <x-account::icon name="shield" class="h-5 w-5" />
            </x-slot:icon>
        </x-account::page-header>

        @if(session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900/50 dark:bg-emerald-900/20">
                <p class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</p>
            </div>
        @endif

        <x-account::account-card>
            @include('user-consent::pages._preferences-form')
        </x-account::account-card>
    </div>
@endcomponent
