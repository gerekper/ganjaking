<?php
/**
 * Class YITH_WCBK_Product_Extra_Cost_Custom
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Product_Extra_Cost_Custom' ) ) {
	/**
	 * Class YITH_WCBK_Product_Extra_Cost_Custom
	 *
	 * @version 2.1.9
	 */
	class YITH_WCBK_Product_Extra_Cost_Custom extends YITH_WCBK_Product_Extra_Cost {

		/**
		 * Object type.
		 *
		 * @var string
		 */
		protected $object_type = 'product_extra_cost_custom';

		/**
		 * Get identifier.
		 *
		 * @return string|int
		 */
		public function get_identifier() {
			return '_' . $this->get_slug();
		}

		/**
		 * Get the slug.
		 *
		 * @return string
		 */
		public function get_slug() {
			return sanitize_title( $this->get_name() );
		}

		/**
		 * Return the name of the Extra Cost
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_name( $context = 'view' ) {
			$name = $this->get_prop( 'name', $context );

			return 'view' === $context ? call_user_func( '__', $name, 'yith-booking-for-woocommerce' ) : $name;
		}


		/**
		 * Is valid?
		 *
		 * @return bool
		 */
		public function is_valid() {
			return $this->get_name();
		}

		/**
		 * Is custom?
		 *
		 * @return bool
		 */
		public function is_custom() {
			return true;
		}
	}
}
