<?php
/**
 * WC_PB_CLI class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * DB updating and other stuff via WP-CLI.
 *
 * @class    WC_PB_CLI
 * @version  5.5.0
 */
class WC_PB_CLI {

	/**
	 * Load required files and hooks.
	 */
	public function __construct() {
		$this->includes();
		$this->hooks();
	}

	/**
	 * Load command files.
	 */
	private function includes() {
		require_once( WC_PB_ABSPATH . 'includes/cli/class-wc-pb-cli-update.php' );
	}

	/**
	 * Sets up and hooks WP CLI to our CLI code.
	 */
	private function hooks() {
		WP_CLI::add_hook( 'after_wp_load', 'WC_PB_CLI_Update::register_command' );
	}
}

new WC_PB_CLI;
