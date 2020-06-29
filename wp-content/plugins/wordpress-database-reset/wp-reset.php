<?php
/*
Plugin Name: WP Database Reset
Plugin URI: https://wordpress.org/plugins/wordpress-database-reset/
Description: Reset all or some WP database tables back to their original state.
Version: 3.15
Author: WebFactory Ltd
Author URI: https://www.webfactoryltd.com/
License: GNU General Public License
Text-domain: wordpress-database-reset
*/

define( 'DB_RESET_VERSION', '3.15' );
define( 'DB_RESET_PATH', dirname( __FILE__ ) );
define( 'DB_RESET_NAME', basename( DB_RESET_PATH ) );
define( 'DB_RESET_FILE', __FILE__ );
define( 'AUTOLOADER', DB_RESET_PATH . '/lib/class-plugin-autoloader.php' );

require_once( DB_RESET_PATH . '/lib/helpers.php' );

register_activation_hook( __FILE__, 'db_reset_activate' );

load_plugin_textdomain( 'wordpress-database-reset' );

if ( file_exists( AUTOLOADER ) ) {
  require_once( AUTOLOADER );
  new Plugin_Autoloader( DB_RESET_PATH );

  add_action(
    'wp_loaded',
    array ( new DB_Reset_Manager( DB_RESET_VERSION ), 'run' )
  );
}

if ( is_command_line() ) {
  require_once( __DIR__ . '/class-db-reset-command.php' );
}
