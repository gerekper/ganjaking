<?php
/**
 * Plugin Name: WooCommerce Helper
 * Plugin URI: https://woocommerce.com/products/
 * Description: Hi there. I'm here to help you manage subscriptions for your WooCommerce products, as well as help out when you need a guiding hand.
 * Version: 1.7.2
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Network: true
 * Requires at least: 3.8.1
 * Tested up to: 4.6.0
 *
 * Text Domain: woothemes-updater
 * Domain Path: /languages/
 */
/*
    Copyright 2012  Automattic  (email : info@automattic.com)

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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_admin() ) {
	add_action( 'plugins_loaded', '__woothemes_updater' );
}

function __woothemes_updater () {
    require_once( 'classes/class-woothemes-updater.php' );

    global $woothemes_updater;
    $woothemes_updater = new WooThemes_Updater( __FILE__, '1.7.2' );
}
