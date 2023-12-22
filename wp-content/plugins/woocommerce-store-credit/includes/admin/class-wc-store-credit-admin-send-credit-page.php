<?php
/**
 * The Send Store Credit admin page.
 *
 * @package WC_Store_Credit/Admin
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin_Send_Credit_Page class.
 */
class WC_Store_Credit_Admin_Send_Credit_Page {

	/**
	 * Error messages.
	 *
	 * @var array
	 */
	private static $errors = array();

	/**
	 * Update messages.
	 *
	 * @var array
	 */
	private static $messages = array();

	/**
	 * Initializes the page.
	 *
	 * @since 3.0.0
	 */
	public static function init() {
		if (
			! empty( $_POST['save'] ) && ! empty( $_POST['_wpnonce'] ) &&
			wp_verify_nonce( wc_clean( wp_unslash( $_POST['_wpnonce'] ) ), 'wc_send_store_credit' )
		) {
			self::save();
		}
	}

	/**
	 * Adds a message.
	 *
	 * @since 3.0.0
	 *
	 * @param string $text Message.
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}

	/**
	 * Adds an error.
	 *
	 * @since 3.0.0
	 *
	 * @param string $text Message.
	 */
	public static function add_error( $text ) {
		self::$errors[] = $text;
	}

	/**
	 * Outputs messages + errors.
	 *
	 * @since 3.0.0
	 */
	public static function show_messages() {
		if ( count( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error ) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		} elseif ( count( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message ) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}

	/**
	 * Gets the form fields.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public static function get_form_fields() {
		$product_categories_options = wc_store_credit_get_product_categories_choices( true );

		$fields = array(
			array(
				'id'   => 'send_store_credit_section',
				'type' => 'title',
				'desc' => _x( 'Send a Store credit coupon to a customer.', 'send credit: desc', 'woocommerce-store-credit' ),
			),
			array(
				'id'          => 'credit_amount',
				'title'       => _x( 'Credit amount', 'send credit: field label', 'woocommerce-store-credit' ),
				'desc'        => _x( 'The amount the store credit coupon is worth.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'class'       => 'wc_input_price',
				'default'     => '',
				'placeholder' => wp_strip_all_tags( wc_price( '10', array( 'price_format' => '%2$s' ) ) ), // Remove currency from format.
			),
			array(
				'id'                => 'customer_id',
				'title'             => _x( 'Customer', 'send credit: field label', 'woocommerce-store-credit' ),
				'desc_tip'          => _x( 'The customer who will receive the coupon.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'type'              => 'select',
				'desc'              => _x( 'Accepts emails from non-registered customers.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'class'             => 'wc-customer-search',
				'options'           => array(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Choose customer&hellip;', 'woocommerce-store-credit' ),
					'data-tags'        => true, // Allow guest users.
				),
			),
			array(
				'id'                => 'customer_note',
				'title'             => _x( 'Note', 'send credit: field label', 'woocommerce-store-credit' ),
				'type'              => 'textarea',
				'desc_tip'          => true,
				'desc'              => _x( 'A note for the customer who will receive the coupon.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'placeholder'       => _x( 'Enter a note or the reason for this coupon.', 'send credit: field placeholder', 'woocommerce-store-credit' ),
				'custom_attributes' => array(
					'rows' => 5,
				),
			),
			array(
				'id'       => 'expiration',
				'title'    => _x( 'Expiration', 'send credit: desc', 'woocommerce-store-credit' ),
				'type'     => 'select',
				'desc_tip' => _x( 'Define when the coupon expires.', 'send credit: desc', 'woocommerce-store-credit' ),
				'value'    => 'never',
				'options'  => array(
					'never'  => __( 'Never expires', 'woocommerce-store-credit' ),
					'date'   => __( 'Specific date', 'woocommerce-store-credit' ),
					'period' => __( 'Specific period', 'woocommerce-store-credit' ),
				),
			),
			array(
				'id'                => 'expiration_date',
				'desc_tip'          => _x( 'The coupon will expire on the specified date.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'type'              => 'text',
				'class'             => 'date-picker',
				'css'               => 'width:150px;',
				'placeholder'       => gmdate( 'Y-m-d' ),
				'custom_attributes' => array(
					'pattern'      => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
					'maxlength'    => 10,
					'data-minDate' => gmdate( 'Y-m-d' ),
				),
			),
			array(
				'id'       => 'expiration_period',
				'desc_tip' => _x( 'The coupon will expire when the period passes.', 'send credit: field desc', 'woocommerce-store-credit' ),
				'type'     => 'relative_date_selector',
				'options'  => array(),
			),
			array(
				'id'   => 'send_store_credit_section',
				'type' => 'sectionend',
			),
			array(
				'id'    => 'send_store_credit_restrictions_section',
				'type'  => 'title',
				'title' => _x( 'Usage restriction', 'coupon: section title', 'woocommerce-store-credit' ),
			),
			array(
				'id'    => 'individual_use',
				'title' => _x( 'Individual use only', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc'  => _x( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'  => 'checkbox',
				'value' => get_option( 'wc_store_credit_individual_use', 'no' ),
			),
			array(
				'id'    => 'exclude_sale_items',
				'title' => _x( 'Exclude sale items', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc'  => _x( 'Check this box if the coupon should not apply to items on sale.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'  => 'checkbox',
				'value' => 'no',
			),
			array(
				'id'                => 'product_ids',
				'title'             => _x( 'Products', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc_tip'          => _x( 'Product that the coupon will be applied to, or that need to be in the cart in order to be applied.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'              => 'multiselect',
				'class'             => 'wc-product-search',
				'options'           => array(),
				'custom_attributes' => array(
					'multiple'         => true,
					'data-placeholder' => __( 'Search for a product&hellip;', 'woocommerce-store-credit' ),
				),
			),
			array(
				'id'                => 'excluded_product_ids',
				'title'             => _x( 'Exclude products', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc_tip'          => _x( 'Product that the coupon will not be applied to, or that cannot be in the cart in order to be applied.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'              => 'multiselect',
				'class'             => 'wc-product-search',
				'options'           => array(),
				'custom_attributes' => array(
					'multiple'         => true,
					'data-placeholder' => __( 'Search for a product&hellip;', 'woocommerce-store-credit' ),
				),
			),
			array(
				'id'                => 'product_categories',
				'title'             => _x( 'Product categories', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc_tip'          => _x( 'Product categories that the coupon will be applied to, or that need to be in the cart in order to be applied.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'options'           => $product_categories_options,
				'custom_attributes' => array(
					'data-placeholder' => _x( 'Select product categories', 'setting placeholder', 'woocommerce-store-credit' ),
				),
			),
			array(
				'id'                => 'excluded_product_categories',
				'title'             => _x( 'Exclude categories', 'coupon: field label', 'woocommerce-store-credit' ),
				'desc_tip'          => _x( 'Product categories that the coupon will not be applied to, or that cannot be in the cart in order to be applied.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'options'           => $product_categories_options,
				'custom_attributes' => array(
					'data-placeholder' => _x( 'Select product categories', 'setting placeholder', 'woocommerce-store-credit' ),
				),
			),
			array(
				'id'   => 'send_store_credit_restrictions_section',
				'type' => 'sectionend',
			),
		);

		/**
		 * Filters the 'Send Store Credit' form fields.
		 *
		 * @since 3.0.0
		 *
		 * @param array $fields The form fields.
		 */
		return apply_filters( 'wc_store_credit_send_credit_form_fields', $fields );
	}

	/**
	 * Gets the form field value.
	 *
	 * @since 3.1.0
	 *
	 * @param array $field The field data.
	 * @return mixed
	 */
	public static function get_form_field_value( $field ) {
		return ( isset( $_POST[ $field['id'] ] ) ? wc_clean( wp_unslash( $_POST[ $field['id'] ] ) ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	/**
	 * Outputs the page content.
	 *
	 * @since 3.0.0
	 */
	public static function output() {
		$fields = self::get_form_fields();

		// Populate the form fields' values if the form contains errors.
		if ( ! empty( self::$errors ) ) {
			$fields = self::populate_form_fields_values( $fields );
		}

		include __DIR__ . '/views/html-admin-page-send-credit.php';
	}

	/**
	 * Save the page form.
	 *
	 * @since 3.0.0
	 */
	public static function save() {
		$data = self::get_sanitized_data();

		$amount = wc_format_decimal( $data['credit_amount'], false, true );

		if ( empty( $amount ) ) {
			self::add_error( _x( 'You need to provide a credit amount for the coupon.', 'form validation error', 'woocommerce-store-credit' ) );
			return;
		}

		if ( empty( $data['customer_id'] ) ) {
			self::add_error( _x( 'You need to choose a customer who to send the coupon.', 'form validation error', 'woocommerce-store-credit' ) );
			return;
		}

		$customer = ( is_email( $data['customer_id'] ) ? $data['customer_id'] : wc_store_credit_get_customer( $data['customer_id'] ) );

		if ( ! $customer ) {
			self::add_error( _x( 'Customer not found.', 'form validation error', 'woocommerce-store-credit' ) );
			return;
		}

		if ( 'date' === $data['expiration'] && ! $data['expiration_date'] ) {
			self::add_error( _x( 'An expiration date is required.', 'form validation error', 'woocommerce-store-credit' ) );
			return;
		}

		if ( 'period' === $data['expiration'] && ! $data['expiration_period']['number'] ) {
			self::add_error( _x( 'An expiration period is required.', 'form validation error', 'woocommerce-store-credit' ) );
			return;
		}

		$args = array();

		if ( ! empty( $data['customer_note'] ) ) {
			$args['description'] = $data['customer_note'];
		}

		if ( 'never' !== $data['expiration'] ) {
			$args['expiration'] = $data[ "expiration_{$data['expiration']}" ];
		}

		$bool_props = array( 'individual_use', 'exclude_sale_items' );

		foreach ( $bool_props as $bool_prop ) {
			$args[ $bool_prop ] = wc_string_to_bool( isset( $data[ $bool_prop ] ) && $data[ $bool_prop ] );
		}

		$keys = array( 'product_ids', 'excluded_product_ids', 'product_categories', 'excluded_product_categories' );

		foreach ( $keys as $key ) {
			if ( ! empty( $data[ $key ] ) ) {
				$args[ $key ] = $data[ $key ];
			}
		}

		if ( wc_store_credit_send_credit_to_customer( $customer, $amount, $args ) ) {
			self::add_message( __( 'Store credit sent to the customer.', 'woocommerce-store-credit' ) );
		} else {
			self::add_error( _x( 'An unexpected error happened.', 'form validation error', 'woocommerce-store-credit' ) );
		}
	}

	/**
	 * Gets if the field needs to be validated.
	 *
	 * @since 3.1.0
	 *
	 * @param array $field The field data.
	 * @return bool
	 */
	protected static function needs_validation( $field ) {
		return (
			! empty( $field['id'] ) &&
			! empty( $field['type'] ) &&
			! in_array( $field['type'], array( 'title', 'sectionend' ), true )
		);
	}

	/**
	 * Populates the form fields' values.
	 *
	 * @since 3.1.0
	 *
	 * @param array $fields An array with the fields' data.
	 * @return array
	 */
	protected static function populate_form_fields_values( $fields ) {
		foreach ( $fields as $key => $field ) {
			if ( ! self::needs_validation( $field ) ) {
				continue;
			}

			$value = self::get_form_field_value( $field );

			if ( 'customer_id' === $field['id'] ) {
				$label = ( is_numeric( $value ) ? wc_store_credit_get_customer_choice_label( intval( $value ) ) : $value );

				$field['options'] = array( $value => $label );
			} elseif ( 'checkbox' === $field['type'] ) {
				$field['value'] = wc_bool_to_string( $value );
			} elseif ( 'product_ids' === $field['id'] || 'excluded_product_ids' === $field['id'] ) {
				$product_ids = array_filter( (array) $value );

				$field['options'] = array_combine( $product_ids, array_map( 'wc_store_credit_get_product_choice_label', $product_ids ) );
				$field['value']   = $value;
			} else {
				$field['value'] = $value;
			}

			$fields[ $key ] = $field;
		}

		return $fields;
	}

	/**
	 * Sanitizes the posted data.
	 *
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected static function get_sanitized_data() {
		$data   = array();
		$fields = self::get_form_fields();

		foreach ( $fields as $field ) {
			if ( ! self::needs_validation( $field ) ) {
				continue;
			}

			$value = self::get_form_field_value( $field );

			switch ( $field['id'] ) {
				case 'credit_amount':
					$value = wc_format_decimal( $value );
					break;
				case 'customer_id':
					$value = ( is_email( $value ) ? sanitize_email( $value ) : intval( $value ) );
					break;
				case 'customer_note':
					$value = sanitize_textarea_field( $value );
					break;
				case 'expiration_period':
					$value = wc_parse_relative_date_option( $value );
					break;
				default:
					$value = wc_clean( $value );
					break;
			}

			$data[ $field['id'] ] = $value;
		}

		/**
		 * Filters the posted data in the 'Send Store Credit' form.
		 *
		 * @since 3.0.0
		 *
		 * @param array $data   The posted data.
		 * @param array $fields The form fields.
		 */
		return apply_filters( 'wc_store_credit_send_credit_form_data', $data, $fields );
	}
}
