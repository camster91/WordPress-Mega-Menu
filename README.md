# Easy Mega Menu

WordPress plugin to build corporate-style mega menus (sidebar categories + feature grid) from a visual admin panel — no coding required.

## What it looks like

Matches the common “Platforms” mega menu pattern:

- **Top nav** with links and optional CTA button  
- **Left sidebar** of categories (icon + title + short description)  
- **Right panel** with a heading and multi-column icon + link grid  
- Hover/click a category to switch the right-side content  

A demo menu (Spend / Sign / HR / CRM) is created on activation.

## Install

1. Copy this folder into `wp-content/plugins/mega-menu-plugin/`
2. In WP Admin → **Plugins**, activate **Easy Mega Menu**
3. Open **Mega Menu** in the left admin menu

## For non-technical editors

1. Click **Edit Menu** on the demo menu (or **Create New Menu**)
2. Add top-level items (e.g. Platforms, Company, Resources)
3. Set an item type to **Mega Menu**, then click **Edit Mega Content**
4. Add **categories** on the left (title, description, icon)
5. For each category, add **grid links** (icon, label, URL)
6. Adjust colors and column count under **Colors & Layout**
7. Click **Save Menu**
8. Copy the shortcode and paste it into a page, header block, or widget

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
|--------|--------|
| Visual builder + live preview | See the menu as you edit |
| Drag-and-drop reorder | Top items, categories, and links |
| Icon library | 20+ built-in outline icons |
| Custom icons | Upload via Media Library |
| CTA button | Label, URL, show/hide |
| Brand colors | Sidebar, active state, accent |
| Grid columns | 2–4 columns |
| Mobile-friendly | Accordion-style on small screens |

## Requirements

- WordPress 5.8+
- PHP 7.4+

## File structure

```
mega-menu-plugin/
├── mega-menu-plugin.php      # Bootstrap
├── includes/
│   ├── class-emm-data.php    # Storage + demo data
│   ├── class-emm-icons.php   # SVG icon set
│   ├── class-emm-admin.php   # Admin pages + AJAX
│   ├── class-emm-frontend.php
│   └── class-emm-shortcode.php
├── admin/views/              # List + builder screens
└── assets/                   # CSS / JS
```
