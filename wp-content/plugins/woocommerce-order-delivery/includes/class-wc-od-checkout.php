<?php
/**
 * Class to handle the plugin behaviour in the checkout page
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Checkout' ) ) {

	/**
	 * Class WC_OD_Checkout
	 */
	class WC_OD_Checkout {

		use WC_OD_Singleton_Trait;

		/**
		 * The first allowed date for ship an order.
		 *
		 * Calculate this data is a heavy process, so we defined this property
		 * to store the value and execute the process just one time per request.
		 *
		 * @since 1.0.0
		 *
		 * @var int A timestamp representing the first allowed date to ship an order.
		 */
		private $first_shipping_date;

		/**
		 * The first allowed date for deliver an order.
		 *
		 * Calculate this data is a heavy process, so we defined this property
		 * to store the value and execute the process just one time per request.
		 *
		 * @since 1.0.0
		 *
		 * @var int A timestamp representing the first allowed date to deliver an order.
		 */
		private $first_delivery_date;


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		protected function __construct() {
			// WP Hooks.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'wp_footer', array( $this, 'print_calendar_settings' ) );

			// WooCommerce hooks.
			add_action( 'woocommerce_checkout_init', array( $this, 'register_location' ) );
			add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );
			add_filter( 'woocommerce_checkout_get_value', array( $this, 'checkout_get_value' ), 10, 2 );
			add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'checkout_fragments' ) );
			add_action( 'woocommerce_cart_emptied', array( $this, 'cart_emptied' ) );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ) );
			add_action( 'woocommerce_cart_calculate_fees', array( $this, 'cart_calculate_fees' ) );
			add_action( 'woocommerce_after_checkout_validation', array( $this, 'checkout_validation' ), 10, 2 );
			add_action( 'woocommerce_checkout_create_order_fee_item', array( $this, 'create_order_fee_item' ), 10, 2 );
			add_action( 'woocommerce_checkout_create_order', array( $this, 'update_order_meta' ) );
		}

		/**
		 * Registers the location of the delivery details in the checkout form.
		 *
		 * @since 1.9.0
		 */
		public function register_location() {
			$locations = array(
				'before_customer_details' => array(
					'hook'     => 'woocommerce_checkout_before_customer_details',
					'priority' => 10,
				),
				'before_billing'          => array(
					'hook'     => 'woocommerce_checkout_billing',
					'priority' => 5,
				),
				'after_billing'           => array(
					'hook'     => 'woocommerce_checkout_billing',
					'priority' => 99,
				),
				'before_order_notes'      => array(
					'hook'     => 'woocommerce_before_order_notes',
					'priority' => 10,
				),
				'after_order_notes'       => array(
					'hook'     => 'woocommerce_after_order_notes',
					'priority' => 10,
				),
				'after_additional_fields' => array(
					'hook'     => 'woocommerce_checkout_shipping',
					'priority' => 99,
				),
				'after_order_review'      => array(
					'hook'     => 'woocommerce_checkout_order_review',
					'priority' => 15,
				),
				'after_customer_details'  => array(
					'hook'     => 'woocommerce_checkout_after_customer_details',
					'priority' => 10,
				),
			);

			$key      = WC_OD()->settings()->get_setting( 'checkout_location' );
			$location = ( isset( $locations[ $key ] ) ? $locations[ $key ] : $locations['after_additional_fields'] );

			/**
			 * Filters the location of the delivery details in the checkout form.
			 *
			 * @since 1.9.0
			 *
			 * @param array  $location An array with the action hook and its priority.
			 * @param string $key      The location key.
			 */
			$location = apply_filters( 'wc_od_checkout_location', $location, $key );

			add_action( $location['hook'], array( $this, 'checkout_content' ), $location['priority'] );
		}

		/**
		 * Gets the chosen shipping method for the specified package.
		 *
		 * @since 1.5.0
		 *
		 * @param int $package The shipping package index.
		 * @return string|false The shipping method ID. False otherwise.
		 */
		public function get_shipping_method( $package = 0 ) {
			$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );

			return ( ! empty( $chosen_methods ) && isset( $chosen_methods[ $package ] ) ? $chosen_methods[ $package ] : false );
		}

		/**
		 * Gets if the selected shipping method is local pickup or not.
		 *
		 * @since 1.4.0
		 *
		 * @return bool
		 */
		public function is_local_pickup() {
			return wc_od_shipping_method_is_local_pickup( $this->get_shipping_method() );
		}

		/**
		 * Gets if it's necessary to display the 'Shipping & Delivery' information on the checkout page.
		 *
		 * @since 1.4.0
		 *
		 * @return bool
		 */
		public function needs_details() {
			$needs_details = (
				is_checkout() && WC()->cart->needs_shipping() &&
				( ! $this->is_local_pickup() || wc_od_is_local_pickup_enabled() )
			);

			/**
			 * Filter if it's necessary to display the 'Shipping & Delivery' information on the checkout page.
			 *
			 * @since 1.4.0
			 *
			 * @param bool $needs_details
			 */
			return apply_filters( 'wc_od_checkout_needs_details', $needs_details );
		}

		/**
		 * Gets if it's necessary to display a date field on the checkout page.
		 *
		 * @since 1.4.0
		 *
		 * @return bool
		 */
		public function needs_date() {
			$needs_date = (
				$this->needs_details() &&
				'calendar' === WC_OD()->settings()->get_setting( 'checkout_delivery_option' )
			);

			/**
			 * Filters if it's necessary to display a date field on the checkout page.
			 *
			 * @since 1.4.0
			 *
			 * @param bool $needs_date
			 */
			return apply_filters( 'wc_od_checkout_needs_date', $needs_date );
		}

		/**
		 * Gets if there are available dates for delivery.
		 *
		 * @since 2.0.0
		 *
		 * @return bool
		 */
		public function has_available_dates() {
			return ( $this->needs_date() && 0 < $this->get_first_delivery_date() );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.0.0
		 */
		public function enqueue_scripts() {
			// Don't use the 'needs_date' method. The conditions may vary on refreshing the fragments.
			if ( ! is_checkout() || ! WC()->cart->needs_shipping() ) {
				return;
			}

			$suffix = wc_od_get_scripts_suffix();

			wc_od_enqueue_datepicker( 'checkout' );
			wp_enqueue_script( 'wc-od-checkout', WC_OD_URL . "assets/js/wc-od-checkout{$suffix}.js", array( 'jquery', 'wc-od-datepicker' ), WC_OD_VERSION, true );
		}

		/**
		 * Gets the delivery date field arguments.
		 *
		 * @since 1.0.1
		 * @since 1.5.0 Added `$args` parameter.
		 *
		 * @param array $args Optional. The arguments to overwrite.
		 * @return array An array with the delivery date field arguments.
		 */
		public function get_delivery_date_field_args( $args = array() ) {
			if ( $this->is_local_pickup() ) {
				$args['label'] = __( 'Pickup date', 'woocommerce-order-delivery' );
			}

			return wc_od_get_delivery_date_field_args( $args, 'checkout' );
		}

		/**
		 * Gets the available time frames for the specified date.
		 *
		 * @since 2.0.0
		 *
		 * @param string|int $date The date or timestamp.
		 * @return WC_OD_Collection_Time_Frames.
		 */
		public function get_time_frames_for_date( $date ) {
			return wc_od_get_time_frames_for_date(
				$date,
				array(
					'shipping_method' => $this->get_shipping_method(),
				),
				'checkout'
			);
		}

		/**
		 * Register the delivery fields in the checkout form.
		 *
		 * @since 1.5.0
		 *
		 * @param array $fields The checkout fields.
		 * @return array
		 */
		public function checkout_fields( $fields ) {
			if ( ! $this->has_available_dates() ) {
				return $fields;
			}

			$fields['delivery'] = array(
				'delivery_date' => $this->get_delivery_date_field_args(),
			);

			$delivery_date = WC()->checkout()->get_value( 'delivery_date' );

			if ( $delivery_date ) {
				add_filter( 'wc_od_get_time_frames_for_date', array( $this, 'filter_unavailable_time_frames' ), 10, 2 );

				$time_frames = $this->get_time_frames_for_date( $delivery_date );

				if ( count( $time_frames ) ) {
					$fields['delivery']['delivery_time_frame'] = array(
						'label'    => _x( 'Time frame', 'checkout field label', 'woocommerce-order-delivery' ),
						'type'     => 'select',
						'class'    => array( 'form-row-wide' ),
						'required' => ( 'required' === WC_OD()->settings()->get_setting( 'delivery_fields_option' ) ),
						'options'  => wc_od_get_time_frames_choices( $time_frames, 'checkout' ),
						'priority' => 20,
					);
				}
			}

			return $fields;
		}

		/**
		 * Gets the value for a checkout field.
		 *
		 * Load the delivery fields value on refreshing the checkout fragments.
		 *
		 * @since 1.4.0
		 *
		 * @param mixed  $value The field value.
		 * @param string $input The input key.
		 * @return mixed
		 */
		public function checkout_get_value( $value, $input ) {
			// We cannot use the method 'WC_Checkout->get_checkout_fields()' due to nested calls.
			if ( 0 === strpos( $input, 'delivery_' ) ) {
				$value = WC()->session->get( $input );
			}

			return $value;
		}

		/**
		 * Cart emptied.
		 *
		 * @since 2.0.0
		 */
		public function cart_emptied() {
			// Deletes the delivery data stored in session.
			WC()->session->set( 'delivery_date', null );
			WC()->session->set( 'delivery_time_frame', null );
		}

		/**
		 * Performs actions before calculating the cart totals.
		 *
		 * When calling the hook `woocommerce_checkout_update_order_review`, the session, customer, and cart data
		 * haven't been updated yet. So, we use this hook instead for validating the delivery details.
		 *
		 * @since 2.2.1
		 */
		public function before_calculate_totals() {
			// Hook not triggered inside the method `WC_Ajax::update_order_review()`.
			if ( ! isset( $_REQUEST['security'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['security'] ), 'update-order-review' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return;
			}

			$posted_data = ( isset( $_POST['post_data'] ) ? wp_unslash( $_POST['post_data'] ) : '' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$this->update_order_review( $posted_data );
		}

		/**
		 * Updates the order review during checkout.
		 *
		 * @since 2.0.0
		 *
		 * @param string $posted_data The posted data.
		 */
		public function update_order_review( $posted_data ) {
			parse_str( $posted_data, $data );

			// Use an empty string instead of null to disambiguate a non-initialized value in the session.
			$delivery_date       = '';
			$delivery_time_frame = '';

			// Validates the delivery date.
			if ( ! empty( $data['delivery_date'] ) ) {
				$value = wc_od_localize_date( $data['delivery_date'], 'Y-m-d' );

				// The delivery days statuses haven't been processed with the shipping method.
				$args = $this->get_delivery_date_args();
				unset( $args['delivery_days'] );

				if ( wc_od_validate_delivery_date( $value, $args, 'checkout' ) ) {
					$delivery_date = $value;
				}
			}

			if ( $delivery_date ) {
				/*
				 * When the customer changes the delivery date or the shipping method,
				 * the submitted time frame might not be available.
				 */
				$time_frames = $this->get_time_frames_for_date( $delivery_date );
				$count       = count( $time_frames );

				// Assign the unique time frame available.
				if ( 1 === $count ) {
					$delivery_time_frame = $time_frames->first()->get_id();
				} elseif ( 1 < $count && ! empty( $data['delivery_time_frame'] ) ) {
					$time_frame_id = wc_clean( wp_unslash( $data['delivery_time_frame'] ) );

					if ( is_numeric( $time_frame_id ) ) {
						$time_frame = $time_frames->get( $time_frame_id );
					} else {
						$time_frame = wc_od_get_time_frame_for_date( $delivery_date, $time_frame_id );
					}

					/*
					 * If the time frame exists, the customer might have selected the same week day or
					 * just changed the field value.
					 */
					if ( $time_frame ) {
						$delivery_time_frame = $time_frame_id;
					}
				}
			}

			// Store the delivery details in session.
			WC()->session->set( 'delivery_date', $delivery_date );
			WC()->session->set( 'delivery_time_frame', $delivery_time_frame );
		}

		/**
		 * Adds custom fees to the cart.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_Cart $cart Cart object.
		 */
		public function cart_calculate_fees( $cart ) {
			/**
			 * Filters whether to enable the delivery fees for the specified cart.
			 *
			 * @since 2.0.0
			 *
			 * @param bool    $enable_fees Whether to enable the fees.
			 * @param WC_Cart $cart        Cart object.
			 */
			$enable_fees = apply_filters( 'wc_od_enable_fees_for_cart', is_checkout(), $cart );

			if ( ! $enable_fees ) {
				return;
			}

			$args = array(
				'delivery_date' => WC()->session->get( 'delivery_date' ),
				'time_frame'    => WC()->session->get( 'delivery_time_frame' ),
			);

			$cart_fees = new WC_OD_Cart_Fees( $cart );
			$cart_fees->calculate_fees( $args );
			$cart_fees->add_fees();
		}

		/**
		 * Register the checkout fragments.
		 *
		 * Allow refresh the content when the checkout form changes.
		 *
		 * @since 1.4.0
		 *
		 * @param array $fragments The fragments to update in the checkout form.
		 * @return mixed An array with the checkout fragments.
		 */
		public function checkout_fragments( $fragments ) {
			ob_start();
			$this->checkout_content();
			$fragments['#wc-od'] = ob_get_clean();

			return $this->add_calendar_settings_fragment( $fragments );
		}

		/**
		 * Outputs the checkout content.
		 *
		 * @since 1.0.0
		 */
		public function checkout_content() {
			if ( ! $this->needs_details() || ( $this->needs_date() && ! $this->has_available_dates() ) ) {
				// Only prints the container to allow the fragment refresh.
				echo '<div id="wc-od"></div>';
				return;
			}

			$is_local_pickup = $this->is_local_pickup();
			$checkout_option = WC_OD()->settings()->get_setting( 'checkout_delivery_option' );
			$range           = WC_OD_Delivery_Ranges::get_range_matching_shipping_method( $this->get_shipping_method() );

			if ( $is_local_pickup ) {
				$title         = __( 'Pickup details', 'woocommerce-order-delivery' );
				$checkout_text = WC_OD()->settings()->get_setting( 'pickup_text' );
			} else {
				$title         = __( 'Delivery details', 'woocommerce-order-delivery' );
				$checkout_text = WC_OD()->settings()->get_setting( 'checkout_text' );
			}

			/**
			 * Filters the arguments used by the checkout/form-delivery-date.php template.
			 *
			 * @since 1.1.0
			 * @since 1.5.0 The parameter `delivery_date_field` is deprecated.
			 * @since 1.9.5 Added `checkout_text` parameter.
			 * @since 2.0.0 The parameter `delivery_date_field` is no longer provided.
			 * @since 2.6.0 Added `checkout_option`, and `is_local_pickup` parameters.
			 * @since 2.6.0 The parameters `delivery_option` is deprecated. Use `checkout_option` instead.
			 *
			 * @param array $args The template arguments.
			 */
			$args = apply_filters(
				'wc_od_checkout_delivery_details_args',
				array(
					'title'           => $title,
					'checkout_text'   => $checkout_text,
					'checkout_option' => $checkout_option,
					'is_local_pickup' => $is_local_pickup,
					'checkout'        => WC()->checkout(),
					'delivery_option' => $checkout_option, // Deprecated.
					'shipping_date'   => wc_od_localize_date( $this->get_first_shipping_date() ),
					'delivery_range'  => array(
						'min' => $range->get_from(),
						'max' => $range->get_to(),
					),
				)
			);

			wc_od_get_template( 'checkout/form-delivery-date.php', $args );
		}

		/**
		 * Gets the arguments used to calculate the delivery date.
		 *
		 * @since 1.1.0
		 *
		 * @return array An array with the arguments.
		 */
		public function get_delivery_date_args() {
			$today           = wc_od_get_local_date();
			$start_timestamp = strtotime( $this->min_delivery_days() . ' days', $today );
			$end_timestamp   = strtotime( ( $this->max_delivery_days() + 1 ) . ' days', $today ); // Non-inclusive.
			$delivery_days   = wc_od_get_delivery_days()->all();

			$disabled_dates = wc_od_get_disabled_days(
				array(
					'type'    => 'delivery',
					'start'   => date( 'Y-m-d', $start_timestamp ),
					'end'     => date( 'Y-m-d', $end_timestamp ),
					'country' => WC()->customer->get_shipping_country(),
					'state'   => WC()->customer->get_shipping_state(),
				)
			);

			$disabled_dates = array_merge(
				$disabled_dates,
				WC_OD_Delivery_Dates::get_disabled_dates(
					array(
						'start_date'      => $start_timestamp,
						'end_date'        => $end_timestamp,
						'delivery_days'   => $delivery_days,
						'disabled_days'   => $disabled_dates,
						'shipping_method' => $this->get_shipping_method(),
					),
					'Y-m-d'
				)
			);

			/**
			 * Filter the arguments used to calculate the delivery date.
			 *
			 * @since 1.1.0
			 * @since 1.5.0 Added `shipping_method` parameter.
			 *
			 * @param array $args The arguments.
			 */
			return apply_filters(
				'wc_od_delivery_date_args',
				array(
					'shipping_method' => $this->get_shipping_method(),
					'start_date'      => $start_timestamp,
					'end_date'        => $end_timestamp,
					'delivery_days'   => $delivery_days,
					'disabled_days'   => $disabled_dates,
				)
			);
		}

		/**
		 * Gets the calendar settings.
		 *
		 * @since 1.1.0
		 *
		 * @return array An array with the calendar settings.
		 */
		public function get_calendar_settings() {
			$date_format = wc_od_get_date_format( 'php' );
			$args        = $this->get_delivery_date_args();

			$delivery_days_status = wc_od_get_delivery_days_status(
				$args['delivery_days'],
				array(
					'shipping_method' => $args['shipping_method'],
				),
				'checkout'
			);

			return wc_od_get_calendar_settings(
				array(
					'startDate'          => wc_od_localize_date( $args['start_date'], $date_format ),
					'endDate'            => wc_od_localize_date( ( wc_od_get_timestamp( $args['end_date'] ) - DAY_IN_SECONDS ), $date_format ), // Inclusive.
					'daysOfWeekDisabled' => array_keys( $delivery_days_status, 'no', true ),
					'datesDisabled'      => array_map( 'wc_od_localize_date', $args['disabled_days'] ),
				),
				'checkout'
			);
		}

		/**
		 * Prints the script with the calendar settings.
		 *
		 * NOTE: This script is equivalent to use the wp_localize_script(), but adding an id attribute to the script tag.
		 * This id is necessary to identify the script to refresh on the update_order_review_fragments action.
		 *
		 * @since 1.1.0
		 */
		public function print_calendar_settings() {
			$settings = ( $this->has_available_dates() ? $this->get_calendar_settings() : array() );
			?>
			<script id="wc_od_checkout_l10n" type="text/javascript">
				/* <![CDATA[ */
				var wc_od_checkout_l10n = <?php echo wp_json_encode( $settings ); ?>;
				/* ]]> */
			</script>
			<?php
		}

		/**
		 * Adds the calendar settings fragment.
		 *
		 * NOTE: Allow refresh the calendar settings when the checkout form change.
		 *
		 * @since 1.1.0
		 *
		 * @param array $fragments The fragments to update in the checkout form.
		 * @return mixed An array with the checkout fragments.
		 */
		public function add_calendar_settings_fragment( $fragments ) {
			ob_start();
			$this->print_calendar_settings();
			$fragments['#wc_od_checkout_l10n'] = ob_get_clean();

			return $fragments;
		}

		/**
		 * Validates the delivery fields on the checkout process.
		 *
		 * @since 1.5.0
		 * @since 2.2.2 Added parameters `$data` and `$errors`.
		 *
		 * @param  array    $data   An array of posted data.
		 * @param  WP_Error $errors Validation errors.
		 */
		public function checkout_validation( $data, $errors ) {
			if ( ! $this->has_available_dates() ) {
				return;
			}

			$fields = WC()->checkout()->get_checkout_fields( 'delivery' );

			foreach ( $fields as $field_id => $field ) {
				$value = ( isset( $data[ $field_id ] ) ? $data[ $field_id ] : '' );

				/**
				 * Filters the callback used to validate the checkout field.
				 *
				 * The dynamic portion of the hook name refers to the field ID.
				 *
				 * @since 1.5.0
				 *
				 * @param mixed $callback The validation callback.
				 * @param array $field    The field data.
				 */
				$callback = apply_filters( "wc_od_checkout_{$field_id}_validation_callback", array( $this, 'validate_' . $field_id ), $field );

				// Check the returned value is exactly 'false' to disambiguate legacy callbacks returning nothing.
				if ( is_callable( $callback ) && false === call_user_func( $callback, $value, $field ) ) {
					$errors->add(
						$field_id,
						/* translators: %s: field label */
						sprintf( __( '%s is not valid.', 'woocommerce-order-delivery' ), '<strong>' . esc_html( $field['label'] ) . '</strong>' )
					);
				}
			}
		}

		/**
		 * Validates the delivery date field.
		 *
		 * @since 1.0.0
		 * @since 1.5.0 Added `$value` and `$field` parameters.
		 * @since 2.2.2 Returns a boolean value.
		 *
		 * @param mixed $value The field value.
		 * @param array $field The field data.
		 * @return bool
		 */
		public function validate_delivery_date( $value, $field ) {
			// The required field validation is done by WooCommerce. So, an empty value is allowed at this point.
			if ( ! $value ) {
				return true;
			}

			// The delivery days statuses haven't been processed with the shipping method.
			$args = $this->get_delivery_date_args();
			unset( $args['delivery_days'] );

			return wc_od_validate_delivery_date( $value, $args, 'checkout' );
		}

		/**
		 * Validates the delivery time frame field.
		 *
		 * @since 1.5.0
		 * @since 2.2.2 Returns a boolean value.
		 *
		 * @param mixed $value The field value.
		 * @param array $field The field data.
		 * @return bool
		 */
		public function validate_delivery_time_frame( $value, $field ) {
			return in_array( $value, array_keys( $field['options'] ) ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		}

		/**
		 * Gets the delivery date to save with the order.
		 *
		 * @since 1.1.0
		 *
		 * @return string|false The delivery date string. False otherwise.
		 */
		public function get_order_delivery_date() {
			$checkout      = WC()->checkout();
			$delivery_date = $checkout->get_value( 'delivery_date' );

			if ( $delivery_date ) {
				// Stores the date in the ISO 8601 format.
				$delivery_date = wc_od_localize_date( $delivery_date, 'Y-m-d' );
			}

			return ( $delivery_date ? $delivery_date : false );
		}

		/**
		 * Gets the shipping date to save with the order.
		 *
		 * @since 1.4.0
		 * @since 2.0.0 The parameter `$delivery_date` is required.
		 *
		 * @param string|int $delivery_date The order delivery date.
		 * @return string|false The shipping date string. False otherwise.
		 */
		public function get_order_shipping_date( $delivery_date = null ) {
			if ( ! $delivery_date ) {
				wc_doing_it_wrong( __FUNCTION__, 'You must provide a delivery date as the first argument.', '2.0.0' );
				return false;
			}

			// Assigns a shipping date from the delivery date.
			$shipping_date = wc_od_get_last_shipping_date(
				array(
					'shipping_method'             => $this->get_shipping_method(),
					'delivery_date'               => $delivery_date,
					'disabled_delivery_days_args' => array(
						'type'    => 'delivery',
						'country' => WC()->customer->get_shipping_country(),
						'state'   => WC()->customer->get_shipping_state(),
					),
				),
				'checkout-auto'
			);

			if ( $shipping_date ) {
				// Stores the date in the ISO 8601 format.
				$shipping_date = wc_od_localize_date( $shipping_date, 'Y-m-d' );
			}

			return ( $shipping_date ? $shipping_date : false );
		}

		/**
		 * Gets the time frame to save with the order.
		 *
		 * @since 1.5.0
		 *
		 * @param string|int $delivery_date The order delivery date.
		 * @return array|false An array with the time frame data. False otherwise.
		 */
		public function get_order_time_frame( $delivery_date ) {
			$checkout      = WC()->checkout();
			$time_frame_id = $checkout->get_value( 'delivery_time_frame' );
			$time_frame    = ( $time_frame_id ? wc_od_get_time_frame_for_date( $delivery_date, $time_frame_id ) : false );

			return ( $time_frame ? wc_od_time_frame_to_order( $time_frame ) : false );
		}

		/**
		 * Adjusts the order item fee before saving the order.
		 *
		 * @since 2.0.0
		 *
		 * @param WC_Order_Item_Fee $item    Order item fee.
		 * @param string            $fee_key The fee key.
		 * @return WC_Order_Item_Fee
		 */
		public function create_order_fee_item( $item, $fee_key ) {
			if ( 0 === strpos( $fee_key, 'delivery_' ) ) {
				$item->add_meta_data( '_delivery_fee', 'yes', true );
			}

			return $item;
		}

		/**
		 * Saves the order delivery details during checkout.
		 *
		 * @since 1.0.0
		 * @since 1.1.0 Accepts a WC_Order as parameter.
		 * @since 1.7.0 Doesn't accept an Order ID as a parameter anymore.
		 *
		 * @param WC_Order $order Order object.
		 */
		public function update_order_meta( $order ) {
			if ( ! $order instanceof WC_Order ) {
				wc_doing_it_wrong( __FUNCTION__, 'Expected a WC_Order object.', '1.7.0' );
				return;
			}

			if ( ! $this->has_available_dates() ) {
				return;
			}

			$delivery_date = $this->get_order_delivery_date();

			if ( ! $delivery_date ) {
				return;
			}

			$order->update_meta_data( '_delivery_date', $delivery_date );

			$time_frame = $this->get_order_time_frame( $delivery_date );

			if ( $time_frame ) {
				$order->update_meta_data( '_delivery_time_frame', $time_frame );
			}

			// Don't calculate the shipping date for local pickup orders.
			if ( wc_od_order_is_local_pickup( $order ) ) {
				return;
			}

			$shipping_date = $this->get_order_shipping_date( $delivery_date );

			if ( $shipping_date ) {
				$order->update_meta_data( '_shipping_date', $shipping_date );
			}
		}

		/**
		 * Gets the first day to ship the orders.
		 *
		 * @since 1.0.0
		 * @since 1.5.5 Added `$context` parameter.
		 *
		 * @param string $context Optional. The context.
		 * @return int A timestamp representing the first allowed date to ship the orders.
		 */
		public function get_first_shipping_date( $context = 'checkout' ) {
			if ( ! $this->first_shipping_date ) {
				$this->first_shipping_date = wc_od_get_first_shipping_date( array(), $context );
			}

			return $this->first_shipping_date;
		}

		/**
		 * Gets the first day to deliver the orders.
		 *
		 * @since 1.0.0
		 * @since 1.4.0 Added `$context` parameter.
		 *
		 * @param string $context Optional. The context.
		 * @return int A timestamp representing the first allowed date to deliver the orders.
		 */
		public function get_first_delivery_date( $context = 'checkout' ) {
			if ( ! $this->first_delivery_date ) {
				$this->first_delivery_date = wc_od_get_first_delivery_date(
					array(
						'shipping_date'      => $this->get_first_shipping_date( $context ),
						'shipping_method'    => $this->get_shipping_method(),
						'end_date'           => strtotime( ( $this->max_delivery_days() + 1 ) . ' days', wc_od_get_local_date() ), // Non-inclusive.
						'disabled_days_args' => array(
							'type'    => 'delivery',
							'country' => WC()->customer->get_shipping_country(),
							'state'   => WC()->customer->get_shipping_state(),
						),
					),
					$context
				);
			}

			return $this->first_delivery_date;
		}

		/**
		 * Gets the minimum days for delivery.
		 *
		 * @since 1.0.0
		 *
		 * @return int The minimum days for delivery.
		 */
		public function min_delivery_days() {
			$min_delivery_days = 0;
			$delivery_date     = $this->get_first_delivery_date();

			if ( $delivery_date ) {
				$min_delivery_days = ( ( $delivery_date - wc_od_get_local_date() ) / DAY_IN_SECONDS );
			}

			/**
			 * Filters the minimum days for delivery.
			 *
			 * @since 1.0.0
			 *
			 * @param int $min_delivery_days The minimum days for delivery.
			 */
			$min_delivery_days = apply_filters( 'wc_od_min_delivery_days', $min_delivery_days );

			return intval( $min_delivery_days );
		}

		/**
		 * Gets the maximum days for delivery.
		 *
		 * @since 1.0.0
		 *
		 * @return int The maximum days for delivery.
		 */
		public function max_delivery_days() {
			/**
			 * Filters the maximum days for delivery.
			 *
			 * @since 1.0.0
			 *
			 * @param int $max_delivery_days The maximum days for delivery.
			 */
			$max_delivery_days = apply_filters( 'wc_od_max_delivery_days', WC_OD()->settings()->get_setting( 'max_delivery_days' ) );

			return intval( $max_delivery_days );
		}

		/**
		 * Checks if the time frames have room for more orders. Otherwise the time frame will be removed.
		 *
		 * @since 1.8.0
		 *
		 * @param WC_OD_Collection_Time_Frames $time_frames A collection of time frames.
		 * @param int|string                   $timestamp The timestamp date.
		 *
		 * @return WC_OD_Collection_Time_Frames
		 */
		public function filter_unavailable_time_frames( $time_frames, $timestamp ) {
			$available_time_frames = new WC_OD_Collection_Time_Frames();

			/* @var WC_OD_Time_Frame $time_frame A WC_OD_Time_Frame object. */
			foreach ( $time_frames as $index => $time_frame ) {
				if ( ! wc_od_time_frame_is_full( $timestamp, $time_frame ) ) {
					$available_time_frames->set( $index, $time_frame );
				}
			}

			return $available_time_frames;
		}
	}
}
