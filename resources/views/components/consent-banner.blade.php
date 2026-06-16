<style>
#consent-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: #fff;
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.1);
    color: #374151;
    font-family: system-ui, -apple-system, sans-serif;
    line-height: 1.5;
}

html.dark #consent-banner {
    background: #111827;
    border-top-color: #374151;
    color: #d1d5db;
}

#consent-banner *,
#consent-banner *::before,
#consent-banner *::after {
    box-sizing: border-box;
}

.cb-wrap {
    max-width: 56rem;
    margin: 0 auto;
    padding: 1rem;
}

.cb-text {
    font-size: 0.875rem;
    line-height: 1.5;
    color: #4b5563;
}

html.dark .cb-text {
    color: #d1d5db;
}

.cb-categories {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.cb-cat {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    font-weight: 500;
    border-radius: 0.25rem;
    border: 1.5px solid #d1d5db;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.15s ease;
    user-select: none;
}

html.dark .cb-cat {
    border-color: #4b5563;
    color: #9ca3af;
}

.cb-cat:hover {
    border-color: #818cf8;
    color: #4f46e5;
}

html.dark .cb-cat:hover {
    border-color: #6366f1;
    color: #a5b4fc;
}

.cb-cat.cb-cat-on {
    border-color: #4f46e5;
    background: #eef2ff;
    color: #4338ca;
}

html.dark .cb-cat.cb-cat-on {
    border-color: #6366f1;
    background: #1e1b4b;
    color: #a5b4fc;
}

.cb-cat.cb-cat-off {
    opacity: 0.6;
}

.cb-cat.cb-cat-off:hover {
    opacity: 1;
}

.cb-cat-required {
    cursor: default;
    opacity: 1 !important;
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #4338ca;
}

html.dark .cb-cat-required {
    border-color: #3730a3;
    background: #1e1b4b;
    color: #a5b4fc;
}

.cb-cat-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: 0.125rem;
    background: currentColor;
    opacity: 0.5;
    transition: opacity 0.15s ease;
}

.cb-cat-on .cb-cat-dot,
.cb-cat-required .cb-cat-dot {
    opacity: 1;
}

.cb-cat-label {
    font-weight: 500;
}

.cb-cat-badge {
    font-size: 0.6875rem;
    font-weight: 500;
    color: #6366f1;
    margin-left: 0.125rem;
}

html.dark .cb-cat-badge {
    color: #a5b4fc;
}

.cb-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.75rem;
}

.cb-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.25;
    cursor: pointer;
    border: 1px solid;
    outline: none;
    background: transparent;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
}

.cb-btn:focus-visible {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

.cb-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.cb-btn-primary {
    color: #059669;
    border-color: #a7f3d0;
}

.cb-btn-primary:hover:not(:disabled) {
    background: #ecfdf5;
}

html.dark .cb-btn-primary {
    color: #6ee7b7;
    border-color: #065f46;
}

html.dark .cb-btn-primary:hover:not(:disabled) {
    background: #022c22;
}

.cb-btn-secondary {
    color: #dc2626;
    border-color: #fca5a5;
}

.cb-btn-secondary:hover:not(:disabled) {
    background: #fef2f2;
}

html.dark .cb-btn-secondary {
    color: #f87171;
    border-color: #7f1d1d;
}

html.dark .cb-btn-secondary:hover:not(:disabled) {
    background: #450a0a;
}

.cb-btn-outline {
    color: #6b7280;
    border-color: #d1d5db;
}

.cb-btn-outline:hover:not(:disabled) {
    background: #f9fafb;
}

html.dark .cb-btn-outline {
    color: #9ca3af;
    border-color: #4b5563;
}

html.dark .cb-btn-outline:hover:not(:disabled) {
    background: #1f2937;
}
</style>

<div id="consent-banner">
    <div class="cb-wrap">
        <p class="cb-text">{{ __('user-consent::messages.banner_text') }}</p>
        <div id="consent-categories" class="cb-categories"></div>
        <div class="cb-actions">
            <button onclick="bannerAcceptAll()" id="banner-btn-accept-all" class="cb-btn cb-btn-primary">{{ __('user-consent::messages.accept_all') }}</button>
            <button onclick="bannerRejectAll()" id="banner-btn-reject-all" class="cb-btn cb-btn-secondary">{{ __('user-consent::messages.reject_all') }}</button>
            <a href="{{ route('consent.preferences') }}" class="cb-btn cb-btn-outline">{{ __('user-consent::messages.settings') }}</a>
        </div>
    </div>
</div>

<script>
(function () {
    var _bannerDecisions = {};
    var bannerEl = document.getElementById('consent-banner');
    var categoriesEl = document.getElementById('consent-categories');

    var actionButtons = ['banner-btn-accept-all', 'banner-btn-reject-all'];

    function setButtonsDisabled(disabled) {
        actionButtons.forEach(function (id) {
            var btn = document.getElementById(id);
            if (btn) btn.disabled = disabled;
        });
    }

    function escHtml(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    function renderCategories(categories) {
        categoriesEl.innerHTML = '';
        _bannerDecisions = {};

        function buildDecisions() {
            var d = {};
            categoriesEl.querySelectorAll('.cb-cat:not(.cb-cat-required)').forEach(function (pill) {
                var slug = pill.getAttribute('data-cat-slug');
                d[slug] = pill.classList.contains('cb-cat-on');
            });
            return d;
        }

        categories.forEach(function (cat) {
            var decided = cat.granted != null ? cat.granted : true;
            _bannerDecisions[cat.slug] = decided;

            var pill = document.createElement('span');
            var isOn = decided;
            var isRequired = cat.is_required;

            pill.className = 'cb-cat' +
                (isRequired ? ' cb-cat-required' : '') +
                (isOn ? ' cb-cat-on' : ' cb-cat-off');
            pill.setAttribute('data-cat-slug', cat.slug);

            var dot = document.createElement('span');
            dot.className = 'cb-cat-dot';
            pill.appendChild(dot);

            var lbl = document.createElement('span');
            lbl.className = 'cb-cat-label';
            lbl.textContent = cat.label;
            pill.appendChild(lbl);

            if (isRequired) {
                var badge = document.createElement('span');
                badge.className = 'cb-cat-badge';
                badge.textContent = '{{ __('user-consent::messages.always_active') }}';
                pill.appendChild(badge);
            } else {
                pill.tabIndex = 0;
                pill.setAttribute('role', 'switch');
                pill.setAttribute('aria-checked', isOn ? 'true' : 'false');

                pill.onclick = function () {
                    var currentlyOn = pill.classList.contains('cb-cat-on');
                    var newOn = !currentlyOn;

                    pill.classList.toggle('cb-cat-on', newOn);
                    pill.classList.toggle('cb-cat-off', !newOn);
                    pill.setAttribute('aria-checked', newOn ? 'true' : 'false');
                    _bannerDecisions[cat.slug] = newOn;

                    var decisions = buildDecisions();
                    var r = new XMLHttpRequest();
                    r.open('POST', '/__consent/save', true);
                    r.setRequestHeader('Content-Type', 'application/json');
                    r.send(JSON.stringify({ decisions: decisions }));
                };

                pill.onkeydown = function (e) {
                    if (e.key === ' ' || e.key === 'Enter') {
                        e.preventDefault();
                        pill.onclick();
                    }
                };
            }

            categoriesEl.appendChild(pill);
        });
    }

    var req = new XMLHttpRequest();
    req.open('GET', '/__consent/state', true);
    req.onload = function () {
        try {
            var data = JSON.parse(req.responseText);
            var cats = data.categories || {};
            renderCategories(Object.values(cats));
            if (data.all_decided) {
                bannerEl.style.display = 'none';
            }
        } catch (e) {
            console.error('[CONSENT] parse error:', e);
        }
    };
    req.onerror = function () {
        console.error('[CONSENT] XHR failed');
    };
    req.send();

    function updatePills(allOn) {
        categoriesEl.querySelectorAll('.cb-cat:not(.cb-cat-required)').forEach(function (pill) {
            pill.classList.toggle('cb-cat-on', allOn);
            pill.classList.toggle('cb-cat-off', !allOn);
            pill.setAttribute('aria-checked', allOn ? 'true' : 'false');
            var slug = pill.getAttribute('data-cat-slug');
            _bannerDecisions[slug] = allOn;
        });
    }

    window.bannerAcceptAll = function () {
        setButtonsDisabled(true);
        var decisions = {};
        categoriesEl.querySelectorAll('.cb-cat').forEach(function (pill) {
            var slug = pill.getAttribute('data-cat-slug');
            decisions[slug] = pill.classList.contains('cb-cat-on');
        });
        var r = new XMLHttpRequest();
        r.open('POST', '/__consent/save', true);
        r.setRequestHeader('Content-Type', 'application/json');
        r.onload = function () {
            setButtonsDisabled(false);
            if (r.status >= 200 && r.status < 300) {
                bannerEl.style.display = 'none';
                window.dispatchEvent(new CustomEvent('consent-updated'));
            }
        };
        r.onerror = function () { setButtonsDisabled(false); };
        r.send(JSON.stringify({ decisions: decisions }));
    };

    window.bannerRejectAll = function () {
        setButtonsDisabled(true);
        updatePills(false);
        var r = new XMLHttpRequest();
        r.open('POST', '/__consent/revoke-all', true);
        r.onload = function () {
            setButtonsDisabled(false);
            if (r.status >= 200 && r.status < 300) {
                bannerEl.style.display = 'none';
                window.dispatchEvent(new CustomEvent('consent-updated'));
            }
        };
        r.onerror = function () { setButtonsDisabled(false); };
        r.send();
    };
})();
</script>
