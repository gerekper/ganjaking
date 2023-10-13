<?php
/**
 * Tools class.
 * Handle tools available in YITh Plugins > Booking > Tools
 *
 * @package YITH\Booking\Classes
 * @author  YITH <plugins@yithemes.com>
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Tools' ) ) {
	/**
	 * Class YITH_WCBK_Tools
	 */
	class YITH_WCBK_Tools {

		use YITH_WCBK_Singleton_Trait;

		/**
		 * Notices to be shown after redirect.
		 *
		 * @var array
		 */
		private $redirect_notices = array();

		/**
		 * YITH_WCBK_Tools constructor.
		 */
		private function __construct() {
			add_action( 'wp_loaded', array( $this, 'handle_actions' ), 90 );

			add_action( 'admin_notices', array( $this, 'print_notices' ) );
		}

		/**
		 * Handle all actions
		 */
		public function handle_actions() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$action = ! empty( $_REQUEST['yith_wcbk_tools_action'] ) ? wc_clean( wp_unslash( $_REQUEST['yith_wcbk_tools_action'] ) ) : false;

			if ( $action ) {
				check_admin_referer( 'yith_wcbk_tools_' . $action );

				$method = 'handle_action_' . sanitize_key( $action );
				if ( is_callable( array( $this, $method ) ) ) {
					$this->$method();
				}
				$this->maybe_redirect();
			}
		}

		/**
		 * Redirect to proper page if the redirect is set in request.
		 *
		 * @since 3.0.0
		 */
		private function maybe_redirect() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$redirect = isset( $_REQUEST['yith_wcbk_tools_redirect'] ) ? esc_url_raw( wp_unslash( $_REQUEST['yith_wcbk_tools_redirect'] ) ) : false;
			if ( $redirect ) {
				if ( $this->redirect_notices ) {
					$redirect = add_query_arg( array( 'yith_wcbk_tools_notices' => $this->redirect_notices ), $redirect );
				}
				wp_safe_redirect( $redirect );
				exit;
			}
		}

		/**
		 * Handles Sync Booking Product Prices
		 */
		private function handle_action_sync_booking_product_prices() {
			$success = yith_wcbk_sync_booking_product_prices();
			if ( ! $success ) {
				$this->add_notice_before_redirect( 'price-sync-no-bookings' );
			}
		}

		/**
		 * Handles Sync Booking Product Prices
		 */
		private function handle_action_regenerate_booking_lookup_tables() {
			if ( ! yith_wcbk_update_product_lookup_tables_is_running() ) {
				yith_wcbk_update_booking_lookup_tables( true );
			}
		}

		/**
		 * Add notice before redirect
		 *
		 * @param string $key The key.
		 *
		 * @since 3.0.0 Change visibility from public to private.
		 */
		private function add_notice_before_redirect( $key ) {
			$this->redirect_notices[] = $key;
			$this->redirect_notices   = array_unique( $this->redirect_notices );
		}

		/**
		 * Print notices
		 */
		public function print_notices() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$notices = wc_clean( wp_unslash( $_REQUEST['yith_wcbk_tools_notices'] ?? array() ) );
			$notices = is_array( $notices ) ? $notices : array();

			if ( yith_wcbk()->admin ) {

				foreach ( $notices as $notice_key ) {
					yith_wcbk()->admin->notices()->add_notice( $notice_key );
				}

				if ( yith_wcbk_sync_booking_product_prices_is_running() ) {
					yith_wcbk()->admin->notices()->add_notice( 'product-prices-sync-running' );
				}
			}
		}
	}
}
