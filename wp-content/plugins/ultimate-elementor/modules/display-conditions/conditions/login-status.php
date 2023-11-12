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
 * Class Login_status
 * contain all element of login status condition
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Login_Status extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'login_status';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Login Status', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
			return array(
				'label'       => $this->get_title(),
				'show_label'  => false,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'login',
				'label_block' => true,
				'options'     => array(
					'login' => __( 'Logged In', 'uael' ),
				),
				'condition'   => $condition,
			);
	}

	/**
	 * Compare Condition value
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @return bool|void
	 * @since 1.32.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		return UAEL_Helper::display_conditions_compare( is_user_logged_in(), true, $operator );
	}
}
