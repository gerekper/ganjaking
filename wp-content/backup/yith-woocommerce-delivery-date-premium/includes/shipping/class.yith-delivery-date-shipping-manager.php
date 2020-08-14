<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Shipping_Manager' ) ) {

	class YITH_Delivery_Date_Shipping_Manager {

		protected static $_instance;
		protected $shipping_method;

		public function __construct() {
			add_action( 'admin_init', array( $this, 'set_shipping_method' ), 99 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ), 25 );
			add_action( 'wp_ajax_update_datepicker', array( $this, 'update_datepicker' ) );
			add_action( 'wp_ajax_nopriv_update_datepicker', array( $this, 'update_datepicker' ) );
			add_action( 'wp_ajax_update_timeslot', array( $this, 'update_timeslot_ajax' ) );
			add_action( 'wp_ajax_nopriv_update_timeslot', array( $this, 'update_timeslot_ajax' ) );
			add_action( 'wp_ajax_update_carrier_list', array( $this, 'update_carrier_list_by_shipping_method' ) );
			add_action( 'wp_ajax_nopriv_update_carrier_list', array(
				$this,
				'update_carrier_list_by_shipping_method'
			) );

			add_action( 'woocommerce_after_checkout_validation', array(
				$this,
				'validate_checkout_width_delivery_date'
			), 10 );

			if ( version_compare( WC()->version, '2.7.0', '>=' ) ) {

				add_action( 'woocommerce_checkout_create_order', array( $this, 'add_delivery_date_info_order_meta' ) );
				add_action( 'woocommerce_checkout_shipping', array( $this, 'print_delivery_from' ), 20 );
			} else {
				add_action( 'woocommerce_checkout_update_order_meta', array(
					$this,
					'add_delivery_date_info_order_meta'
				), 10, 1 );
				add_action( 'woocommerce_after_order_notes', array( $this, 'print_delivery_from' ), 20 );
			}


			add_action( 'woocommerce_order_details_after_order_table', array(
				$this,
				'show_delivery_order_details_after_order_table'
			) );

			add_action( 'woocommerce_order_status_changed', array( $this, 'manage_order_event' ), 20, 3 );

			//add delivery date information into woocommerce email
			add_action( 'woocommerce_email_order_meta', array( $this, 'print_delivery_date_into_email' ), 10, 4 );


			$enable_gdpr_option = get_option( 'ywcdd_user_privacy', 'no' );

			if ( 'yes' == $enable_gdpr_option ) {

				add_action( 'woocommerce_checkout_terms_and_conditions', array( $this, 'show_checkbox' ), 15 );
				add_action( 'woocommerce_checkout_create_order', array( $this, 'register_customer_choose' ), 20 );
			}

		}

		/**
		 * @return YITH_Delivery_Date_Shipping_Manager
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * get the shipping method and add custom form fields filter
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function set_shipping_method() {

			WC()->shipping->load_shipping_methods();
			$this->shipping_method = wp_list_pluck( WC()->shipping()->get_shipping_methods(), 'id' );

			if ( ! empty( $this->shipping_method ) ) {

				foreach ( $this->shipping_method as $key => $shipping_id ) {

					if ( apply_filters( 'ywcdd_disable_delivery_date_for_shipping_method', false, $key, $shipping_id ) ) {
						continue;
					}
					add_filter( 'woocommerce_settings_api_form_fields_' . $shipping_id, array(
						$this,
						'add_custom_fields'
					), 99 );
					/**
					 * added compatibility with wc 2.6
					 */
					add_filter( 'woocommerce_shipping_instance_form_fields_' . $shipping_id, array(
						$this,
						'add_custom_fields'
					), 99 );

				}
			}
		}

		/**
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function enqueue_frontend_scripts() {

			if ( is_checkout() ) {
				wp_enqueue_script( 'ywcdd_frontend', YITH_DELIVERY_DATE_ASSETS_URL . 'js/' . yit_load_js_file( 'yith_deliverydate_checkout.js' ), array(
					'jquery',
					'jquery-ui-datepicker',
					'select2'
				), YITH_DELIVERY_DATE_VERSION, true );
				$params = array(
					'ajax_url'          => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'           => array(
						'update_datepicker'   => 'update_datepicker',
						'update_timeslot'     => 'update_timeslot',
						'update_carrier_list' => 'update_carrier_list'
					),
					'timeformat'        => 'H:i',
					'dateformat'        => get_option( 'yith_delivery_date_format', 'yy-mm-dd' ),
					'yearSuffix'        => apply_filters( 'ywcdd_change_year_suffix', '' ),
					'numberOfMonths'    => wp_is_mobile() ? 1 : 2,
					'open_datepicker'   => ywcdd_get_delivery_mode(),
					'show_the_min_date' => apply_filters( 'ywcdd_set_first_available_date', 'yes' )

				);

				wp_localize_script( 'ywcdd_frontend', 'ywcdd_params', $params );
				wp_enqueue_style( 'ywcdd_style', YITH_DELIVERY_DATE_ASSETS_URL . 'css/yith_delivery_date_frontend.css', array(), YITH_DELIVERY_DATE_VERSION );
			}
		}

		/**
		 * add custom form fields in shipping method
		 *
		 * @param array $defaults
		 * @param array $form_fields
		 *
		 * @return array
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function add_custom_fields( $form_fields ) {
			$all_processing_method = get_posts( array(
				'post_type'   => 'yith_proc_method',
				'post_status' => 'publish',
				'numberposts' => - 1
			) );

			$options = array();

			$options[''] = __( 'Select a processing method', 'yith-woocommerce-delivery-date' );

			foreach ( $all_processing_method as $key => $method ) {
				$options[ $method->ID ] = get_the_title( $method->ID );
			}


			$form_fields['select_process_method'] = array(
				'title'   => __( 'Processing Method', 'yith-woocommerce-delivery-date' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'ywcdd_processing_method wc-enhanced-select',
				'options' => $options,
			);

			$form_fields['set_method_as_mandatory'] = array(
				'title'       => __( 'Set as required', 'yith-woocommerce-delivery-date' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'class'       => 'ywcdd_set_mandatory',
				'description' => __( 'If enabled, customers must select a date for the delivery', 'yith-woocommerce-delivery-date' )
			);

			return $form_fields;
		}

		/**
		 * @param WC_Checkout $checkout
		 */
		public function print_delivery_from() {

			$chosen_methods  = WC()->session->get( 'chosen_shipping_methods' );
			$chosen_shipping = ! empty( $chosen_methods[0] ) ? $chosen_methods[0] : '';

			if ( apply_filters( 'ywcdd_load_always_delivery_date', true, $chosen_shipping ) ) {

				$this->load_delivery_template( $chosen_shipping );
			}


		}

		/**
		 * @param      $shipping_method
		 * @param bool $html
		 *
		 * @return false|string
		 */
		public function load_delivery_template( $shipping_method, $html = false ) {

			$shipping_settings = $this->get_woocommerce_shipping_option( $shipping_method );

			$processing_method = isset( $shipping_settings['select_process_method'] ) ? $shipping_settings['select_process_method'] : '';
			$is_mandatory      = isset( $shipping_settings['set_method_as_mandatory'] ) ? $shipping_settings['set_method_as_mandatory'] : 'no';


			if ( $html ) {
				return wc_get_template_html( 'woocommerce/checkout/delivery-date-content.php', array(
					'shipping_id'       => $shipping_method,
					'is_mandatory'      => $is_mandatory,
					'processing_method' => $processing_method
				), YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
			} else {
				wc_get_template( 'woocommerce/checkout/delivery-date-content.php', array(
					'shipping_id'       => $shipping_method,
					'is_mandatory'      => $is_mandatory,
					'processing_method' => $processing_method
				), YITH_DELIVERY_DATE_TEMPLATE_PATH, YITH_DELIVERY_DATE_TEMPLATE_PATH );
			}
		}

		/**
		 * @param string $shipping_option
		 *
		 * @return array
		 * @author Salvatore Strano
		 * get the woocommerce shipping options by shipping name
		 *
		 */
		public function get_woocommerce_shipping_option( $shipping_option ) {

			if( is_array( $shipping_option ) ){
				return array();
			}

			if ( version_compare( WC()->version, '2.6.0', '>=' ) ) {
				$shipping_option = str_replace( ':', '_', $shipping_option );
			}

			$shipping_settings = get_option( 'woocommerce_' . $shipping_option . '_settings' );

			return apply_filters( 'ywcdd_get_shipping_method_option', $shipping_settings, $shipping_option );
		}

		public function update_carrier_list_by_shipping_method() {

			$shipping_id = isset( $_POST['ywcdd_shipping_id'] ) ? $_POST['ywcdd_shipping_id'] : '';

			$shipping_settings = $this->get_woocommerce_shipping_option( $shipping_id );

			$processing_method = ! empty( $shipping_settings['select_process_method'] ) ? $shipping_settings['select_process_method'] : '';
			$template          = '';
			$change_template   = true;

			$current_proc_method = isset( $_POST['ywcdd_process_method'] ) ? $_POST['ywcdd_process_method'] : '';


			if ( $processing_method != '' && $change_template ) {

				$shipping_id = isset( $_POST['ywcdd_shipping_id'] ) ? $_POST['ywcdd_shipping_id'] : '';

				$template = $this->load_delivery_template( $shipping_id, true );
			}


			wp_send_json( array( 'template' => $template, 'update_delivery_form' => $change_template ) );

		}

		/**
		 * find the right min days for process an order
		 *
		 * @param int $base_day
		 * @param $process_shipping_method_id
		 *
		 * @return int
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function find_day_for_shipping( $base_day, $process_shipping_method_id ) {

			$new_base_days = array();

			/**
			 * @var WC_Cart
			 */
			$cart = WC()->cart;

			if ( isset( $cart ) && ! $cart->is_empty() ) {

				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					/**
					 * @var WC_Product $_product
					 */
					$_product = $values['data'];
					$quantity = $values['quantity'];

					$new_base_days[] = YITH_Delivery_Date_Product_Frontend()->get_custom_base_day_for_product( $_product, $quantity );

				}


			}

			$max_day = count( $new_base_days ) > 0 ? max( $new_base_days ) : 0;

			return max( $max_day, $base_day );
		}

		/**
		 * get all available dates for delivery
		 *
		 * @param int $processing_id
		 * @param int $carrier_id
		 *
		 * @return array
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function get_available_date_range( $processing_id, $carrier_id, $format = true ) {

			add_filter( 'ywcdd_get_processing_working_day', array( $this, 'find_day_for_shipping' ), 10, 2 );
			$shipping_date   = YITH_Delivery_Date_Manager()->get_first_shipping_date( $processing_id );
			$delivery_date   = YITH_Delivery_Date_Manager()->get_first_delivery_date( $carrier_id, array( 'shipping_date' => $shipping_date ) );
			$all_select_days = YITH_Delivery_Date_Manager()->get_all_delivery_dates( $carrier_id, array( 'from_date' => $delivery_date ) );

			remove_filter( 'ywcdd_get_processing_working_day', array( $this, 'find_day_for_shipping' ), 10 );

			return $all_select_days;
		}

		public function get_format_available_date_range( $date_range ) {

			$available_days = array();


			foreach ( $date_range as $date ) {
				$available_days[] = $date;
			}

			return $available_days;
		}

		/**
		 * set all available delivery date
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function update_datepicker() {

			if ( isset( $_POST['ywcdd_carrier_id'] ) ) {

				$carrier_id = $_POST['ywcdd_carrier_id'];
				$process_id = $_POST['ywcdd_process_id'];

				$all_select_days = $this->get_available_date_range( $process_id, $carrier_id );

				$timeslot_result = array();
				$text            = '';

				if ( count( $all_select_days ) > 0 ) {

					$format_min_date = $all_select_days[0];
					$text            = sprintf( '%s <strong>%s</strong> <a href="" class="ywcdd_edit_date">%s</a>', __( 'Your order will be delivered on', 'yith-woocommerce-delivery-date' ), $format_min_date, __( 'Edit date', 'yith-woocommerce-delivery-date' ) );
					$timeslot_result = $this->update_timeslot( $carrier_id, $all_select_days[0] );
					$result          = array_merge(
						array( 'available_days' => $all_select_days, 'message' => $text ),
						$timeslot_result );


				}

				wp_send_json( $result );

			}
		}

		/**
		 * @author YITHEMES
		 * get all available timeslot for specific date and carrier
		 */
		public function update_timeslot( $carrier_id, $date_selected ) {

			$available_timeslot = YITH_Delivery_Date_Manager()->get_available_time_slots( $carrier_id, $date_selected );

			$json_slot = $this->format_timeslot( $available_timeslot );

			return array( 'available_timeslot' => $json_slot );
		}


		public function update_timeslot_ajax() {

			if ( isset( $_POST['ywcdd_carrier_id'] ) ) {

				$carrier_id    = $_POST['ywcdd_carrier_id'];
				$date_selected = $_POST['ywcdd_date_selected'];
				$results       = $this->update_timeslot( $carrier_id, $date_selected );

				wp_send_json( $results );
			}
		}


		/**
		 * @param array $available_timeslot
		 *
		 * @return array
		 */
		public function format_timeslot( $available_timeslot ) {

			$format_slot = array();

			if ( ! empty( $available_timeslot ) ) {
				foreach ( $available_timeslot as $slot_id => $slot ) {

					$timefrom       = ywcdd_display_timeslot( strtotime( $slot['timefrom'] ) );
					$timeto         = ywcdd_display_timeslot( strtotime( $slot['timeto'] ) );
					$fee            = $slot['fee'];
					$fee_name       = ! empty( $slot['fee_name'] ) ? $slot['fee_name'] : __( 'Fee', 'yith-woocommerce-delivery-date' );
					$timeslotformat = sprintf( '%s: %s - %s: %s', _x( 'From', 'from time', 'yith-woocommerce-delivery-date' ), $timefrom, _x( 'To', 'to time', 'yith-woocommerce-delivery-date' ), $timeto );
					$is_taxable     = false;
					if ( $fee != '' ) {

						$suffix = '';

						$is_taxable = 'yes' == get_option( 'ywcdd_fee_is_taxable', 'no' );
						$is_taxable = apply_filters( 'ywcdd_time_slot_fee_taxable', $is_taxable );
						if ( $is_taxable ) {
							$suffix         = WC()->countries->ex_tax_or_vat();
							$timeslotformat .= sprintf( ' - %s %s ', $fee_name, wc_price( $fee ) . ' ' . $suffix );

						} else {
							$timeslotformat .= sprintf( ' ( %s %s )', $fee_name, wc_price( $fee ) . $suffix );
						}
					}

					$timeslotformat          = apply_filters( 'yith_delivery_date_time_slot_format', $timeslotformat, $timefrom, $timeto, $is_taxable, $fee, $fee_name, $slot );
					$format_slot[ $slot_id ] = $timeslotformat;
				}
			}

			return $format_slot;
		}

		/**
		 * validate checkout with delivery information
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 */
		public function validate_checkout_width_delivery_date() {

			if ( isset( $_POST['ywcdd_carrier'] ) && isset( $_POST['ywcdd_datepicker'] ) ) {

				$carrier_id         = $_POST['ywcdd_carrier'];
				$date_selected      = $_POST['ywcdd_delivery_date'];
				$is_mandatory       = $_POST['ywcdd_is_mandatory'];
				$time_slot_av       = $_POST['ywcdd_timeslot_av'];
				$time_slot_selected = !empty( $_POST['ywcdd_timeslot'] ) ? $_POST['ywcdd_timeslot'] : '' ;

				if( !empty( $date_selected ) ) {
					$now           = strtotime( date('Y-m-d',current_time( 'timestamp' ) ) );
					$date_selected_time = strtotime( $date_selected );

					if ( $date_selected_time < $now ) {
						wc_add_notice( __( 'An error occurred during the checkout,  please try again', 'yith-woocommerce-delivery-date' ), 'error' );
					}
				}

				if ( 'yes' == $is_mandatory ) {

					if ( - 1 != $carrier_id && '' === $carrier_id ) {

						$error = sprintf( '<strong>%s</strong> %s', __( 'Carrier', 'yith-woocommerce-delivery-date' ), __( 'is a required field.', 'yith-woocommerce-delivery-date' ) );
						wc_add_notice( $error, 'error' );
					}
					if ( is_numeric( $carrier_id ) && '' === $date_selected ) {
						$error = sprintf( '<strong>%s</strong> %s', __( 'Delivery Date', 'yith-woocommerce-delivery-date' ), __( 'is a required field.', 'yith-woocommerce-delivery-date' ) );
						wc_add_notice( $error, 'error' );

					}

					if ( 'yes' == $time_slot_av && $time_slot_selected == '' ) {
						$error = sprintf( '<strong>%s</strong> %s', __( 'Time Slot', 'yith-woocommerce-delivery-date' ), __( 'is a required field.', 'yith-woocommerce-delivery-date' ) );
						wc_add_notice( $error, 'error' );
					}

					if( !empty( $time_slot_selected ) ) {
						$slot_selected = YITH_Delivery_Date_Carrier()->get_time_slot_by_id( $carrier_id, $time_slot_selected );
						$is_lock = YITH_Delivery_Date_Manager()->check_if_time_slot_is_lockout( $slot_selected, $carrier_id, strtotime( $date_selected ) );

						if( $is_lock ){

							$error = sprintf(__('<strong>Time slot:</strong> the time slot select is no longer available, choose another slot', 'yith-woocommerce-delivery-date' ) );
							wc_add_notice( $error, 'error');
						}
					}
				}
			} else {
				if( !is_null(WC()->cart) && WC()->cart->needs_shipping() ) {
					$shipping_method = isset( $_POST['shipping_method'] ) ? current( $_POST['shipping_method'] ) : array();

					$option = $this->get_woocommerce_shipping_option( $shipping_method );

					if ( is_array( $shipping_method ) || ! empty( $option['select_process_method'] ) && apply_filters( 'ywcdd_checkout_validation', true ) ) {

						wc_add_notice( __( 'An error occurred during the checkout,  please try again', 'yith-woocommerce-delivery-date' ), 'error' );
					}
				}
			}
		}

		/**
		 * @param $order_id
		 */
		public function add_delivery_date_info_order_meta( $order ) {


			if ( isset( $_POST['ywcdd_datepicker'] ) ) {

				if ( ! $order instanceof WC_Order ) {

					$order = wc_get_order( $order );
				}

				$carrier_id    = isset( $_POST['ywcdd_carrier'] ) ? $_POST['ywcdd_carrier'] : - 1;
				$delivery_date = isset( $_POST['ywcdd_delivery_date'] ) ? $_POST['ywcdd_delivery_date'] : '';
				$slot_id       = isset( $_POST['ywcdd_timeslot'] ) ? $_POST['ywcdd_timeslot'] : '';
				$proc_method   = isset( $_POST['ywcdd_process_method'] ) ? $_POST['ywcdd_process_method'] : - 1;
				$time_from     = '';
				$time_to       = '';

				$last_shipping_date = YITH_Delivery_Date_Manager()->get_last_shipping_date( $delivery_date, $proc_method, $carrier_id );
				$last_shipping_date = date( 'Y-m-d', $last_shipping_date );

				$timeslot = YITH_Delivery_Date_Carrier()->get_time_slot_by_id( $carrier_id, $slot_id );

				if ( $timeslot ) {

					$time_from = date( 'H:i', strtotime( $timeslot['timefrom'] ) );
					$time_to   = date( 'H:i', strtotime( $timeslot['timeto'] ) );

				}

				$delivery_date = strtotime( $delivery_date );

				$delivery_date = date( 'Y-m-d', $delivery_date );

				$order_meta = array(
					'ywcdd_order_delivery_date'     => $delivery_date,
					'ywcdd_order_shipping_date'     => $last_shipping_date,
					'ywcdd_order_slot_from'         => $time_from,
					'ywcdd_order_slot_to'           => $time_to,
					'ywcdd_order_carrier_id'        => $carrier_id,
					'ywcdd_order_processing_method' => $proc_method,
					'ywcdd_order_carrier'           => get_the_title( $carrier_id )

				);

				foreach ( $order_meta as $key => $meta ) {

					$order->update_meta_data( $key, $meta );
				}
				if ( 'woocommerce_checkout_update_order_meta' == current_filter() ) {

					$order->save();
				}
			}
		}

		/**
		 * @param WC_Order $order
		 *
		 * @throws
		 */
		public function show_delivery_order_details( $order, $show_shipping = false ) {


			$carrier_label = $order->get_meta( 'ywcdd_order_carrier' );
			$shipping_date = $order->get_meta( 'ywcdd_order_shipping_date' );
			$delivery_date = $order->get_meta( 'ywcdd_order_delivery_date' );
			$time_from     = $order->get_meta( 'ywcdd_order_slot_from' );
			$time_to       = $order->get_meta( 'ywcdd_order_slot_to' );
			$carrier_id    = $order->get_meta( 'ywcdd_order_carrier_id' );
			$processing_id = $order->get_meta( 'ywcdd_order_processing_method' );

			if ( ! empty( $delivery_date ) ) {
				$delivery_date = wc_format_datetime( new WC_DateTime( $delivery_date, new DateTimeZone( 'UTC' ) ) );
				$shipping_date = wc_format_datetime( new WC_DateTime( $shipping_date, new DateTimeZone( 'UTC' ) ) );
				echo sprintf( '<h2>%s</h2>', apply_filters('ywcdd_delivery_section_title', __( 'Delivery Details', 'yith-woocommerce-delivery-date' ), $carrier_id, $processing_id ) );

				$fields = array(
					'carrier'       => array(
						'label' => apply_filters( 'ywcdd_change_carrier_label', __( 'Carrier', 'yith-woocommerce-delivery-date' ) ),
						'value' => $carrier_label
					),
					'shipping_date' => array(
						'label' => apply_filters( 'ywcdd_change_shipping_date_label', _x( 'Shipping Date', '[Part of]: Shipping Date within 20th March 2019', 'yith-woocommerce-delivery-date' ) ),
						'value' => sprintf( '%s %s', _x( 'within', '[Part of]: Shipping Date within 20th March 2019', 'yith-woocommerce-delivery-date' ), $shipping_date ),
					),
					'delivery_date' => array(
						'label' => apply_filters( 'ywcdd_change_delivery_date_label', __( 'Delivery Date', 'yith-woocommerce-delivery-date' ) ),
						'value' => $delivery_date,
					),
					'timeslot'      => array(
						'label' => apply_filters( 'ywcdd_change_timeslot_label', __( 'Time Slot', 'yith-woocommerce-delivery-date' ) ),
						'value' => ( empty( $time_from ) || empty( $time_to ) ) ? '' : sprintf( '%s - %s', ywcdd_display_timeslot( $time_from ), ywcdd_display_timeslot( $time_to ) )
					)
				);

				$fields = apply_filters( 'yith_delivery_date_email_fields', $fields, $show_shipping, $carrier_id,$processing_id );

				echo '<ul class="order_details bacs_details">' . PHP_EOL;
				foreach ( $fields as $field_key => $field ) {
					if ( ! empty( $field['value'] ) ) {

						if ( $field_key != 'shipping_date' ) {
							echo '<li class="' . esc_attr( $field_key ) . '">' . esc_attr( $field['label'] ) . ': <strong>' . wptexturize( $field['value'] ) . '</strong></li>' . PHP_EOL;
						} elseif ( $field_key == 'shipping_date' && apply_filters( 'ywcdd_show_date_shipping_details', $show_shipping ) ) {
							echo '<li class="' . esc_attr( $field_key ) . '">' . esc_attr( $field['label'] ) . ': <strong>' . wptexturize( $field['value'] ) . '</strong></li>' . PHP_EOL;
						}
					}
				}
				echo '</ul>' . PHP_EOL;
			}

		}

		/**
		 * add delivery details in review order
		 *
		 * @param WC_Order $order
		 *
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function show_delivery_order_details_after_order_table( $order ) {


			$this->show_delivery_order_details( $order );
		}

		/**
		 * add delivery details in woocommerce email
		 *
		 * @param $order
		 * @param $sent_to_admin
		 * @param $plain_text
		 * @param $email
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 *
		 */
		public function print_delivery_date_into_email( $order, $sent_to_admin, $plain_text = false, $email = false ) {

			$this->show_delivery_order_details( $order, $sent_to_admin );
		}


		/**
		 * add/remove even to calendar
		 *
		 * @param $order_id
		 * @param $old_status
		 * @param $new_status
		 *
		 * @since  1.0.0
		 *
		 * @author YITHEMES
		 */
		public function manage_order_event( $order_id, $old_status, $new_status ) {

			$order         = wc_get_order( $order_id );
			$delivery_date = $order->get_meta( 'ywcdd_order_delivery_date' );
			$shipping_date = $order->get_meta( 'ywcdd_order_shipping_date' );
			$carrier_id    = $order->get_meta( 'ywcdd_order_carrier_id' );
			$proc_id       = $order->get_meta( 'ywcdd_order_processing_method' );
			$has_child     = apply_filters( 'yith_delivery_date_order_has_child', false, $order_id );

			if ( ! empty( $delivery_date ) && ! $has_child ) {

				$add_event_order_status = get_option( 'ywcdd_add_event_into_calendar' );

				if ( in_array( $new_status, $add_event_order_status ) ) {
					/**add new shipping event and add new delivery event into calendar*/
					YITH_Delivery_Date_Calendar()->add_calendar_event( $proc_id, '', 'shipping_to_carrier', $shipping_date, '', $order_id );

					YITH_Delivery_Date_Calendar()->add_calendar_event( $carrier_id, '', 'delivery_day', $delivery_date, $delivery_date, $order_id );
				} else {

					YITH_Delivery_Date_Calendar()->delete_event_by_order_id( $order_id );

				}
			}
		}

		/**
		 * @author Salvatore Strano
		 */
		public function show_checkbox() {

			wc_get_template( 'send-mail-checkbox.php', array(), '', YITH_DELIVERY_DATE_TEMPLATE_PATH . 'woocommerce/checkout/' );
		}

		/**
		 * @param WC_Order $order
		 */
		public function register_customer_choose( $order ) {

			if ( ! isset( $_REQUEST['ywcdd_send_email'] ) ) {

				$order->update_meta_data( '_ywcdd_not_send', 'yes' );
			}
		}


	}
}
if ( ! function_exists( 'YITH_Delivery_Date_Shipping_Manager' ) ) {

	function YITH_Delivery_Date_Shipping_Manager() {
		$option = get_option( 'ywcdd_processing_type', 'checkout' );
		if ( 'checkout' == $option ) {
			return YITH_Delivery_Date_Shipping_Manager::get_instance();
		}
	}
}

YITH_Delivery_Date_Shipping_Manager();
