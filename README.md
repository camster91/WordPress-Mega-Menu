# Easy Mega Menu

WordPress plugin to build corporate-style mega menus (sidebar categories + feature grid) from a visual admin panel — no coding required.

## What it looks like

Matches the common "Platforms" mega menu pattern:

- **Top nav** with links and optional CTA button
- **Left sidebar** of categories (icon + title + short description)
- **Right panel** with a heading and multi-column icon + link grid
- Hover/click a category to switch the right-side content

A demo menu (Spend / Sign / HR / CRM) is created on activation.

## Install

1. Download the latest release ZIP from [GitHub Releases](https://github.com/camster91/WordPress-Mega-Menu/releases)
2. In WP Admin → **Plugins → Add New → Upload Plugin**, choose the ZIP and activate **Easy Mega Menu**
3. Open **Mega Menu** in the left admin menu

## For non-technical editors

1. Click **Edit Menu** on the demo menu (or **Create New Menu**)
2. Add top-level items (e.g. Platforms, Company, Resources)
3. Set an item type to **Mega Menu**, then click **Edit Mega Content**
4. Add **categories** on the left (title, description, icon)
5. For each category, add **grid links** (icon, label, URL)
6. Adjust colors and column count under **Colors & Layout**
7. Click **Save Menu**
8. Place the menu using any of the methods below

## Usage

### Block (Gutenberg)

Add the **Easy Mega Menu** block to any page, post, or template. Select the menu from the block sidebar.

### Widget

Go to **Appearance → Widgets** (or the Site Editor), add the **Easy Mega Menu** widget to any sidebar, and choose a menu from the dropdown.

### Shortcode

```
[easy_mega_menu id="menu_demo"]
```

### Theme PHP

```php
<?php
if ( function_exists( 'emm_render_menu' ) ) {
    emm_render_menu( 'menu_demo' );
}
```

## Features

| Feature | Notes |
|--------|-------|
| Visual builder + live preview | See the menu as you edit |
| Drag-and-drop reorder | Top items, categories, and links |
| Icon library | 20+ built-in outline icons |
| Custom icons | Upload via Media Library |
| CTA button | Label, URL, show/hide |
| Brand colors | Sidebar, active state, accent |
| Grid columns | 2–4 columns |
| Mobile-friendly | Accordion-style on small screens |
| Gutenberg block | Native block with inspector controls |
| WordPress widget | Drop into any widget area |
| Shortcode | Classic and builder-friendly |
| Auto-updates | Pulls new releases automatically from GitHub |

## Automatic Updates

The plugin checks GitHub Releases for new versions and installs them through the WordPress updater — no manual ZIP uploads needed.

## Requirements

- WordPress 5.8+
- PHP 7.4+

## File structure

```
mega-menu-plugin/
├── mega-menu-plugin.php          # Bootstrap + version
├── uninstall.php                 # Cleanup on uninstall
├── LICENSE                       # GPL-2.0-or-later
├── README.md                     # This file
├── CHANGELOG.md                  # Version history
├── package.json                  # Block build config
├── includes/
│   ├── class-emm-data.php        # Storage + demo data
│   ├── class-emm-icons.php       # SVG icon set
│   ├── class-emm-admin.php       # Admin pages + AJAX
│   ├── class-emm-frontend.php    # Frontend rendering
│   ├── class-emm-shortcode.php   # [easy_mega_menu]
│   ├── class-emm-block.php       # Gutenberg block
│   ├── class-emm-widget.php      # WordPress widget
│   └── class-emm-updater.php     # GitHub auto-updater
├── admin/views/                  # List + builder screens
├── assets/                       # CSS / JS / icons
├── src/blocks/                   # Block source (React)
└── .github/workflows/            # Release automation
```

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).
