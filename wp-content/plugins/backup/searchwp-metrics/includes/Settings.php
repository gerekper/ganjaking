<?php

namespace SearchWP_Metrics;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

class Settings {

	private $option_names = array(
		'timeframe_before',
		'timeframe_after',
		'engines',
		'blocklists',
		'clear_data_on_uninstall',
		'click_tracking_buoy',
		'utf8mb4',
		'version',
		'last_engines',
	);

	/**
	 * Settings constructor.
	 */
	function __construct() {}

	public function get_option_name( $option = '' ) {
		return in_array( $option, $this->option_names, true ) ? SEARCHWP_METRICS_PREFIX . $option : null;
	}

	public function get_option_names() {
		$names = array();

		foreach ( $this->option_names as $name ) {
			$names[] = $this->get_option_name( $name );
		}

		return $names;
	}

	public function get_option( $option ) {
		return get_option( $this->get_option_name( $option ) );
	}

	public function set_option( $option, $value, $autoload = 'NO' ) {
		$option_name = $this->get_option_name( $option );

		if ( ! is_null( $option_name ) ) {
			update_option( $option_name, $value, $autoload );
		}
	}
}
