<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class WC_Recommender_CLI {

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
		require_once( 'class-wc-recommender-cli-rebuild.php' );
	}

	/**
	 * Sets up and hooks WP CLI to our CLI code.
	 */
	private function hooks() {
		WP_CLI::add_hook( 'after_wp_load', 'WC_Recommender_CLI_Rebuild::register_command' );
	}
}

new WC_Recommender_CLI;
