<?php
/**
 * Wishlist Cron Handler
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Wishlist\Classes
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Cron_Extended' ) ) {
	/**
	 * This class handles cron for wishlist plugin
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Cron_Extended extends YITH_WCWL_Cron {

		/**
		 * Returns registered crons
		 *
		 * @return array Array of registered crons ans callbacks
		 */
		public function get_crons() {
			if ( empty( $this->crons ) ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_extended_crons
				 *
				 * Filter the additional cron tasks created in the plugin.
				 *
				 * @param array $crons Plugin crons
				 *
				 * @return array
				 */
				$this->crons = array_merge(
					parent::get_crons(),
					apply_filters(
						'yith_wcwl_extended_crons',
						array(
							'yith_wcwl_send_back_in_stock_email' => array(
								'schedule' => 'hourly',
								'callback' => array( $this, 'send_back_in_stock_email' ),
							),
						)
					)
				);
			}

			return $this->crons;
		}

		/**
		 * Send back in stock emails
		 *
		 * @return void
		 */
		public function send_back_in_stock_email() {
			// skip if email ain't active.
			$email_options = get_option( 'woocommerce_yith_wcwl_back_in_stock_settings', array() );

			if ( ! isset( $email_options['enabled'] ) || 'yes' !== $email_options['enabled'] ) {
				return;
			}

			// queue handling.
			$queue        = get_option( 'yith_wcwl_back_in_stock_queue', array() );
			$unsubscribed = get_option( 'yith_wcwl_unsubscribed_users', array() );

			if ( empty( $queue ) ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_back_in_stock_execution_limit
			 *
			 * Filter the execution limit of the 'Back in stock' email.
			 *
			 * @param int $limit Execution limit
			 *
			 * @return int
			 */
			$execution_limit = apply_filters( 'yith_wcwl_back_in_stock_execution_limit', 20 );
			$counter         = 1;

			foreach ( $queue as $user_id => $items ) {
				$user = get_user_by( 'id', $user_id );

				if ( ! $user || in_array( $user->user_email, $unsubscribed, true ) ) {
					continue;
				}

				/**
				 * DO_ACTION: send_back_in_stock_mail
				 *
				 * Allows to fire some action when the 'Back in stock' email is sent.
				 *
				 * @param WP_User $user  User object
				 * @param array   $items Wishlist items
				 */
				do_action( 'send_back_in_stock_mail', $user, $items );

				unset( $queue[ $user_id ] );

				if ( $execution_limit > 0 && ++$counter > $execution_limit ) {
					break;
				}
			}

			update_option( 'yith_wcwl_back_in_stock_queue', $queue );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Cron class
 *
 * @return \YITH_WCWL_Cron
 * @since 3.0.0
 */
function YITH_WCWL_Cron_Extended() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Cron_Extended::get_instance();
}
