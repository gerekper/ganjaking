<?php

/**
 * Class CT_Ultimate_GDPR_Service_Google_Analytics_For_Wordpress
 */
class CT_Ultimate_GDPR_Service_GA_Google_Analytics extends CT_Ultimate_GDPR_Service_Abstract {


	/**
	 * Run on init
	 * @return void
	 */
	public function init() {
		$this->maybe_disable_tracking();
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_ga-google-analytics/ga-google-analytics.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_ga-google-analytics/ga-google-analytics.php', '__return_true' );
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
	 * Get service name
	 *
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", "GA Google Analytics" );
	}

	/**
	 * Is it active, eg. whether related plugin is enabled
	 *
	 * @return bool
	 */
	public function is_active() {
		return function_exists( 'ga_google_analytics_tracking_code' );
	}

	/**
	 * Can data be forgotten by this service?
	 *
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * Forget specific user data
	 *
	 * @throws Exception
	 * @return void
	 */
	public function forget() {
	}

	/**
	 * Add admin option fields
	 *
	 * @return mixed
	 */
	public function add_option_fields() {

		// important: if service is not active and option is hidden, then its value gets lost when saved
		if ( ! $this->is_active() ) {
			return;
		}


		add_settings_section(
			'ct-ultimate-gdpr-services-gagoogleanalytics_accordion-8', // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		/* Services section */

		add_settings_field(
			"cookie_services_{$this->get_id()}_disabled", // ID
			"[{$this->get_name()}] Disable all tracking for users who did not accept cookies", // Title
			array( $this, "render_field_cookie_services_ga_google_analytics_disabled" ), // Callback
			$this->front_controller->find_controller('cookie')->get_id(), // Page
			'ct-ultimate-gdpr-services-gagoogleanalytics_accordion-8'
		);


	}

	/**
	 *
	 */
	public function render_field_cookie_services_ga_google_analytics_disabled() {

		$admin      = $this->get_admin_controller();
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
	public function maybe_disable_tracking() {

		if (
			$this->get_admin_controller()->get_option_value( "cookie_services_ga_google_analytics_disabled", '', $this->front_controller->find_controller('cookie')->get_id() ) &&
			! CT_Ultimate_GDPR::instance()->get_controller_by_id( $this->front_controller->find_controller('cookie')->get_id() )->is_consent_valid()
		) {
			remove_action( 'wp_head', 'ga_google_analytics_tracking_code' );
			remove_action( 'wp_footer', 'ga_google_analytics_tracking_code' );
			remove_action( 'admin_head', 'ga_google_analytics_tracking_code' );
			remove_action( 'admin_footer', 'ga_google_analytics_tracking_code' );
		}

	}

	/**
	 * Do optional action on front
	 *
	 * @return mixed
	 */
	public function front_action() {
	}
}