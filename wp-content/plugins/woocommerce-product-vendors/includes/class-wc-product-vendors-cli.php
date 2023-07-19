<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

require_once( dirname( __FILE__ ) . '/cli/class-wc-product-vendors-migrate-vendor-admin-storage.php' );

/**
 * CLI Class.
 *
 * Register CLI commands.
 *
 * @category CLI
 * @package  WooCommerce Product Vendors/CLI
 * @version  future
 * @since future
 */
class WC_Product_Vendors_Cli {
	/**
	 * Constructor
	 *
	 * @access public
	 * @since future
	 * @version future
	 * @return void
	 */
	public function __construct() {
		WP_CLI::add_command( 'wcpv migrate-vendor-admins', 'WC_Product_Vendors_Cli_Vendor_Admin_Storage' );
	}
}

new WC_Product_Vendors_Cli();
