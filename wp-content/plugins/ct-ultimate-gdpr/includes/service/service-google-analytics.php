<?php

/**
 * Class CT_Ultimate_GDPR_Service_Google_Analytics
 */
class CT_Ultimate_GDPR_Service_Google_Analytics extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
	}

	/**
	 * @return $this
	 */

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Google Analytics' );
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

		/* Cookie section */

		add_settings_field(
			"cookie_services_{$this->get_id()}_tracking_id", // ID
			esc_html__( "Google Analytics Tracking ID (disable tracking for this ID when cookies consent not given), eg. UA-118586768-1", 'ct-ultimate-gdpr' ), // Title
			array( $this, "render_field_cookie_services_{$this->get_id()}_tracking_id" ), // Callback
			$this->front_controller->find_controller('cookie')->get_id(), // Page
			'ct-ultimate-gdpr-cookie_tab-1_section-4' // Section
		);

		add_settings_field(
			"cookie_services_{$this->get_id()}_enable_anonymous_tracking", // ID
			sprintf(
				wp_kses_post( __( "Enable Google Analytics anonymized IP tracking (<a href='%s' target='_blank'>read more</a>)", 'ct-ultimate-gdpr' ) ),
				'https://support.google.com/analytics/answer/2763052?hl=en'
			),
			array( $this, "render_field_cookie_services_{$this->get_id()}_enable_anonymous_tracking" ), // Callback
			$this->front_controller->find_controller('cookie')->get_id(), // Page
			'ct-ultimate-gdpr-cookie_tab-1_section-4',
			array(
				'hint' => sprintf(
					wp_kses_post( __( 'Please note that even when enabled, Ultimate GDPR will still block Google Analytics without user consent. If you would like to track anonymously users without accepting cookies, please <a href=\'%s\' target=\'_blank\'>read more here</a>.', 'ct-ultimate-gdpr' ) ),
					'https://gdpr-plugin.readthedocs.io/en/latest/faq/FAQ.html#google-analytics-stats'
				),
			)
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
		if ( $force || $this->get_admin_controller()->get_option_value( 'services_google_analytics_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {
			$cookies_to_block = array( '__utma', '__utmb', '__utmc', '__utmt', '__utmz', '_ga', '_gat', '_gid' );
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_statistics() ] ) ) {
			$cookies[ $this->get_group()->get_level_statistics() ] = array_merge( $cookies[ $this->get_group()->get_level_statistics() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 *
	 */
	public function render_field_cookie_services_google_analytics_enable_anonymous_tracking() {

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
	public function render_field_cookie_services_google_analytics_tracking_id() {

		$admin = $this->get_admin_controller();

		$field_name = $admin->get_field_name( __FUNCTION__ );
		printf(
			"<input class='ct-ultimate-gdpr-field' type='text' id='%s' name='%s' value='%s' />",
			$admin->get_field_name( __FUNCTION__ ),
			$admin->get_field_name_prefixed( $field_name ),
			$admin->get_option_value_escaped( $field_name )
		);

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

		// script for disabling GA tracking
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ), 1 );
		add_filter( 'ct_ultimate_gdpr_controller_cookie_fix_content', array( $this, 'cookie_fix_content_filter' ) );

	}

	/**
	 * @param $content
	 *
	 * @return mixed
	 */
	public function cookie_fix_content_filter( $content ) {

		$enable_anonymous_tracking = CT_Ultimate_GDPR::instance()
		                                             ->get_admin_controller()
		                                             ->get_option_value( 'cookie_services_google_analytics_enable_anonymous_tracking', '', $this->front_controller->find_controller('cookie')->get_id() );

		if ( $enable_anonymous_tracking ) {

			$replaced = preg_replace_callback( '#[\s]{1}ga\([\'"]\s*send[\'"]\w{0,1},\s*[\'"].*?\);#', array(
				$this,
				'add_anonymize_content'
			), $content );

		}

		return ! empty( $replaced ) ? $replaced : $content;
	}

	/**
	 * Inject anonymizeIP param into google scripts
	 *
	 * @param $matches
	 *
	 * @return string
	 */
	public function add_anonymize_content( $matches ) {

		return "ga('set', 'anonymizeIp', true); " . $matches[0];

	}

	/**
	 *
	 */
	public function enqueue_static() {

		$id = $this->get_admin_controller()->get_option_value( 'cookie_services_google_analytics_tracking_id', '', $this->front_controller->find_controller('cookie')->get_id() );

		// no ga id was set in option
		if ( ! $id ) {
			return;
		}

		// consent given, no need to block ga
		if ( CT_Ultimate_GDPR::instance()->get_controller_by_id( $this->front_controller->find_controller('cookie')->get_id() )->is_consent_valid() ) {
			return;
		}

		wp_enqueue_script( 'ct-ultimate-gdpr-service-google-analytics', ct_ultimate_gdpr_url( '/assets/js/google-analytics.js' ) );
		wp_localize_script( 'ct-ultimate-gdpr-service-google-analytics', 'ct_ultimate_gdpr_service_google_analytics', array( 'id' => $id ) );

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

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_google_analytics_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$scripts_to_block = array(
				"google-analytics.com/analytics.js",
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_statistics() ] ) ) {
			$scripts[ $this->get_group()->get_level_statistics() ] = array_merge( $scripts[ $this->get_group()->get_level_statistics() ], $scripts_to_block );
		}

		return $scripts;
	}
}