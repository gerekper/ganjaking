<?php

/**
 * Interface CT_Ultimate_GDPR_Service_Woocommerce
 */
class CT_Ultimate_GDPR_Service_Woocommerce extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return $this
	 */
	public function collect() {

		$orders             = array();
		$customer_orders    = get_posts( array(
			'numberposts'   => -1,
			'meta_key'      => '_billing_email',
			'meta_value'    => $this->user->get_email(),
			'post_type'     => wc_get_order_types(),
			'post_status'   => array_keys( wc_get_order_statuses() ),
		) );

		if ( is_array( $customer_orders ) ) {
			foreach ( $customer_orders as $order ) {
				$order      = new WC_Order( $order->ID );
				$orders[]   = $order;
			}
		}
		return $this->set_collected( $orders );

	}

	/**
	 * @param bool $human_readable
	 *
	 * @return mixed
	 */
	public function render_collected( $human_readable = false ) {

		$data                           = array();
		$removeEmptyField               = CT_Ultimate_GDPR::instance()
			->get_admin_controller()
			->get_option_value(
				"dataaccess_remove_empty_fields",
				'',
				$this->front_controller->find_controller('access')->get_id()
			);

		/** @var WC_Order $order */
		foreach ( $this->collected as $order ) {
			$data[] = $order->get_data();
		}

		$return = '';

		if ( ! empty( $data ) ) {
			if ( $removeEmptyField == 'on' ) {
				$data = $this->dataaccess_array_remove_empty_fields( $data );
			}

			if ( $human_readable ) {
				$return = $this->render_human_data( $data );
			} else {
				$return = ct_ultimate_gdpr_json_encode( $data );
			}
		}

		return apply_filters( 'ct_ultimate_gdpr_service_render_collected', $return, $data, $human_readable, $this->get_id() );

	}

	/**
	 * @param array $array
	 *
	 * @return array
	 */
	public function dataaccess_array_remove_empty_fields( $array ){

		foreach ( $array as $key => $value ) {
			if( is_array( $value ) || is_object( $value ) ){
				$value = $array[$key] = $this->dataaccess_array_remove_empty_fields( $value );
			}

			if ( empty( $value ) ) {
				if( is_array( $array ) ){
					unset( $array[ $key ] );
				} elseif ( is_object( $array ) ) {
					unset( $array->$key );
				}
			}
		}
		return $array;
	}

	/**
	 * @return mixed|string
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", "WooCommerce" );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'wc' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return true && $this->is_active();
	}


	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

		$this->collect();

		/** @var WC_Order $order */
		foreach ( $this->collected as $order ) {

			$order->set_billing_address_1( '' );
			$order->set_billing_address_2( '' );
			$order->set_billing_city( '' );
			$order->set_billing_company( '' );
			$order->set_billing_country( '' );
			$order->set_billing_email( '' );
			$order->set_billing_first_name( '' );
			$order->set_billing_last_name( '' );
			$order->set_billing_phone( '' );
			$order->set_billing_postcode( '' );
			$order->set_billing_state( '' );
			$order->set_customer_id( 0 );
			$order->set_customer_ip_address( '' );
			$order->set_customer_note( '' );
			$order->set_customer_user_agent( '' );
			$order->set_shipping_address_1( '' );
			$order->set_shipping_address_2( '' );
			$order->set_shipping_city( '' );
			$order->set_shipping_company( '' );
			$order->set_shipping_country( '' );
			$order->set_shipping_first_name( '' );
			$order->set_shipping_last_name( '' );
			$order->set_shipping_postcode( '' );
			$order->set_shipping_state( '' );

			$order->save();

		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {


		add_settings_section(
			'ct-ultimate-gdpr-services-woocommerce_accordion-16', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/*add_settings_field(
			'services_woocommerce_header', // ID
			esc_html__( 'WooCommerce', 'ct-ultimate-gdpr' ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			esc_html__( "[WooCommerce] Description", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'services_woocommerce_consent_field', // ID
			esc_html__( '[WooCommerce] Inject consent checkbox to order fields', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_woocommerce_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'services_woocommerce_edit_account_consent_field', // ID
			esc_html__( '[WooCommerce] Inject consent checkbox to account forms', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_woocommerce_edit_account_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'services_woocommerce_checkout_consent_field', // ID
			esc_html__( '[WooCommerce] Inject consent checkbox to checkout', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_woocommerce_checkout_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'services_woocommerce_order_checkout_description', // ID
			esc_html__( '[WooCommerce] Additional checkout consent label', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_woocommerce_order_checkout_description' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'services_woocommerce_order_checkout_consent_field', // ID
			esc_html__( '[WooCommerce] Inject Additional consent checkbox to checkout', 'ct-ultimate-gdpr' ), // Title
			array( $this, 'render_field_services_woocommerce_order_checkout_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-woocommerce_accordion-16' // Section
		);

		add_settings_field(
			'breach_services_woocommerce',
			esc_html__( 'WooCommerce', 'ct-ultimate-gdpr' ),
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
		);

		add_settings_field(
			"pseudo_services_{$this->get_id()}_name", // ID
			esc_html__( "[WooCommerce] Pseudonymize first and last name", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_pseudonymization_services_{$this->get_id()}_name" ), // Callback
			$this->front_controller->find_controller('pseudonymization')->get_id(), // Page
			$this->front_controller->find_controller('pseudonymization')->get_id() // Section
		);

		add_settings_field(
			"pseudo_services_{$this->get_id()}_address", // ID
			esc_html__( "[WooCommerce] Pseudonymize address information", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_pseudonymization_services_{$this->get_id()}_address" ), // Callback
			$this->front_controller->find_controller('pseudonymization')->get_id(), // Page
			$this->front_controller->find_controller('pseudonymization')->get_id() // Section
		);

		add_settings_field(
			"pseudo_services_{$this->get_id()}_email", // ID
			esc_html__( "[WooCommerce] Pseudonymize billing email", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_pseudonymization_services_{$this->get_id()}_email" ), // Callback
			$this->front_controller->find_controller('pseudonymization')->get_id(), // Page
			$this->front_controller->find_controller('pseudonymization')->get_id() // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_woocommerce_block_cookies() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_pseudonymization_services_woocommerce_name() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 * Render field for setting custom service description
	 */
	public function render_field_services_woocommerce_order_checkout_description() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<textarea class='ct-ultimate-gdpr-field' id='%s' name='%s' rows='10' cols='100'>%s</textarea>",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name),
			$admin->get_option_value_escaped( $field_name, esc_html__( 'I consent to the storage of my data according to the Privacy Policy', 'ct-ultimate-gdpr' ) ),
			$admin->get_option_value_escaped( $field_name, $this->get_service_name() )
		);

	}

	/**
	 *
	 */
	public function render_field_pseudonymization_services_woocommerce_email() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 *
	 */
	public function render_field_pseudonymization_services_woocommerce_address() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 *
	 */
	public function render_field_breach_services() {

		$admin      = $this->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		$values     = $admin->get_option_value( $field_name, array(), $this->front_controller->find_controller('breach')->get_id() );
		$checked    = in_array( $this->get_id(), $values ) ? 'checked' : '';
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s[]' value='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$this->get_id(),
			$checked
		);

	}

	/**
	 *
	 */
	public function render_field_services_woocommerce_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 *
	 */
	public function render_field_services_woocommerce_edit_account_consent_field() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	*
	*/
	public function render_field_services_woocommerce_checkout_consent_field() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);
	}

	/**
	*
	*/
	public function render_field_services_woocommerce_order_checkout_consent_field() {

		$admin      = CT_Ultimate_GDPR::instance()->get_admin_controller();
		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='checkbox' id='%s' name='%s' %s />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name ) ? 'checked' : ''
		);

	}

	/**
	 * @return void
	 */
	public function init() {

		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_woocommerce/woocommerce.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_woocommerce/woocommerce.php', '__return_true' );

		add_filter( 'ct_ultimate_gdpr_controller_pseudonymization_get_data_to_encrypt_meta_keys', array(
			$this,
			'add_user_meta_keys_to_encrypt'
		) );
		add_filter( 'ct_ultimate_gdpr_controller_pseudonymization_updated_user_meta_to_encrypt', array(
			$this,
			'add_user_meta_keys_to_encrypt'
		) );

		add_filter( 'woocommerce_save_account_details_required_fields', array(
			$this,
			'save_account_field_filter'
		), 100 );

		add_filter( 'woocommerce_process_registration_errors', array( $this, 'woocommerce_form_errors_filter' ), 100 );

		add_filter( 'ct_ultimate_gdpr_redirect', array( $this, 'disable_checkout_redirect' ) );
	}

	/**
	 * @param $should_redirect
	 *
	 * @return bool
	 */
	public function disable_checkout_redirect( $should_redirect ) {

		if ( is_checkout() || is_order_received_page() ) {
			$should_redirect = false;
		}

		return $should_redirect;
	}

	/**
	 * @param $cookies
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {

		$cookies_to_block = array();
		if ( $force ) {
			$cookies_to_block = array(
				'woocommerce_*',
				'wp_woocommerce_*',
				'wc_cart_hash_*',
				'wc_fragments_*',
			);
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_necessary() ] ) ) {
			$cookies[ $this->get_group()->get_level_necessary() ] = array_merge( $cookies[ $this->get_group()->get_level_necessary() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {
		add_filter( 'woocommerce_checkout_fields', array( $this, 'woocommerce_checkout_fields_filter' ), 100 );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'woocommerce_checkout_description_fields_filter' ), 100 );
		add_action( 'woocommerce_edit_account_form', array( $this, 'edit_account_form_action' ), 100 );
		add_action( 'woocommerce_register_form', array( $this, 'edit_account_form_action' ), 100 );
		add_action( 'woocommerce_lostpassword_form', array( $this, 'edit_account_form_action' ), 100 );
		add_action( 'woocommerce_review_order_before_submit', array( $this, 'checkout_action' ), 100 );
		add_action( 'woocommerce_checkout_process', array( $this, 'checkout_consent_validation' ), 100 );
		add_action( 'woocommerce_checkout_process', array( $this, 'checkout_consent_description_validation' ), 100 );

		// Validation for two consent fields
        add_action( 'woocommerce_checkout_process', array( $this, 'woocommerce_checkout_fields_validation' ), 100 );
        add_action( 'woocommerce_checkout_process', array( $this, 'woocommerce_checkout_description_fields_validation' ), 100 );

        // removed optional text on specific field
        add_filter( 'woocommerce_form_field' , array($this, 'remove_checkout_optional_text'), 10, 4 );
	}

    /**
     * @return mixed
     * $field
     * $key
     * removed optional text on front end if $key(field) is equal to addtional and label-text
     */
    public function remove_checkout_optional_text( $field, $key, $args, $value ) {
        if( is_checkout() && ! is_wc_endpoint_url() ) {
            if($key == 'ct-ultimate-gdpr-consent-field-additional' || $key == 'ct-ultimate-gdpr-consent-field-label-text' ){
                $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
                $field = str_replace( $optional, '', $field );
            }
        }
        return $field;
    }

    /**
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function save_account_field_filter( $fields ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_edit_account_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			$fields['ct-ultimate-gdpr-consent-field'] = esc_html__( 'Consent', 'ct-ultimate-gdpr' );
		}

		return $fields;

	}

	/**
	 * @param WP_Error $errors
	 *
	 * @return mixed
	 */
	public function woocommerce_form_errors_filter( $errors ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_edit_account_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject && empty( $_REQUEST['ct-ultimate-gdpr-consent-field'] ) ) {
			$errors->add( 1, esc_html__( 'Consent is required', 'ct-ultimate-gdpr' ) );
		} elseif ( $inject ) {
			$this->log_user_consent();
		}

		return $errors;
	}

    /**
     * @param $fields
     * validation for  POST ct-ultimate-gdpr-consent-field-additional
     * @return mixed
     */
    public function woocommerce_checkout_fields_validation( $wcc_checkout_fields_validation ) {
        $inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
        $conesent_given = isset($_POST['ct-ultimate-gdpr-consent-field-additional']) ? $_POST['ct-ultimate-gdpr-consent-field-additional'] : false;
        if ( $inject ) {
            if ( ! $conesent_given ) {
                wc_add_notice(ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-consent-field-label', false ) )."  is a required field", 'error' );
            } else {
                $this->log_user_consent();
            }
        }

        return $wcc_checkout_fields_validation;
    }

    /**
     * @param $fields
     * validation for  POST ct-ultimate-gdpr-consent-field-label-text
     * @return mixed
     */
    public function woocommerce_checkout_description_fields_validation( $wcc_checkout_descrition_fields_validation ) {

        $inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_order_checkout_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
        $conesent_given = isset($_POST['ct-ultimate-gdpr-consent-field-label-text']) ? $_POST['ct-ultimate-gdpr-consent-field-label-text'] : false;
        if ( $inject ) {
            if ( ! $conesent_given ) {
                wc_add_notice( ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-order-checkout', false ) )."  is a required field", 'error' );
            } else {
                $this->logger->consent( array(
                    'type'          => 'woocommerce-marketing',
                    'time'          => time(),
                    'user_id'       => wp_get_current_user()->ID,
                    'user_ip'       => ct_ultimate_gdpr_get_permitted_user_ip(),
                    'user_agent'    => ct_ultimate_gdpr_get_permitted_user_agent()
                ) );
            }
        }

        return $wcc_checkout_descrition_fields_validation;

    }


	/**
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function woocommerce_checkout_fields_filter( $fields ) {

		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {

			$fields['order']['ct-ultimate-gdpr-consent-field-additional'] = array(
				'type'     => 'checkbox',
				'label'    => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-consent-field-label', false ) ),
				'required' => false,
			);
		}

		return $fields;
	}

	/**
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function woocommerce_checkout_description_fields_filter( $fields ) {

		$inject_order = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_woocommerce_order_checkout_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject_order ) {
			$fields['order']['ct-ultimate-gdpr-consent-field-label-text'] = array(
				'type'      => 'checkbox',
				'label'     => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-order-checkout', false ) ),
				'required'  => false,
			);
		}

		return $fields;

	}

	/**
	 *
	 */
	public function edit_account_form_action() {

		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_edit_account_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-consent-field', false ), true );
		}


	}

	/**
	 *
	 */
	public function checkout_action() {

		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_checkout_consent_field', false, $this->front_controller->find_controller('services')->get_id() );

		if ( $inject ) {
			ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-checkout-consent-field', false ), true );
		}


	}

	/**
	 * @param $wccs_custom_checkout_field_pro_process
	 *
	 * @return mixed
	 */
	public function checkout_consent_validation( $wccs_custom_checkout_field_pro_process ) {
		$inject = $this->get_admin_controller()->get_option_value( 'services_woocommerce_checkout_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$conesent_given = isset($_POST['ct-ultimate-gdpr-consent-field']) ? $_POST['ct-ultimate-gdpr-consent-field'] : false;
		if ( $inject ) {
			if ( ! $conesent_given ) {
				wc_add_notice( ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-woocommerce-forms-consent-field-error-message', false ) ), 'error' );
			} else {
				$this->log_user_consent();
			}
		}

		return $wccs_custom_checkout_field_pro_process;
	}

	/**
	 * @param $wccs_custom_checkout_field_pro_process
	 *
	 * @return mixed
	 */
	public function checkout_consent_description_validation( $wccs_custom_checkout_field_pro_process ) {

		$inject         = CT_Ultimate_GDPR::instance()->get_admin_controller()->get_option_value( 'services_woocommerce_order_checkout_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		$conesent_given = isset( $_POST['ct-ultimate-gdpr-consent-field-label-text'] ) ? $_POST['ct-ultimate-gdpr-consent-field-label-text'] : false;

		if ( $inject ) {
			if ( $conesent_given ) {
				$this->logger->consent( array(
					'type'          => 'woocommerce-marketing',
					'time'          => time(),
					'user_id'       => wp_get_current_user()->ID,
					'user_ip'       => ct_ultimate_gdpr_get_permitted_user_ip(),
					'user_agent'    => ct_ultimate_gdpr_get_permitted_user_agent()
				) );
			}
		}

		return $wccs_custom_checkout_field_pro_process;
	}

	/**
	 * @param array $recipients
	 *
	 * @return array
	 */
	public function breach_recipients_filter( $recipients ) {

		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}

		$customer_orders = get_posts( array(
			'numberposts' => - 1,
			'meta_key'    => '_billing_email',
			'compare'     => 'EXISTS',
			'post_type'   => wc_get_order_types(),
			'post_status' => array_keys( wc_get_order_statuses() ),
		) );

		if ( is_array( $customer_orders ) ) {

			foreach ( $customer_orders as $order ) {

				$order = new WC_Order( $order->ID );
				$email = $order->get_billing_email();

				if ( is_email( $email ) ) {
					$recipients[] = $email;
				}

			}

		}

		return $recipients;

	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'WooCommerce gathers data entered by users in shop orders', 'ct-ultimate-gdpr' );
	}

	/**
	 * @param $keys
	 *
	 * @return mixed
	 */
	public function add_user_meta_keys_to_encrypt( $keys ) {

		if ( CT_Ultimate_GDPR::instance()
		                     ->get_admin_controller()
		                     ->get_option_value(
			                     "pseudonymization_services_{$this->get_id()}_name",
			                     '',
			                     $this->front_controller->find_controller('pseudonymization')->get_id()
		                     )
		) {
			array_push( $keys, 'billing_first_name', 'billing_last_name', 'shipping_last_name', 'shipping_first_name' );
		}

		if ( CT_Ultimate_GDPR::instance()
		                     ->get_admin_controller()
		                     ->get_option_value(
			                     "pseudonymization_services_{$this->get_id()}_email",
			                     '',
			                     $this->front_controller->find_controller('pseudonymization')->get_id()
		                     )
		) {
			array_push( $keys, 'billing_email' );
		}

		if ( CT_Ultimate_GDPR::instance()
		                     ->get_admin_controller()
		                     ->get_option_value(
			                     "pseudonymization_services_{$this->get_id()}_address",
			                     '',
			                     $this->front_controller->find_controller('pseudonymization')->get_id()
		                     )
		) {
			array_push(
				$keys,
				'billing_company',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_postcode',
				'billing_country',
				'billing_phone',
				'billing_state',
				'shipping_company',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_postcode',
				'shipping_country',
				'shipping_state'
			);
		}

		return $keys;

	}

	/**
	 * @param array $scripts
	 *
	 * @param bool $force
	 *
	 * @return array
	 */
	public function script_blacklist_filter( $scripts, $force = false ) {

		$scripts_to_block = array();

		if ( $force ) {

			// we dont need to remove them as they only generate local cookies
			$scripts_to_block = array(
//				"cart-fragments.",
//				"woocommerce.",
//				'add-to-cart.',
//				'wc_cart_fragments_params',
//				'js.cookie.min.js'
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_targetting() ] ) ) {
			$scripts[ $this->get_group()->get_level_targetting() ] = array_merge( $scripts[ $this->get_group()->get_level_targetting() ], $scripts_to_block );
		}

		return $scripts;
	}

}