<?php
/*
  Plugin Name: WP Database Reset
  Plugin URI: https://wordpress.org/plugins/wordpress-database-reset/
  Description: Reset all or some WP database tables back to their original state.
  Version: 3.17
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  License: GNU General Public License
  Text-domain: wordpress-database-reset
  
  Copyright 2011 - 2020 WebFactory Ltd (email: support@webfactoryltd.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'DB_RESET_VERSION', '3.17' );
define( 'DB_RESET_PATH', dirname( __FILE__ ) );
define( 'DB_RESET_NAME', basename( DB_RESET_PATH ) );
define( 'DB_RESET_FILE', __FILE__ );
define( 'AUTOLOADER', DB_RESET_PATH . '/lib/class-plugin-autoloader.php' );

require_once( DB_RESET_PATH . '/lib/helpers.php' );

require_once 'wp301/wp301.php';
new wf_wp301(__FILE__, 'tools_page_database-reset');

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
