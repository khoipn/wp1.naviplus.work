=== Naviplus Menu Builder ===
Contributors: naviplus, khoipng
Tags: mega menu, navigation menu, tab bar, hamburger menu, grid menu
Requires at least: 5.8
Tested up to: 6.9
Stable tag: 1.2.3
License: GPL-2.0+
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create modern navigation menus for WordPress — mega menu, tab bar, hamburger menu and more.

== Description ==

Official product name: **Naviplus Menu Builder**. Shorter alias used in this document where space matters: **Navi+ Menu Builder**.

WordPress navigation menus are often simple and limited. Navi+ Menu Builder helps you build richer navigation layouts such as mega menus, tab bars, hamburger menus and grid menus, making it easier for visitors to explore your website.

Menus can be displayed globally across your site or embedded in specific locations using a shortcode.

== Why choose Navi+ Menu Builder ==

Navi+ Menu Builder allows you to build modern navigation for WordPress quickly without coding. It provides flexible menu layouts designed for both desktop and mobile browsing.

== Features ==

* Mega menu with columns and rich content  
* Tab bar navigation optimized for mobile  
* Slide / hamburger menu  
* Grid style navigation layouts  
* Floating navigation button  
* Display menus globally or via shortcode  
* Enable or disable menu embed from dashboard  
* Basic navigation interaction analytics  

== How it works ==

1. Install and activate the plugin.
2. Go to **Appearance → Naviplus Menu Builder**.
3. Create your first menu.
4. Design the layout using the Navi+ Menu Builder visual editor.
5. The plugin loads and displays the menu on your website.

You can also embed menus manually using a shortcode.

== Shortcode ==

Use the shortcode below to display a menu inside posts, pages or widgets:

[naviwp embed_id="YOUR-EMBED-ID"]

The legacy tag `[naviplus ...]` is still accepted for backward compatibility.

In the block editor, either paste the shortcode into a **Shortcode** block, or a normal Paragraph (the plugin will detect `[naviwp]` / `[naviplus]` there too).

== Screenshots ==

1. Modern navigation layouts created with Navi+ Menu Builder  
2. Example of a multi-column mega menu  
3. Tab bar navigation designed for mobile browsing  
4. Slide / hamburger menu navigation  
5. Floating button for instant support  

== Installation ==

1. Upload the `naviwp` folder to `/wp-content/plugins/`.
2. Activate the plugin in **Plugins → Installed Plugins**.
3. Go to **Appearance → Naviplus Menu Builder**.
4. Create your first menu.

== External Services ==

This plugin connects to the Navi+ Menu Builder service to generate and render navigation menus.

The plugin loads a JavaScript file from:
https://live.naviplus.app/start.js

This script renders navigation menus created in the Navi+ Menu Builder editor.

Data sent to the service may include:

* Website domain
* Menu configuration
* Minimal usage data required to render menus

Privacy Policy:
https://naviplus.io/privacy

== Frequently Asked Questions ==

= What is Navi+ Menu Builder? =

**Naviplus Menu Builder** is the official product name; **Navi+ Menu Builder** is the short form used in this readme. It is a navigation builder that allows you to create modern menu layouts such as mega menus, tab bars or slide menus.

= Does Navi+ Menu Builder work with WordPress? =

Yes. The plugin integrates directly with WordPress and displays navigation menus on your website.

= Is Navi+ Menu Builder compatible with WooCommerce? =

Yes. Navi+ Menu Builder can be used on WooCommerce websites to navigate product categories, collections or promotional pages.

= Is Navi+ Menu Builder compatible with Elementor? =

Yes. Navi+ Menu Builder works alongside Elementor and menus can be embedded using shortcode.

= Do I need an account before installing the plugin? =

No. The plugin can generate a connection automatically when you create your first menu.

= Where are my menus stored? =

Menu layouts are stored on the Navi+ Menu Builder service. The plugin saves a site identifier in the WordPress database so Navi+ Menu Builder can recognize this website. That value is not your WordPress password and is not used to log into WordPress.

= Does Navi+ Menu Builder support mobile navigation? =

Yes. Navi+ Menu Builder includes navigation layouts designed for mobile devices such as tab bars and slide menus.

= Can I place menus inside a page or post? =

Yes. You can embed menus using the shortcode:

[naviwp embed_id="..."]

= Can I disable the global embed? =

Yes. You can disable the global embed from the dashboard and place menus manually using shortcode.

= What happens if I uninstall the plugin? =

When the plugin is uninstalled, the menu embed script is removed from your website.

If you added menus using shortcode, simply remove the shortcode:

[naviwp embed_id="..."]

Your menu layouts remain stored on the Navi+ Menu Builder service if you want to use them again later.

== Changelog ==

= 1.2.3 =
* Front-end loader uses `wp_enqueue_script` + `wp_print_scripts()` next to shortcode output for Plugin Check compatibility (no raw script tags in PHP)
* Release packaging checklist documented in `golive.md` (developer-only; excluded from distribution zip)

= 1.2.2 =
* Unified code prefix `naviwp` / `Naviwp_` / `NAVIWP_` for WordPress.org coding standards (classes, constants, AJAX, admin screen)
* Shortcode `[naviwp]` added; `[naviplus]` kept as alias
* Admin screen slug updated to `naviwp-app`

= 1.2.1 =
* Aligned readme Stable tag with plugin version (WordPress.org requirement)
* Script registration includes a version string for cache busting
* Stored site link option uses `_navi_connector` with migration from legacy option names

= 1.2.0 =
* Improved dashboard
* Added embed toggle
* Improved onboarding

= 1.1.0 =
* Added admin dashboard integration

= 1.0.0 =
* Initial release