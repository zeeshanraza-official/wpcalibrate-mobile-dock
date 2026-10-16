=== WPCalibrate Mobile Dock ===
Contributors: wpcalibrate
Donate link: https://wpcalibrate.com
Tags: mobile navigation, bottom dock, mobile menu, app navigation, woocommerce mobile
Requires at least: 6.0
Tested up to: 7.1.3
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fast, accessible, app-style bottom navigation system for mobile WordPress websites, with optional WooCommerce-aware navigation actions.

== Description ==

**WPCalibrate Mobile Dock** delivers a fast, modern, app-style bottom navigation bar tailored specifically for mobile and tablet visitors on your WordPress website.

Whether you run a blog, corporate site, portfolio, membership platform, or high-volume WooCommerce store, WPCalibrate Mobile Dock gives visitors an intuitive thumb-zone navigation experience that boosts user engagement and conversions.

### Key Features

* **App-Style Navigation Bar**: Clean, responsive bottom dock fixed to the viewport on mobile devices.
* **Deterministic Priority Rule Engine**: Create multiple independent navigation configurations targeted to specific pages, blog posts, public post types, categories, or user authentication states.
* **Optional WooCommerce Integration**: Dedicated WooCommerce actions for Shop, Product Categories, Cart, and My Account.
* **Live Dynamic Cart Badge**: Automatically updates item quantities in real time via WooCommerce AJAX fragments without full-page reloads.
* **Safe-Area Inset Support**: Full compatibility with notch and home-indicator devices using `env(safe-area-inset-bottom)`.
* **Zero Layout Shift**: Applies calculated bottom spacing dynamically only while the dock is active so footer content is never hidden.
* **Curated Bundled SVG Icons**: 26 lightweight, local SVG icons bundled directly into the plugin. Zero external requests, zero third-party font dependencies.
* **Visual Navigation Item Builder**: Intuitive admin interface with keyboard-accessible reordering, icon picker, and live warning indicators.
* **Accessible & WCAG 2.2 AA Compliant**: Semantic `<nav>` markup, visible focus rings, minimum 44px touch targets, `aria-current="page"` indicators, and `prefers-reduced-motion` support.
* **Lightweight & High Performance**: Clean vanilla JavaScript without heavy external libraries or frameworks.

== Installation ==

1. Upload the `wpcalibrate-mobile-dock` folder to the `/wp-content/plugins/` directory, or upload the ZIP file via WordPress Plugins > Add New > Upload Plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **WPCalibrate > Mobile Dock** to configure your bottom navigation items, appearance presets, and visibility rules.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =
No. WooCommerce is completely optional. If WooCommerce is not active, all general WordPress navigation actions (Home, Pages, Posts, Blog, Search, Back, Scroll-to-Top, Phone, WhatsApp, Email, Custom URLs) work normally without any errors or warnings.

= Can I create different navigation docks for different pages? =
Yes. You can create multiple independent docks with distinct items and appearance overrides, then assign them using visibility rules (e.g. one dock for the Shop, another for the Blog, and a default dock for the rest of the site).

= Does it cover my footer content? =
No. The plugin applies an intelligent padding-bottom spacer to the page body only on mobile viewports while the dock is active, ensuring your footer and bottom content remain accessible.

= Can I customize the styling? =
Yes. You can customize colors, heights, font sizes, icon sizes, and layout modes (edge-to-edge fixed vs floating pill) either globally or per navigation dock. Advanced users can also add scoped Custom CSS in the Advanced tab.

== Screenshots ==

1. Mobile Dock frontend experience in action on mobile viewport.
2. Navigation items visual builder in WordPress admin.
3. Curated SVG icon picker modal.
4. Visibility rules configuration and priority settings.
5. Global appearance and color customization tab.

== Changelog ==

= 1.0.0 =
* Initial production release.
* Core mobile navigation dock architecture.
* Deterministic visibility rule engine.
* Optional WooCommerce Shop, Cart, Account, and live AJAX cart badge support.
* Bundled 26 curated SVG icons with visual admin picker.
* Mobile safe-area inset and dynamic body spacing support.
* Lightweight accessible search overlay dialog.
* Full WCAG 2.2 AA accessibility and RTL support.
