<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'fourwin6_mage217');

/** MySQL database username */
define('DB_USER', 'fourwin6_mage217');

/** MySQL database password */
define('DB_PASSWORD', '388S7-8-P9');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'tb38CM%b^qdDMmc0;sFCry/M]i(!VB&#p#GS3:jlV-|a3PwTd(f<m|_=(ab}GJ_p');
define('SECURE_AUTH_KEY',  'pM^a2o_iu5B10%8<zT{G+WN~~e,G e<uv{K6+!xz$/&!eMs33[krQkI5!JKmdlj>');
define('LOGGED_IN_KEY',    'S;p?z}@0Gxq/^3SVvFRWL1x3@H@shU~t~} a&]-NY3j}E+Jua)b%~fD]m(m-LyB3');
define('NONCE_KEY',        'ouBSYV*Qd#:>:FK/X;[UU=WidS:UD<GF;euL!fw+(e}j>7o1.whw~^f|{/6e6C#x');
define('AUTH_SALT',        '+BM)uOdH):cA6oz7rt-vKwW*-jJj?19a9}O:LOntR,c2YH:V$8Xmp@I}h(_}TZ~^');
define('SECURE_AUTH_SALT', 'gdjQo-TPBb?d#JE>xIEPPo7QGCD8U9H`|l^|(#p4hWl28Eaq3C|27(X+m1anXP &');
define('LOGGED_IN_SALT',   'z!!QY@KkS(,xKZbM/t^C-%LaB/|M+ed4^|J++#[og}wCiAQrv2/f[6!B@OEa@_+k');
define('NONCE_SALT',       'MYbjV?W[IVrF-gI*HuvlcJ&sC5;r1.K=(ACgB~ir?3|D.M~}adRIE;EaHldt7iK7');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
