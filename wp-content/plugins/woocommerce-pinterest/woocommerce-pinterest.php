<?php

use Premmerce\WooCommercePinterest\PinterestPlugin;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsTaxonomy;
use Premmerce\WooCommercePinterest\Installer\Installer;

/**
 *
 * Plugin Name:       Pinterest for WooCommerce
 * Plugin URI:        https://premmerce.com/woocommerce-pinterest/
 * Description:       Track Conversions, Rich Pins product data and bulk creation and editing of Pins
 * Version:           2.3.1
 * Author:            premmerce
 * Author URI:        https://premmerce.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-pinterest
 * Domain Path:       /languages
 *
 * WC requires at least: 3.5
 * WC tested up to: 4.6.0
 *
 * Woo: 4596443:2d20474ebd307aae8c752b60a48c5b0c
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

call_user_func( function () {

	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	$main = new PinterestPlugin( __FILE__ );

	register_activation_hook( __FILE__, array( $main, 'activate' ) );

	register_uninstall_hook( __FILE__, array( Installer::class, 'uninstall' ) );

	add_action( 'init', array( ( new PinterestTagsTaxonomy() ), 'registerTaxonomy' ), 0 );

	add_action( 'woocommerce_init', array( $main, 'run' ) );
} );

