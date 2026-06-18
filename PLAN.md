# Plan: Sidebar Link Fix for `laravel-user-consent`

## Problem

`registerSidebarLink()` in `ConsentServiceProvider` uses a fragile pattern:

```php
config([
    'account.navigation.extras' => [
        'Account' => [
            [
                'label' => __('user-consent::messages.settings'),
                'icon' => 'shield',
                'url' => url('/consent/preferences'),
                'sort' => 60,
            ],
        ],
    ],
]);
```

This manipulates `config()` at runtime without going through a registry API.
- Runs inside `$app->booted()` (not `packageBooted()`)
- Depends on the account package reading from `config('account.navigation')` during rendering
- No registry, no sort-order guarantees, no sealing
- The `try/catch` is now dead code (account is a hard dependency)

## Goal

Replace with a proper sidebar link injection API — **if one exists in `yezzmedia/laravel-account`** — or create one.

## Steps

1. **Explore account package** — does `laravel-account` already provide a sidebar link registry/method? If yes, use it.
2. **Create registry if missing** — if no registry exists, add a `SidebarLinkRegistry` or extend the existing `NavigationRegistry` in account.
3. **Refactor `registerSidebarLink()`** — call from `packageBooted()` directly (not `$app->booted()`), use the registry API.
4. **Remove dead code** — remove `try/catch` and `$app->booted()` wrapping.
5. **Write tests** — `SidebarLinkInjectionTest` verifying the link appears in the correct section with correct sort order.

## Dependencies

- `yezzmedia/laravel-account: ^0.1` (already in `require` after the bottom-bar fix)

## Effort

- ~30–60 min, depending on whether account already has a sidebar registry API.

---

## Done in this Task (Bottom Bar)

- `yezzmedia/laravel-dashboard` and `yezzmedia/laravel-account` moved from `suggest` to `require`
- `registerBottomBarLink()` now uses `BottomBarLinkRegistry::add()` in `packageBooted()`
- `registerSidebarLink()` still in `$app->booted()` — this PLAN covers the follow-up
- `tests/Feature/BottomBarLinkInjectionTest.php` — 3 tests, 6 assertions
