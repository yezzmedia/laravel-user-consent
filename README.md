<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/yezzmedia/.github/main/profile/yezzmedia-dark.svg">
    <img src="https://raw.githubusercontent.com/yezzmedia/.github/main/profile/yezzmedia-light.svg" alt="Yezz Media" height="40">
  </picture>
</p>

<p align="center">
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-consent"><img src="https://img.shields.io/packagist/v/yezzmedia/laravel-user-consent?style=flat-square" alt="Latest Version"></a>
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-consent"><img src="https://img.shields.io/packagist/php-v/yezzmedia/laravel-user-consent?style=flat-square" alt="PHP Version"></a>
  <a href="https://packagist.org/packages/yezzmedia/laravel-user-consent"><img src="https://img.shields.io/packagist/l/yezzmedia/laravel-user-consent?style=flat-square" alt="License"></a>
</p>

---

# Laravel User &middot; Consent

`yezzmedia/laravel-user-consent` provides cookie consent management, consent-aware analytics gating, and a user-facing preferences page for the Yezz Media platform.

It injects consent banners into the dashboard bottom-bar, persists decisions per session, and integrates with the account sidebar for preference management.

## Version

Current release: `0.2.0`

## Requirements

- PHP `^8.5`
- Laravel `^13.0` components
- `spatie/laravel-package-tools ^1.93`
- `yezzmedia/laravel-foundation ^0.2`
- `yezzmedia/laravel-account ^0.2`
- `yezzmedia/laravel-dashboard ^0.2`

## Installation

```bash
composer require yezzmedia/laravel-user-consent
```

## What The Package Provides

### Consent Banner

A cookie consent banner injected into the dashboard bottom-bar via `BottomBarLinkRegistry`, with accept/reject buttons and a link to the preferences page.

### Consent Persistence

Decisions are persisted per session with configurable consent versioning. When consent definitions change, users are re-prompted.

### Preferences Page

The `/consent/preferences` route renders a user-facing preferences page with:

- Toggle controls per consent category
- Auto-renders in the account layout when a default Filament panel is configured
- Falls back to a standalone layout with full Tailwind CSS support

### Consent-Aware Gating

Integrates with `laravel-ops-analytics` to gate tracker dispatch on active consent decisions.

### Install Steps

Foundation-aligned install step creates the consent tables migration and ensures the consent store is ready.

### Account Integration

Registers navigation items in the account sidebar for consent preferences access.

## Development

```bash
composer test
composer analyse
composer format
```

## License

MIT
