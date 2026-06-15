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
    border-radius: 0.5rem;
    cursor: pointer;
    border: none;
    outline: none;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
}

.cb-btn:focus-visible {
    outline: 2px solid #6366f1;
    outline-offset: 2px;
}

.cb-btn-primary {
    background: #4f46e5;
    color: #fff;
}

.cb-btn-primary:hover {
    background: #4338ca;
}

.cb-btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.cb-btn-secondary:hover {
    background: #d1d5db;
}

html.dark .cb-btn-secondary {
    background: #374151;
    color: #e5e7eb;
}

html.dark .cb-btn-secondary:hover {
    background: #4b5563;
}

.cb-btn-outline {
    background: transparent;
    color: #4b5563;
    border: 1px solid #d1d5db;
}

.cb-btn-outline:hover {
    background: #f9fafb;
}

html.dark .cb-btn-outline {
    color: #d1d5db;
    border-color: #4b5563;
}

html.dark .cb-btn-outline:hover {
    background: #1f2937;
}
</style>

<div id="consent-banner">
    <div class="cb-wrap">
        <p class="cb-text">{{ __('user-consent::messages.banner_text') }}</p>
        <div class="cb-actions">
            <button onclick="consentAcceptAll()" class="cb-btn cb-btn-primary">{{ __('user-consent::messages.accept_all') }}</button>
            <button onclick="consentRejectAll()" class="cb-btn cb-btn-secondary">{{ __('user-consent::messages.reject_all') }}</button>
            <a href="{{ route('consent.preferences') }}" class="cb-btn cb-btn-outline">{{ __('user-consent::messages.settings') }}</a>
        </div>
    </div>
</div>

<script>
(function initConsentBanner() {
    var req = new XMLHttpRequest();
    req.open('GET', '/__consent/state', true);
    req.onload = function () {
        try {
            var data = JSON.parse(req.responseText);
            if (data.all_decided) {
                document.getElementById('consent-banner').style.display = 'none';
            }
        } catch (e) {
            console.error('[CONSENT] parse error:', e);
        }
    };
    req.onerror = function () {
        console.error('[CONSENT] XHR failed');
    };
    req.send();
})();

function consentAcceptAll() {
    var req = new XMLHttpRequest();
    req.open('POST', '/__consent/grant-all', true);
    req.send();
    document.getElementById('consent-banner').style.display = 'none';
    window.dispatchEvent(new CustomEvent('consent-updated'));
}

function consentRejectAll() {
    var req = new XMLHttpRequest();
    req.open('POST', '/__consent/revoke-all', true);
    req.send();
    document.getElementById('consent-banner').style.display = 'none';
    window.dispatchEvent(new CustomEvent('consent-updated'));
}


</script>
