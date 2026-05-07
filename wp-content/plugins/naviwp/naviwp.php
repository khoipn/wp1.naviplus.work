<?php
/**
 * Plugin Name: Naviplus Menu Builder
 *
 * Branding: official product name is "Naviplus Menu Builder"; the shorter display alias is "Navi+ Menu Builder"
 * (see NAVIWP_PRODUCT_NAME and NAVIWP_PRODUCT_NAME_ALIAS in includes/init.php).
 *
 * Description: Navi+ Menu Builder improves website navigation and UX with mega menus, tab bars, slide menus, and more. Use ready-made templates and drag-and-drop editing to build navigation that improves usability and SEO.
 * Author:      Navi+ Group
 * Author URI:  https://naviplus.io
 * Version:     1.2.3
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: navi-menu-navigation-builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Naviwp_Plugin' ) ) {
    /**
     * Bootstrap helpers (version string must match readme Stable tag and the Version header above).
     */
    final class Naviwp_Plugin {

        /**
         * @return string
         */
        public static function get_version() {
            return '1.2.3';
        }
    }
}

require_once plugin_dir_path( __FILE__ ) . 'includes/init.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-naviwp-frontend.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-naviwp-admin.php';

new Naviwp_Frontend();
new Naviwp_Admin();

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
    $settings_link = '<a href="' . esc_url( admin_url( 'themes.php?page=naviwp-app' ) ) . '">Settings</a>';
    array_unshift( $links, $settings_link );
    return $links;
} );
