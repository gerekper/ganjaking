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
				'id'   => 'send_store_credit_section',
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

		include dirname( __FILE__ ) . '/views/html-admin-page-send-credit.php';
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

		$args = array();

		if ( ! empty( $data['customer_note'] ) ) {
			$args['description'] = $data['customer_note'];
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
	 * @param array $fields An array with the fields data.
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
