<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Branding — human-readable labels only (not for API keys, slugs, or script handles).
 *
 * Official product name: Naviplus Menu Builder.
 * Shorter alias for prose and compact UI: Navi+ Menu Builder.
 */
define( 'NAVIWP_PRODUCT_NAME', 'Naviplus Menu Builder' );
define( 'NAVIWP_PRODUCT_NAME_ALIAS', 'Navi+ Menu Builder' );

// ========== PRODUCTION CONFIG ==========


define( 'NAVIWP_ENV', 'production' );
define( 'NAVIWP_BASE_URL', 'https://dash.naviplus.app' );
define( 'NAVIWP_START_JS', 'https://live.naviplus.app/start.js' );

// ========== FEATURE FLAGS ==========
// Set true to show the "Partner with us" card on the dashboard.
define( 'NAVIWP_SHOW_PARTNER_CARD', false );

// ========== ENDPOINTS (Naviplus Menu Builder backend / dash.naviplus.app; change only if the server paths change) ==========
define( 'NAVIWP_ENDPOINT_REGISTER', NAVIWP_BASE_URL . '/naviplus/authen/wordpress/register.php' );
define( 'NAVIWP_ENDPOINT_APP', NAVIWP_BASE_URL . '/naviplus/authen/wordpress/login.php' );
define( 'NAVIWP_API_URL', NAVIWP_BASE_URL . '/naviplus/authen/wordpress/api.menus.php' );
define( 'NAVIWP_ANALYTICS_URL', NAVIWP_BASE_URL . '/naviplus/authen/wordpress/api.analytics.php' );

/**
 * HTTP API — POST field names and fixed values (must match the PHP handlers on dash.naviplus.app).
 *
 * PLUGIN_MARK: POST field name + fixed value used to identify requests from this WordPress plugin (not an end-user password).
 * SITE_HANDLE: POST field name for the public site id (NAVI…).
 */
define( 'NAVIWP_API_VALUE_PLUGIN_MARK', 'naviplus_wp_2026' );
/** POST parameter name for PLUGIN_MARK; the API must read this exact key. */
define( 'NAVIWP_API_FIELD_PLUGIN_MARK', 'wp_plugin_mark' );

define( 'NAVIWP_API_FIELD_SITE_HANDLE', 'token' );
define( 'NAVIWP_API_POST_REGISTER_PAYLOAD', 'input' );
define( 'NAVIWP_API_POST_ACTION', 'action' );
define( 'NAVIWP_API_POST_EMBED_ID', 'embed_id' );
define( 'NAVIWP_API_POST_DAYS', 'days' );
define( 'NAVIWP_API_POST_LIMIT', 'limit' );
define( 'NAVIWP_API_POST_PLATFORM', 'platform' );

/** Query keys when opening the remote editor app (stored blob, market = WordPress). */
define( 'NAVIWP_APP_QUERY_PAYLOAD', 'para' );
define( 'NAVIWP_APP_QUERY_MARKET', 'market' );
define( 'NAVIWP_APP_MARKET_VALUE', 'wp' );

/** POST action value sent to api.menus.php when deleting a menu. */
define( 'NAVIWP_API_MENUS_ACTION_DELETE', 'delete' );

/**
 * Keys passed to start.js via window._navi_setting; property names must match the deployed loader.
 */
define( 'NAVIWP_EMBED_FIELD_SITE_HANDLE', 'token' );
define( 'NAVIWP_EMBED_KEY_ENV', 'env' );
define( 'NAVIWP_EMBED_KEY_MARKET', 'market' );
define( 'NAVIWP_EMBED_KEY_EMBED_ID', 'embed_id' );
define( 'NAVIWP_EMBED_VALUE_ENV', 'wp' );
define( 'NAVIWP_EMBED_VALUE_MARKET', 'wp' );

/**
 * WordPress options: base64 site link blob and optional registration error flag.
 */
define( 'NAVIWP_OPTION_SITE_LINK', '_navi_connector' );
define( 'NAVIWP_OPTION_SITE_LINK_ERROR', '_navi_connector_error' );

/**
 * Retrieve the stored site link payload. Migrates legacy `_navi_token` / `_navi_token_error` when present.
 *
 * @return string Base64 string or empty.
 */
function naviwp_get_site_link_option() {
    $val = get_option( NAVIWP_OPTION_SITE_LINK, '' );
    if ( is_string( $val ) && $val !== '' ) {
        return $val;
    }
    $legacy = get_option( '_navi_token', '' );
    if ( ! is_string( $legacy ) || $legacy === '' ) {
        return '';
    }
    update_option( NAVIWP_OPTION_SITE_LINK, $legacy );
    delete_option( '_navi_token' );
    $legacy_err = get_option( '_navi_token_error', false );
    if ( false !== $legacy_err ) {
        update_option( NAVIWP_OPTION_SITE_LINK_ERROR, $legacy_err );
        delete_option( '_navi_token_error' );
    }
    return $legacy;
}
