@php
    $requiredCats = [];
    $optionalCats = [];

    foreach ($categories as $cat) {
        if ($cat['is_required']) {
            $requiredCats[] = $cat;
        } else {
            $optionalCats[] = $cat;
        }
    }
@endphp

<div id="preferences-app">
    <div id="pref-flash" class="mx-4 sm:mx-6 mb-2" style="display:none"></div>

    <div class="divide-y divide-gray-200 dark:divide-gray-800">
        @if($requiredCats)
            <div class="p-4 sm:p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    {{ __('user-consent::messages.required_heading') }}
                </h2>
                <div class="space-y-3">
                    @foreach($requiredCats as $cat)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-900/30">
                            <span class="mt-1 flex-shrink-0 w-5 h-5 rounded border-2 border-indigo-300 dark:border-indigo-600 bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center" aria-hidden="true">
                                <svg class="w-3 h-3 text-indigo-500 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $cat['label'] }}
                                    <span class="inline-flex items-center gap-1 ml-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-100 dark:bg-indigo-900/50 px-2 py-0.5 rounded-full">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9-6a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        {{ __('user-consent::messages.always_active') }}
                                    </span>
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $cat['description'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    <p class="text-xs text-gray-400 dark:text-gray-500 italic mt-2">
                        {{ __('user-consent::messages.required_explanation') }}
                    </p>
                </div>
            </div>
        @endif

        @if($optionalCats)
            <div class="p-4 sm:p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">
                    {{ __('user-consent::messages.optional_heading') }}
                </h2>
                <div class="space-y-4" id="pref-categories">
                    @foreach($optionalCats as $cat)
                        <label class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition" data-cat-slug="{{ $cat['slug'] }}">
                            <input
                                type="checkbox"
                                onchange="prefToggle('{{ $cat['slug'] }}', this.checked)"
                                {{ $cat['granted'] ?? false ? 'checked' : '' }}
                                class="cb-pref-input mt-1 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500"
                            />
                            <div>
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $cat['label'] }}
                                </span>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $cat['description'] }}
                                </p>
                                @if($cat['granted'] !== null)
                                    <span class="cb-pref-badge inline-block mt-1 text-xs font-medium px-2 py-0.5 rounded-full
                                        {{ $cat['granted'] ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300' }}">
                                        {{ $cat['granted'] ? __('user-consent::messages.granted') : __('user-consent::messages.denied') }}
                                    </span>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="p-4 sm:p-6 flex flex-wrap gap-2">
            <button
                onclick="prefAcceptAll()"
                id="pref-btn-accept-all"
                class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ __('user-consent::messages.accept_all') }}
            </button>
            <button
                onclick="prefRejectAll()"
                id="pref-btn-reject-all"
                class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ __('user-consent::messages.reject_all') }}
            </button>
            <button
                onclick="prefSave()"
                id="pref-btn-save"
                class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ __('user-consent::messages.save_settings') }}
            </button>
            <span class="flex-1"></span>
            <button
                onclick="prefResetAll()"
                id="pref-btn-reset"
                class="px-4 py-2 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 text-sm font-medium rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ __('user-consent::messages.reset_all') }}
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var _prefDecisions = {};

    @json($categories).forEach(function (cat) {
        _prefDecisions[cat.slug] = cat.granted != null ? cat.granted : cat.is_required;
    });

    var flashEl = document.getElementById('pref-flash');
    var flashTimeout = null;

    function prefFlash(message, type) {
        if (flashTimeout) clearTimeout(flashTimeout);
        flashEl.textContent = message;
        flashEl.className = 'mx-4 sm:mx-6 mb-2 p-3 rounded-lg text-sm font-medium ' +
            (type === 'success'
                ? 'bg-emerald-50 text-emerald-800 border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:border-emerald-900/50'
                : 'bg-red-50 text-red-800 border border-red-200 dark:bg-red-900/20 dark:text-red-300 dark:border-red-900/50');
        flashEl.style.display = 'block';
        flashTimeout = setTimeout(function () {
            flashEl.style.display = 'none';
        }, 4000);
    }

    var buttons = ['pref-btn-accept-all', 'pref-btn-reject-all', 'pref-btn-save', 'pref-btn-reset'];

    function prefLoading(loading) {
        buttons.forEach(function (id) {
            var btn = document.getElementById(id);
            if (btn) btn.disabled = loading;
        });
    }

    function prefUpdateBadges() {
        document.querySelectorAll('#pref-categories .cb-pref-input').forEach(function (input) {
            var label = input.closest('[data-cat-slug]');
            if (!label) return;
            var slug = label.getAttribute('data-cat-slug');
            var badge = label.querySelector('.cb-pref-badge');
            if (!badge) return;
            var granted = _prefDecisions[slug];
            var isGreen = granted;
            badge.textContent = isGreen ? '{{ __('user-consent::messages.granted') }}' : '{{ __('user-consent::messages.denied') }}';
            badge.className = 'cb-pref-badge inline-block mt-1 text-xs font-medium px-2 py-0.5 rounded-full ' +
                (isGreen
                    ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                    : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300');
        });
    }

    window.prefToggle = function (slug, checked) {
        _prefDecisions[slug] = checked;
    };

    window.prefAcceptAll = function () {
        prefLoading(true);
        var req = new XMLHttpRequest();
        req.open('POST', '/__consent/grant-all', true);
        req.onload = function () {
            prefLoading(false);
            if (req.status >= 200 && req.status < 300) {
                document.querySelectorAll('#pref-categories .cb-pref-input').forEach(function (input) {
                    input.checked = true;
                    var label = input.closest('[data-cat-slug]');
                    if (label) {
                        _prefDecisions[label.getAttribute('data-cat-slug')] = true;
                    }
                });
                prefUpdateBadges();
                prefFlash('{{ __('user-consent::messages.preferences_saved') }}', 'success');
            }
        };
        req.onerror = function () {
            prefLoading(false);
            prefFlash('Request failed', 'error');
        };
        req.send();
    };

    window.prefRejectAll = function () {
        prefLoading(true);
        var req = new XMLHttpRequest();
        req.open('POST', '/__consent/revoke-all', true);
        req.onload = function () {
            prefLoading(false);
            if (req.status >= 200 && req.status < 300) {
                document.querySelectorAll('#pref-categories .cb-pref-input').forEach(function (input) {
                    input.checked = false;
                    var label = input.closest('[data-cat-slug]');
                    if (label) {
                        _prefDecisions[label.getAttribute('data-cat-slug')] = false;
                    }
                });
                prefUpdateBadges();
                prefFlash('{{ __('user-consent::messages.preferences_saved') }}', 'success');
            }
        };
        req.onerror = function () {
            prefLoading(false);
            prefFlash('Request failed', 'error');
        };
        req.send();
    };

    window.prefSave = function () {
        prefLoading(true);
        var decisions = {};
        document.querySelectorAll('#pref-categories .cb-pref-input').forEach(function (input) {
            var slug = input.closest('[data-cat-slug]').getAttribute('data-cat-slug');
            decisions[slug] = input.checked;
        });
        var req = new XMLHttpRequest();
        req.open('POST', '/__consent/save', true);
        req.setRequestHeader('Content-Type', 'application/json');
        req.onload = function () {
            prefLoading(false);
            if (req.status >= 200 && req.status < 300) {
                prefUpdateBadges();
                prefFlash('{{ __('user-consent::messages.preferences_saved') }}', 'success');
            } else {
                prefFlash('Save failed', 'error');
            }
        };
        req.onerror = function () {
            prefLoading(false);
            prefFlash('Save failed', 'error');
        };
        req.send(JSON.stringify({ decisions: decisions }));
    };

    window.prefResetAll = function () {
        if (!confirm('{{ __('user-consent::messages.reset_confirm') }}')) return;
        prefLoading(true);
        var req = new XMLHttpRequest();
        req.open('POST', '/__consent/reset', true);
        req.onload = function () {
            if (req.status >= 200 && req.status < 300) {
                location.reload();
            } else {
                prefLoading(false);
                prefFlash('Reset failed', 'error');
            }
        };
        req.onerror = function () {
            prefLoading(false);
            prefFlash('Reset failed', 'error');
        };
        req.send();
    };
})();
</script>
