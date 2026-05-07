<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin UI. Branding: official name NAVIWP_PRODUCT_NAME; compact UI alias NAVIWP_PRODUCT_NAME_ALIAS (see includes/init.php).
 */
class Naviwp_Admin {

    public function __construct() {
        add_action( 'admin_menu',                 array( $this, 'register_menu' ) );
        add_action( 'wp_ajax_naviwp_register',  array( $this, 'ajax_register' ) );
        add_action( 'wp_ajax_naviwp_get_menus',    array( $this, 'ajax_get_menus' ) );
        add_action( 'wp_ajax_naviwp_delete_menu',  array( $this, 'ajax_delete_menu' ) );
        add_action( 'wp_ajax_naviwp_analytics',    array( $this, 'ajax_analytics' ) );
        add_action( 'wp_ajax_naviwp_toggle_embed', array( $this, 'ajax_toggle_embed' ) );
    }

    public function register_menu() {
        add_submenu_page(
            'themes.php',
            NAVIWP_PRODUCT_NAME,
            NAVIWP_PRODUCT_NAME,
            'manage_options',
            'naviwp-app',
            array( $this, 'render_page' )
        );
    }

    /**
     * AJAX: Link this WordPress site to the Naviplus Menu Builder remote service (alias: Navi+ Menu Builder).
     * No WordPress password is sent.
     */
    public function ajax_register() {
        check_ajax_referer( 'naviwp_register_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to do this.' );
        }

        if ( naviwp_get_site_link_option() ) {
            wp_send_json_error( 'This site is already linked to ' . NAVIWP_PRODUCT_NAME_ALIAS . '.' );
        }

        $navi_id    = 'NAVI' . wp_rand( 100000, 999999 );
        $email      = 'wp_' . $navi_id . '_' . get_option( 'admin_email' );
        $domain     = wp_parse_url( get_option( 'siteurl' ), PHP_URL_HOST );
        $timestamp  = time();
        $user       = wp_get_current_user();
        $admin_name = $user->display_name;
        $admin_email_real = $user->user_email;
        $input      = $navi_id . ' | ' . $email . ' | ' . $domain . ' | ' . $timestamp . ' | ' . $admin_name . ' | ' . $admin_email_real;

        $response = wp_remote_post( NAVIWP_ENDPOINT_REGISTER, array(
            'timeout' => 15,
            'body'    => array(
                NAVIWP_API_FIELD_PLUGIN_MARK         => NAVIWP_API_VALUE_PLUGIN_MARK,
                NAVIWP_API_POST_REGISTER_PAYLOAD => base64_encode( $input ),
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $raw  = wp_remote_retrieve_body( $response );
        $code = wp_remote_retrieve_response_code( $response );
        $body = json_decode( $raw, true );

        if ( ! empty( $body['success'] ) ) {
            update_option( NAVIWP_OPTION_SITE_LINK, base64_encode( $input ) );
            delete_option( NAVIWP_OPTION_SITE_LINK_ERROR );
            wp_send_json_success( array( 'site_id' => $navi_id ) );
        } else {
            $msg = isset( $body['message'] ) ? $body['message'] : 'Registration failed. Please try again.';
            wp_send_json_error( $msg );
        }
    }

    /**
     * AJAX: Fetch menu list from the Naviplus Menu Builder API (alias: Navi+ Menu Builder).
     */
    public function ajax_get_menus() {
        check_ajax_referer( 'naviwp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to do this.' );
        }

        $token_id = $this->get_token_id();
        if ( ! $token_id ) {
            wp_send_json_error( 'This site is not linked to ' . NAVIWP_PRODUCT_NAME_ALIAS . ' yet.' );
        }

        $response = wp_remote_post( NAVIWP_API_URL, array(
            'timeout' => 15,
            'body'    => array(
                NAVIWP_API_FIELD_PLUGIN_MARK => NAVIWP_API_VALUE_PLUGIN_MARK,
                NAVIWP_API_FIELD_SITE_HANDLE  => $token_id,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $body['success'] ) ) {
            wp_send_json_success( $body['data'] );
        } else {
            $msg = isset( $body['message'] ) ? $body['message'] : 'Unknown error.';
            wp_send_json_error( $msg );
        }
    }

    /**
     * AJAX: Delete a menu by embed_id.
     */
    public function ajax_delete_menu() {
        check_ajax_referer( 'naviwp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to do this.' );
        }

        $embed_id = isset( $_POST['embed_id'] ) ? sanitize_text_field( wp_unslash( $_POST['embed_id'] ) ) : '';
        if ( ! $embed_id ) {
            wp_send_json_error( 'Missing embed_id.' );
        }

        $token_id = $this->get_token_id();
        if ( ! $token_id ) {
            wp_send_json_error( 'This site is not linked to ' . NAVIWP_PRODUCT_NAME_ALIAS . ' yet.' );
        }

        $response = wp_remote_post( NAVIWP_API_URL, array(
            'timeout' => 15,
            'body'    => array(
                NAVIWP_API_FIELD_PLUGIN_MARK => NAVIWP_API_VALUE_PLUGIN_MARK,
                NAVIWP_API_FIELD_SITE_HANDLE  => $token_id,
                NAVIWP_API_POST_ACTION    => NAVIWP_API_MENUS_ACTION_DELETE,
                NAVIWP_API_POST_EMBED_ID  => $embed_id,
            ),
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $body['success'] ) ) {
            wp_send_json_success();
        } else {
            $msg = isset( $body['message'] ) ? $body['message'] : 'Unknown error.';
            wp_send_json_error( $msg );
        }
    }

    /**
     * AJAX: Proxy analytics requests to the Naviplus Menu Builder analytics API (alias: Navi+ Menu Builder).
     */
    public function ajax_analytics() {
        check_ajax_referer( 'naviwp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to do this.' );
        }

        $token_id = $this->get_token_id();
        if ( ! $token_id ) {
            wp_send_json_error( 'This site is not linked to ' . NAVIWP_PRODUCT_NAME_ALIAS . ' yet.' );
        }

        $valid            = array( 'summary', 'clicks-by-date', 'top-items', 'recent-clicks' );
        $analytics_action = isset( $_POST['analytics_action'] ) ? sanitize_text_field( wp_unslash( $_POST['analytics_action'] ) ) : '';
        if ( ! in_array( $analytics_action, $valid, true ) ) {
            wp_send_json_error( 'Invalid analytics action.' );
        }

        $body = array(
            NAVIWP_API_FIELD_PLUGIN_MARK => NAVIWP_API_VALUE_PLUGIN_MARK,
            NAVIWP_API_FIELD_SITE_HANDLE  => $token_id,
            NAVIWP_API_POST_ACTION    => $analytics_action,
        );

        if ( isset( $_POST['days'] ) ) {
            $days = absint( $_POST['days'] );
            $body[ NAVIWP_API_POST_DAYS ] = ( $days >= 1 && $days <= 365 ) ? $days : 30;
        }
        if ( isset( $_POST['limit'] ) ) {
            $limit = absint( $_POST['limit'] );
            $body[ NAVIWP_API_POST_LIMIT ] = ( $limit >= 1 && $limit <= 1000 ) ? $limit : 20;
        }
        if ( isset( $_POST['embed_id'] ) ) {
            $body[ NAVIWP_API_POST_EMBED_ID ] = sanitize_text_field( wp_unslash( $_POST['embed_id'] ) );
        }
        if ( isset( $_POST['platform'] ) ) {
            $body[ NAVIWP_API_POST_PLATFORM ] = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
        }

        $response = wp_remote_post( NAVIWP_ANALYTICS_URL, array(
            'timeout' => 15,
            'body'    => $body,
        ) );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( $response->get_error_message() );
        }

        $result = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( ! empty( $result['success'] ) ) {
            wp_send_json_success( $result['data'] );
        } else {
            $msg = isset( $result['message'] ) ? $result['message'] : 'Unknown error.';
            wp_send_json_error( $msg );
        }
    }

    /**
     * AJAX: Save the global embed toggle. UI copy is built with NAVIWP_PRODUCT_NAME_ALIAS ("Navi+ Menu Builder"); official name: Naviplus Menu Builder.
     */
    public function ajax_toggle_embed() {
        check_ajax_referer( 'naviwp_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'You do not have permission to do this.' );
        }

        $enabled = ( isset( $_POST['enabled'] ) && $_POST['enabled'] === '1' ) ? '1' : '0';
        update_option( '_navi_embed_enabled', $enabled );
        wp_send_json_success();
    }

    /**
     * Public site ID (e.g. "NAVI784167") from the base64-encoded stored link payload.
     */
    private function get_token_id() {
        $encoded = naviwp_get_site_link_option();
        if ( ! $encoded ) {
            return '';
        }
        $decoded = base64_decode( $encoded, true );
        if ( ! $decoded ) {
            return '';
        }
        $parts = explode( ' | ', $decoded );
        return trim( $parts[0] );
    }

    /**
     * Render admin page.
     */
    public function render_page() {
        $encoded     = naviwp_get_site_link_option();
        $admin_email = get_option( 'admin_email' );
        $admin_name  = wp_get_current_user()->display_name;
        $domain      = wp_parse_url( get_option( 'siteurl' ), PHP_URL_HOST );
        ?>
        <style>
            #navi-wrap {
                max-width: 1280px;
                margin: 20px auto;
                padding: 0 20px;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                font-size: 14px;
                color: #1d2327;
            }
            /* Card */
            .navi-card {
                background: #fff;
                border: 1px solid #c3c4c7;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
                border-radius: 3px;
                margin-bottom: 20px;
            }
            .navi-card-header {
                padding: 12px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .navi-card-header h2 {
                margin: 0;
                font-size: 14px;
                font-weight: 600;
                color: #1d2327;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .navi-card-body { padding: 16px; }
            /* Logo header */
            .navi-page-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 16px;
            }
            .navi-page-header img { height: 28px; }
            .navi-page-header span { font-size: 18px; font-weight: 700; color: #1d2327; }
            /* Info grid */
            .navi-meta {
                display: flex;
                gap: 24px;
                flex-wrap: wrap;
            }
            .navi-meta-item { display: flex; flex-direction: column; gap: 2px; }
            .navi-meta-item .label { font-size: 11px; color: #646970; text-transform: uppercase; letter-spacing: .5px; }
            .navi-meta-item .value { font-weight: 600; font-size: 14px; }
            /* Buttons */
            .navi-btn {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                background: #2271b1;
                color: #fff;
                padding: 6px 14px;
                border-radius: 3px;
                border: 1px solid #2271b1;
                font-size: 14px;
                font-weight: 400;
                cursor: pointer;
                text-decoration: none;
                line-height: 2;
                white-space: nowrap;
            }
            .navi-btn:hover, .navi-btn:focus { background: #135e96; border-color: #135e96; color: #fff; }
            .navi-btn:disabled { opacity: .6; cursor: default; }
            .navi-btn-secondary {
                background: #f6f7f7;
                color: #2271b1;
                border-color: #2271b1;
            }
            .navi-btn-secondary:hover { background: #f0f0f1; color: #135e96; border-color: #135e96; }
            .navi-btn-sm { padding: 2px 10px; font-size: 12px; line-height: 1.8; }
            .navi-btn-large { padding: 8px 64px; font-size: 18px; font-weight: 600;margin: auto;width: 100%;text-align: center;display: block; }
            /* Badges */
            .navi-badge {
                display: inline-block;
                font-size: 11px;
                font-weight: 500;
                padding: 1px 8px;
                border-radius: 3px;
                line-height: 1.8;
            }
            .navi-badge-blue   { background: #dbeafe; color: #1e40af; }
            .navi-badge-green  { background: #dcfce7; color: #166534; }
            .navi-badge-gray   { background: #f1f5f9; color: #475569; }
            .navi-site-id-display {
                font-family: Consolas, monospace;
                background: #f6f7f7;
                border: 1px solid #dcdcde;
                padding: 1px 7px;
                border-radius: 3px;
                font-size: 12px;
            }
            /* Table */
            .navi-table-wrap { overflow-x: visible; }
            table.navi-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 14px;
            }
            table.navi-table thead { border-bottom: 1px solid #c3c4c7; }
            table.navi-table th {
                text-align: left;
                padding: 8px 12px;
                font-weight: 600;
                color: #646970;
                white-space: nowrap;
                background: #f6f7f7;
            }
            table.navi-table td {
                padding: 10px 12px;
                border-bottom: 1px solid #f0f0f1;
                vertical-align: middle;
            }
            table.navi-table tbody tr:last-child td { border-bottom: none; }
            table.navi-table tbody tr:hover { background: #f6f7f7; }
            /* Actions cell */
            .navi-actions { display: flex; gap: 6px; align-items: center; }
            /* Dropdown */
            .navi-dropdown { position: relative; display: inline-block; }
            .navi-dropdown-toggle {
                background: #f6f7f7;
                color: #2271b1;
                border: 1px solid #2271b1;
                border-radius: 3px;
                padding: 2px 8px;
                font-size: 16px;
                line-height: 1.5;
                cursor: pointer;
                letter-spacing: 1px;
            }
            .navi-dropdown-toggle:hover { background: #f0f0f1; }
            .navi-dropdown-menu {
                display: none;
                position: fixed;
                background: #fff;
                border: 1px solid #c3c4c7;
                box-shadow: 0 3px 8px rgba(0,0,0,.12);
                border-radius: 3px;
                min-width: 240px;
                z-index: 99999;
                overflow: hidden;
            }
            .navi-dropdown-menu.open { display: block; }
            .navi-dropdown-menu button,
            .navi-dropdown-menu a {
                display: block;
                width: 100%;
                padding: 8px 14px;
                text-align: left;
                background: none;
                border: none;
                font-size: 14px;
                color: #1d2327;
                cursor: pointer;
                text-decoration: none;
                box-sizing: border-box;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .navi-dropdown-menu button:hover,
            .navi-dropdown-menu a:hover { background: #f6f7f7; color: #1d2327; }
            .navi-dropdown-menu .navi-dd-copy {
                font-family: Consolas, monospace;
                font-size: 11px;
                color: #646970;
            }
            .navi-dropdown-menu .navi-dd-copy:hover { color: #1d2327; }
            .navi-dropdown-divider { border-top: 1px solid #f0f0f1; margin: 4px 0; }
            .navi-dropdown-menu .navi-dd-danger { color: #d63638; }
            .navi-dropdown-menu .navi-dd-danger:hover { background: #fce8e8; color: #b32d2e; }
            /* Messages */
            .navi-notice {
                padding: 10px 14px;
                border-left: 4px solid;
                border-radius: 0 3px 3px 0;
                font-size: 14px;
                display: none;
                margin-top: 12px;
            }
            .navi-notice.success { background: #ecfdf5; border-color: #00a32a; color: #1a5c2a; }
            .navi-notice.error   { background: #fce8e8; border-color: #d63638; color: #8a2424; }
            /* Intro page */
            .navi-intro-desc { color: #50575e; line-height: 1.7; margin: 0 0 20px; }
            .navi-info-table td { padding: 5px 0; }
            .navi-info-table td:first-child { color: #646970; width: 80px; }
            .navi-info-table td:last-child { font-weight: 600; }
            /* Spinner */
            .navi-spin {
                display: inline-block;
                width: 13px; height: 13px;
                border: 2px solid #dcdcde;
                border-top-color: #2271b1;
                border-radius: 50%;
                animation: naviSpin 0.7s linear infinite;
                vertical-align: middle;
                margin-right: 5px;
            }
            @keyframes naviSpin { to { transform: rotate(360deg); } }
            @keyframes naviPulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(34,113,177,.6); transform: scale(1); }
                50%       { box-shadow: 0 0 0 8px rgba(34,113,177,0); transform: scale(1.04); }
            }
            .navi-btn-pulse { animation: naviPulse 1.2s ease-in-out infinite; }
            /* Welcome notice */
            .navi-welcome-notice {
                background: #eaf3fb;
                border-left: 4px solid #2271b1;
                border-radius: 0 3px 3px 0;
                padding: 12px 16px;
                font-size: 14px;
                color: #1d2327;
                margin-bottom: 16px;
                display: none;
            }
            #navi-table-count { font-size: 12px; font-weight: 400; color: #646970; }
            /* Empty state */
            .navi-empty-state { text-align: center; padding: 48px 20px; }
            .navi-empty-state img { width: 140px; height: auto; margin-bottom: 18px; opacity: .9; }
            .navi-empty-state h3 { font-size: 15px; font-weight: 600; color: #1d2327; margin: 0 0 8px; }
            .navi-empty-state p { font-size: 14px; color: #646970; margin: 0 0 20px; line-height: 1.6; }
            .navi-empty-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
            /* Support buttons */
            .navi-support-btns { display: flex; gap: 10px; flex-wrap: wrap; }
            .navi-support-btn {
                display: inline-flex;
                align-items: center;
                padding: 9px 16px;
                border: 1px solid #dcdcde;
                border-radius: 3px;
                background: #f6f7f7;
                color: #1d2327;
                font-size: 14px;
                text-decoration: none;
                transition: background .15s, border-color .15s;
                white-space: nowrap;
            }
            .navi-support-btn:hover { background: #f0f0f1; border-color: #a7aaad; color: #1d2327; }
            /* Partner card */
            .navi-partner-card {
                background: linear-gradient(135deg, #1e3a5f 0%, #2271b1 100%);
                border-color: transparent;
            }
            .navi-partner-card .navi-card-header { border-bottom-color: rgba(255,255,255,.15); }
            .navi-partner-card .navi-card-header h2 { color: #fff; }
            .navi-partner-desc { color: rgba(255,255,255,.85); font-size: 14px; line-height: 1.7; margin: 0; }
            /* Footer */
            .navi-footer {
                text-align: center;
                color: #a7aaad;
                font-size: 12px;
                padding: 14px 0 4px;
                line-height: 1.6;
            }
            .navi-footer a { color: #a7aaad; text-decoration: underline; }
            .navi-footer a:hover { color: #646970; }
            /* ── Analytics ── */
            .navi-stats-row {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 20px;
            }
            .navi-stat-box {
                flex: 1;
                min-width: 110px;
                background: #f6f7f7;
                border: 1px solid #dcdcde;
                border-radius: 3px;
                padding: 12px 14px;
            }
            .navi-stat-value {
                font-size: 22px;
                font-weight: 700;
                color: #1d2327;
                line-height: 1.2;
                min-height: 28px;
                display: flex;
                align-items: center;
            }
            .navi-stat-label {
                font-size: 11px;
                color: #646970;
                text-transform: uppercase;
                letter-spacing: .5px;
                margin-bottom: 6px;
            }
            .navi-stat-sub { font-size: 11px; color: #a7aaad; margin-top: 3px; }
            /* Chart */
            .navi-chart-section { margin-bottom: 20px; }
            .navi-chart-title {
                font-size: 11px;
                font-weight: 600;
                color: #646970;
                text-transform: uppercase;
                letter-spacing: .5px;
                margin-bottom: 8px;
            }
            #navi-analytics-chart { height: 110px; overflow: hidden; }
            .navi-bar-wrap {
                height: 100%;
                display: flex;
                align-items: flex-end;
                gap: 2px;
                padding: 0 2px;
            }
            .navi-bar-col {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                height: 100%;
                min-width: 0;
                cursor: default;
                gap: 3px;
            }
            .navi-bar-inner {
                flex: 1;
                width: 100%;
                display: flex;
                align-items: flex-end;
                min-height: 0;
            }
            .navi-bar {
                width: 80%;
                background: #2271b1;
                border-radius: 2px 2px 0 0;
                min-height: 2px;
                margin: 0 auto;
                transition: background .15s;
            }
            .navi-bar-col:hover .navi-bar { background: #135e96; }
            .navi-bar-label {
                font-size: 9px;
                color: #a7aaad;
                white-space: nowrap;
                overflow: hidden;
                width: 100%;
                text-align: center;
                line-height: 1.3;
                height: 13px;
            }
            .navi-chart-empty {
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #646970;
                font-size: 12px;
                background: #fafafa;
            }
            /* Two-column bottom */
            .navi-analytics-cols { display: flex; gap: 20px; }
            .navi-analytics-col { flex: 1; min-width: 0; }
            .navi-col-header {
                font-size: 11px;
                font-weight: 600;
                color: #646970;
                text-transform: uppercase;
                letter-spacing: .5px;
                margin-bottom: 8px;
                padding-bottom: 8px;
                border-bottom: 1px solid #f0f0f1;
            }
            /* Top items */
            .navi-top-item { padding: 7px 0; border-bottom: 1px solid #f0f0f1; }
            .navi-top-item:last-child { border-bottom: none; }
            .navi-top-item-row {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 5px;
            }
            .navi-top-item-rank { font-size: 11px; color: #a7aaad; width: 16px; text-align: right; flex-shrink: 0; }
            .navi-top-item-name { flex: 1; font-size: 14px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .navi-top-item-count { font-size: 12px; font-weight: 600; color: #2271b1; flex-shrink: 0; }
            .navi-top-bar { height: 3px; background: #f0f0f1; border-radius: 2px; overflow: hidden; margin-left: 24px; }
            .navi-top-bar-fill { height: 100%; background: #2271b1; border-radius: 2px; }
            /* Recent clicks */
            .navi-recent-item {
                padding: 7px 0;
                border-bottom: 1px solid #f0f0f1;
                display: flex;
                align-items: flex-start;
                gap: 8px;
            }
            .navi-recent-item:last-child { border-bottom: none; }
            .navi-recent-platform {
                font-size: 10px;
                font-weight: 600;
                width: 20px;
                height: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 3px;
                flex-shrink: 0;
                margin-top: 1px;
            }
            .navi-recent-platform.mobile  { background: #dbeafe; color: #1e40af; }
            .navi-recent-platform.desktop { background: #dcfce7; color: #166534; }
            .navi-recent-info { flex: 1; min-width: 0; }
            .navi-recent-name { font-size: 12px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .navi-recent-url  { font-size: 11px; color: #646970; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .navi-recent-date { font-size: 11px; color: #a7aaad; flex-shrink: 0; white-space: nowrap; }
            /* Account embed toggle */
            .navi-account-layout { display: flex; align-items: center; flex-wrap: wrap; gap: 0; }
            .navi-embed-box {
                border-left: 1px solid #dcdcde;
                padding-left: 20px;
                margin-left: 20px;
                min-width: 210px;
            }
            .navi-embed-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .navi-embed-title { font-size: 14px; font-weight: 500; color: #1d2327; margin-bottom: 2px; }
            .navi-embed-desc  { font-size: 11px; color: #646970; }
            .navi-toggle-switch {
                position: relative;
                display: inline-block;
                width: 38px; height: 22px;
                flex-shrink: 0; cursor: pointer;
            }
            .navi-toggle-switch input { opacity: 0; width: 0; height: 0; }
            .navi-toggle-slider {
                position: absolute;
                top: 0; left: 0; right: 0; bottom: 0;
                background: #dcdcde;
                border-radius: 22px;
                transition: .2s;
            }
            .navi-toggle-slider:before {
                content: '';
                position: absolute;
                width: 16px; height: 16px;
                left: 3px; bottom: 3px;
                background: #fff;
                border-radius: 50%;
                box-shadow: 0 1px 3px rgba(0,0,0,.2);
                transition: .2s;
            }
            .navi-toggle-switch input:checked + .navi-toggle-slider { background: #00a32a; }
            .navi-toggle-switch input:checked + .navi-toggle-slider:before { transform: translateX(16px); }
            #navi-embed-saving { font-size: 11px; color: #646970; margin-top: 6px; min-height: 16px; }
        </style>

        <div id="navi-wrap">
            <div class="navi-page-header">
                <img src="<?php echo esc_url( plugins_url( '../assets/images/logo_wp_leftmenu.svg', __FILE__ ) ); ?>" alt="<?php echo esc_attr( NAVIWP_PRODUCT_NAME ); ?>">
                <span><?php echo esc_html( NAVIWP_PRODUCT_NAME ); ?></span>
            </div>

            <?php if ( $encoded ) :
                $decoded       = base64_decode( $encoded, true );
                $parts         = $decoded ? explode( ' | ', $decoded ) : array();
                $token_id      = ! empty( $parts[0] ) ? trim( $parts[0] ) : '—';
                $app_url       = add_query_arg( array(
                    NAVIWP_APP_QUERY_MARKET  => NAVIWP_APP_MARKET_VALUE,
                    NAVIWP_APP_QUERY_PAYLOAD => rawurlencode( $encoded ),
                ), NAVIWP_ENDPOINT_APP );
                $add_menu_url  = add_query_arg( 'deeplink', 'addMenu', $app_url );
                $embed_enabled = get_option( '_navi_embed_enabled', '1' );
            ?>
                <!-- Welcome notice (shown once after first registration) -->
                <div id="navi-welcome-notice" class="navi-welcome-notice">
                    The app is ready, please create your first menu by clicking the <strong>+Add menu</strong> button below.
                </div>

                <!-- Account card -->
                <div class="navi-card">
                    <div class="navi-card-header">
                        <h2>Connection</h2>
                    </div>
                    <div class="navi-card-body navi-account-layout">
                        <div class="navi-meta">
                            <div class="navi-meta-item">
                                <span class="label">Site ID</span>
                                <span class="value"><span class="navi-site-id-display"><?php echo esc_html( $token_id ); ?></span></span>
                            </div>
                            <div class="navi-meta-item">
                                <span class="label">Plan</span>
                                <span class="value" id="navi-account-plan">
                                    <a href="https://naviplus.io/pricing/" target="_blank" style="text-decoration:none;">
                                        <span class="navi-badge navi-badge-gray">&hellip;</span>
                                    </a>
                                </span>
                            </div>
                            <div class="navi-meta-item">
                                <span class="label">Domain</span>
                                <span class="value"><?php echo esc_html( $domain ); ?></span>
                            </div>
                        </div>
                        <div class="navi-embed-box">
                            <div class="navi-embed-row">
                                <div>
                                    <div class="navi-embed-title"><?php echo esc_html( 'Embed ' . NAVIWP_PRODUCT_NAME_ALIAS . ' into website' ); ?></div>
                                </div>
                                <label class="navi-toggle-switch">
                                    <input type="checkbox" id="navi-embed-toggle"<?php echo $embed_enabled ? ' checked' : ''; ?>>
                                    <span class="navi-toggle-slider"></span>
                                </label>
                            </div>
                            <div id="navi-embed-saving">
                                <span class="navi-badge <?php echo esc_attr( $embed_enabled ? 'navi-badge-green' : 'navi-badge-gray' ); ?>" id="navi-embed-status">
                                    <?php echo esc_html( $embed_enabled ? 'Active' : 'Disabled' ); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menus card -->
                <div class="navi-card">
                    <div class="navi-card-header">
                        <h2>Your Menus <span id="navi-table-count"></span></h2>
                        <div style="display:flex;gap:8px;">
                            <button id="navi-refresh-btn" class="navi-btn navi-btn-secondary navi-btn-sm">&#8635; Refresh</button>
                            <a href="<?php echo esc_url( $add_menu_url ); ?>" target="_blank" class="navi-btn navi-btn-sm">+ Add Menu</a>
                        </div>
                    </div>
                    <div class="navi-table-wrap">
                        <table class="navi-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Visible</th>
                                    <th>Updated</th>
                                    <th style="width:100px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="navi-table-body">
                                <tr><td colspan="5" style="text-align:center;padding:28px;color:#646970;">
                                    <span class="navi-spin"></span> Loading menus…
                                </td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Analytics card -->
                <div class="navi-card">
                    <div class="navi-card-header">
                        <h2>Analytics</h2>
                    </div>
                    <div class="navi-card-body">
                        <div id="navi-analytics-stats" class="navi-stats-row">
                            <div style="width:100%;text-align:center;padding:8px;"><span class="navi-spin"></span></div>
                        </div>
                        <div class="navi-chart-section">
                            <div class="navi-chart-title">Clicks per day</div>
                            <div id="navi-analytics-chart">
                                <div style="height:100%;display:flex;align-items:center;justify-content:center;color:#646970;gap:8px;"><span class="navi-spin"></span> Loading&hellip;</div>
                            </div>
                        </div>
                        <div class="navi-analytics-cols">
                            <div class="navi-analytics-col">
                                <div class="navi-col-header">Top Items</div>
                                <div id="navi-analytics-top"><span class="navi-spin"></span></div>
                            </div>
                            <div class="navi-analytics-col">
                                <div class="navi-col-header">Recent Clicks</div>
                                <div id="navi-analytics-recent"><span class="navi-spin"></span></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="navi-copy-msg" class="navi-notice success">Shortcode copied to clipboard!</div>

                <script>
                (function () {
                    var APP_URL      = '<?php echo esc_js( $app_url ); ?>';
                    var ADD_MENU_URL    = '<?php echo esc_js( $add_menu_url ); ?>';
                    var AJAX_URL        = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
                    var NONCE           = '<?php echo esc_js( wp_create_nonce( 'naviwp_nonce' ) ); ?>';
                    var EMPTY_STATE_IMG = '<?php echo esc_js( plugins_url( '../assets/images/emptystate-files.png', __FILE__ ) ); ?>';

                    var KIND_LABELS = {
                        1:   'Sticky / Bottom, Tab bar (Mobile + Desktop)',
                        2:   'Sticky / Mobile header',
                        11:  'Sticky / FAB, Support bar',
                        20:  'Section / Mobile header',
                        31:  'Section / Mobile Megamenu',
                        41:  'Section / Mobile grid menu',
                        42:  'Section / Mobile banner',
                        131: 'Section / Desktop Megamenu',
                        141: 'Context / Slide menu'
                    };

                    /* Kinds that support shortcode embed (megamenu + grid) */
                    var SHORTCODE_KINDS = [31, 41, 131];

                    var PRODUCT_NAME_ALIAS = <?php echo wp_json_encode( NAVIWP_PRODUCT_NAME_ALIAS ); ?>;

                    /* ── Close any open dropdown on outside click ── */
                    document.addEventListener('click', function (e) {
                        if (!e.target.closest('.navi-dropdown')) {
                            document.querySelectorAll('.navi-dropdown-menu.open').forEach(function (m) {
                                m.classList.remove('open');
                            });
                        }
                    });

                    function loadMenus() {
                        var tbody = document.getElementById('navi-table-body');
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#646970;"><span class="navi-spin"></span> Loading…</td></tr>';
                        document.getElementById('navi-table-count').textContent = '';

                        fetch(AJAX_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'naviwp_get_menus', nonce: NONCE })
                        })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (!res.success) {
                                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#d63638;">Error: ' + escHtml(res.data) + '</td></tr>';
                                return;
                            }
                            var menus = res.data.menus || [];
                            document.getElementById('navi-table-count').textContent = '(' + menus.length + ')';

                            if (!menus.length) {
                                tbody.innerHTML =
                                    '<tr><td colspan="5">' +
                                        '<div class="navi-empty-state">' +
                                            '<img src="' + EMPTY_STATE_IMG + '" alt="No menus yet">' +
                                            '<h3>No menu has been created yet. Let\'s get started!</h3>' +
                                            '<p>Click <strong>Create new menu</strong> to get started &mdash; don\'t worry, we\'ll guide you step by step!</p>' +
                                            '<div class="navi-empty-actions">' +
                                                '<a id="navi-create-menu-btn" href="' + ADD_MENU_URL + '" target="_blank" class="navi-btn">+ Add menu</a>' +
                                                '<a href="https://naviplus.io/demo/" target="_blank" class="navi-btn navi-btn-secondary">View example</a>' +
                                            '</div>' +
                                        '</div>' +
                                    '</td></tr>';
                                if (sessionStorage.getItem('navi_just_registered')) {
                                    sessionStorage.removeItem('navi_just_registered');
                                    var notice = document.getElementById('navi-welcome-notice');
                                    if (notice) notice.style.display = 'block';
                                    setTimeout(function () {
                                        var btn = document.getElementById('navi-create-menu-btn');
                                        if (btn) btn.classList.add('navi-btn-pulse');
                                    }, 100);
                                }
                                return;
                            }

                            tbody.innerHTML = menus.map(function (menu) {
                                var visibleBadge = '<span class="navi-badge navi-badge-blue">' + escHtml(String(menu.visible)) + '</span>';

                                var updated = menu.updated_date ? menu.updated_date.substring(0, 10) : '—';

                                var hasShortcode = SHORTCODE_KINDS.indexOf(menu.kind) !== -1;
                                var sc = '[naviwp embed_id=&quot;' + menu.embed_id + '&quot;]';
                                var editUrl = ADD_MENU_URL.replace('deeplink=addMenu', 'deeplink=editMenu[' + menu.id + ']');

                                var dropdownItems = '';
                                if (hasShortcode) {
                                    dropdownItems =
                                        '<button class="navi-dd-copy" onclick="naviCopyShortcode(\'' + menu.embed_id + '\')">' +
                                            'Copy shortcode ' + sc +
                                        '</button>';
                                } else {
                                    dropdownItems =
                                        '<span style="display:block;padding:8px 12px;font-size:12px;color:#646970;font-style:italic;max-width:240px;white-space:normal;">' +
                                            'Note: Will be displayed when &ldquo;Embed ' + escHtml(PRODUCT_NAME_ALIAS) + ' into website&rdquo; toggle is enabled.' +
                                        '</span>';
                                }

                                var thumb = menu.thumbnail_url
                                    ? '<img src="' + escHtml(menu.thumbnail_url) + '" alt="" ' +
                                        'style="width:42px;border-radius:2px;' +
                                        'flex-shrink:0;" ' +
                                        'onerror="this.style.display=\'none\'">'
                                    : '';

                                return '<tr>' +
                                    '<td><div style="display:flex;align-items:center;gap:10px;">' +
                                        thumb +
                                        '<div><strong>' + escHtml(menu.name) + '</strong><br>' +
                                        '<span style="font-size:12px;color:#a7aaad;">' + escHtml(menu.embed_id) + '</span></div>' +
                                    '</div></td>' +
                                    '<td><span class="navi-badge navi-badge-blue">' + escHtml(KIND_LABELS[menu.kind] || menu.kind_label || 'Unknown') + '</span></td>' +
                                    '<td>' + visibleBadge + '</td>' +
                                    '<td style="color:#646970;">' + escHtml(updated) + '</td>' +
                                    '<td>' +
                                        '<div class="navi-actions">' +
                                            '<a href="' + editUrl + '" target="_blank" class="navi-btn navi-btn-secondary navi-btn-sm">Edit</a>' +
                                            '<div class="navi-dropdown">' +
                                                '<button class="navi-dropdown-toggle" onclick="naviToggleDropdown(this)">···</button>' +
                                                '<div class="navi-dropdown-menu">' + dropdownItems + '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</td>' +
                                '</tr>';
                            }).join('');
                        })
                        .catch(function () {
                            tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#d63638;">Could not reach the server. Please try again.</td></tr>';
                        });
                    }

                    window.naviToggleDropdown = function (btn) {
                        var menu = btn.nextElementSibling;
                        var isOpen = menu.classList.contains('open');
                        document.querySelectorAll('.navi-dropdown-menu.open').forEach(function (m) { m.classList.remove('open'); });
                        if (!isOpen) {
                            var r = btn.getBoundingClientRect();
                            menu.style.top  = (r.bottom + 4) + 'px';
                            menu.style.left = (r.right - 240) + 'px';
                            menu.classList.add('open');
                        }
                    };

                    window.naviDeleteMenu = function (btn, embedId) {
                        document.querySelectorAll('.navi-dropdown-menu.open').forEach(function (m) { m.classList.remove('open'); });
                        if (!confirm('Delete "' + embedId + '"?\nThis action cannot be undone.')) return;

                        btn.disabled = true;
                        btn.textContent = 'Deleting…';

                        fetch(AJAX_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'naviwp_delete_menu', nonce: NONCE, embed_id: embedId })
                        })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (res.success) {
                                var row = btn.closest('tr');
                                row.style.transition = 'opacity 0.25s';
                                row.style.opacity = '0';
                                setTimeout(function () { row.remove(); }, 260);
                            } else {
                                alert('Error: ' + res.data);
                                btn.disabled = false;
                                btn.textContent = 'Delete';
                            }
                        })
                        .catch(function () {
                            alert('Could not reach the server. Please try again.');
                            btn.disabled = false;
                            btn.textContent = 'Delete';
                        });
                    };

                    window.naviCopyShortcode = function (embedId) {
                        document.querySelectorAll('.navi-dropdown-menu.open').forEach(function (m) { m.classList.remove('open'); });
                        var shortcode = '[naviwp embed_id="' + embedId + '"]';
                        navigator.clipboard.writeText(shortcode).then(function () {
                            var msg = document.getElementById('navi-copy-msg');
                            msg.style.display = 'block';
                            setTimeout(function () { msg.style.display = 'none'; }, 2500);
                        });
                    };

                    function escHtml(str) {
                        return String(str)
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;');
                    }

                    document.getElementById('navi-refresh-btn').addEventListener('click', loadMenus);
                    loadMenus();

                    /* ── Embed toggle ── */
                    document.getElementById('navi-embed-toggle').addEventListener('change', function () {
                        var enabled  = this.checked ? '1' : '0';
                        var statusEl = document.getElementById('navi-embed-status');
                        var savingEl = document.getElementById('navi-embed-saving');
                        savingEl.innerHTML = '<span style="color:#646970;font-size:11px;">Saving…</span>';

                        fetch(AJAX_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams({ action: 'naviwp_toggle_embed', nonce: NONCE, enabled: enabled })
                        })
                        .then(function (r) { return r.json(); })
                        .then(function (res) {
                            if (res.success) {
                                savingEl.innerHTML = enabled === '1'
                                    ? '<span class="navi-badge navi-badge-green" id="navi-embed-status">Active</span>'
                                    : '<span class="navi-badge navi-badge-gray"  id="navi-embed-status">Disabled</span>';
                            } else {
                                savingEl.innerHTML = '<span style="color:#d63638;font-size:11px;">Error saving.</span>';
                            }
                        })
                        .catch(function () {
                            savingEl.innerHTML = '<span style="color:#d63638;font-size:11px;">Error saving.</span>';
                        });
                    });
                })();
                </script>

                <script>
                (function () {
                    var AJAX_URL = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
                    var NONCE    = '<?php echo esc_js( wp_create_nonce( 'naviwp_nonce' ) ); ?>';

                    function escHtml(s) {
                        return String(s)
                            .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                    }

                    function fmtNum(n) {
                        return String(Math.floor(n || 0)).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    }

                    function fmtDate(s) {
                        if (!s) return '\u2014';
                        var p = s.split(' ');
                        var d = p[0] ? p[0].substring(5).replace('-', '/') : '';
                        var t = p[1] ? p[1].substring(0, 5) : '';
                        return d + ' ' + t;
                    }

                    function apiCall(analyticsAction, extra, cb) {
                        var params = { action: 'naviwp_analytics', nonce: NONCE, analytics_action: analyticsAction };
                        if (extra) Object.keys(extra).forEach(function(k) { params[k] = extra[k]; });
                        fetch(AJAX_URL, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: new URLSearchParams(params)
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(res) { cb(res.success ? res.data : null); })
                        .catch(function() { cb(null); });
                    }

                    function col(label, value, sub) {
                        return '<div class="navi-stat-box">' +
                            '<div class="navi-stat-label">' + label + '</div>' +
                            '<div class="navi-stat-value">' + value + '</div>' +
                            (sub ? '<div class="navi-stat-sub">' + sub + '</div>' : '') +
                        '</div>';
                    }

                    function badge(text, cls) {
                        return '<span class="navi-badge ' + cls + '">' + escHtml(text) + '</span>';
                    }

                    function renderSummary(data) {
                        var el   = document.getElementById('navi-analytics-stats');
                        var dash = '\u2014\u2014';
                        if (!data) {
                            el.innerHTML =
                                col('Navigation Events', dash) + col('Total Visits', dash) +
                                col('Menu Created', dash)      + col('SEO Support', dash) +
                                col('Service Status', dash);
                            return;
                        }

                        var nav    = data.navigation_events || {};
                        var visits = data.total_visits      || {};
                        var menus  = data.menu_created      || {};

                        var seoClass = data.seo_support    === 'ON'        ? 'navi-badge-green' : 'navi-badge-gray';
                        var svcClass = data.service_status === 'Very good' ? 'navi-badge-green' : 'navi-badge-blue';

                        /* Update Account card plan */
                        var planEl = document.getElementById('navi-account-plan');
                        if (planEl && data.plan) {
                            var planCls = { Elite: 'navi-badge-green', Business: 'navi-badge-blue', Starter: 'navi-badge-gray' };
                            var pc = planCls[data.plan] || 'navi-badge-gray';
                            planEl.innerHTML = '<a href="https://naviplus.io/pricing/" target="_blank" style="text-decoration:none;">' +
                                '<span class="navi-badge ' + pc + '">' + escHtml(data.plan) + '</span></a>';
                        }

                        el.innerHTML =
                            col('Navigation Events',
                                nav.value    !== undefined ? fmtNum(nav.value)    : dash,
                                nav.period   ? escHtml(nav.period)  : '') +
                            col('Total Visits',
                                visits.value !== undefined ? fmtNum(visits.value) : dash,
                                visits.limit ? 'of ' + fmtNum(visits.limit)       : '') +
                            col('Menu Created',
                                menus.value  !== undefined ? fmtNum(menus.value)  : dash,
                                menus.limit  ? 'of ' + menus.limit                : '') +
                            col('SEO Support',
                                data.seo_support    ? badge(data.seo_support,    seoClass) : dash) +
                            col('Service Status',
                                data.service_status ? badge(data.service_status, svcClass) : dash);
                    }

                    function renderChart(rows) {
                        var el = document.getElementById('navi-analytics-chart');
                        if (!rows || !rows.length) {
                            el.innerHTML = '<div class="navi-chart-empty">No click data for this period.</div>';
                            return;
                        }
                        var max = Math.max.apply(null, rows.map(function(r) { return r.clicks; }));
                        if (max === 0) max = 1;
                        var n = rows.length;
                        var every = n > 21 ? 7 : (n > 10 ? 5 : (n > 6 ? 3 : 1));
                        var html = '';
                        rows.forEach(function(r, i) {
                            var pct = Math.max(2, Math.round((r.clicks / max) * 100));
                            var lbl = (i % every === 0 || i === n - 1) ? escHtml(r.date.substring(5).replace('-', '/')) : '';
                            html += '<div class="navi-bar-col" title="' + escHtml(r.date) + ': ' + r.clicks + ' clicks">' +
                                '<div class="navi-bar-inner"><div class="navi-bar" style="height:' + pct + '%"></div></div>' +
                                '<div class="navi-bar-label">' + (lbl || '&nbsp;') + '</div>' +
                                '</div>';
                        });
                        el.innerHTML = '<div class="navi-bar-wrap">' + html + '</div>';
                    }

                    function renderTopItems(items) {
                        var el = document.getElementById('navi-analytics-top');
                        if (!items || !items.length) {
                            el.innerHTML = '<div style="color:#646970;font-size:12px;padding:12px 0;">No data for this period.</div>';
                            return;
                        }
                        var max = items[0].clicks || 1;
                        el.innerHTML = items.slice(0, 10).map(function(item, i) {
                            var pct = Math.round((item.clicks / max) * 100);
                            return '<div class="navi-top-item">' +
                                '<div class="navi-top-item-row">' +
                                    '<span class="navi-top-item-rank">' + (i + 1) + '</span>' +
                                    '<span class="navi-top-item-name" title="' + escHtml(item.name) + '">' + escHtml(item.name) + '</span>' +
                                    '<span class="navi-top-item-count">' + fmtNum(item.clicks) + '</span>' +
                                '</div>' +
                                '<div class="navi-top-bar"><div class="navi-top-bar-fill" style="width:' + pct + '%"></div></div>' +
                            '</div>';
                        }).join('');
                    }

                    function renderRecentClicks(clicks) {
                        var el = document.getElementById('navi-analytics-recent');
                        if (!clicks || !clicks.length) {
                            el.innerHTML = '<div style="color:#646970;font-size:12px;padding:12px 0;">No recent clicks.</div>';
                            return;
                        }
                        el.innerHTML = clicks.slice(0, 20).map(function(c) {
                            var isMobile = c.platform === 'M';
                            return '<div class="navi-recent-item">' +
                                '<span class="navi-recent-platform ' + (isMobile ? 'mobile' : 'desktop') + '">' +
                                    (isMobile ? 'M' : 'D') +
                                '</span>' +
                                '<div class="navi-recent-info">' +
                                    '<div class="navi-recent-name" title="' + escHtml(c.item_name) + '">' + escHtml(c.item_name) + '</div>' +
                                    '<div class="navi-recent-url" title="' + escHtml(c.from_url) + '">' + escHtml(c.from_url) + '</div>' +
                                '</div>' +
                                '<div class="navi-recent-date">' + escHtml(fmtDate(c.action_date)) + '</div>' +
                            '</div>';
                        }).join('');
                    }

                    function loadAll() {
                        document.getElementById('navi-analytics-stats').innerHTML =
                            '<div style="width:100%;text-align:center;padding:8px;"><span class="navi-spin"></span></div>';
                        document.getElementById('navi-analytics-chart').innerHTML =
                            '<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#646970;gap:8px;">' +
                            '<span class="navi-spin"></span> Loading\u2026</div>';
                        document.getElementById('navi-analytics-top').innerHTML    = '<span class="navi-spin"></span>';
                        document.getElementById('navi-analytics-recent').innerHTML = '<span class="navi-spin"></span>';

                        apiCall('summary',        {},                    renderSummary);
                        apiCall('clicks-by-date', { days: 30 },          function(data) { renderChart(data ? data.rows  : null); });
                        apiCall('top-items',      { days: 30, limit: 10 }, function(data) { renderTopItems(data ? data.items : null); });
                        apiCall('recent-clicks',  { limit: 20 },         function(data) { renderRecentClicks(data ? data.clicks : null); });
                    }

                    loadAll();
                })();
                </script>

            <?php else : ?>
                <div class="navi-card" style="font-size: 14px;max-width: 800px;margin:auto">
                    <div class="navi-card-body">
                        <p class="navi-intro-desc">
                            <img src="<?php echo esc_url( plugins_url( '../assets/images/1.webp', __FILE__ ) ); ?>" style="width: 100%; height: auto;">
                            <p>Default website menus are often simple and outdated, making it harder for visitors to find products or key pages. Poor navigation slows browsing and hurts the shopping experience. <?php echo esc_html( NAVIWP_PRODUCT_NAME_ALIAS ); ?> helps you build modern navigation for your WordPress site:</p> 
                            <ul>    
                                <li>• Mega menus</li>
                                <li>• Tab bars</li>
                                <li>• Slide menus (Hamburger menu)</li>
                                <li>• Grid menus</li>
                                <li>• And more</li>
                            </ul>
                            <p>Use ready-made templates and drag-and-drop editing to create beautiful navigation without writing any code.</p>
                        </p>
                        <button id="navi-register-btn" class="navi-btn navi-btn-large">Create your first menu</button>
                        <div id="navi-message" class="navi-notice"></div>
                    </div>
                </div>

                <script>
                document.getElementById('navi-register-btn').addEventListener('click', function () {
                    var btn = this;
                    btn.disabled = true;
                    btn.textContent = 'Initializing the application…';

                    var msg = document.getElementById('navi-message');
                    msg.className = 'navi-notice';
                    msg.style.display = 'none';

                    fetch('<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({
                            action: 'naviwp_register',
                            nonce:  '<?php echo esc_js( wp_create_nonce( 'naviwp_register_nonce' ) ); ?>'
                        })
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.success) {
                            sessionStorage.setItem('navi_just_registered', '1');
                            location.reload();
                        } else {
                            msg.className = 'navi-notice error';
                            msg.textContent = 'Error: ' + res.data;
                            msg.style.display = 'block';
                            btn.disabled = false;
                            btn.textContent = 'Create your first menu';
                        }
                    })
                    .catch(function () {
                        msg.className = 'navi-notice error';
                        msg.textContent = 'Could not reach the server. Please try again.';
                        msg.style.display = 'block';
                        btn.disabled = false;
                        btn.textContent = 'Create your first menu';
                    });
                });
                </script>
            <?php endif; ?>

            <?php if ( $encoded ) : ?>
            <!-- Support card -->
            <div class="navi-card">
                <div class="navi-card-header">
                    <h2>Have a question or comment?</h2>
                </div>
                <div class="navi-card-body">
                    <div class="navi-support-btns">
                        <a href="https://help.naviplus.io/" target="_blank" class="navi-support-btn">Documentation Support &nearr;</a>
                        <a href="mailto:khoipham@naviplus.io" class="navi-support-btn">Email to me</a>
                        <a href="https://wa.me/84981911011" target="_blank" class="navi-support-btn">Message me on WhatsApp</a>
                    </div>
                </div>
            </div>

            <?php if ( defined( 'NAVIWP_SHOW_PARTNER_CARD' ) && NAVIWP_SHOW_PARTNER_CARD ) : ?>
            <!-- Partner card -->
            <div class="navi-card navi-partner-card">
                <div class="navi-card-header">
                    <h2>Partner with us &ndash; <?php echo esc_html( NAVIWP_PRODUCT_NAME_ALIAS ); ?> RevShare</h2>
                </div>
                <div class="navi-card-body">
                    <p class="navi-partner-desc">Build Shopify websites for clients? Turn your projects into passive income. Become a <?php echo esc_html( NAVIWP_PRODUCT_NAME_ALIAS ); ?> Partner and earn recurring revenue every month.</p>
                </div>
            </div>
            <?php endif; ?>


            <?php endif; ?>
        </div>
        <?php
    }
}
