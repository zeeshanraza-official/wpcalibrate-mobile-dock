# WPCalibrate Mobile Dock

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B%20%7C%207.1.3%20Tested-21759b.svg?logo=wordpress&logoColor=white)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%2B-777bb4.svg?logo=php&logoColor=white)](https://www.php.net/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-8.0%2B%20%7C%2011.2%20Tested-96588a.svg?logo=woocommerce&logoColor=white)](https://woocommerce.com/)
[![License](https://img.shields.io/badge/License-GPL--2.0--or--later-blue.svg)](LICENSE.txt)
[![Accessibility](https://img.shields.io/badge/Accessibility-WCAG%202.2%20AA-success.svg)](https://www.w3.org/WAI/standards-guidelines/wcag/)

> **Fast, accessible, app-style bottom navigation system for mobile WordPress websites, with optional WooCommerce-aware navigation actions.**

---

## 📖 About

**WPCalibrate Mobile Dock** brings native mobile app navigation paradigms to mobile WordPress websites. Designed from the ground up for modern thumb-zone ergonomics, it replaces clunky hamburger menus with an elegant, always-accessible bottom docking bar.

### Why WPCalibrate Mobile Dock?
- **Universal Compatibility**: Optimized for content blogs, service agencies, SaaS platforms, LMS membership communities, and high-volume WooCommerce stores.
- **Zero External Dependencies**: Zero remote fonts, zero CDN links, and zero third-party JavaScript libraries. Bundles 26 ultra-crisp vector SVG icons locally. Total CSS & JS payload is under 50KB combined.
- **Non-Destructive & Resilient**: Does not alter WordPress core, themes, or third-party plugins. Works with classic themes, modern block themes, and headless frontends.
- **WooCommerce Ready**: Seamlessly binds to WooCommerce cart fragments via AJAX, displaying live, animated cart quantity badges without requiring full-page reloads.
- **Dashboard Updates**: Fully compatible with WordPress GitHub Updater plugins, allowing administrators to receive update notifications and perform one-click updates directly inside `wp-admin`.

---

## ✨ Features

### 📱 Modern Dock Layout & Aesthetics
* **Floating Pill & Full-Width Modes**: Choose between a modern rounded floating pill (`border-radius: 28px`) or an edge-to-edge docked bar.
* **Glassmorphism Backdrop Blur**: Built-in CSS `backdrop-filter: blur(20px)` provides a sleek translucent background that blends harmoniously into any theme.
* **Center-Raised Action Button**: Elevate a high-priority action (e.g. Search, Quick Call, Booking, or Shop) into an elevated, circular floating action button with customizable glow and elevation shadows.
* **Active State Indicator**: Intelligent page-matching logic displays active states with an accessible indicator dot and high-contrast color shifts without false positives.
* **Zero Layout Shift**: Automatically injects dynamic bottom spacing into the document layout only while the dock is active, ensuring footers and legal notices are never obscured.
* **iOS / Android Safe-Area Insets**: Full native notch and home-bar support using `env(safe-area-inset-bottom)`.

### 🎯 Deterministic Specificity Rule Engine
Create multiple independent docks across your site. The rule engine calculates deterministic priority scores to render the most relevant navigation:
* **Explicit Content Targets (Score: 400)**: Target specific Pages, Posts, or custom post types.
* **WooCommerce Target Rules (Score: 300)**: Target Shop, Cart, Checkout, My Account, or Product Categories.
* **Authentication States (Score: 200)**: Display different docks for logged-in members vs. guest visitors.
* **Site-Wide Fallbacks (Score: 100 / 50)**: Global fallback docks ensuring mobile visitors always have a navigation bar.
* **Exclusion Precedence**: Any negative exclusion rule takes absolute priority over inclusions.

### 🎨 Bundled Stroke Vector Icon System
* **26 Curated Feather-Style Stroke SVGs**: Includes `home`, `search`, `menu`, `user`, `cart`, `bag`, `store`, `phone`, `whatsapp`, `chat`, `email`, `bell`, `heart`, `share`, `settings`, `grid`, and more.
* **Crisp Outline Rendering**: Styled strictly with `fill: none; stroke: currentColor; stroke-width: 2;` to guarantee crisp rendering across standard and Retina displays.
* **Admin Visual Picker**: Choose icons visually from within the navigation builder with instant previewing.

### ♿ Accessibility & Standards Compliance (WCAG 2.2 AA)
* **Semantic HTML5**: Emits semantic `<nav role="navigation">` and `<ul role="menubar">` structure.
* **Visible Focus & Touch Cleanliness**: Suppresses intrusive mobile tap highlight frames while maintaining a 2px offset focus ring for keyboard tab navigation.
* **Touch Targets**: Minimum 44×44px interactive touch surface for thumb-friendly tapping.
* **Motion Accessibility**: Full support for `@media (prefers-reduced-motion: reduce)`.

---

## 🚀 Documentation & Setup Guide

### 1. Installation

#### Via WordPress Dashboard (ZIP Upload)
1. Download [`wpcalibrate-mobile-dock-1.0.0.zip`](https://github.com/zeeshanraza-official/wpcalibrate-mobile-dock/releases).
2. In your WordPress admin, go to **Plugins → Add New Plugin → Upload Plugin**.
3. Choose the ZIP file and click **Install Now**, then click **Activate Plugin**.

#### Via Git
Clone this repository directly into your WordPress `wp-content/plugins/` directory:
```bash
cd wp-content/plugins/
git clone https://github.com/zeeshanraza-official/wpcalibrate-mobile-dock.git
```

#### Automatic Updates via Dashboard
To receive automatic update notifications and perform one-click updates directly from your WordPress dashboard:
1. Install and activate the free [Git Updater](https://git-updater.com/) or [GitHub Updater](https://github.com/afragen/github-updater) plugin.
2. WPCalibrate Mobile Dock includes native `GitHub Plugin URI` headers. Updates published to this repository will appear under **Dashboard → Updates** automatically.

---

### 2. Quick 3-Step Setup

1. **Step 1: Create a Navigation Dock**  
   Navigate to **WPCalibrate → Mobile Dock** in your WordPress admin. Click **+ Add New Dock** or edit the automatically generated starter dock.
2. **Step 2: Add Items & Actions**  
   Add between 2 and 7 navigation items. Assign actions:
   * **URL**: Link to internal or external pages.
   * **Action Trigger**: Trigger Search modal, Scroll to Top, or Browser Back.
   * **Communication**: One-tap Telephone (`tel:`), WhatsApp chat, or Email (`mailto:`).
   * **WooCommerce**: Direct links to Shop, Cart (with live counter badge), or My Account.
3. **Step 3: Define Visibility Rules**  
   In the **Visibility** tab, configure where this dock should appear (e.g. *Entire Website*, *Only on WooCommerce*, or *Only for Logged-In Users*).

---

### 3. Developer Hooks & Filters

Extend or customize dock behavior using native WordPress hooks:

```php
// Filter dock items before rendering
add_filter( 'wpcalibrate_mobile_dock_items', function( array $items, int $dock_id ) {
    // Custom item manipulation
    return $items;
}, 10, 2 );

// Customize CSS custom properties
add_filter( 'wpcalibrate_mobile_dock_css_vars', function( array $vars, int $dock_id ) {
    $vars['--wpcal-dock-bg'] = 'rgba(15, 23, 42, 0.85)';
    return $vars;
}, 10, 2 );

// Override which dock ID is rendered on a specific template
add_filter( 'wpcal_mobile_dock_active_dock_id', function( ?int $dock_id ) {
    if ( is_page( 'special-promo' ) ) {
        return 42; // Force specific dock ID
    }
    return $dock_id;
} );
```

---

## 📋 Changelog

### [1.0.3] - 2026-10-16
* **Fixed**: Center raised action button clipping at bottom of screen. Repositioned coordinate from negative `bottom` to `top: -20px; transform: translateX(-50%)` and added `overflow: visible !important;` on dock hierarchy.
* **Fixed**: SVG icon solid blob rendering bug. Removed erroneous `fill: currentColor;` rule and configured `fill: none; stroke: currentColor; stroke-width: 2;` for crisp Feather vector rendering.
* **Fixed**: Suppressed rectangular blue tap focus frame on touch devices while preserving `:focus-visible` offset rings for keyboard accessibility.
* **Added**: Glassmorphic blur (`backdrop-filter: blur(20px)`), refined floating pill geometry (`border-radius: 28px`), and subtle active item indicator dot.
* **Added**: Elevated admin dashboard across all tabs with soft card borders, pulse badges, and table action bars.

### [1.0.2] - 2026-10-16
* **Added**: Comprehensive Overview tab with live diagnostics, 3-step setup guide, and architectural scoring insights.
* **Fixed**: Replaced opaque raster admin menu icon with clean, scalable base64 SVG data URI with global `admin_head` constraint styles.

### [1.0.1] - 2026-10-16
* **Fixed**: Added `.vscode/settings.json` stubs and `@var` PHPDoc annotations resolving all Intelephense diagnostics.
* **Added**: Automated FTP deployment pipeline script (`deploy-ftp.ps1`).

### [1.0.0] - 2026-10-16
* **Initial Release**: Private CPT `wpcal_mobile_dock`, 26 SVG icons, deterministic Rule Engine, live WooCommerce AJAX cart badge, and automated 33-test suite.

---

## 📄 License & Credits

* **License**: Licensed under the [GNU General Public License v2.0 or later](LICENSE.txt).
* **Author**: [WPCalibrate](https://wpcalibrate.com)
* **Support**: [support@wpcalibrate.com](mailto:support@wpcalibrate.com) | [WhatsApp Support](https://wa.me/447474795976)
