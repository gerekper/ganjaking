<?php
/**
 * Class YITH_WCBK_Costs_Product_Data_Extension
 * Handle product data for the Costs module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Costs_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_Costs_Product_Data_Extension class.
	 */
	class YITH_WCBK_Costs_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * YITH_WCBK_Costs_Product_Data_Extension constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'yith_wcbk_costs_product_tab_after_standard_prices', array( $this, 'print_product_costs_fields' ), 10, 1 );
		}

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					'_yith_booking_weekly_discount'                          => 'weekly_discount',
					'_yith_booking_monthly_discount'                         => 'monthly_discount',
					'_yith_booking_last_minute_discount'                     => 'last_minute_discount',
					'_yith_booking_last_minute_discount_days_before_arrival' => 'last_minute_discount_days_before_arrival',
					'_yith_booking_extra_costs'                              => 'extra_costs',
				),
				'internal_meta_keys' => array(
					'_yith_booking_weekly_discount',
					'_yith_booking_monthly_discount',
					'_yith_booking_last_minute_discount',
					'_yith_booking_last_minute_discount_days_before_arrival',
					'_yith_booking_extra_costs',
				),
			);
		}

		/**
		 * Save booking product meta for people.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function set_product_meta_before_saving( WC_Product_Booking $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing

			$product->set_props(
				array(
					'weekly_discount'                          => isset( $_POST['_yith_booking_weekly_discount'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_weekly_discount'] ) ) : null,
					'monthly_discount'                         => isset( $_POST['_yith_booking_monthly_discount'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_monthly_discount'] ) ) : null,
					'last_minute_discount'                     => isset( $_POST['_yith_booking_last_minute_discount'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_last_minute_discount'] ) ) : null,
					'last_minute_discount_days_before_arrival' => isset( $_POST['_yith_booking_last_minute_discount_days_before_arrival'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_last_minute_discount_days_before_arrival'] ) ) : null,
					'extra_costs'                              => isset( $_POST['_yith_booking_extra_costs'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_extra_costs'] ) ) : array(),
				)
			);

			// phpcs:enable
		}

		/**
		 * Triggered before updating product props.
		 *
		 * @param mixed              $value   The value.
		 * @param string             $prop    The prop.
		 * @param WC_Product_Booking $product The booking product.
		 *
		 * @return mixed The sanitized value.
		 */
		protected function sanitize_prop_value_before_saving( $value, string $prop, WC_Product_Booking $product ) {
			switch ( $prop ) {
				case 'extra_costs':
					$value = yith_wcbk_simple_objects_to_array( $value );
					break;
			}

			return $value;
		}

		/**
		 * Print fields in product costs tab.
		 *
		 * @param WC_Product_Booking|false $booking_product The booking product.
		 */
		public function print_product_costs_fields( $booking_product ) {
			$args = compact( 'booking_product' );

			yith_wcbk_get_module_view( 'costs', 'product-tabs/discounts.php', $args );
			yith_wcbk_get_module_view( 'costs', 'product-tabs/extra-costs.php', $args );
		}
	}
}
