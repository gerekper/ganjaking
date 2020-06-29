<?php

/**
 * Class CT_Ultimate_GDPR_Service_WP_Simple_Paypal_Shopping_Cart
 */
class CT_Ultimate_GDPR_Service_WP_Simple_Paypal_Shopping_Cart extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_wp-simple-paypal-shopping-cart/wp_shopping_cart.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_wp-simple-paypal-shopping-cart/wp_shopping_cart.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_pseudonymization_get_data_to_encrypt_postmeta_keys', array(
			$this,
			'add_postmeta_keys_to_encrypt'
		) );
		add_filter( 'ct_ultimate_gdpr_controller_pseudonymization_updated_user_meta_to_encrypt', array(
			$this,
			'add_postmeta_keys_to_encrypt'
		) );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_wordpress-simple-paypal-shopping-cart/wp_shopping_cart.php', '__return_false' );
	}

	public function add_postmeta_keys_to_encrypt( $keys ) {

		if ( CT_Ultimate_GDPR::instance()
		                     ->get_admin_controller()
		                     ->get_option_value(
			                     'pseudonymization_services_wp_simple_paypal_shopping_cart_data',
			                     '',
			                     $this->front_controller->find_controller('pseudonymization')->get_id()
		                     )
		) {
			array_push( $keys, 'wpspsc_phone', 'wpsc_last_name', 'wpsc_first_name', 'wpsc_email_address', 'wpsc_address' );
		}

		return $keys;

	}

	/**
	 * @return $this
	 */
	public function collect() {

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT postmeta2.meta_key, postmeta2.meta_value
				FROM {$wpdb->postmeta} as postmeta
					INNER JOIN {$wpdb->postmeta} as postmeta2
					ON postmeta.post_id = postmeta2.post_id
				WHERE postmeta.meta_value = %s
					AND postmeta2.meta_key LIKE 'wpsc%'  
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		return $this->set_collected( $results );
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'WP Simple Paypal Shopping Cart' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'wpspc_cart_actions_handler' );
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

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare( "
				SELECT postmeta2.meta_id
				FROM {$wpdb->postmeta} as postmeta
					INNER JOIN {$wpdb->postmeta} as postmeta2
					ON postmeta.post_id = postmeta2.post_id
				WHERE postmeta.meta_value = %s
					AND postmeta2.meta_key LIKE 'wps%'  
				",
				$this->user->get_email()
			),
			ARRAY_A
		);

		foreach ( $results as $result ) {
			delete_meta( $result['meta_id'], true );
		}

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

		add_settings_section(
			'ct-ultimate-gdpr-services-wpsimplepaypalshoppingcart_accordion-20', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);


		// services

		/*add_settings_field(
			"services_{$this->get_id()}_header", // ID
			$this->get_name(), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpsimplepaypalshoppingcart_accordion-20' // Section
		);*/

        add_settings_field(
            "services_{$this->get_id()}_service_name", // ID
            sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_name_field" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-wpsimplepaypalshoppingcart_accordion-20' // Section
        );

		add_settings_field(
			"services_{$this->get_id()}_description", // ID
			sprintf( esc_html__( "[%s] Description", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_description_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-wpsimplepaypalshoppingcart_accordion-20' // Section
		);

		// breach

		add_settings_field(
			"breach_services_{$this->get_id()}",
			esc_html( $this->get_name() ), // Title
			array( $this, 'render_field_breach_services' ),
			$this->front_controller->find_controller('breach')->get_id(),
			'ct-ultimate-gdpr-breach_section-2'
		);

		// pseudo

		add_settings_field(
			"pseudo_services_{$this->get_id()}_name", // ID
			esc_html__( "[WP Simple Paypal Shopping Cart] Pseudonymize user order data", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_pseudonymization_services_{$this->get_id()}_data" ), // Callback
			$this->front_controller->find_controller('pseudonymization')->get_id(), // Page
			$this->front_controller->find_controller('pseudonymization')->get_id() // Section
		);

	}

	public function render_field_pseudonymization_services_wp_simple_paypal_shopping_cart_data() {

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
	 * @param array $recipients
	 *
	 * @return array
	 */
	public function breach_recipients_filter( $recipients ) {

		if ( ! $this->is_breach_enabled() ) {
			return $recipients;
		}

		global $wpdb;

		$results = $wpdb->get_results("
				SELECT meta_value
				FROM {$wpdb->postmeta}
				WHERE meta_key LIKE 'wpsc%'
					AND meta_value REGEXP '^[A-Za-z0-9._%\-+!#$&/=?^|~]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'
				",
			ARRAY_A
		);

		foreach ( $results as $result ) {

			if ( ! empty( $result['meta_value'] ) ) {
				$recipients[ $result['meta_value'] ] = $result['meta_value'];
			}

		}

		return $recipients;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return esc_html__( 'WP Simple Paypal Shopping Cart gathers consumer orders data', 'ct-ultimate-gdpr' );
	}

}
