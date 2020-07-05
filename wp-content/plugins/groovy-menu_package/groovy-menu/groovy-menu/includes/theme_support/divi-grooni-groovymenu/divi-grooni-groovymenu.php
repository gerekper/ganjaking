<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );
/*
Plugin Name: Divi Grooni Groovymenu
Plugin URI:  https://groovymenu.grooni.com
Description: add module with Groovy Menu plugin
Version:     1.0.3
Author:      grooni.com
Author URI:  https://grooni.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: groovy-menu
Domain Path: /languages

Divi Grooni Groovymenu is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Divi Grooni Groovymenu is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Divi Grooni Groovymenu. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/


if ( ! function_exists( 'groovymenu_initialize_extension' ) ) {
	/**
	 * Creates the extension's main class instance.
	 *
	 * @since 1.0.0
	 */
	function groovymenu_initialize_extension() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/DiviGrooniGroovyMenu_init.php';
	}

	add_action( 'divi_extensions_init', 'groovymenu_initialize_extension' );

}
