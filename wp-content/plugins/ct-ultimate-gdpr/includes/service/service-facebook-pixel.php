<?php

/**
 * Class CT_Ultimate_GDPR_Service_Facebook_Pixel
 */
class CT_Ultimate_GDPR_Service_Facebook_Pixel extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_pixelyoursite/facebook-pixel-master.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_pixelyoursite/facebook-pixel-master.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_pixelyoursite/pixelyoursite.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_pixelyoursite/pixelyoursite.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_pixelyoursite-pro/pixelyoursite-pro.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_pixelyoursite-pro/pixelyoursite-pro.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_pixelyoursitepro/pixelyoursite-pro.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_pixelyoursitepro/pixelyoursite-pro.php', '__return_true' );
	}

	/**
	 * @return $this
	 */

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Facebook Pixel' );
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
	 * @param array $scripts
	 *
	 * @param bool $force
	 *
	 * @return array
	 */
	public function script_blacklist_filter( $scripts, $force = false ) {

		$scripts_to_block = array();

		if ( $force || $this->get_admin_controller()->get_option_value( 'services_facebook_pixel_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {

			$scripts_to_block = array(
				"pixel-cat.",
				"fbq('init'",
				"www.facebook.com/tr?id",
				"/pixelyoursite/",
				"/pixelyoursitepro/",
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_targetting() ] ) ) {
			$scripts[ $this->get_group()->get_level_targetting() ] = array_merge( $scripts[ $this->get_group()->get_level_targetting() ], $scripts_to_block );
		}

		return $scripts;

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {
	}

	/**
	 *
	 */

	/**
	 * @param array $cookies
	 * @param bool $force
	 *
	 * @return array
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {

		$cookies_to_block = array();
		if ( $force || $this->get_admin_controller()->get_option_value( 'services_facebook_pixel_block_cookies', '', $this->front_controller->find_controller('services')->get_id() ) ) {
			$cookies_to_block = array(
				'act',
				'wd',
				'xs',
				'datr',
				'sb',
				'presence',
				'c_user',
				'fr',
				'pl',
				'reg_ext_ref',
				'reg_fb_gate',
				'reg_fb_ref'
			);
		}
		$cookies_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_cookies_to_block", $cookies_to_block );

		if ( is_array( $cookies[ $this->get_group()->get_level_targetting() ] ) ) {
			$cookies[ $this->get_group()->get_level_targetting() ] = array_merge( $cookies[ $this->get_group()->get_level_targetting() ], $cookies_to_block );
		}

		return $cookies;

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

		if ( apply_filters( 'ct_ultimate_gdpr_controller_cookie_group_level', 0 ) < min( $this->get_group_levels() ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_static' ), 1 );
		}

	}

	/**
	 *
	 */
	public function enqueue_static() {
		wp_enqueue_script( 'ct_ultimate_gdpr_service_facebook_pixel', ct_ultimate_gdpr_url( 'assets/js/service-facebook-pixel.js' ) );
	}

	/**
	 * @return string
	 */
	protected function get_default_description() {
		return '';
	}

	/**
	 * @return array
	 */
	public function get_group_levels() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_group_levels", array( $this->get_group()->get_level_targetting() ) );
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