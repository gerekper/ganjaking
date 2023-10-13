<?php
/**
 * Class YITH_WCBK_Premium_Module
 * Handle the Premium module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Premium_Module' ) ) {
	/**
	 * YITH_WCBK_Premium_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_Premium_Module extends YITH_WCBK_Module {

		const KEY = 'premium';

		/**
		 * On load.
		 */
		public function on_load() {
			YITH_WCBK_Cron::get_instance();
			YITH_WCBK_Premium_Products::get_instance();
			YITH_WCBK_Cart_Checkout_Blocks::get_instance();

			if ( yith_wcbk()->settings->should_block_dates_for_pending_confirmation_bookings() ) {
				add_filter( 'yith_wcbk_get_booked_statuses', array( $this, 'add_pending_to_booked_statuses' ), 10, 1 );
			}
			add_action( 'update_option_yith-wcbk-block-dates-for-pending-confirmation-bookings', 'yith_wcbk_invalidate_product_cache' );

			// Register plugin to licence/update system.
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
		}

		/**
		 * Add "pending" status to booked ones.
		 *
		 * @param string[] $statuses Booked statuses.
		 *
		 * @return string[]
		 * @since 5.0.0
		 */
		public function add_pending_to_booked_statuses( $statuses ) {
			$statuses[] = 'bk-pending-confirm';

			return $statuses;
		}

		/**
		 * Register plugins for activation tab
		 */
		public function register_plugin_for_activation() {
			if ( function_exists( 'YIT_Plugin_Licence' ) ) {
				YIT_Plugin_Licence()->register( YITH_WCBK_INIT, YITH_WCBK_SECRET_KEY, YITH_WCBK_SLUG );
			}
		}

		/**
		 * Register plugins for update tab
		 */
		public function register_plugin_for_updates() {
			if ( function_exists( 'YIT_Upgrade' ) ) {
				YIT_Upgrade()->register( YITH_WCBK_SLUG, YITH_WCBK_INIT );
			}
		}
	}
}
