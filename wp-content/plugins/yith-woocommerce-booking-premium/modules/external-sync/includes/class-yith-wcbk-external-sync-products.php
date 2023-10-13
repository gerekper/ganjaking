<?php
/**
 * Class YITH_WCBK_External_Sync_Products
 * Handle products for the External Sync module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\ExternalSync
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_External_Sync_Products' ) ) {
	/**
	 * YITH_WCBK_External_Sync_Products class.
	 */
	class YITH_WCBK_External_Sync_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			YITH_WCBK_External_Sync_Product_Data_Extension::get_instance();

		}
	}
}
