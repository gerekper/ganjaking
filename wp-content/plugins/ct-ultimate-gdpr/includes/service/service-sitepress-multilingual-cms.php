<?php

/**
 * Class CT_Ultimate_GDPR_Service_Sitepress_WPML
 */
class CT_Ultimate_GDPR_Service_Sitepress_WPML extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_sitepress-multilingual-cms/sitepress.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_sitepress-multilingual-cms/sitepress.php', '__return_true' );
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

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_sitepress_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$scripts_to_block = array(
				'sitepress_widget',
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_convenience() ] ) ) {
			$scripts[ $this->get_group()->get_level_convenience() ] = array_merge( $scripts[ $this->get_group()->get_level_convenience() ], $scripts_to_block );
		}


		return $scripts;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'WPML' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		add_settings_section(
			'ct-ultimate-gdpr-services-sitepress_accordion-44', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);
	
		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			esc_html( $this->get_name() ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-sitepress_accordion-44' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_block_cookies", // ID
			sprintf( esc_html__( "[%s] Block WPML cookies when a user doesn't accept Functionality cookies", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_services_sitepress_block_cookies" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-sitepress_accordion-44' // Section
		);

        add_settings_field(
            "services_{$this->get_id()}_hide_from_forgetme_form", // ID
            sprintf( esc_html__( "[%s] Hide from Forget Me Form", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
            array( $this, "render_hide_from_forgetme_form" ), // Callback
            $this->front_controller->find_controller('services')->get_id(), // Page
            'ct-ultimate-gdpr-services-sitepress_accordion-44' // Section
        );

    }

	/**
	 *
	 */
	public function render_field_services_sitepress_block_cookies() {

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
	 * @param $cookies
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {
		echo '<script>console.log("cookies123");</script>';
		$cookies_to_block = array();

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_sitepress_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$cookies_to_block = array(
				'wp-wpml_current_language',
				'_icl_visitor_lang_js',
				//'wp-wpml_current_admin_language_{hash}',
				'wpml_browser_redirect_test'
			);

		}

		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $this->get_group()->get_level_convenience() ) ) {
			$cookies[ $this->get_group()->get_level_convenience() ] = array_merge( $cookies[ $this->get_group()->get_level_convenience() ], $cookies_to_block );
		}

		return $cookies;

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
		return '';
	}

	/**
	 * Collect data of a specific user
	 *
	 * @return $this
	 */
	public function collect() {
		return $this;
	}
}