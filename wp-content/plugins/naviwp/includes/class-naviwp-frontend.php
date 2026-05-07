<?php
/**
 * Front-end embed for Navi+ Menu Builder (official name: Naviplus Menu Builder; see NAVIWP_PRODUCT_NAME in includes/init.php).
 * Loader: live.naviplus.app/start.js
 *
 * INTEGRATION CONTRACT — do not change without an explicit product / Navi+ Menu Builder integration spec:
 * - Shortcode output: mount node (`#%embed_id%-container`) then immediately the same
 *   inline `window._navi_setting` push + `start.js`, via `wp_enqueue_script` + `wp_print_scripts`
 *   so output stays adjacent in HTML and satisfies Plugin Check (no raw script tags in source).
 * - Site-wide embed: same push + script via enqueue (printed in head with the rest of head scripts).
 * - Payload keys/values must stay aligned with defines in init.php and the deployed loader.
 *
 * Shortcodes: `[naviwp]` is the preferred tag; `[naviplus]` is a legacy alias — same callback, wording only.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Naviwp_Frontend {

    const SCRIPT_HANDLE_GLOBAL = 'navi-mnb-start-global';

    /**
     * Per-invocation handles for shortcode embeds (one queue item per start.js run).
     *
     * @var int
     */
    private static $embed_script_seq = 0;

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_global_start' ), 20 );
        add_filter( 'script_loader_tag', array( $this, 'add_defer_to_navi_scripts' ), 10, 3 );
        add_shortcode( 'naviplus', array( $this, 'shortcode_embed' ) );
        add_shortcode( 'naviwp', array( $this, 'shortcode_embed' ) );
        // Block editor: Paragraph does not run do_shortcode on plain text.
        add_filter( 'render_block', array( $this, 'render_block_paragraph_shortcodes' ), 10, 2 );
        add_filter( 'widget_block_content', array( $this, 'maybe_do_shortcode_in_html' ), 11 );
        add_filter( 'widget_text', array( $this, 'maybe_do_shortcode_in_html' ), 11 );
    }

    /**
     * Run our shortcodes when they appear as plain text inside a core Paragraph block.
     *
     * @param string $block_content Rendered block HTML.
     * @param array  $block         Block data.
     * @return string
     */
    public function render_block_paragraph_shortcodes( $block_content, $block ) {
        if ( empty( $block['blockName'] ) || 'core/paragraph' !== $block['blockName'] ) {
            return $block_content;
        }
        if ( false === strpos( $block_content, '[naviwp' ) && false === strpos( $block_content, '[naviplus' ) ) {
            return $block_content;
        }
        if ( preg_match( '/^\s*<p[^>]*>\s*(\[(?:naviwp|naviplus)[^\]]*\])\s*<\/p>\s*$/is', $block_content, $m ) ) {
            return do_shortcode( $m[1] );
        }
        return do_shortcode( $block_content );
    }

    /**
     * Run shortcodes in widget HTML when our tags are present.
     *
     * @param string $html Widget or block-widget HTML.
     * @return string
     */
    public function maybe_do_shortcode_in_html( $html ) {
        if ( ! is_string( $html ) || $html === '' ) {
            return $html;
        }
        if ( false === strpos( $html, '[naviwp' ) && false === strpos( $html, '[naviplus' ) ) {
            return $html;
        }
        return do_shortcode( $html );
    }

    /**
     * Site-wide embed: enqueue start.js for head (not wp_footer).
     */
    public function maybe_enqueue_global_start() {
        if ( is_admin() ) {
            return;
        }
        if ( ! get_option( '_navi_embed_enabled', '1' ) ) {
            return;
        }
        $token_id = $this->get_token_id();
        if ( ! $token_id ) {
            return;
        }
        $payload = array(
            NAVIWP_EMBED_FIELD_SITE_HANDLE => $token_id,
            NAVIWP_EMBED_KEY_ENV           => NAVIWP_EMBED_VALUE_ENV,
        );
        $this->register_enqueue_start_script( self::SCRIPT_HANDLE_GLOBAL, $payload, false );
    }

    /**
     * Defer Navi+ Menu Builder loader tags (WP &lt; 6.3 has no script strategy).
     *
     * @param string $tag    The script HTML.
     * @param string $handle The script handle.
     * @param string $src    The script source URL.
     * @return string
     */
    public function add_defer_to_navi_scripts( $tag, $handle, $src ) {
        if ( 0 !== strpos( $handle, 'navi-mnb-' ) ) {
            return $tag;
        }
        if ( false !== strpos( $tag, ' defer' ) ) {
            return $tag;
        }
        $tag = preg_replace( '/\s+async\b/', '', $tag );
        return str_replace( '<script ', '<script defer ', $tag );
    }

    /**
     * Register + enqueue start.js with inline queue push; optional WP 6.3+ defer strategy.
     *
     * @param string               $handle    Unique script handle.
     * @param array<string, string> $payload   Object passed to window._navi_setting.push().
     * @param bool                 $in_footer Passed to wp_register_script.
     */
    private function register_enqueue_start_script( $handle, array $payload, $in_footer ) {
        $ver = Naviwp_Plugin::get_version();
        wp_register_script( $handle, NAVIWP_START_JS, array(), $ver, $in_footer );
        wp_add_inline_script(
            $handle,
            sprintf( '(window._navi_setting ||= []).push(%s);', wp_json_encode( $payload ) ),
            'before'
        );
        if ( function_exists( 'wp_script_add_data' ) ) {
            wp_script_add_data( $handle, 'strategy', 'defer' );
        }
        wp_enqueue_script( $handle );
    }

    /**
     * Print enqueued script(s) for this handle at the current output position.
     *
     * @param string $handle Script handle.
     * @return string
     */
    private function get_printed_script_html( $handle ) {
        if ( ! wp_script_is( $handle, 'enqueued' ) ) {
            return '';
        }
        ob_start();
        wp_print_scripts( $handle );
        return (string) ob_get_clean();
    }

    /**
     * Shortcodes [naviwp] and legacy [naviplus] — container + loader directly under it in content.
     */
    public function shortcode_embed( $atts, $content = null, $tag = '' ) {
        $sc_tag = in_array( $tag, array( 'naviwp', 'naviplus' ), true ) ? $tag : 'naviwp';
        $atts   = shortcode_atts( array( 'embed_id' => '' ), $atts, $sc_tag );

        $embed_id = sanitize_text_field( $atts['embed_id'] );
        if ( ! $embed_id ) {
            return '';
        }

        $token_id = $this->get_token_id();
        if ( ! $token_id ) {
            return '';
        }

        $payload = array(
            NAVIWP_EMBED_FIELD_SITE_HANDLE => $token_id,
            NAVIWP_EMBED_KEY_MARKET        => NAVIWP_EMBED_VALUE_MARKET,
            NAVIWP_EMBED_KEY_EMBED_ID      => $embed_id,
        );

        ++self::$embed_script_seq;
        $handle = 'navi-mnb-embed-' . self::$embed_script_seq;
        $this->register_enqueue_start_script( $handle, $payload, true );

        $html  = sprintf(
            '<div class="naviman_app section_naviman_app" id="%1$s-container"></div>',
            esc_attr( $embed_id )
        );
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML from WordPress script APIs.
        $html .= $this->get_printed_script_html( $handle );

        return $html;
    }

    /**
     * Public site id from the stored site link option.
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
}
