<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Box_Office_Ticket_Form {
	/**
	 * Ticket-enabled product.
	 *
	 * @var WC_Product
	 */
	public $product;

	/**
	 * Ticket fields which is set from product.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Prefix for field's name.
	 *
	 * @var string
	 */
	public $field_name_prefix = 'ticket_fields';

	/**
	 * Posted data.
	 *
	 * @var array
	 */
	private $_posted_data;

	/**
	 * Ticket fields data after validated.
	 *
	 * @var array
	 */
	private $_clean_data = array();

	/**
	 * Flag to indicate whether validation has been performed.
	 *
	 * @var bool
	 */
	private $_validated = false;

	/**
	 * Constructor.
	 *
	 * @param $product WC_Product
	 */
	public function __construct( WC_Product $product, $data = null ) {
		$this->product = $product;

		$fields = get_post_meta( $product->get_id(), '_ticket_fields', true );
		if ( ! empty( $fields ) ) {
			$this->fields = $fields;
		}

		$this->_posted_data = $data;

		if ( ! empty( $this->fields ) && ! empty( $this->_posted_data ) ) {
			foreach ( $this->fields as $key => $val ) {
				// First assume this is ticket form as found on single-product page.
				// Otherwise it's edit-ticket page.
				if ( is_numeric( $key ) && is_array( $val ) ) {
					foreach ( $val as $_key => $_val ) {
						if ( isset( $this->_posted_data[ $key ][ $_key ] ) ) {
							$this->fields[ $key ][ $_key ]['value'] = $this->_posted_data[ $key ][ $_key ];
						}
					}
				} else {
					$this->fields[ $key ]['value'] = isset( $this->_posted_data[ $key ] ) ? $this->_posted_data[ $key ] : '';
				}
			}
		}
	}

	public function render( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'field_name_prefix' => $this->field_name_prefix,
				'multiple_tickets'  => false,
			)
		);

		$this->field_name_prefix = $args['field_name_prefix'];

		if ( $args['multiple_tickets'] ) {
			$this->_multiple_tickets_js();
		}

		$customer = $this->_get_customer_data();

		foreach ( $this->fields as $field_key => $field ) {
			if ( is_string( $field['options'] ) && ! empty( $field['options'] ) ) {
				$field['options'] = array_map( 'trim', explode( ',', $field['options'] ) );
			} else {
				$field['options'] = array();
			}

			if ( $field['autofill'] !== 'none' && ! isset( $field['value'] ) && isset( $customer[ $field['autofill'] ] ) ) {
				$field['value'] = $customer[ $field['autofill'] ];
			}

			switch ( $field['type'] ) {
				case 'text':
				case 'first_name':
				case 'last_name':
				case 'email':
				case 'url':
				case 'twitter':
					$vars = $this->_input_field_to_template_vars( $field_key, $field );
					$this->_load_field_template( 'input', $vars );
					break;

				case 'select':
					$vars = $this->_option_field_to_template_vars( $field_key, $field );
					$this->_load_field_template( 'select', $vars );
					break;

				case 'radio':
					$vars = $this->_option_field_to_template_vars( $field_key, $field );
					$this->_load_field_template( 'radio', $vars );
					break;

				case 'checkbox':
					$vars = $this->_option_field_to_template_vars( $field_key, $field );
					$this->_load_field_template( 'checkbox', $vars );
					break;
			}
		}
	}

	/**
	 * Retrieve customer data to populate the fields in ticket form.
	 *
	 * Customer can be logged-in user viewing the single product ticket or
	 * selected customer when creating ticket via admin page.
	 *
	 * @since 1.1.2
	 *
	 * @return array Customer data.
	 */
	private function _get_customer_data() {
		$customer = array(
			'billing_first_name' => '',
			'billing_last_name'  => '',
			'billing_company'    => '',
			'billing_address_1'  => '',
			'billing_address_2'  => '',
			'billing_city'       => '',
			'billing_postcode'   => '',
			'billing_country'    => '',
			'billing_state'      => '',
			'billing_phone'      => '',
			'billing_email'      => '',
		);

		$customer_id = null;
		if ( is_singular() && is_user_logged_in() ) {
			$customer_id  = get_current_user_id();
		} elseif ( is_admin() && is_callable( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( 'event_ticket_page_create_ticket' === $screen->id && ! empty( $_POST['customer_id'] ) ) {
				$customer_id = absint( $_POST['customer_id']  );
			}
		}

		if ( $customer_id ) {
			foreach ( $customer as $key => $value ) {
				$customer[ $key ] = get_user_meta( $customer_id, $key, true );
			}
		}

		return array_filter( $customer );
	}

	private function _multiple_tickets_js() {
		$suffix = WCBO()->script_suffix;
		wp_enqueue_script(
			'wc-box-office-multiple-tickets',
			WCBO()->assets_url . 'js/multiple-tickets' . $suffix . '.js',
			array( 'backbone' ),
			WCBO()->_version,
			true
		);

		$ticket_title_prefix = get_option( 'box_office_ticket_title_prefix', '' );
		if ( empty( $ticket_title_prefix ) ) {
			$ticket_title_prefix = __( 'Ticket #', 'woocommerce-box-office' );
		}

		$add_to_cart_singular = get_option( 'box_office_add_to_cart_singular', '' );
		if ( empty( $add_to_cart_singular ) ) {
			$add_to_cart_singular = __( 'Buy Ticket Now', 'woocommerce-box-office' );
		}

		$add_to_cart_plural = get_option( 'box_office_add_to_cart_plural', '' );
		if ( empty( $add_to_cart_plural ) ) {
			$add_to_cart_plural = __( 'Buy Tickets Now', 'woocommerce-box-office' );
		}

		$params = apply_filters( 'woocommerce_box_office_ticket_form_params', array(
			'ajax_url'                  => WC()->ajax_url(),
			'field_name_prefix'         => $this->field_name_prefix,
			'posted_data'               => $this->_posted_data,
			'i18n_ticket_title_prefix'  => $ticket_title_prefix,
			'i18n_fields_required'      => __( 'Fields are required', 'woocommerce-box-office' ),
			'i18n_add_to_cart_singular' => $add_to_cart_singular,
			'i18n_add_to_cart_plural'   => $add_to_cart_plural,
			'is_admin'                  => is_admin(),
		) );

		wp_localize_script( 'wc-box-office-multiple-tickets', 'ticketFormParams', $params );
	}

	/**
	 * Return input-based template vars from given field key and properties.
	 *
	 * @param string $field_key Field's key
	 * @param array  $field     Field properties
	 *
	 * @return array Template vars
	 */
	private function _input_field_to_template_vars( $field_key, $field ) {

		return apply_filters( 'wocommerce_box_office_input_field_template_vars', array(
			'before_field' => '<p class="form-row">',
			'after_field'  => '</p>',
			'id'           => 'field_' . $field_key,
			'required'     => ( 'yes' === $field['required'] ),
			'disabled'     => false,
			'value'        => isset( $field['value'] ) ? $field['value'] : '',
			'input_class'  => 'input-text ticket-field-input',
			'label'        => $field['label'],
			'label_class'  => ( 'yes' === $field['required'] ) ? 'required-field' : '',
			'name'         => sprintf( '%s[%s]', $this->field_name_prefix, $field_key ),
			'type'         => 'text',
		) );
	}

	/**
	 * Return option-based template vars from given field key and properties.
	 *
	 * @param string $field_key Field's key
	 * @param array  $field     Field properties
	 *
	 * @return array Template vars
	 */
	private function _option_field_to_template_vars( $field_key, $field ) {
		$input_class = 'ticket-field-input';
		switch ( $field['type'] ) {
			case 'radio':
				$input_class .= ' input-radio';
				break;
			case 'checkbox':
				$input_class .= ' input-checbox';
				break;
		}

		return apply_filters( 'wocommerce_box_office_option_field_template_vars', array(
			'before_field' => '<p class="form-row">',
			'after_field'  => '</p>',
			'id'           => 'field_' . $field_key,
			'required'     => ( 'yes' === $field['required'] ),
			'disabled'     => false,
			'value'        => isset( $field['value'] ) ? $field['value'] : '',
			'input_class'  => $input_class,
			'label'        => $field['label'],
			'label_class'  => ( 'yes' === $field['required'] ) ? 'required-field' : '',
			'name'         => sprintf( '%s[%s]', $this->field_name_prefix, $field_key ),
			'options'      => $field['options'],
		) );
	}

	/**
	 * Load field template with passed args.
	 *
	 * @param string $type Field type that can be found in templates/ticket-fields
	 * @param array  $vars List of variables that will be available on template
	 *
	 * @return void
	 */
	private function _load_field_template( $type, $vars = array() ) {
		wc_get_template(
			'ticket-fields/' . $type . '.php',
			$vars,
			'woocommerce-box-office',
			WCBO()->dir . 'templates/'
		);
	}

	/**
	 * Validate posted data.
	 *
	 * @throws Exception
	 *
	 * @param array $posted_data Posted data to the form.
	 */
	public function validate( $posted_data ) {
		// No need to do anything if no fields or no data.
		if ( empty( $this->fields ) || empty( $posted_data['ticket_fields'] ) ) {
			return;
		}

		foreach ( $posted_data['ticket_fields'] as $key => $val ) {
			// First assume this is ticket form as found on single-product page.
			// Otherwise it's edit-ticket page.
			if ( is_numeric( $key ) && is_array( $val ) ) {
				foreach ( $val as $_key => $_val ) {
					$this->_validate_field( $_key, $_val );
				}
			} else {
				$this->_validate_field( $key, $val );
			}
		}

		// If we reach here, fields were validated.
		$this->_validated = true;

		// Set clean data.
		$this->_clean_data = $posted_data['ticket_fields'];
	}

	/**
	 * Validate field of the given key and given value.
	 *
	 * @throws Exception
	 *
	 * @param string $key   Field's key
	 * @param string $value Field's value
	 *
	 * @return void
	 */
	private function _validate_field( $key, $value ) {
		// Is required field?
		if ( $this->_field_prop_is( $key, 'required', 'yes' ) && empty( $value ) ) {
			throw new Exception(
				sprintf(
					__( '%s is required field and can not be empty.', 'woocommerce-box-office' ),
					$this->_field_prop_val( $key, 'label' )
				)
			);
		}

		$type = $this->_field_prop_val( $key, 'type' );
		if ( ! empty( $value ) ) {
			switch ( $type ) {
				case 'email':
					if ( ! is_email( $value ) ) {
						throw new Exception( __( 'Invalid email is provided.', 'woocommerce-box-office' ) );
					}
					break;
				case 'url':
					$url_parts = parse_url( $value );
					if ( empty( $url_parts['scheme'] ) ) {
						$value = 'http://' . $value;
					}
					if ( false === filter_var( $value, FILTER_VALIDATE_URL ) ) {
						throw new Exception( __( 'Invalid URL is provided', 'woocommerce-box-office' ) );
					}
					break;
				case 'select':
				case 'radio':
				case 'checkbox':
					$options = $this->_field_prop_val( $key, 'options', '' );
					$options = array_map( 'trim', explode( ',', $options ) );
					if ( ! is_array( $value ) ) {
						$value = array( $value );
					}

					$value = stripslashes_deep( $value );
					foreach ( $value as $val ) {
						if ( ! in_array( $val, $options ) ) {
							throw new Exception( sprintf( __( 'Invalid value for field %s', 'woocommerce-box-office' ), $this->_field_prop_val( $key, 'label' ) ) );
						}
					}
					break;
			}
		}
	}

	/**
	 * Check whether field, from the given, key has given property stored.
	 *
	 * @param string $key  Field's key
	 * @param string $prop Field's property
	 *
	 * @return bool True if field has the property
	 */
	private function _field_has_prop( $key, $prop ) {
		return (
			! empty( $this->fields[ $key ] )
			&&
			! empty( $this->fields[ $key ][ $prop ] )
		);
	}

	/**
	 * Get the value of field's property.
	 *
	 * @param string $key     Field's key
	 * @param string $prop    Field's property
	 * @param mixed  $default Default value to return of propety doesn't exist
	 *
	 * @return mixed Value of field's property
	 */
	private function _field_prop_val( $key, $prop, $default = null ) {
		if ( $this->_field_has_prop( $key, $prop ) ) {
			return $this->fields[ $key ][ $prop ];
		}

		return $default;
	}

	/**
	 * Check whether the value of field's property matches with the given value.
	 *
	 * @param string $key      Field's key
	 * @param string $prop     Field's property
	 * @param mixed  $prop_val Value to match
	 *
	 * @return bool True if matches
	 */
	private function _field_prop_is( $key, $prop, $prop_val = '' ) {
		return ( $this->_field_prop_val( $key, $prop ) === $prop_val );
	}

	/**
	 * Get posted data.
	 *
	 * @return array
	 */
	public function get_posted_data() {
		return $this->_posted_data;
	}

	/**
	 * Get clean data after validated.
	 *
	 * @return array Clean data
	 */
	public function get_clean_data() {
		if ( ! $this->_validated ) {
			_doing_it_wrong( __FUNCTION__, sprintf( __( 'Call %s::validate first', 'woocommerce-box-office' ), __CLASS__ ), WCBO()->_version );
		}

		return $this->_clean_data;
	}
}
