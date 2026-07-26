<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dbhasrs9kbpuu4' );

/** Database username */
define( 'DB_USER', 'uqjxjntfgg2ak' );

/** Database password */
define( 'DB_PASSWORD', 'tujndcuxth85' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          '`^dAGwfN/CF(JEpG?vv?BgSMOsKcj=G+_-elWHpXPV%v{gO!lha1uAgplTN*L))K' );
define( 'SECURE_AUTH_KEY',   'lSo[_G^d1T!F{I]:wW3T?$.3l}OeLhQ`O*WGv2>eGubFz)ZX]hPF<FkrMGU>,Zrm' );
define( 'LOGGED_IN_KEY',     'aAq$jQV7/V_s;oYZ)S{VwX5!KlQ(F{b8YF$b},O6Xwd3*CwyW3bC$tl06PhkA~/)' );
define( 'NONCE_KEY',         'A8h}z/zF=w0aY(G3{7uXCQeEwoe3?B5R(irb.]f+!>,E3wi;XzOfa6_ATY<ZcBp2' );
define( 'AUTH_SALT',         '$0iAw~e$2:6.0x$^w%C7q5NQ34}-t:(EtTGAMLEL5=fv7s f,A*H_{uxa|-!?H!O' );
define( 'SECURE_AUTH_SALT',  'i>K>#r`JFN_^nV[2RQUHV5vb@b]a$)KNm&X~*k]v{+BViYK2nI^FY*sW4a^3}Is.' );
define( 'LOGGED_IN_SALT',    ',?SBj5 ;y8i9vMG6KQ4qof%wfeLTY=aq&_aPlkNsas4e{+LdOGuQ[%0lrtT6OGR`' );
define( 'NONCE_SALT',        'yv<2`cOB(oK9;},rU(7%Zu(I123yf^<^zh]ge-r_9Q?nE*9A3qjU4wC)/VfX7~[a' );
define( 'WP_CACHE_KEY_SALT', 'hif9]6B6)](O*[xEf~[q-|aBG>%YF}xq!{{!^&=i;zVOK$_u8ZP6=Q~u!s%PRM[y' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wmv_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
@include_once('/var/lib/sec/wp-settings-pre.php'); // Added by SiteGround WordPress management system
require_once ABSPATH . 'wp-settings.php';
@include_once('/var/lib/sec/wp-settings.php'); // Added by SiteGround WordPress management system
