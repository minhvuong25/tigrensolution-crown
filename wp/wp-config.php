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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'crownsp_m23x' );

/** MySQL database username */
define( 'DB_USER', 'crownsecurityproducts' );

/** MySQL database password */
define( 'DB_PASSWORD', 'oM4~?#89!i6N' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'L5YG=03QR,A4Uf|S,vt,*$^baXlRg}h<abPnxrQ_{b(7. (g+UDi)Z3|E}!&tjkI' );
define( 'SECURE_AUTH_KEY',  'l;d0Lh#Cf};QS7=*ol*7!XmnVhjKOk]m(w}Ne]QBKw>m]%wtVi{3L;[.vB s#=f;' );
define( 'LOGGED_IN_KEY',    'P`=|J%o<~?nD2U&<pSR&F*?eVO9{_pyn!*a2H3TuO+-CJCou[dflv3Ll)v(qO1%_' );
define( 'NONCE_KEY',        '!<Pf@w47mJ2,R`kW8sBP$#S9.8EJ>taudJ.Po!)<}[+7|Q-LIjU2s5a$C52Lfo8x' );
define( 'AUTH_SALT',        '`gfmX8y8VB{w6%jJX|;0z*]xJAM6t)N#&OLfhp/9^xvIPNB=Br~V BG?1&Fmun=S' );
define( 'SECURE_AUTH_SALT', 'C3$==E!:aH-u+rR0E1A%Pl0y)J1D&J= 4?6&%WE_)#mrco;!5veP(zn1=bCjdZK-' );
define( 'LOGGED_IN_SALT',   '24m7*|%{B^t@)Yr#<7RhOL9zYu<hl4)H8b%(ypCn%BNr/!}@RbP{Oub}_/Q+sAXS' );
define( 'NONCE_SALT',       'Lv[m*kk{*fIKkvh&*BcHxnGRi0]=2W]QyK5Op>gI ROd@X$:%xoTPpsebQZ;pSz[' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
