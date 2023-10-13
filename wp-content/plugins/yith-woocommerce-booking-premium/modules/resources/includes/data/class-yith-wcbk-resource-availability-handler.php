<?php
/**
 * Class YITH_WCBK_Resource_Availability_Handler
 *
 * Handles the Resource availability.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resource_Availability_Handler' ) ) {
	/**
	 * Class YITH_WCBK_Resource_Availability_Handler
	 */
	class YITH_WCBK_Resource_Availability_Handler extends YITH_WCBK_Availability_Handler {
		const TYPE = 'resource';

		/**
		 * The resource.
		 *
		 * @var YITH_WCBK_Resource
		 */
		protected $resource;

		/**
		 * The product.
		 *
		 * @var WC_Product_Booking
		 */
		protected $product;

		/**
		 * Initialization.
		 *
		 * @param YITH_WCBK_Resource $resource The resource.
		 * @param WC_Product_Booking $product  The booking product.
		 */
		public function init( YITH_WCBK_Resource $resource, WC_Product_Booking $product ) {
			$this->resource              = $resource;
			$this->product               = $product;
			$this->non_available_reasons = array();

			$this->set_availability_rules( $resource->get_availability_rules() );
			$this->set_default_availability( $resource->get_default_availability() );

			$this->set_duration_unit( $product->get_duration_unit() );
		}

		/**
		 * Is this initialized?
		 *
		 * @return bool
		 */
		protected function is_initialized(): bool {
			return isset( $this->resource ) && isset( $this->product );
		}

		/**
		 * Check for the default availability.
		 *
		 * @param int   $from The 'from' timestamp.
		 * @param int   $to   The 'to' timestamp.
		 * @param array $args Arguments.
		 *
		 * @return bool
		 */
		public function check_booked_availability( int $from, int $to, array $args = array() ): bool {
			if ( ! $this->is_initialized() ) {
				return false;
			}
			$resource           = $this->resource;
			$product            = $this->product;
			$available          = true;
			$available_quantity = $resource->get_available_quantity();

			if ( $available_quantity ) {
				$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
				$persons                   = isset( $args['persons'] ) ? max( 1, absint( $args['persons'] ) ) : $product->get_minimum_number_of_people();

				if ( ! $product->has_people() ) {
					$persons = 1;
				}

				$count_args         = array(
					'exclude'                  => $args['exclude'] ?? false,
					'exclude_order_id'         => $args['exclude_order_id'] ?? false,
					'include_bookings_in_cart' => $args['include_bookings_in_cart'] ?? false,
				);
				$number_of_bookings = $this->count_booked( $from, $to, $count_args );
				$booking_weight     = $count_persons_as_bookings ? $persons : 1;

				if ( $number_of_bookings + $booking_weight > $available_quantity ) {
					$available = false;
					$remained  = $available_quantity - $number_of_bookings;

					if ( $remained > 0 ) {
						$reason_data = array(
							'remained' => $remained,
						);
						if ( $product->has_people() && $count_persons_as_bookings ) {
							$this->add_non_available_reason(
								'resource-max-bookings-per-unit',
								// translators: %s is the remaining people number.
								sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $remained ),
								$reason_data
							);
						} else {
							$this->add_non_available_reason(
								'resource-max-bookings-per-unit',
								// translators: %s is the remaining quantity.
								sprintf( __( '%s remaining', 'yith-booking-for-woocommerce' ), $remained ),
								$reason_data
							);
						}
					}
				}
			}

			return $available;
		}

		/**
		 * Count booked resources.
		 *
		 * @param int   $from The 'from' timestamp.
		 * @param int   $to   The 'to' timestamp.
		 * @param array $args Arguments.
		 *
		 * @return int
		 */
		public function count_booked( int $from, int $to, array $args = array() ): int {
			if ( ! $this->is_initialized() ) {
				return 0;
			}

			$defaults = array(
				'exclude'                  => false,
				'exclude_order_id'         => false,
				'include_bookings_in_cart' => false,
			);
			$args     = wp_parse_args( $args, $defaults );
			$resource = $this->resource;
			$product  = $this->product;

			$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
			$available_quantity        = $resource->get_available_quantity();

			$count_args         = array(
				'resources'                 => $resource->get_id(),
				'from'                      => $from,
				'to'                        => $to,
				'unit'                      => $product->get_duration_unit(),
				'include_externals'         => false, // Resources have no externals.
				'count_persons_as_bookings' => $count_persons_as_bookings,
				'exclude'                   => $args['exclude'],
				'exclude_order_id'          => $args['exclude_order_id'],
				'return'                    => $available_quantity < 2 ? 'total' : 'max_by_unit',
			);
			$number_of_bookings = yith_wcbk_booking_helper()->count_max_booked_bookings_per_unit_in_period( $count_args );

			if ( $args['include_bookings_in_cart'] ) {
				$bookings_in_cart = $this->count_bookings_in_cart( $from, $to );

				$number_of_bookings += $bookings_in_cart;
			}

			return $number_of_bookings;
		}

		/**
		 * Count bookings in cart.
		 *
		 * @param int $from The 'from' timestamp.
		 * @param int $to   The 'to' timestamp.
		 *
		 * @return int
		 */
		protected function count_bookings_in_cart( int $from, int $to ): int {
			if ( ! $this->is_initialized() ) {
				return 0;
			}
			$resource                  = $this->resource;
			$product                   = $this->product;
			$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
			$found_bookings            = 0;

			$cart          = WC()->cart ?? false;
			$cart_contents = ! ! $cart ? $cart->get_cart_contents() : array();
			$cart_contents = ! ! $cart_contents ? $cart_contents : array();

			foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {
				$booking_data   = $cart_item_data['yith_booking_data'] ?? array();
				$item_from      = $booking_data['from'] ?? false;
				$item_to        = $booking_data['to'] ?? false;
				$item_resources = array_map( 'absint', (array) ( $booking_data['resource_ids'] ?? array() ) );
				$item_persons   = absint( $booking_data['persons'] ?? 1 );

				if ( $item_from && $item_to && $item_resources ) {
					if ( $item_from < $to && $item_to > $from && in_array( $resource->get_id(), $item_resources, true ) ) {
						if ( $count_persons_as_bookings ) {
							$found_bookings += max( 1, $item_persons );
						} else {
							$found_bookings ++;
						}
					}
				}
			}

			return $found_bookings;
		}
	}
}
