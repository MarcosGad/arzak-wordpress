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
define( 'DB_NAME', 'arzak' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'BjFZ(s&AF_rMu^Tj!hEV,9rTYX20g1!h8 BM4|P2+==>PN0^qVR{BN$3a{(phu8h' );
define( 'SECURE_AUTH_KEY',  'cK96R!Dvq8Nx>1^K,UQ-}?l6h@mO{M- Fm. +o4Jx25el>qh-@jny.*mL5v_|-v|' );
define( 'LOGGED_IN_KEY',    'X!k~w2m/SmfZsOQ3v4bkv5(n3_:fq,SLo[z;kyjj]!~q{Mx+tlb*-hdY)QG{GG!>' );
define( 'NONCE_KEY',        'fK=EzKpbkb^/M`_3PXdDs/BRcEMTr4? In5:ReK /x{eHA}@(Lf+sJkp?r6)t_f6' );
define( 'AUTH_SALT',        'oB#rD]5y:A66o>FwV;_Zm#A/7;Iru,V_]oZnYCAnDHe=PDB}1}-(kVhY)V~_<dhI' );
define( 'SECURE_AUTH_SALT', '(Z(o3b.,Z{x,80lMM$e8,06@ahK={=&,,.EffvY{dm 8LcS`bZ&$&q;[b( k<*?E' );
define( 'LOGGED_IN_SALT',   'v_K]FO8b5IA9+ dE<DC3lRT-A%e~qJ]8aV>K_@$!8:eUe/As_Wp64(uZ01QS5Y+Q' );
define( 'NONCE_SALT',       '1y6MkM y;`KKZiLlvjyG&.R=/B7D<OF+85*?rqt*Tm#)[#d!,HL/]P5kGS](hI~k' );

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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
