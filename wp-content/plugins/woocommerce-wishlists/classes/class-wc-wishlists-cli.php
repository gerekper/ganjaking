<?php

class WC_Wishlists_CLI {

	private static $registered = false;
	public static function register() {
		if (!self::$registered) {
			WP_CLI::add_command( 'wishlists', 'WC_Wishlists_CLI' );
		}
	}


	/**
	 * Sends wishlist price reduction notifications.
	 *
	 * ## OPTIONS
	 *
	 * [--resend]
	 * : Whether or not to resend notifications that may have already been sent.
	 *
	 * ## EXAMPLES
	 *
	 *     wp wishlist send_notifications --resend
	 *
	 * @when after_wp_load
	 */
	public function send_notifications($args, $assoc_args = []) {
		$resend = isset($assoc_args['resend']) ? $assoc_args['resend'] : false;

		WP_CLI::success( 'Begin Sending Messages.  This may take a while...' );
		$instance = WC_Wishlists_Cron::instance();
		$instance->send_price_changes($resend, true);
		WP_CLI::success( 'Finished Processing' );
	}

}
