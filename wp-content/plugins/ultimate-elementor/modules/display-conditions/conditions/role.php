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
 * Class Role
 * contain all element of user role condition.
 *
 * @package UltimateElementor\Modules\DisplayConditions\Conditions
 */
class Role extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_key_name() {
		return 'role';
	}

	/**
	 * Get Condition Title
	 *
	 * @since 1.32.0
	 * @return string|void
	 */
	public function get_title() {
		return __( 'User Role', 'uael' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @since 1.32.0
	 * @param array $condition return key's.
	 * @return array|void
	 */
	public function get_repeater_control( array $condition ) {
		global $wp_roles;
		return array(
			'label'       => $this->get_title(),
			'show_label'  => false,
			'type'        => Controls_Manager::SELECT,
			'description' => sprintf( '<strong>%s</strong>%s', __( 'Note: ', 'uael' ), __( 'This condition applies only to logged in users.', 'uael' ) ),
			'default'     => 'subscriber',
			'label_block' => true,
			'options'     => $wp_roles->get_names(),
			'condition'   => $condition,
		);

	}

	/**
	 * Check condition
	 *
	 * @access public
	 *
	 * @param String $settings return settings.
	 * @param String $operator return relationship operator.
	 * @param String $value value.
	 * @since 1.32.0
	 */
	public function compare_value( $settings, $operator, $value ) {
		$user = wp_get_current_user();
		// if $user and $value is equal it return true.
		return UAEL_Helper::display_conditions_compare( is_user_logged_in() && in_array( $value, $user->roles, true ), true, $operator );
	}
}
