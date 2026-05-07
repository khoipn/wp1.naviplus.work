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
        add_action( 'admin_enqueue_scripts',      array( $this, 'enqueue_admin_assets' ) );
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
     * Enqueue admin assets only on this plugin screen.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_admin_assets( $hook ) {
        if ( 'appearance_page_naviwp-app' !== $hook ) {
            return;
        }
        $asset_version = class_exists( 'Naviwp_Plugin' ) ? Naviwp_Plugin::get_version() : '1.0.0';

        $encoded = naviwp_get_site_link_option();
        $app_url = '';
        $add_menu_url = '';

        if ( $encoded ) {
            $app_url = add_query_arg(
                array(
                    NAVIWP_APP_QUERY_MARKET  => NAVIWP_APP_MARKET_VALUE,
                    NAVIWP_APP_QUERY_PAYLOAD => rawurlencode( $encoded ),
                ),
                NAVIWP_ENDPOINT_APP
            );
            $add_menu_url = add_query_arg( 'deeplink', 'addMenu', $app_url );
        }

        wp_enqueue_style(
            'naviwp-admin',
            plugins_url( '../assets/css/admin.css', __FILE__ ),
            array(),
            $asset_version
        );

        wp_enqueue_script(
            'naviwp-admin',
            plugins_url( '../assets/js/admin.js', __FILE__ ),
            array(),
            $asset_version,
            true
        );

        wp_localize_script(
            'naviwp-admin',
            'naviwpAdminData',
            array(
                'ajaxUrl'          => admin_url( 'admin-ajax.php' ),
                'nonce'            => wp_create_nonce( 'naviwp_nonce' ),
                'registerNonce'    => wp_create_nonce( 'naviwp_register_nonce' ),
                'appUrl'           => $app_url,
                'addMenuUrl'       => $add_menu_url,
                'emptyStateImg'    => plugins_url( '../assets/images/emptystate-files.png', __FILE__ ),
                'productNameAlias' => NAVIWP_PRODUCT_NAME_ALIAS,
            )
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

        $enabled_raw = isset( $_POST['enabled'] ) ? sanitize_text_field( wp_unslash( $_POST['enabled'] ) ) : '';
        $enabled     = ( '1' === $enabled_raw ) ? '1' : '0';
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
                                    <a href="https://naviplus.io/pricing/" target="_blank" rel="noopener noreferrer" style="text-decoration:none;">
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
                            <a href="<?php echo esc_url( $add_menu_url ); ?>" target="_blank" rel="noopener noreferrer" class="navi-btn navi-btn-sm">+ Add Menu</a>
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

            <?php else : ?>
                <div class="navi-card" style="font-size: 14px;max-width: 800px;margin:auto">
                    <div class="navi-card-body">
                        <div class="navi-intro-desc">
                            <img src="<?php echo esc_url( plugins_url( '../assets/images/1.webp', __FILE__ ) ); ?>" style="width: 100%; height: auto;" alt="<?php echo esc_attr( NAVIWP_PRODUCT_NAME ); ?>">
                            <p>Default website menus are often simple and outdated, making it harder for visitors to find products or key pages. Poor navigation slows browsing and hurts the shopping experience. <?php echo esc_html( NAVIWP_PRODUCT_NAME_ALIAS ); ?> helps you build modern navigation for your WordPress site:</p>
                            <ul>
                                <li>Mega menus</li>
                                <li>Tab bars</li>
                                <li>Slide menus (Hamburger menu)</li>
                                <li>Grid menus</li>
                                <li>And more</li>
                            </ul>
                            <p>Use ready-made templates and drag-and-drop editing to create beautiful navigation without writing any code.</p>
                        </div>
                        <button id="navi-register-btn" class="navi-btn navi-btn-large">Create your first menu</button>
                        <div id="navi-message" class="navi-notice"></div>
                    </div>
                </div>

            <?php endif; ?>

            <?php if ( $encoded ) : ?>
            <!-- Support card -->
            <div class="navi-card">
                <div class="navi-card-header">
                    <h2>Have a question or comment?</h2>
                </div>
                <div class="navi-card-body">
                    <div class="navi-support-btns">
                        <a href="https://help.naviplus.io/" target="_blank" rel="noopener noreferrer" class="navi-support-btn">Documentation Support &nearr;</a>
                        <a href="mailto:khoipham@naviplus.io" class="navi-support-btn">Email to me</a>
                        <a href="https://wa.me/84981911011" target="_blank" rel="noopener noreferrer" class="navi-support-btn">Message me on WhatsApp</a>
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
