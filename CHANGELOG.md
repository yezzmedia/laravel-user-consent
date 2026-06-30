# Changelog

All notable changes to `yezzmedia/laravel-user-consent` will be documented in this file.

The format is based on Keep a Changelog and this package follows Semantic Versioning.

## [Unreleased]

## [0.2.0] - 2026-06-30

### Fixed

- Consent preferences page now falls back to standalone view when no default Filament panel is configured, preventing a 500 error
- Standalone preferences view now loads Tailwind CSS via `@vite()`, fixing missing styles in dark mode
- Removed `--path` from migrate call in `CreateConsentTablesInstallStep`

### Changed

- Bumped minimum `yezzmedia/laravel-foundation` dependency to `^0.2`
- Bumped minimum `yezzmedia/laravel-account` dependency to `^0.2`
- Bumped minimum `yezzmedia/laravel-dashboard` dependency to `^0.2`

## [0.1.0] - 2026-03-31

### Added

- Cookie consent banner injected into dashboard bottom-bar
- Consent decision persistence with versioning
- User-facing preferences page at `/consent/preferences`
- Consent-aware gating integration with analytics trackers
- Foundation-aligned install step for consent tables
- Account sidebar navigation integration
