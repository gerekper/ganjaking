<?php
/**
 * Class CT_Ultimate_GDPR_Service_Custom_Facebook_Feed
 */
class CT_Ultimate_GDPR_Service_Custom_Facebook_Feed extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_custom-facebook-feed/custom-facebook-feed.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_custom-facebook-feed/custom-facebook-feed.php', '__return_false' );
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Custom Facebook Feed' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return function_exists('cff_activate');
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
	public function forget() {}


	/**
	 * @return mixed
	 */
	public function add_option_fields() {
		add_settings_section(
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}", // ID
			esc_html( $this->get_name() ), // Title
			null, // callback
			$this->front_controller->find_controller('services')->get_id() // Page
		);

		add_settings_field(
			"services_{$this->get_id()}_service_name", // ID
			sprintf( esc_html__( "[%s] Name", 'ct-ultimate-gdpr' ), $this->get_name() ), // Title
			array( $this, "render_name_field" ), // Callback
			$this->front_controller->find_controller('services')->get_id(), // Page
			"ct-ultimate-gdpr-services-{$this->get_id()}_accordion-{$this->get_id()}"
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
			$cookies_to_block = array( 'fr', 'sb');
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_statistics()] ) ) {
			$cookies[ $this->get_group()->get_level_statistics() ] = array_merge( $cookies[ $this->get_group()->get_level_statistics() ], $cookies_to_block );
		}

		return $cookies;
	}

	/**
	 * @return mixed
	 */
	public function front_action() {

	}

	public function enqueue_static() {

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