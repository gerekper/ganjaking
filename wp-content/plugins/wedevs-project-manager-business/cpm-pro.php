<?php
/**
 * Plugin Name: WP Project Manager Pro
 * Plugin URI: https://wedevs.com/wp-project-manager-pro/
 * Description: Premium version of WordPress Project Manager.
 * Author: weDevs
 * Author URI: https://wedevs.com
 * Version: 2.6.0
 * Text Domain: pm-pro
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
/**
 * Copyright (c) 2018 weDevs (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

require __DIR__ . '/bootstrap/start.php';

register_activation_hook( __FILE__, 'pm_pro_activate' );
register_deactivation_hook( __FILE__, 'pm_pro_deactive' );
add_action( 'wp_initialize_site', 'pm_pro_after_insert_site', 100 );
add_action( 'plugins_loaded', 'pm_pro_load_plugin_textdomain' );

/**
 * load plugin text domain
 * @return [type] [description]
 */
function pm_pro_load_plugin_textdomain() {
    load_plugin_textdomain( 'pm-pro', true, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function pm_pro_activate() {
    if ( is_multisite() && is_network_admin() ) {
        $sites = get_sites();

        foreach ( $sites as $key => $site ) {
            pm_pro_after_insert_site( $site );
        }
    } else {
        pm_pro_run_install();
    }
}

function pm_pro_after_insert_site( $blog ) {
    switch_to_blog( $blog->blog_id );

    pm_pro_run_install();

    restore_current_blog();
}

function pm_pro_run_install() {
    new \WeDevs\PM_Pro\Core\WP\Active();
}

function pm_pro_deactive() {
    new \WeDevs\PM_Pro\Core\WP\Deactive();
}
