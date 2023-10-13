<?php
/**
 * Class YITH_WCBK_Extra_Cost_Helper
 * helper class for Extra Cost
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Extra_Cost_Helper' ) ) {
	/**
	 * Class YITH_WCBK_Extra_Cost_Helper
	 *
	 * @deprecated 4.0.0
	 */
	class YITH_WCBK_Extra_Cost_Helper {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * The constructor.
		 */
		protected function __construct() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Extra_Cost_Helper::__construct', '4.0.0' );
		}

		/**
		 * Get all person types by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 * @deprecated 4.0.0 | use yith_wcbk_get_extra_cost_ids instead.
		 */
		public function get_extra_costs( $args = array() ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Extra_Cost_Helper::get_extra_costs', '4.0.0', 'yith_wcbk_get_extra_cost_ids' );

			if ( function_exists( 'yith_wcbk_get_extra_cost_ids' ) ) {
				$default_args = array(
					'suppress_filters' => false,
				);
				$args         = wp_parse_args( $args, $default_args );

				return yith_wcbk_get_extra_cost_ids( $args );
			}

			return array();
		}
	}
}
