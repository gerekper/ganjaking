<?php
/**
 * Class YITH_WCBK_People_Product_Data_Extension
 * Handle product data for the People module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_People_Product_Data_Extension' ) ) {
	/**
	 * YITH_WCBK_People_Product_Data_Extension class.
	 */
	class YITH_WCBK_People_Product_Data_Extension extends YITH_WCBK_Product_Data_Extension {

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		protected function get_settings(): array {
			return array(
				'meta_keys_to_props' => array(
					// Costs.
					'_yith_booking_multiply_base_price_by_number_of_people'     => 'multiply_base_price_by_number_of_people',
					'_yith_booking_extra_price_per_person'                      => 'extra_price_per_person',
					'_yith_booking_extra_price_per_person_greater_than'         => 'extra_price_per_person_greater_than',
					'_yith_booking_multiply_fixed_base_fee_by_number_of_people' => 'multiply_fixed_base_fee_by_number_of_people',
					// People tabs.
					'_yith_booking_has_persons'                                 => 'enable_people',
					'_yith_booking_min_persons'                                 => 'minimum_number_of_people',
					'_yith_booking_max_persons'                                 => 'maximum_number_of_people',
					'_yith_booking_count_persons_as_bookings'                   => 'count_people_as_separate_bookings',
					'_yith_booking_enable_person_types'                         => 'enable_people_types',
					'_yith_booking_person_types'                                => 'people_types',
				),
				'internal_meta_keys' => array(
					'_yith_booking_has_persons',
					'_yith_booking_min_persons',
					'_yith_booking_max_persons',
					'_yith_booking_count_persons_as_bookings',
					'_yith_booking_enable_person_types',
					'_yith_booking_person_types',
					'_yith_booking_multiply_base_price_by_number_of_people',
					'_yith_booking_extra_price_per_person',
					'_yith_booking_extra_price_per_person_greater_than',
					'_yith_booking_multiply_fixed_base_fee_by_number_of_people',
				),
				'tabs'               => array(
					'people' => array(
						'id'     => 'yith_booking_people_tab',
						'wc_key' => 'yith_booking_people',
						'tab'    => array(
							'label'    => _x( 'People', 'Product tab title', 'yith-booking-for-woocommerce' ),
							'target'   => 'yith_booking_people_tab',
							'priority' => 20,
						),
						'module' => 'people',
					),
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
					'multiply_base_price_by_number_of_people'     => isset( $_POST['_yith_booking_multiply_base_price_by_number_of_people'] ),
					'extra_price_per_person'                      => isset( $_POST['_yith_booking_extra_price_per_person'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_extra_price_per_person'] ) ) : null,
					'extra_price_per_person_greater_than'         => isset( $_POST['_yith_booking_extra_price_per_person_greater_than'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_extra_price_per_person_greater_than'] ) ) : null,
					'multiply_fixed_base_fee_by_number_of_people' => isset( $_POST['_yith_booking_multiply_fixed_base_fee_by_number_of_people'] ),
					'enable_people'                               => isset( $_POST['_yith_booking_has_persons'] ),
					'minimum_number_of_people'                    => isset( $_POST['_yith_booking_min_persons'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_min_persons'] ) ) : null,
					'maximum_number_of_people'                    => isset( $_POST['_yith_booking_max_persons'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_max_persons'] ) ) : null,
					'count_people_as_separate_bookings'           => isset( $_POST['_yith_booking_count_persons_as_bookings'] ),
					'enable_people_types'                         => isset( $_POST['_yith_booking_enable_person_types'] ),
					'people_types'                                => isset( $_POST['_yith_booking_person_types'] ) ? wc_clean( wp_unslash( $_POST['_yith_booking_person_types'] ) ) : array(),
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
				case 'multiply_base_price_by_number_of_people':
				case 'multiply_fixed_base_fee_by_number_of_people':
				case 'enable_people':
				case 'count_people_as_separate_bookings':
				case 'enable_people_types':
					$value = wc_bool_to_string( $value );
					break;
			}

			return $value;
		}
	}
}
