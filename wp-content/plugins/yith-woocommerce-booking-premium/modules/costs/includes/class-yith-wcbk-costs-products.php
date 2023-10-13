<?php
/**
 * Class YITH_WCBK_Costs_Products
 * Handle products for the Costs module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Costs_Products' ) ) {
	/**
	 * YITH_WCBK_Costs_Products class.
	 */
	class YITH_WCBK_Costs_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Costs_Products constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Costs_Product_Data_Extension::get_instance();

			add_filter( 'yith_wcbk_costs_included_in_shown_price_options', array( $this, 'filter_costs_included_in_shown_price_options' ), 10, 1 );
		}

		/**
		 * Add extra costs in options for costs included in shown price.
		 *
		 * @param array $options Options.
		 *
		 * @return array
		 */
		public function filter_costs_included_in_shown_price_options( array $options ): array {
			$options['extra-costs'] = __( 'Extra costs', 'yith-booking-for-woocommerce' );

			return $options;
		}
	}
}
