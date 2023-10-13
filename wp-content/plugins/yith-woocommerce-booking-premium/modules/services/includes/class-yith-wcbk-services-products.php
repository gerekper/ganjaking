<?php
/**
 * Class YITH_WCBK_Services_Products
 * Handle products for the Services module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Services_Products' ) ) {
	/**
	 * YITH_WCBK_Services_Products class.
	 */
	class YITH_WCBK_Services_Products {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Services_Products constructor.
		 */
		protected function __construct() {
			YITH_WCBK_Services_Product_Data_Extension::get_instance();

			// Booking form.
			add_action( 'yith_wcbk_booking_form_content', array( $this, 'maybe_print_services_field' ), 30, 1 );
			add_filter( 'yith_wcbk_booking_product_calculated_price_totals', array( $this, 'filter_product_calculated_price_totals' ), 10, 4 );
			add_filter( 'yith_wcbk_costs_included_in_shown_price_options', array( $this, 'filter_costs_included_in_shown_price_options' ), 10, 1 );

			// Cart.
			add_filter( 'yith_wcbk_cart_get_booking_data_from_request', array( $this, 'filter_booking_data_from_request' ), 10, 3 );
			add_filter( 'yith_wcbk_cart_get_booking_data_from_booking', array( $this, 'filter_booking_data_from_booking' ), 10, 2 );
			add_filter( 'yith_wcbk_cart_get_booking_props_from_booking_data', array( $this, 'filter_booking_props_from_booking_data' ), 10, 1 );
			add_filter( 'yith_wcbk_cart_booking_item_data_before_totals', array( $this, 'filter_cart_booking_item_data' ), 10, 3 );
		}

		/**
		 * Print services' fields, if the booking product has services.
		 *
		 * @param WC_Product_Booking $product The booking product.
		 */
		public function maybe_print_services_field( WC_Product_Booking $product ) {
			if ( $product->has_services() ) {
				yith_wcbk_get_module_template( 'services', 'booking-form/services.php', compact( 'product' ), 'single-product/add-to-cart/' );
			}
		}

		/**
		 * Include services from request in booking data.
		 *
		 * @param array              $booking_data Booking data.
		 * @param array              $request      The request.
		 * @param WC_Product_Booking $product      The booking product.
		 *
		 * @return array
		 */
		public function filter_booking_data_from_request( array $booking_data, array $request, WC_Product_Booking $product ): array {
			$services           = $product->get_service_ids();
			$selected_services  = array_filter( array_map( 'absint', $booking_data['booking_services'] ) );
			$service_quantities = $booking_data['booking_service_quantities'] ?? array();
			if ( $services && is_array( $services ) ) {
				$all_services = array();
				foreach ( $services as $service_id ) {
					$service = yith_wcbk_get_service( $service_id );
					if ( ! $service ) {
						continue;
					}

					if ( $service->is_quantity_enabled() ) {
						$selected_quantity = isset( $service_quantities[ $service_id ] ) ? absint( $service_quantities[ $service_id ] ) : 0;
						$quantity          = $service->validate_quantity( $selected_quantity );

						if ( $quantity < 1 ) {
							continue;
						}
					}

					if ( $service->is_optional() && ! in_array( $service_id, $selected_services, true ) ) {
						continue;
					}

					$all_services[] = $service_id;
				}

				$booking_data['booking_services'] = $all_services;
			}

			return $booking_data;
		}

		/**
		 * Include services from booking in booking data.
		 * Useful when adding to the cart a booking after confirmation.
		 *
		 * @param array             $booking_data Booking data.
		 * @param YITH_WCBK_Booking $booking      The booking.
		 *
		 * @return array
		 */
		public function filter_booking_data_from_booking( array $booking_data, YITH_WCBK_Booking $booking ): array {
			$service_ids = $booking->get_service_ids();
			if ( $service_ids ) {
				$booking_data['booking_services']           = $service_ids;
				$booking_data['booking_service_quantities'] = $booking->get_service_quantities();
			}

			return $booking_data;
		}

		/**
		 * Include services from booking props in booking data.
		 *
		 * @param array $props Props.
		 *
		 * @return array
		 */
		public function filter_booking_props_from_booking_data( array $props ): array {
			$data_to_prop_map = array(
				'booking_services'           => 'service_ids',
				'booking_service_quantities' => 'service_quantities',
			);

			foreach ( $data_to_prop_map as $data => $prop ) {
				if ( isset( $props[ $data ] ) ) {
					$props[ $prop ] = $props[ $data ];
					unset( $props[ $data ] );
				}
			}

			return $props;
		}

		/**
		 * Filter cart item data.
		 *
		 * @param array              $item_data The booking item data.
		 * @param array              $cart_item The cart item.
		 * @param WC_Product_Booking $product   The booking product.
		 *
		 * @return array
		 */
		public function filter_cart_booking_item_data( array $item_data, array $cart_item, WC_Product_Booking $product ): array {
			$booking_data = $cart_item['yith_booking_data'];

			$services           = $product->get_service_ids();
			$selected_services  = array_filter( array_map( 'absint', $booking_data['booking_services'] ?? array() ) );
			$service_quantities = $booking_data['booking_service_quantities'] ?? array();
			if ( $services && is_array( $services ) ) {
				$my_services = array();
				foreach ( $services as $service_id ) {
					$service = yith_wcbk_get_service( $service_id );
					if ( ! $service ) {
						continue;
					}

					if ( $service->is_quantity_enabled() ) {
						$selected_quantity = isset( $service_quantities[ $service_id ] ) ? absint( $service_quantities[ $service_id ] ) : 0;
						$quantity          = $service->validate_quantity( $selected_quantity );
						if ( $quantity !== $selected_quantity ) {
							$booking_data['booking_service_quantities'][ $service_id ] = $quantity;
						}

						if ( $quantity < 1 ) {
							continue;
						}
					}

					if ( $service->is_optional() && ! in_array( $service_id, $selected_services, true ) ) {
						continue;
					}

					if ( ! $service->is_hidden() ) {
						$quantity      = $service_quantities[ $service_id ] ?? false;
						$my_services[] = $service->get_name_with_quantity( $quantity );
					}
				}

				if ( ! ! $my_services ) {
					$item_data['yith_booking_services'] = array(
						'key'   => yith_wcbk_get_label( 'booking-services' ),
						'value' => yith_wcbk_booking_services_html( $my_services ),
					);
				}
			}

			return $item_data;
		}

		/**
		 * Filter totals.
		 *
		 * @param array              $totals    The totals.
		 * @param array              $args      Arguments.
		 * @param bool               $formatted Formatted flag.
		 * @param WC_Product_Booking $product   The booking product.
		 *
		 * @return array
		 */
		public function filter_product_calculated_price_totals( array $totals, array $args, bool $formatted, WC_Product_Booking $product ): array {
			$people_number        = $args['persons'] ?? 1;
			$duration             = $args['duration'] ?? 1;
			$default_people_types = array(
				array(
					'id'     => 0,
					'number' => $people_number,
				),
			);
			$people_types         = $args['person_types'] ?? $default_people_types;
			$services             = array_map( 'absint', $args['booking_services'] ?? array() );
			$service_quantities   = $args['booking_service_quantities'] ?? array();

			$price = 0;

			if ( $people_number > 0 && $product->has_services() ) {
				$available_services = $product->get_service_ids();
				foreach ( $available_services as $service_id ) {
					$service = yith_wcbk_get_service( $service_id );

					if ( ! $service ) {
						continue;
					}

					if ( $service->is_optional() && ! in_array( $service_id, $services, true ) ) {
						continue;
					}

					$service_cost_total = 0;

					if ( $service->is_multiply_per_persons() ) {
						foreach ( $people_types as $person_type ) {
							$pt_id                = absint( $person_type['id'] );
							$pt_number            = absint( $person_type['number'] );
							$current_service_cost = $service->get_price( $pt_id );

							if ( $service->is_multiply_per_blocks() ) {
								$current_service_cost *= $duration;
							}
							if ( $service->is_multiply_per_persons() ) {
								$current_service_cost *= $pt_number;
							}

							$service_cost_total += floatval( $current_service_cost );
						}
					} else {
						$service_cost_total = $service->get_price();
						if ( $service->is_multiply_per_blocks() ) {
							$service_cost_total *= $duration;
						}
					}

					if ( $service->is_quantity_enabled() ) {
						$quantity = $service_quantities[ $service->get_id() ] ?? 0;
						$quantity = $service->validate_quantity( $quantity );

						$service_cost_total = $service_cost_total * $quantity;
					}

					$service_cost_total = apply_filters( 'yith_wcbk_booking_product_single_service_cost_total', $service_cost_total, $service, $args, $this );

					$price += floatval( $service_cost_total );
				}
			}

			$price = (float) apply_filters( 'yith_wcbk_booking_product_calculate_service_costs', $price, $args, $this );

			if ( $price ) {
				$totals['services'] = array(
					'label' => __( 'Services', 'yith-booking-for-woocommerce' ),
					'value' => $price,
				);
			}

			return $totals;
		}

		/**
		 * Add services in options for costs included in shown price.
		 *
		 * @param array $options Options.
		 *
		 * @return array
		 */
		public function filter_costs_included_in_shown_price_options( array $options ): array {
			$options['services'] = __( 'Service costs', 'yith-booking-for-woocommerce' );

			return $options;
		}
	}
}
