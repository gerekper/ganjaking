<?php

/**
 * Class CT_Ultimate_GDPR_Service_Google_Adsense
 */
class CT_Ultimate_GDPR_Service_Google_Adsense extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_adsense-plugin/adsense-plugin.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_adsense-plugin/adsense-plugin.php', '__return_false' );
	}

	/**
	 * @return $this
	 */

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Google Adsense' );
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

	}

	/**
	 * @param $cookies
	 * @param bool $force
	 *
	 * @return mixed
	 */
	public function cookies_to_block_filter( $cookies, $force = false ) {

		$cookies_to_block = array();

		// only external cookies, so no point in blocking
		if ( $force ) {
			$cookies_to_block = array( '__gads', 'DSID', 'IDE', 'SAPISID', 'HSID', 'test_cookie' );
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
				"googlesyndication.com/pagead/js/adsbygoogle.js",
				"googleadservices.com/pagead/",
				"var google_conversion_id",
			);

		}

		$scripts_to_block = apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_script_blacklist", $scripts_to_block );

		if ( is_array( $scripts[ $this->get_group()->get_level_targetting() ] ) ) {
			$scripts[ $this->get_group()->get_level_targetting() ] = array_merge( $scripts[ $this->get_group()->get_level_targetting() ], $scripts_to_block );
		}

		return $scripts;
	}
}