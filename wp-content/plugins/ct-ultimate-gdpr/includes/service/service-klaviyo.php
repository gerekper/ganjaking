<?php

/**
 * Class CT_Ultimate_GDPR_Service_Klaviyo
 */
class CT_Ultimate_GDPR_Service_Klaviyo extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_klaviyo/klaviyo.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_klaviyo/klaviyo.php', '__return_true' );
	}

	/**
	 * @return $this
	 */

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Klaviyo' );
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
			'ct-ultimate-gdpr-services-klaviyo_accordion-klaviyo', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			'services_klaviyo_consent_field', // ID
			sprintf(
				esc_html__( "[%s] Inject consent checkbox to all forms", 'ct-ultimate-gdpr' ),
				$this->get_name()
			),
			array( $this, 'render_field_services_klaviyo_consent_field' ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			'ct-ultimate-gdpr-services-klaviyo_accordion-klaviyo' // Section
		);

	}

	/**
	 *
	 */
	public function render_field_services_klaviyo_consent_field() {

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
		if ( $force ) {
			$cookies_to_block = array( '__kla_id', '__zlcmid' );
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_statistics() ] ) ) {
			$cookies[ $this->get_group()->get_level_statistics() ] = array_merge( $cookies[ $this->get_group()->get_level_statistics() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

		$inject = $this->get_admin_controller()->get_option_value( 'services_klaviyo_consent_field', false, $this->front_controller->find_controller('services')->get_id() );
		if ( $inject ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ) );
		}

	}

	public function enqueue_static(  ) {

		wp_enqueue_script( 'ct-ultimate-gdpr-service-klaviyo', ct_ultimate_gdpr_url( 'assets/js/service-klaviyo.js' ) );
		wp_localize_script( 'ct-ultimate-gdpr-service-klaviyo', 'ct_ultimate_gdpr_klaviyo', array(
			'checkbox' => ct_ultimate_gdpr_render_template( ct_ultimate_gdpr_locate_template( 'service/service-klaviyo-consent-field', false ) ),
		) );

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

			$scripts_to_block = array(
				"klaviyo.com/media/js/analytics/analytics.js",
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_statistics() ] ) ) {
			$scripts[ $this->get_group()->get_level_statistics() ] = array_merge( $scripts[ $this->get_group()->get_level_statistics() ], $scripts_to_block );
		}

		return $scripts;
	}
}