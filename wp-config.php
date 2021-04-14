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
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'QksRxSgY6jOqcHU1ZGiA1ZGU1YreJjg6Fu8LkA4soKe+IBmsH6R6Z5fvP913urdNaDFsL1YRXZHGjBYeNjNeDQ==');
define('SECURE_AUTH_KEY',  'iCb2MlHo28P5IK6CWVf2ru6lgWCy+YarD38YBy5k/1MfxPo9zxEbXOB0YFrreuXGtnBFTa+0Mh+zpT3n8dUTog==');
define('LOGGED_IN_KEY',    'UT0bCdWp9IWEyNv3TUaA+ofYAZjoqNbehjDusAC/iEMc51fwJ/N+HJtcaEJUpZfY7aRx5DZ6q0+mQoY0CgXcag==');
define('NONCE_KEY',        'QCHt41d7k2i43C1GL7D/4OVUq+2vxHzD60hKDvzrIQSsu2lGyy+ZMhf/3Cdzbgrn8yqMwyUK4t3FauYPVnkpUw==');
define('AUTH_SALT',        'QDxMQSxNX6/ErRtwbZcSohAMnqdnYEu3OmqNsChJD5xIBlOKcFL03I7tmsn5JPTNHr4O4OS7rvloG0gy5xq2GQ==');
define('SECURE_AUTH_SALT', 'At+K/N3bWsKDHhZB2Qva45oCoWUGLiWi3IbA98zjy7Mo52nrsJiW9KjjxUdp6yiyMrTBATbVyT4mM8oC6/poAg==');
define('LOGGED_IN_SALT',   'jTzwgy6G7h/Ms8C9aI+iA/ty3DRCBDRzEoYf2mPb+KSaDF6FV/KOoOYz9ekgTe2syWGwsHLgIDtghiMHRXBqXg==');
define('NONCE_SALT',       'dlLnmJ8cqq/al+PKq5U5tsrhU9tpa7P6GEtyv1iDKRVQCb/2HPp/0rBlDxg16toMQLzkZ5X2e/ymUUt6zXM7fA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
