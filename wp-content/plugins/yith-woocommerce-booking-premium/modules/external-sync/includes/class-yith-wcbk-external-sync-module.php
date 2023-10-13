<?php
/**
 * Class YITH_WCBK_External_Sync_Module
 * Handle the External Sync module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\ExternalSync
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_External_Sync_Module' ) ) {
	/**
	 * YITH_WCBK_External_Sync_Module class.
	 *
	 * @since   4.0
	 */
	class YITH_WCBK_External_Sync_Module extends YITH_WCBK_Module {

		const KEY = 'external-sync';

		/**
		 * On load.
		 */
		public function on_load() {
			YITH_WCBK_External_Sync_Products::get_instance();
			YITH_WCBK_External_Sync_Bookings::get_instance();
		}

		/**
		 * Add admin scripts.
		 *
		 * @param array  $scripts The scripts.
		 * @param string $context The context [admin or frontend].
		 *
		 * @return array
		 */
		public function filter_scripts( array $scripts, string $context ): array {
			$scripts['yith-wcbk-external-sync-admin-product'] = array(
				'src'     => $this->get_url( 'assets/js/admin/product.js' ),
				'context' => 'admin',
				'deps'    => array( 'jquery' ),
				'enqueue' => 'product',
			);

			return $scripts;
		}
	}
}
