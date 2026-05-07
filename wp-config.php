<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */


define('FS_METHOD', 'direct');

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp1.naviplus.work' );

/** Database username */
define( 'DB_USER', 'khoipn' );

/** Database password */
define( 'DB_PASSWORD', '11011@aaA' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('WP_HOME', 'https://wp1.naviplus.work');
define('WP_SITEURL', 'https://wp1.naviplus.work');


define('FORCE_SSL_ADMIN', true);

if (
    isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
    && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
) {
    $_SERVER['HTTPS'] = 'on';
}
define('COOKIE_DOMAIN', '');





/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'u~gE3jXcd<],`UxSz.@dzs=e?yqM<3*(~a:>Xc<Ti5TQ&=;tjg_G~DN0t/w6zs$4' );
define( 'SECURE_AUTH_KEY',  'lJS2egqr+Vuf.e/$/]T9;wm0,q3e7Kn8e!W=<L.lK4^UAzu.?XGDe3(9QvQ|[Cur' );
define( 'LOGGED_IN_KEY',    ']QeaNOq:W[4d%:ZGw3^lvd`jea;;Ve9g/4sY`%CWs _(o<Hc}A; n&4d6=ENAdrV' );
define( 'NONCE_KEY',        '_lKX9_6%6vxC&@D4kQ>.s{W:(# O^pcYBPIk$0aZTF-dY*K_!:Aqcus`.-kK4~J[' );
define( 'AUTH_SALT',        'g69;#?Ml #0aV7ERMEl^6<F32bxd?iXosRq^`ZRB=l4G$mHt?[7pH,gX!51oX)(*' );
define( 'SECURE_AUTH_SALT', '^RF&,a0{2QV>8kqA%exNn2|HF(:EUtDt|unL9k;j@HsFN#jL3QaOJUNC^bKB^}~_' );
define( 'LOGGED_IN_SALT',   ')g3jXZ/S+HaiGX1zt0$(@t09N{yTu EH$$NlX0:nqP~iK=AoI h]dH+Rq9f&4`9F' );
define( 'NONCE_SALT',       '/R!a{(Tk`1$MkOXAGc!|(I{fCmFr$[ik68_pg@*R/o[!%lSwX)otuFy #hy$ha*N' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
