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

if ( ! class_exists( 'YITH_WCWL_Cron_Premium' ) ) {
	/**
	 * This class handles cron for wishlist plugin
	 *
	 * @since 3.0.0
	 */
	class YITH_WCWL_Cron_Premium extends YITH_WCWL_Cron_Extended {

		/**
		 * Returns registered crons
		 *
		 * @return array Array of registered crons ans callbacks
		 */
		public function get_crons() {
			if ( empty( $this->crons ) ) {
				/**
				 * APPLY_FILTERS: yith_wcwl_premium_crons
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
						'yith_wcwl_premium_crons',
						array(
							'yith_wcwl_register_on_sale_items'   => array(
								'schedule' => 'daily',
								'callback' => array( $this, 'register_on_sale_items' ),
							),
							'yith_wcwl_send_on_sale_item_email'  => array(
								'schedule' => 'hourly',
								'callback' => array( $this, 'send_on_sale_item_email' ),
							),
							'yith_wcwl_send_promotion_email'     => array(
								'schedule' => 'hourly',
								'callback' => array( $this, 'send_promotion_email' ),
							),
						)
					)
				);
			}

			return $this->crons;
		}

		/**
		 * Register on sale items
		 *
		 * @return void
		 */
		public function register_on_sale_items() {
			$products_on_sale = wc_get_product_ids_on_sale();

			$items_on_sale = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
				array(
					'user_id'     => false,
					'session_id'  => false,
					'wishlist_id' => 'all',
					'on_sale'     => 1,
				)
			);

			if ( ! empty( $items_on_sale ) ) {
				foreach ( $items_on_sale as $item ) {
					$product_id = $item->get_product_id();

					if ( ! in_array( $product_id, $products_on_sale, true ) ) {
						$item->set_on_sale( false );
						$item->save();
					}
				}
			}

			if ( ! empty( $products_on_sale ) ) {
				foreach ( $products_on_sale as $product_id ) {
					$items = YITH_WCWL_Wishlist_Factory::get_wishlist_items(
						array(
							'user_id'     => false,
							'session_id'  => false,
							'wishlist_id' => 'all',
							'product_id'  => $product_id,
							'on_sale'     => 0,
						)
					);

					if ( ! empty( $items ) ) {
						foreach ( $items as $item ) {
							$item->set_on_sale( true );
							$item->save();
						}
					}
				}
			}
		}

		/**
		 * Send on sale item emails
		 *
		 * @return void
		 */
		public function send_on_sale_item_email() {
			// skip if email ain't active.
			$email_options = get_option( 'woocommerce_yith_wcwl_on_sale_item_settings', array() );

			if ( ! isset( $email_options['enabled'] ) || 'yes' !== $email_options['enabled'] ) {
				return;
			}

			// queue handling.
			$queue        = get_option( 'yith_wcwl_on_sale_item_queue', array() );
			$unsubscribed = get_option( 'yith_wcwl_unsubscribed_users', array() );

			if ( empty( $queue ) ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_on_sale_item_execution_limit
			 *
			 * Filter the execution limit of the 'On sale item' email.
			 *
			 * @param int $limit Execution limit
			 *
			 * @return int
			 */
			$execution_limit = apply_filters( 'yith_wcwl_on_sale_item_execution_limit', 20 );
			$counter         = 1;

			foreach ( $queue as $user_id => $items ) {
				$user = get_user_by( 'id', $user_id );

				if ( ! $user || in_array( $user->user_email, $unsubscribed, true ) ) {
					continue;
				}

				/**
				 * DO_ACTION: send_on_sale_item_mail
				 *
				 * Allows to fire some action when the 'On sale item' email is sent.
				 *
				 * @param WP_User $user  User object
				 * @param array   $items Wishlist items
				 */
				do_action( 'send_on_sale_item_mail', $user, $items );

				unset( $queue[ $user_id ] );

				if ( $execution_limit > 0 && ++$counter > $execution_limit ) {
					break;
				}
			}

			update_option( 'yith_wcwl_on_sale_item_queue', $queue );
		}

		/**
		 * Send promotional email, processing 1 request per time, never exceeding number of sending per hour
		 *
		 * @return void
		 */
		public function send_promotion_email() {
			// queue handling.
			$queue = get_option( 'yith_wcwl_promotion_campaign_queue', array() );

			if ( empty( $queue ) ) {
				return;
			}

			/**
			 * APPLY_FILTERS: yith_wcwl_promotion_email_limit
			 *
			 * Filter the execution limit of the 'Promotional' email.
			 *
			 * @param int $limit Execution limit
			 *
			 * @return int
			 */
			$execution_limit = apply_filters( 'yith_wcwl_promotion_email_limit', 20 );
			$queue           = array_values( $queue );

			/**
			 * APPLY_FILTERS: yith_wcwl_promotion_email_item
			 *
			 * Filter the execution limit of the 'Promotional' email.
			 *
			 * @param array $item Email content
			 *
			 * @return int
			 */
			$item = ! empty( $queue ) ? apply_filters( 'yith_wcwl_promotion_email_item', $queue[0] ) : false;

			if ( ! $item ) {
				return;
			}

			$receivers = $item['receivers'];

			if ( count( $receivers ) > $execution_limit ) {
				$receivers = array_slice( $receivers, 0, $execution_limit );
			}

			/**
			 * DO_ACTION: send_promotion_mail
			 *
			 * Allows to fire some action when the 'Promotional' email is sent.
			 *
			 * @param array $receivers Array of user IDs
			 * @param array $item      Email content
			 */
			do_action( 'send_promotion_mail', $receivers, $item );

			$queue[0]['receivers'] = array_diff( $queue[0]['receivers'], $receivers );

			if ( empty( $queue[0]['receivers'] ) ) {
				unset( $queue[0] );
			} else {
				$queue[0]['counters']['sent']    += $execution_limit;
				$queue[0]['counters']['to_send'] -= $execution_limit;
			}

			update_option( 'yith_wcwl_promotion_campaign_queue', $queue );
		}
	}
}

/**
 * Unique access to instance of YITH_WCWL_Cron class
 *
 * @return \YITH_WCWL_Cron
 * @since 3.0.0
 */
function YITH_WCWL_Cron_Premium() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid, Universal.Files.SeparateFunctionsFromOO
	return YITH_WCWL_Cron_Premium::get_instance();
}
