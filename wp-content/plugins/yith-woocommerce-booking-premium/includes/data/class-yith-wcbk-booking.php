<?php
/**
 * Class YITH_WCBK_Booking
 * Handles the Booking object.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Booking' ) ) {
	/**
	 * Class YITH_WCBK_Booking
	 * the Booking class
	 */
	class YITH_WCBK_Booking extends YITH_WCBK_Booking_Abstract {

		/**
		 * The ID
		 *
		 * @var int
		 */
		protected $id;

		/**
		 * The data
		 *
		 * @var array
		 */
		protected $data = array(
			'product_id'                  => 0,
			'status'                      => 'unpaid',
			'raw_title'                   => '',
			'from'                        => 0,
			'to'                          => 0,
			'duration'                    => 1,
			'duration_unit'               => '',
			'date_created'                => '',
			'date_modified'               => '',
			'persons'                     => 1,
			'person_types'                => array(),
			'order_id'                    => 0,
			'order_item_id'               => 0,
			'user_id'                     => 0,
			'service_ids'                 => array(),
			'service_quantities'          => array(),
			'can_be_cancelled'            => 'no',
			'cancelled_duration'          => 0,
			'cancelled_unit'              => 'month',
			'location'                    => '',
			'all_day'                     => 'no',
			'has_persons'                 => '',
			'google_calendar_last_update' => '',
			'resource_ids'                => array(),
		);

		/**
		 * The object type
		 *
		 * @var string
		 */
		protected $object_type = 'booking';

		/**
		 * The queued notes
		 *
		 * @var array
		 */
		protected $queued_notes = array();

		/**
		 * Stores data about status changes so relevant hooks can be fired.
		 *
		 * @var bool|array
		 */
		protected $status_transition = false;

		/**
		 * Related objects
		 *
		 * @var array
		 */
		protected $related_objects = array();

		/**
		 * Cache group.
		 *
		 * @var string
		 */
		protected $cache_group = 'yith_bookings';

		/**
		 * YITH_WCBK_Booking constructor.
		 *
		 * @param int|YITH_WCBK_Booking|WP_Post $booking The object.
		 *
		 * @throws Exception If passed booking is invalid.
		 */
		public function __construct( $booking = 0 ) {
			parent::__construct( $booking );

			$func_args = func_get_args(); // phpcs:ignore PHPCompatibility.FunctionUse.ArgumentFunctionsReportCurrentValue.NeedsInspection

			$this->data_store = WC_Data_Store::load( 'yith-booking' );

			if ( count( $func_args ) > 1 ) {
				// Backward compatibility for creating the booking.
				$args = $func_args[1];
				$this->create_booking( $args );
			} else {
				if ( is_numeric( $booking ) && $booking > 0 ) {
					$this->set_id( $booking );
				} elseif ( $booking instanceof self ) {
					$this->set_id( absint( $booking->get_id() ) );
				} elseif ( ! empty( $booking->ID ) ) {
					$this->set_id( absint( $booking->ID ) );
				} else {
					$this->set_object_read( true );
				}

				if ( $this->get_id() > 0 ) {
					$this->data_store->read( $this );
				}
			}
		}

		/**
		 * Magic Getter for backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			yith_wcbk_doing_it_wrong( $key, 'Booking properties should not be accessed directly.', '3.0.0' );

			$getter            = 'get_' . $key;
			$map_old_new_props = array(
				'services' => 'service_ids',
			);
			$key               = strtr( $key, $map_old_new_props );

			if ( 'post' === $key ) {
				$this->post = get_post( $this->get_id() );

				return $this->post;
			} elseif ( is_callable( array( $this, $getter ) ) ) {
				return $this->$getter();
			} elseif ( $this->meta_exists( $key ) ) {
				return $this->get_meta( $key );
			} elseif ( $this->meta_exists( '_' . $key ) ) {
				return $this->get_meta( '_' . $key );
			}

			return null;
		}

		/**
		 * Magic Isset for backward compatibility.
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			$getter = 'get_' . $key;

			return 'post' === $key || is_callable( array( $this, $getter ) );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Getters
		|--------------------------------------------------------------------------
		|
		| Functions for getting booking data.
		*/

		/**
		 * Return the product ID
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_product_id( $context = 'view' ) {
			return $this->get_prop( 'product_id', $context );
		}

		/**
		 * Return the status
		 * by default, it's set to the product name
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_status( $context = 'view' ) {
			return $this->get_prop( 'status', $context );
		}

		/**
		 * Return the raw Title
		 * by default, it's set to the product name
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_raw_title( $context = 'view' ) {
			return $this->get_prop( 'raw_title', $context );
		}

		/**
		 * Return the "from" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_from( $context = 'view' ) {
			return $this->get_prop( 'from', $context );
		}

		/**
		 * Return the "to" timestamp
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_to( $context = 'view' ) {
			return $this->get_prop( 'to', $context );
		}

		/**
		 * Return the duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.0.4
		 */
		public function get_duration( $context = 'view' ) {
			return $this->get_prop( 'duration', $context );
		}

		/**
		 * Return the duration unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_duration_unit( $context = 'view' ) {
			return $this->get_prop( 'duration_unit', $context );
		}

		/**
		 * Get product created date.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
		 * @since  3.0.0
		 */
		public function get_date_created( $context = 'view' ) {
			return $this->get_prop( 'date_created', $context );
		}

		/**
		 * Get product modified date.
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return WC_DateTime|NULL object if the date is set or null if there is no date.
		 * @since  3.0.0
		 */
		public function get_date_modified( $context = 'view' ) {
			return $this->get_prop( 'date_modified', $context );
		}

		/**
		 * Return the number of persons
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 */
		public function get_persons( $context = 'view' ) {
			return $this->get_prop( 'persons', $context );
		}

		/**
		 * Return the person types
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_person_types( $context = 'view' ) {
			return $this->get_prop( 'person_types', $context );
		}


		/**
		 * Return the order id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 2.1.9
		 */
		public function get_order_id( $context = 'view' ) {
			return $this->get_prop( 'order_id', $context );
		}

		/**
		 * Return the order item id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_order_item_id( $context = 'view' ) {
			return $this->get_prop( 'order_item_id', $context );
		}

		/**
		 * Return the user_id
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_user_id( $context = 'view' ) {
			return $this->get_prop( 'user_id', $context );
		}

		/**
		 * Return the service_ids
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_service_ids( $context = 'view' ) {
			return $this->get_prop( 'service_ids', $context );
		}

		/**
		 * Return the service quantities
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 2.0.5
		 */
		public function get_service_quantities( $context = 'view' ) {
			return $this->get_prop( 'service_quantities', $context );
		}

		/**
		 * Return the can_be_cancelled
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_can_be_cancelled( $context = 'view' ) {
			return $this->get_prop( 'can_be_cancelled', $context );
		}

		/**
		 * Return the cancelled_duration
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_cancelled_duration( $context = 'view' ) {
			return $this->get_prop( 'cancelled_duration', $context );
		}

		/**
		 * Return the cancelled_unit
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_cancelled_unit( $context = 'view' ) {
			return $this->get_prop( 'cancelled_unit', $context );
		}

		/**
		 * Return the location
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_location( $context = 'view' ) {
			return $this->get_prop( 'location', $context );
		}

		/**
		 * Return the all_day
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_all_day( $context = 'view' ) {
			return $this->get_prop( 'all_day', $context );
		}

		/**
		 * Return the has_persons
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_has_persons( $context = 'view' ) {
			return $this->get_prop( 'has_persons', $context );
		}

		/**
		 * Return the google_calendar_last_update
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_google_calendar_last_update( $context = 'view' ) {
			return $this->get_prop( 'google_calendar_last_update', $context );
		}

		/**
		 * Return the resource_ids
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return array
		 * @since 4.0.0
		 */
		public function get_resource_ids( string $context = 'view' ): array {
			return $this->get_prop( 'resource_ids', $context );
		}

		/*
		|--------------------------------------------------------------------------
		| CRUD Setters
		|--------------------------------------------------------------------------
		|
		| Functions for getting booking data.
		*/

		/**
		 * Set the product_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_product_id( $value ) {
			$this->set_prop( 'product_id', absint( $value ) );
		}

		/**
		 * Set the status
		 *
		 * @param string $status_to The value to set.
		 * @param string $note      The note.
		 */
		public function set_status( $status_to, $note = '' ) {
			$statuses  = array_merge( array_keys( yith_wcbk_get_booking_statuses( true ) ), array( 'trash' ) );
			$status_to = 'bk-' === substr( $status_to, 0, 3 ) ? substr( $status_to, 3 ) : $status_to;
			$status_to = in_array( $status_to, $statuses, true ) ? $status_to : 'unpaid';
			$edited_by = '';

			if ( ! $this->can_be( $status_to ) ) {
				return false;
			}

			if ( 'cancelled_by_user' === $status_to ) {
				$status_to = 'cancelled';
				$edited_by = 'customer';
			}

			$status_from = $this->get_status();

			if ( true === $this->object_read && $status_to !== $status_from ) {
				$this->status_transition = array(
					'from'      => ! empty( $this->status_transition['from'] ) ? $this->status_transition['from'] : $status_from,
					'to'        => $status_to,
					'note'      => $note,
					'edited_by' => $edited_by,
				);
			}

			$this->set_prop( 'status', $status_to );

			return array(
				'from' => $status_from,
				'to'   => $status_to,
			);
		}

		/**
		 * Set the raw_title
		 *
		 * @param string $value The value to set.
		 */
		public function set_raw_title( $value ) {
			$this->set_prop( 'raw_title', $value );
		}

		/**
		 * Set the from value
		 *
		 * @param int $value The value to set.
		 */
		public function set_from( $value ) {
			$this->set_prop( 'from', absint( $value ) );
		}

		/**
		 * Set the to value
		 *
		 * @param int $value The value to set.
		 */
		public function set_to( $value ) {
			$this->set_prop( 'to', absint( $value ) );
		}

		/**
		 * Set the duration
		 *
		 * @param int $value The value to set.
		 */
		public function set_duration( $value ) {
			$this->set_prop( 'duration', absint( $value ) );
		}

		/**
		 * Set the duration
		 *
		 * @param string $value The value to set.
		 */
		public function set_duration_unit( $value ) {
			$units = array_keys( yith_wcbk_get_duration_units() );
			$value = in_array( $value, $units, true ) ? $value : current( $units );
			$this->set_prop( 'duration_unit', $value );
		}

		/**
		 * Set product created date.
		 *
		 * @param string|integer|null $value UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
		 *
		 * @since 3.0.0
		 */
		public function set_date_created( $value ) {
			$this->set_date_prop( 'date_created', $value );
		}

		/**
		 * Set product modified date.
		 *
		 * @param string|integer|null $value UTC timestamp, or ISO 8601 DateTime. If the DateTime string has no timezone or offset, WordPress site timezone will be assumed. Null if their is no date.
		 *
		 * @since 3.0.0
		 */
		public function set_date_modified( $value ) {
			$this->set_date_prop( 'date_modified', $value );
		}

		/**
		 * Set the persons
		 *
		 * @param int $value The value to set.
		 */
		public function set_persons( $value ) {
			$this->set_prop( 'persons', absint( $value ) );
		}


		/**
		 * Set the person_types
		 *
		 * @param array $value The value to set.
		 */
		public function set_person_types( $value ) {
			$this->set_prop( 'person_types', $value );
		}

		/**
		 * Set the order_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_order_id( $value ) {
			$this->set_prop( 'order_id', absint( $value ) );
		}


		/**
		 * Set the order_item_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_order_item_id( $value ) {
			$this->set_prop( 'order_item_id', absint( $value ) );
		}


		/**
		 * Set the user_id
		 *
		 * @param int $value The value to set.
		 */
		public function set_user_id( $value ) {
			$this->set_prop( 'user_id', absint( $value ) );
		}

		/**
		 * Set the service_ids
		 *
		 * @param array $value The value to set.
		 */
		public function set_service_ids( $value ) {
			if ( yith_wcbk_is_services_module_active() ) {
				$value = is_array( $value ) ? array_filter( array_map( 'absint', $value ) ) : array();
				$this->set_prop( 'service_ids', $value );
			}
		}

		/**
		 * Set the service_quantities
		 *
		 * @param array $value The value to set.
		 */
		public function set_service_quantities( $value ) {
			if ( yith_wcbk_is_services_module_active() ) {
				$value = is_array( $value ) ? $value : array();
				$this->set_prop( 'service_quantities', $value );
			}
		}

		/**
		 * Set the can_be_cancelled
		 *
		 * @param bool $value The value to set.
		 */
		public function set_can_be_cancelled( $value ) {
			$this->set_prop( 'can_be_cancelled', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the cancelled_duration
		 *
		 * @param int $value The value to set.
		 */
		public function set_cancelled_duration( $value ) {
			$this->set_prop( 'cancelled_duration', absint( $value ) );
		}

		/**
		 * Set the cancelled_unit
		 *
		 * @param string $value The value to set.
		 */
		public function set_cancelled_unit( $value ) {
			$allowed_units = array_keys( yith_wcbk_get_cancel_duration_units() );
			$value         = in_array( $value, $allowed_units, true ) ? $value : current( $allowed_units );
			$this->set_prop( 'cancelled_unit', $value );
		}

		/**
		 * Set the location
		 *
		 * @param string $value The value to set.
		 */
		public function set_location( $value ) {
			$this->set_prop( 'location', $value );
		}

		/**
		 * Set the all_day
		 *
		 * @param bool $value The value to set.
		 */
		public function set_all_day( $value ) {
			$this->set_prop( 'all_day', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the has_persons
		 *
		 * @param bool $value The value to set.
		 */
		public function set_has_persons( $value ) {
			$this->set_prop( 'has_persons', wc_bool_to_string( $value ) );
		}

		/**
		 * Set the google_calendar_last_update
		 *
		 * @param int $value The value to set.
		 */
		public function set_google_calendar_last_update( $value ) {
			$this->set_prop( 'google_calendar_last_update', absint( $value ) );
		}

		/**
		 * Set the resource_ids
		 *
		 * @param array|false $value The value to set.
		 *
		 * @since 4.0.0
		 */
		public function set_resource_ids( $value ) {
			if ( yith_wcbk_is_resources_module_active() ) {
				$value = is_array( $value ) ? array_filter( array_map( 'absint', $value ) ) : array();
				$this->set_prop( 'resource_ids', $value );
			}
		}

		/*
		|--------------------------------------------------------------------------
		| NON-CRUD Getters
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Retrieve the sold price from the product
		 *
		 * @param bool|null $include_tax Set true if the price should include tax. By default, it retrieves the WC settings.
		 *
		 * @return false|string
		 * @since 3.0.0
		 */
		public function get_sold_price( $include_tax = null ) {
			$include_tax   = ! is_null( $include_tax ) ? $include_tax : ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) );
			$price         = false;
			$order         = $this->get_order();
			$order_item_id = $this->get_order_item_id();
			if ( $order && $order_item_id ) {
				$item = $order->get_item( $order_item_id );
				if ( $item instanceof WC_Order_Item_Product ) {
					$price = $item->get_total();
					if ( $include_tax ) {
						$price += $item->get_total_tax();
					}

					$price = wc_format_decimal( $price );
					$price = apply_filters( 'yith_wcbk_booking_get_sold_price_item_total', $price, $item, $include_tax, $this );
				}
			}

			return apply_filters( 'yith_wcbk_booking_get_sold_price', $price, $include_tax, $this );
		}

		/**
		 * Retrieve the calculated price.
		 *
		 * @return false|string
		 * @since 3.0.0
		 */
		public function get_calculated_price() {
			$price = false;
			if ( $this->get_product() ) {
				$args  = YITH_WCBK_Cart::get_booking_data_from_booking( $this );
				$price = $this->get_product()->calculate_price( $args );
				$price = wc_format_decimal( $price );
			}

			return $price;
		}

		/**
		 * Retrieve a formatted name by a "format" parameter.
		 *
		 * @param string $format The format.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_formatted_name( $format ) {
			$placeholders = array(
				'{id}'           => $this->get_id(),
				'{user_name}'    => yith_wcbk_get_user_name( $this->get_user() ),
				'{product_name}' => $this->get_product() ? $this->get_product()->get_name() : '',
			);

			$name = strtr( $format, $placeholders );
			$name = str_replace( '()', '', $name );

			preg_match_all( '/{[a-z_]+}/', $name, $custom_placeholders );

			if ( ! empty( $custom_placeholders ) && ! empty( $custom_placeholders[0] ) ) {
				foreach ( $custom_placeholders[0] as $occurrence ) {
					$key    = str_replace( array( '{', '}' ), '', $occurrence );
					$getter = 'get_' . $key;

					if ( is_callable( array( $this, $getter ) ) ) {
						try {
							$method = new ReflectionMethod( get_class( $this ), $getter );
							if ( 0 === $method->getNumberOfRequiredParameters() ) {
								$value = $this->$getter();
								if ( is_scalar( $value ) ) {
									$name = str_replace( $occurrence, $value, $name );
								}
							}
						} catch ( ReflectionException $exception ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
							// In case of exception, the variable will be not replaced.
						}
					}
				}
			}

			return $name;
		}

		/**
		 * Retrieve the user email from the order or from the user.
		 *
		 * @return string|bool
		 * @since 3.0.0
		 */
		public function get_user_email() {
			$email = false;

			if ( $this->get_order() ) {
				$email = $this->get_order()->get_billing_email();
			} elseif ( $this->get_user() ) {
				$email = $this->get_user()->user_email;
			}

			return apply_filters( 'yith_wcbk_booking_get_user_email', $email, $this );
		}

		/**
		 * Get the order
		 *
		 * @return bool|WC_Order|WC_Order_Refund
		 * @since 2.1.9
		 */
		public function get_order() {
			if ( ! isset( $this->related_objects['order'] ) ) {
				$this->related_objects['order'] = $this->get_order_id() ? wc_get_order( $this->get_order_id() ) : false;
			}

			return $this->related_objects['order'];
		}

		/**
		 * Get the order item
		 *
		 * @return bool|WC_Order_Item_Product
		 * @since 5.1.0
		 */
		public function get_order_item() {
			if ( ! isset( $this->related_objects['order_item'] ) ) {
				$order                               = $this->get_order();
				$this->related_objects['order_item'] = ! ! $order ? $order->get_item( $this->get_order_item_id() ) : false;
			}

			return $this->related_objects['order_item'];
		}

		/**
		 * Get the user data
		 *
		 * @return WP_User|false
		 * @since 3.0.0
		 */
		public function get_user() {
			if ( ! isset( $this->related_objects['user'] ) ) {
				$this->related_objects['user'] = $this->get_user_id() ? get_userdata( $this->get_user_id() ) : false;
			}

			return $this->related_objects['user'];
		}

		/**
		 * Get the edit link
		 *
		 * @return string
		 */
		public function get_edit_link() {
			return get_edit_post_link( $this->get_id() );
		}

		/**
		 * Get the person types HTML
		 *
		 * @param string $format The format of each "row". You can use placeholders: {id}, {title}, {number}.
		 * @param string $join   Join rows with.
		 *
		 * @return string
		 */
		public function get_person_types_html( string $format = '', string $join = '<br />' ): string {
			$format = ! ! $format ? $format : '<strong>{title}:</strong> {number}';
			$parts  = array();
			foreach ( $this->get_person_types() as $person_type ) {
				$id     = $person_type['id'] ?? false;
				$title  = $person_type['title'] ?? false;
				$number = $person_type['number'] ?? false;

				if ( false === $id || false === $title || ! $number ) {
					continue;
				}

				$person_type_title = get_the_title( $id );
				$title             = ! ! $person_type_title ? $person_type_title : $title;

				$placeholders = array(
					'{id}'     => $id,
					'{title}'  => $title,
					'{number}' => $number,
				);

				$parts[] = strtr( $format, $placeholders );
			}

			return implode( $join, $parts );
		}

		/**
		 * Return string for status
		 *
		 * @return string
		 */
		public function get_status_text() {
			$text = strtr( $this->get_status(), yith_wcbk_get_booking_statuses() );

			return apply_filters( 'yith_wcbk_booking_get_status_text', $text, $this );
		}

		/**
		 * Get the title with ID
		 *
		 * @param string $context What the value is for. Valid values are view and edit.
		 *
		 * @return string
		 */
		public function get_title( $context = 'view' ) {
			$title = sprintf( '#%s %s', $this->get_id(), $this->get_raw_title( $context ) );

			return 'view' === $context ? apply_filters( 'yith_wcbk_booking_get_title', $title, $this ) : $title;
		}

		/**
		 * Get the service names for current booking
		 *
		 * @param bool   $show_hidden Show hidden services or not.
		 * @param string $type        The service type; possible values 'included' | 'additional'. Leave empty to get all services.
		 *
		 * @return array
		 */
		public function get_service_names( $show_hidden = true, $type = '' ) {
			$names = array();
			if ( yith_wcbk_is_services_module_active() ) {
				$services = $this->get_service_ids();
				if ( $type ) {
					$split_services = yith_wcbk_split_services_by_type( $services );
					$services       = $split_services[ $type ] ?? array();
				}

				if ( ! ! $services ) {
					foreach ( $services as $service ) {
						$service = yith_wcbk_get_service( $service );
						if ( $service ) {
							if ( $show_hidden || ! $service->is_hidden() ) {
								$names[] = $service->get_name_with_quantity( $this->get_service_quantity( $service->get_id() ) );
							}
						}
					}
				}
				$names = apply_filters( 'yith_wcbk_booking_get_service_names', $names, $show_hidden, $type, $this );
			}

			return $names;
		}

		/**
		 * Get the duration of booking including duration unit
		 */
		public function get_duration_html() {
			$duration_html = yith_wcbk_format_duration( $this->get_duration(), $this->get_duration_unit() );

			$duration_html .= $this->is_all_day() ? ' ' . __( '(All Day)', 'yith-booking-for-woocommerce' ) : '';

			return apply_filters( 'yith_wcbk_booking_get_duration_html', $duration_html, $this );
		}

		/**
		 * Generates a URL to view a booking from the my account page.
		 *
		 * @return string
		 */
		public function get_view_booking_url() {
			$view_booking_endpoint = yith_wcbk()->endpoints->get_endpoint( 'view-booking' );
			$view_booking_url      = wc_get_endpoint_url( $view_booking_endpoint, $this->get_id(), wc_get_page_permalink( 'myaccount' ) );

			return apply_filters( 'yith_wcbk_get_view_booking_url', $view_booking_url, $this );
		}

		/**
		 * Generates a URL to cancel a booking from the my account page.
		 *
		 * @return string
		 */
		public function get_cancel_booking_url() {
			$bookings_endpoint = yith_wcbk()->endpoints->get_endpoint( 'bookings' );
			$bookings_url      = wc_get_endpoint_url( $bookings_endpoint, '', wc_get_page_permalink( 'myaccount' ) );
			$cancel_url        = add_query_arg(
				array(
					'cancel-booking' => $this->get_id(),
					'security'       => wp_create_nonce( 'cancel-booking' ),
				),
				$bookings_url
			);

			return apply_filters( 'yith_wcbk_get_cancel_booking_url', $cancel_url, $this );
		}


		/**
		 * Return the mark action url
		 *
		 * @param string $status The status.
		 * @param array  $params Additional parameters.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_mark_action_url( $status, $params = array() ) {
			$allowed_statuses = yith_wcbk_get_mark_action_allowed_booking_statuses();
			$allowed_statuses = apply_filters( 'yith_wcbk_booking_product_get_mark_action_url_allowed_statuses', $allowed_statuses, $this );
			$url              = '';
			if ( in_array( $status, $allowed_statuses, true ) ) {
				$params['action']     = 'yith_wcbk_mark_booking_status';
				$params['status']     = $status;
				$params['booking_id'] = $this->get_id();
				$url                  = add_query_arg( $params, admin_url( 'admin.php' ) );
				$url                  = wp_nonce_url( $url, 'mark-booking-status-' . $status . '-' . $this->get_id() );
			}

			return apply_filters( 'yith_wcbk_booking_product_get_mark_action_url', $url, $status, $params, $allowed_statuses, $this );
		}

		/**
		 * Generates a URL to pay a booking from the my account page.
		 *
		 * @return string
		 */
		public function get_confirmed_booking_payment_url() {
			$bookings_endpoint = yith_wcbk()->endpoints->get_endpoint( 'bookings' );
			$bookings_url      = wc_get_endpoint_url( $bookings_endpoint, '', wc_get_page_permalink( 'myaccount' ) );
			$payment_url       = add_query_arg( array( 'pay-confirmed-booking' => $this->get_id() ), $bookings_url );

			return apply_filters( 'yith_wcbk_get_confirmed_booking_payment_url', $payment_url, $this );
		}

		/**
		 * Return the booking product.
		 *
		 * @return WC_Product_Booking|false
		 * @since 2.0.0
		 */
		public function get_product() {
			if ( ! isset( $this->related_objects['product'] ) ) {
				$product                          = $this->get_product_id() ? wc_get_product( $this->get_product_id() ) : false;
				$this->related_objects['product'] = $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) ? $product : false;
			}

			return $this->related_objects['product'];
		}

		/**
		 * Return the service quantity
		 *
		 * @param int $service_id The service ID.
		 *
		 * @return int
		 * @since 2.0.5
		 */
		public function get_service_quantity( $service_id ) {
			$quantities = $this->get_service_quantities();

			return $quantities[ $service_id ] ?? 0;
		}

		/*
		|--------------------------------------------------------------------------
		| Conditionals
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Check if the booking can change status to $status
		 *
		 * @param string $status The status.
		 *
		 * @return bool
		 */
		public function can_be( $status ) {
			$value = false;

			switch ( $status ) {
				case 'cancelled_by_user':
					if ( 'yes' === $this->get_can_be_cancelled() && $this->has_status( array( 'unpaid', 'paid', 'pending-confirm', 'confirmed' ) ) ) {
						$now              = strtotime( 'now midnight' );
						$last_cancel_date = yith_wcbk_date_helper()->get_time_sum( $this->get_from(), - $this->get_cancelled_duration(), $this->get_cancelled_unit() );
						if ( $now <= $last_cancel_date ) {
							$value = true;
						}
					}
					break;
				default:
					$value = true;
			}

			return apply_filters( 'yith_wcbk_booking_can_be_' . $status, $value, $this );
		}

		/**
		 * Check if booking has person types
		 *
		 * @return bool
		 */
		public function has_person_types() {
			return ! empty( $this->get_person_types() );
		}

		/**
		 * Check if the booking has persons
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_persons() {
			return 'yes' === $this->get_has_persons();
		}

		/**
		 * Checks the booking status against a passed in status.
		 *
		 * @param array|string $status The status.
		 *
		 * @return bool
		 */
		public function has_status( $status ) {
			return apply_filters( 'yith_wcbk_booking_has_status', ( is_array( $status ) && in_array( $this->get_status(), $status, true ) ) || $this->get_status() === $status, $this, $status );
		}

		/**
		 * Return true if duration unit is hour or minute.
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function has_time() {
			return in_array( $this->get_duration_unit(), array( 'hour', 'minute' ), true );
		}

		/**
		 * Check if the product is all day
		 *
		 * @return bool
		 * @since 2.0.0
		 */
		public function is_all_day() {
			return 'yes' === $this->get_all_day();
		}

		/**
		 * Check if the booking is valid
		 */
		public function is_valid() {
			return ! empty( $this->get_id() ) && get_post_type( $this->get_id() ) === YITH_WCBK_Post_Types::BOOKING;
		}


		/*
		|--------------------------------------------------------------------------
		| Notes
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Add a note to the booking.
		 *
		 * @param string $type The type of the note.
		 * @param string $note The note.
		 *
		 * @return false|int
		 */
		public function add_note( $type, $note = '' ) {
			return yith_wcbk()->notes->add_booking_note( $this->get_id(), $type, $note );
		}

		/**
		 * Add a note to the queue; it'll be stored when the booking will be saved.
		 *
		 * @param string      $type The type of the note.
		 * @param string      $note The note.
		 * @param bool|string $key  The key (to use as unique type).
		 */
		public function enqueue_note( $type, $note = '', $key = false ) {
			$key                        = ! ! $key ? $key : md5( $type . ':' . $note );
			$this->queued_notes[ $key ] = array(
				'type' => $type,
				'note' => $note,
			);
		}

		/**
		 * Remove a note from the queue
		 *
		 * @param string $key The key (to use as unique type).
		 */
		public function dequeue_note( $key = false ) {
			if ( isset( $this->queued_notes[ $key ] ) ) {
				unset( $this->queued_notes[ $key ] );
			}
		}

		/**
		 * Get booking notes
		 *
		 * @return array|null|object
		 */
		public function get_notes() {
			return yith_wcbk()->notes->get_booking_notes( $this->get_id() );
		}

		/**
		 * Save queued notes
		 */
		public function save_queued_notes() {
			foreach ( $this->queued_notes as $note ) {
				yith_wcbk()->notes->add_booking_note( $this->get_id(), $note['type'], $note['note'] );
			}

			$this->queued_notes = array();
		}

		/*
		|--------------------------------------------------------------------------
		| Other Useful Methods
		|--------------------------------------------------------------------------
		|
		*/

		/**
		 * Calculate duration based on From and To
		 *
		 * @return int
		 * @since      2.0.4
		 */
		public function calculate_duration() {
			$date_helper = yith_wcbk_date_helper();
			$duration    = $date_helper->get_time_diff( $this->get_from(), $this->get_to(), $this->get_duration_unit() );
			if ( $this->is_all_day() ) {
				$duration ++;
			}

			return $duration;
		}

		/**
		 * If the booking is 'all day' adjust the To
		 *
		 * @since 2.0.4
		 */
		public function maybe_adjust_all_day_to() {
			if ( $this->is_all_day() ) {
				$this->set_to( strtotime( '23:59:59', $this->get_to() ) );
			}
		}

		/**
		 * Update the product duration based on "from" and "to" values
		 */
		public function update_duration() {
			if ( $this->get_from() && $this->get_to() && $this->get_duration_unit() ) {
				$this->set_duration( $this->calculate_duration() );
			}
		}

		/**
		 * Update status of booking immediately.
		 *
		 * @param string $new_status      The new status.
		 * @param string $additional_note The additional note.
		 * @param string $deprecated      Deprecated argument.
		 */
		public function update_status( $new_status, $additional_note = '', $deprecated = '' ) {
			$additional_note = ! ! $deprecated ? $deprecated : $additional_note;

			if ( ! $this->id ) {
				return;
			}

			// Standardise status names.
			$new_status = 'bk-' === substr( $new_status, 0, 3 ) ? substr( $new_status, 3 ) : $new_status;
			$results    = $this->set_status( $new_status, $additional_note );

			if ( $results ) {
				$this->save();
			}
		}

		/**
		 * Handle the status transition.
		 */
		protected function status_transition() {
			$status_transition = $this->status_transition;

			// Reset status transition variable.
			$this->status_transition = false;

			if ( $status_transition ) {
				try {
					$status_to   = $status_transition['to'];
					$status_from = ! empty( $status_transition['from'] ) ? $status_transition['from'] : false;
					$note        = ! empty( $status_transition['note'] ) ? $status_transition['note'] : false;
					$edited_by   = ! empty( $status_transition['edited_by'] ) && 'customer' === $status_transition['edited_by'] ? ( ' ' . __( 'by customer', 'yith-booking-for-woocommerce' ) ) : '';

					do_action( 'yith_wcbk_booking_status_' . $status_to, $this->get_id(), $this );

					if ( $status_from ) {
						// translators: 1: old booking status 2: new booking status.
						$booking_note = sprintf( __( 'Booking status changed from %1$s to %2$s.', 'yith-booking-for-woocommerce' ), yith_wcbk_get_booking_status_name( $status_from ), yith_wcbk_get_booking_status_name( $status_to ) . $edited_by );

						$this->add_note( 'status_changed', $booking_note . ' ' . trim( $note ) );

						do_action( 'yith_wcbk_booking_status_' . $status_from . '_to_' . $status_to, $this->get_id(), $this );
						do_action( 'yith_wcbk_booking_status_changed', $this->get_id(), $status_from, $status_to, $this );

					} else {
						// translators: %s: new booking status.
						$booking_note = sprintf( __( 'Booking status set to %s.', 'yith-booking-for-woocommerce' ), yith_wcbk_get_booking_status_name( $status_to ) );

						$this->add_note( 'status_changed', $booking_note . ' ' . trim( $note ) );
					}
				} catch ( Exception $e ) {
					$logger = wc_get_logger();
					$logger->error(
						sprintf(
							'Status transition of booking #%d errored!',
							$this->get_id()
						),
						array(
							'order' => $this,
							'error' => $e,
						)
					);
					$this->add_note( 'status_changed', __( 'Error during status transition.', 'yith-booking-for-woocommerce' ) . ' ' . $e->getMessage() );
				}
			}
		}

		/**
		 * Save
		 *
		 * @return int
		 */
		public function save() {
			$is_creating = ! $this->get_id();
			parent::save();

			$this->save_queued_notes();
			$this->status_transition();

			return $this->get_id();
		}

		/**
		 * Fill the default metadata with the post meta stored in db
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public function get_booking_meta() {
			return $this->get_data();
		}

		/**
		 * Create new booking
		 *
		 * @param array $args Arguments to create booking.
		 */
		private function create_booking( $args ) {
			/**
			 * The booking product
			 *
			 * @var WC_Product_Booking $product
			 */
			$product_id = ! empty( $args['product_id'] ) ? absint( $args['product_id'] ) : false;
			$product    = $product_id ? wc_get_product( $product_id ) : false;

			if ( $product ) {
				$props_to_set               = $args;
				$props_to_set['raw_title']  = $args['title'] ?? '';
				$props_to_set['status']     = $args['status'] ?? 'bk-unpaid';
				$props_to_set['product_id'] = $product_id;

				if ( isset( $args['title'] ) ) {
					unset( $args['title'] );
				}

				$this->set_props( $props_to_set );

				$this->maybe_adjust_all_day_to();

				$this->save();
				$this->set_object_read( true );
			}
		}

		/**
		 * Update booking data based on product data.
		 */
		public function update_product_data() {
			$product = $this->get_product();
			if ( $product ) {
				$this->set_duration_unit( $product->get_duration_unit() );
				$this->set_can_be_cancelled( $product->is_cancellation_available() );
				$this->set_cancelled_duration( $product->get_cancellation_available_up_to() );
				$this->set_cancelled_unit( $product->get_cancellation_available_up_to_unit() );
				$this->set_location( $product->get_location() );
				$this->set_all_day( $product->is_full_day() );
				$this->set_has_persons( $product->has_people() );
			}
		}

		/**
		 * Populate the booking
		 *
		 * @deprecated 3.0.0
		 */
		public function populate() {

		}

		/**
		 * Update post meta in booking
		 *
		 * @param array $meta the meta array.
		 *
		 * @deprecated 3.0.0 | use the CRUD instead
		 */
		public function update_booking_meta( $meta ) {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Booking::update_booking_meta', '3.0.0', 'CRUD' );
			if ( isset( $meta['services'] ) && ! isset( $meta['service_ids'] ) ) {
				$meta['service_ids'] = $meta['services'];
				unset( $meta['services'] );
			}
			$this->set_props( $meta );
			$this->save();
		}

		/**
		 * Retrieve the PDF URL.
		 *
		 * @param string $type The PDF Type (admin or customer).
		 *
		 * @return string
		 */
		public function get_pdf_url( $type = 'customer' ) {
			$type = in_array( $type, array( 'admin', 'customer' ), true ) ? $type : 'customer';

			return wp_nonce_url(
				add_query_arg(
					array(
						'action'     => 'yith_wcbk_generate_pdf',
						'pdf_type'   => $type,
						'booking_id' => $this->get_id(),
					),
					admin_url()
				),
				"generate-pdf-{$type}-" . $this->get_id()
			);
		}


		/**
		 * Retrieve details about the time to start the booking.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function get_time_to_start_details() {
			$now = yith_wcbk_get_local_timezone_timestamp(); // The local timezone timestamp is used to consider the "local time".
			if ( $now < $this->get_from() ) {
				$status = 'future';
			} elseif ( $now < $this->get_to() ) {
				$status = 'current';
			} else {
				$status = 'past';
			}
			$interval           = yith_wcbk_date_helper()->get_time_diff( $now, $this->get_from() );
			$formatted_interval = yith_wcbk_date_helper()->format_interval(
				$interval,
				array(
					'minimum_unit' => $this->has_time() ? 'minute' : 'day',
				)
			);

			return compact( 'status', 'interval', 'formatted_interval' );
		}

		/**
		 * Retrieve the "time to start" HTML
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_time_to_start_html() {
			$details = $this->get_time_to_start_details();
			$labels  = array(
				// translators: %s is the interval; ex: Starts in 1 month, 12 days.
				'future'  => __( 'Starts in %s', 'yith-booking-for-woocommerce' ),
				// translators: it's related to current bookings, so "in progress" bookings.
				'current' => __( 'In progress', 'yith-booking-for-woocommerce' ),
				// translators: it's related to past bookings, so "finished" bookings.
				'past'    => __( 'Finished', 'yith-booking-for-woocommerce' ),
			);

			$label = strtr( $details['status'], $labels );

			if ( 'future' === $details['status'] ) {
				$label = sprintf( $label, $details['formatted_interval'] );
			}

			return '<div class="yith-wcbk-booking-time-to-start yith-wcbk-booking-time-to-start--' . esc_attr( $details['status'] ) . '">' . esc_html( $label ) . '</span>';
		}

		/**
		 * Get booking data to be displayed.
		 * Useful in booking-details on frontend, emails, admin calendar.
		 *
		 * @param string $context The context (frontend or admin).
		 * @param array  $args    Args.
		 *
		 * @return array
		 */
		public function get_booking_data_to_display( string $context = 'frontend', array $args = array() ): array {
			$parent_data = parent::get_booking_data_to_display( $context, $args );
			$is_admin    = 'admin' === $context;
			$is_frontend = ! $is_admin;
			$data        = array(
				'status'   => array(
					'label'    => __( 'Status', 'yith-booking-for-woocommerce' ),
					'display'  => esc_html( $this->get_status_text() ),
					'priority' => 10,
				),
				'product'  => array(
					'label'    => __( 'Product', 'yith-booking-for-woocommerce' ),
					'display'  => $parent_data['product']['display'] ?? null,
					'priority' => 20,
				),
				'order'    => array(
					'label'    => __( 'Order', 'yith-booking-for-woocommerce' ),
					'priority' => 30,
				),
				'user'     => array(
					'label'    => __( 'User', 'yith-booking-for-woocommerce' ),
					'priority' => 40,
				),
				'from'     => array(
					'label'    => $is_frontend ? yith_wcbk_get_label( 'from' ) : __( 'From', 'yith-booking-for-woocommerce' ),
					'display'  => $this->get_formatted_from(),
					'priority' => 50,
				),
				'to'       => array(
					'label'    => $is_frontend ? yith_wcbk_get_label( 'to' ) : __( 'To', 'yith-booking-for-woocommerce' ),
					'display'  => $this->get_formatted_to(),
					'priority' => 60,
				),
				'duration' => array(
					'label'    => $is_frontend ? yith_wcbk_get_label( 'duration' ) : __( 'Duration', 'yith-booking-for-woocommerce' ),
					'display'  => $this->get_duration_html(),
					'priority' => 70,
				),
			);

			$order    = $args['order'] ?? $this->get_order();
			$order_id = $args['order_id'] ?? $this->get_order_id();

			if ( $order_id ) {
				if ( $is_frontend ) {
					if ( $order ) {
						$link  = $order->get_view_order_url();
						$title = _x( '#', 'hash before order number', 'woocommerce' ) . $order->get_order_number();

						$data['order']['display'] = sprintf( '<a href="%s">%s</a>', esc_url( $link ), esc_html( $title ) );
					}
				} else {
					$data['order']['display'] = yith_wcbk_admin_order_info_html(
						$this,
						array(
							'show_email'  => false,
							'show_status' => false,
						),
						false
					);
				}
			} elseif ( $is_admin ) {
				$data['user']['display'] = yith_wcbk_admin_user_info_html( $this, false );
			}

			$data = apply_filters( 'yith_wcbk_booking_get_booking_data_to_display', $data, $context, $args, $this );

			yith_wcbk_array_sort( $data );

			return $data;
		}
	}
}
