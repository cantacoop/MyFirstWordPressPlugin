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
define('DB_NAME', 'mfwp');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

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
define('AUTH_KEY',         '>1<;SNtc=CYp.85~KRR}kfEe$8,#w&85-km?1}g-<ze[-y6MA[YdbI61:N:M=6:j');
define('SECURE_AUTH_KEY',  ',++d>^:px[SBT]7=Tnz$scNF4Ho2oTOWio`nD&)rSzC0_l?|1Z]>lE<e= ;qH@>E');
define('LOGGED_IN_KEY',    '|]gtOz9jy@EO+AA7+<P4gWxpVg_AdK38Q;3j 5{3q4V@njS!>rp9:Y`sXwcl@Bj-');
define('NONCE_KEY',        ';])edX[H#Y]vyCgv,F!R>N$knT<bNf:w1zmRVg$1N+KN~i:5YA_8;4!pQvb.;Y<{');
define('AUTH_SALT',        '53eoGBtT&V/1Pd|HEvm<wrGi}i70/9@<)d)s`G_^QXv?kmC?|S{2ztjWQt5>>Y*U');
define('SECURE_AUTH_SALT', 'd2jXy:}(+FX9D oj2u#X#CVfu=$T +=-K1E*`mV0(|ShZS.G=,ksMF:Jn^N6}Z4h');
define('LOGGED_IN_SALT',   '}I*UaR*[3vD]fBM@Il;EV7}s&wN?K-Mtt<Dy!(.;ZDB`UK(f>jONl}% Wsi*^nT|');
define('NONCE_SALT',       'b2-4#r56mi2JSo`)CWF5o-cffa9G6K;{8V^v[~eDDZqNXSpf3R%@qoW2})3lJMBK');

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
