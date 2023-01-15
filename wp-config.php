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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// Securing Session: https://www.php.net/manual/en/session.security.ini.php
@ini_set('session.cookie_httponly', true); 
@ini_set('session.cookie_secure', true); 
@ini_set('session.use_only_cookies', true);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();


/** This will ensure these are only loaded on Lando */
if (getenv('LANDO_INFO')) {
  /**  Parse the LANDO INFO  */
  $lando_info = json_decode(getenv('LANDO_INFO'));

  /** Get the database config */
  $database_config = $lando_info->database;
  /** The name of the database for WordPress */
  define('DB_NAME', $database_config->creds->database);
  /** MySQL database username */
  define('DB_USER', $database_config->creds->user);
  /** MySQL database password */
  define('DB_PASSWORD', $database_config->creds->password);
  /** MySQL hostname */
  define('DB_HOST', $database_config->internal_connection->host);

  /** URL routing (Optional, may not be necessary) */
  // define('WP_HOME','https://mysite.lndo.site');
  // define('WP_SITEURL','https://mysite.lndo.site');
} else {

  // ** MySQL settings - You can get this info from your web host ** //
  /** The name of the database for WordPress */
  define( 'DB_NAME', $_SERVER['DB_NAME'] );

  /** MySQL database username */
  define( 'DB_USER', $_SERVER['DB_USER'] );

  /** MySQL database password */
  define( 'DB_PASSWORD', $_SERVER['DB_PASSWORD'] );

  /** MySQL hostname */
  define( 'DB_HOST', $_SERVER['DB_HOST'] );

  /** Database charset to use in creating database tables. */
  define( 'DB_CHARSET', 'utf8' );

  /** The database collate type. Don't change this if in doubt. */
  define( 'DB_COLLATE', '' );
}

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
require_once 'wp-salts/wp-config.php';

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = $_SERVER['TABLE_PREFIX'];

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
//define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

if ( 
  isset( $_SERVER['HTTPS'] ) &&
  ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 ) ||
  isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
   $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' 
  ) {
  $protocol = 'https://';
} else {
  $protocol = 'http://';
}
 
if ( ( isset( $_SERVER['DISALLOW_FILE_MODS'] ) && ( $_SERVER['DISALLOW_FILE_MODS'] == true ) ) ) {
  define( 'DISALLOW_FILE_MODS',  true );
}
define( 'DISALLOW_FILE_EDIT', true );

if ( ! defined( 'WP_CONTENT_DIR' ) ) {
  define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
}

if ( ! defined( 'WP_CONTENT_URL' ) ) {
  define( 'WP_CONTENT_URL', $protocol . $_SERVER['SERVER_NAME'] . '/wp-content' );
}

if ( ! defined( 'WP_HOME' ) ) {
  define( 'WP_HOME', $protocol . $_SERVER['SERVER_NAME'] );
}

if ( ! defined( 'WP_SITEURL' ) ) {
  define( 'WP_SITEURL', $protocol . $_SERVER['SERVER_NAME'] );
}

if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
  define( 'WP_ENVIRONMENT_TYPE', $_ENV['WP_ENVIRONMENT_TYPE'] );
}

if ( ! defined( 'JETPACK_IP_ADDRESS_OK' ) ) {
	define('JETPACK_IP_ADDRESS_OK', $_SERVER['ALLOWED_IP'] ); 
}

$dotenv->required('IN_MAINTENANCE')->allowedValues(['ON', 'OFF']);
define( 'IN_MAINTENANCE', $_SERVER['IN_MAINTENANCE'] );

if( isset( $_ENV['WP_ENVIRONMENT_TYPE'] ) && $_ENV['WP_ENVIRONMENT_TYPE'] == 'local' ) {
  define( 'JETPACK_DEV_DEBUG', true );
}

define( 'DISABLE_WP_CRON', false );
// define( 'SCRIPT_DEBUG', true );
define( 'ALLOW_UNFILTERED_UPLOADS', true );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
