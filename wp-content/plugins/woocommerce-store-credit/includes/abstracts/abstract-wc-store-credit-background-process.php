<?php
/**
 * Abstract Background Process class.
 *
 * @package WC_Store_Credit/Abstracts
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once dirname( WC_PLUGIN_FILE ) . '/includes/abstracts/class-wc-background-process.php';
}

/**
 * Class WC_Store_Credit_Background_Process.
 */
abstract class WC_Store_Credit_Background_Process extends WC_Background_Process {

	/**
	 * Initiates new background process.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id() . '_wc_store_credit';

		parent::__construct();
	}

	/**
	 * Forces the process execution.
	 *
	 * @since 4.0.0
	 */
	public function force_process() {
		do_action( $this->cron_hook_identifier ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
	}
}
