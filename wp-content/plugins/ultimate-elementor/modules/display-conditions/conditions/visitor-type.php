<?php
/**
 * UAEL Display Conditions feature.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\DisplayConditions\Conditions;

use Elementor\Controls_Manager;
use UltimateElementor\Classes\UAEL_Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Day
 * contain all element of day condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Visitor_Type extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.34.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'visitor_type';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.34.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Visitor Type', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.34.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT,
			'default'     => 'new',
			'label_block' => true,
			'options'     => array(
				'new'       => __( 'First Time Visitor', 'uael' ),
				'returning' => __( 'Returning Visitor', 'uael' ),
			),
			'condition'   => $condition,

		);
	}

	/**
	 * Compare Condition value
	 *
	 * @since 1.34.0
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value ) {

		if ( ! isset( $_COOKIE['UAEVisitorData'] ) && ! isset( $_COOKIE['UAENewVisitor'] ) ) {

			wp_add_inline_script(
				'elementor-frontend',
				'jQuery( window ).on( "elementor/frontend/init", function() {

					var current_time = new Date().getTime();
	
					var uael_secure = ( document.location.protocol === "https:" ) ? "secure" : "";
					var visit_date = new Date( current_time + 1000 * 86400 * 365 ).toGMTString();
					var visit_date_expire = new Date( current_time + 86400 * 1000 ).toGMTString();
	
					document.cookie = "UAEVisitorData=enabled;expires=" + visit_date + "SameSite=Strict;" + uael_secure;
					document.cookie = "UAENewVisitor=enabled;expires=" + visit_date_expire + "SameSite=Strict;" + uael_secure;
	
				}); '
			);

		}

		$user_type = 'new';

		if ( isset( $_COOKIE['UAEVisitorData'] ) && ! isset( $_COOKIE['UAENewVisitor'] ) ) {
			$user_type = 'returning';
		}

		return UAEL_Helper::display_conditions_compare( $user_type, $value, $operator );
	}

}
