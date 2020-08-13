<?php

/**
 * Class CT_Ultimate_GDPR_Service_Google_Analytics
 */
class CT_Ultimate_GDPR_Service_Addthis extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_addthis/addthis_social_widget.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_addthis/addthis_social_widget.php', '__return_false' );
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

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_addthis_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$scripts_to_block = array(
				'addthis_widget',
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
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Addthis' );
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
			'ct-ultimate-gdpr-services-addthis_accordion-1', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_header", // ID
			esc_html( $this->get_name() ), // Title
			'__return_empty_string', // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-addthis_accordion-1' // Section
		);

		add_settings_field(
			"services_{$this->get_id()}_block_cookies", // ID
			sprintf( esc_html__( "[%s] Block Addthis cookies when a user doesn't accept Functionality cookies", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_field_services_addthis_block_cookies" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-addthis_accordion-1' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_addthis_block_cookies() {

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

		$cookies_to_block = array();

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_addthis_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$cookies_to_block = array(
				'km_ai',
				'km_lv',
				'km_vs',
				'__atuvs',
				'__atuvc',
				'uvc'
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