# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0] - 2026-07-23

### Added
- **WordPress Widget** — new "Easy Mega Menu" widget with title field and menu dropdown selector for any widget area.
- **LICENSE file** — GPL-2.0-or-later license included at repo root.

### Changed
- Updated README with full usage docs covering Block, Widget, Shortcode, and Theme PHP.

## [1.3.0] - 2026-07-23

### Added
- **Gutenberg Block** — native "Easy Mega Menu" block with inspector menu selector.
- **GitHub Auto-Updater** — automatic updates via WordPress updater pulling from GitHub Releases.
- **Release workflow** — GitHub Actions auto-builds and publishes a release ZIP on every version bump.
- `uninstall.php` — complete cleanup of options and transients on uninstall.

## [1.2.0] - 2026-07-22

### Added
- **Shortcode** — `[easy_mega_menu id="..."]` for classic editors and builder compatibility.
- `emm_render_menu()` PHP helper for theme developers.

## [1.1.0] - 2026-07-22

### Added
- **Visual Builder** — drag-and-drop admin interface with live preview.
- Icon library with 20+ built-in SVG icons.
- Custom icon upload via Media Library.
- Brand color customization (sidebar, active state, accent).
- Grid column control (2–4 columns).
- Mobile accordion menu.

## [1.0.0] - 2026-07-21

### Added
- Initial release.
- Frontend mega menu rendering (sidebar categories + feature grid).
- Admin menu list and basic settings.
- Demo menu created on activation.
