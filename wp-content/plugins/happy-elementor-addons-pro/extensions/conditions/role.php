<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Role
 * contain all element of user role condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Role  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'role';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'User Role', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
		global $wp_roles;
		return [
			'label' 		=> $this->get_title(),
			'show_label' 	=> false,
			'type' 			=> Controls_Manager::SELECT,
			'description' 	=> sprintf('<strong>%s</strong>%s',__( 'Note: ', 'happy-addons-pro' ),__( 'This condition applies only to logged in users.', 'happy-addons-pro' )),
			'default' 		=> 'subscriber',
			'label_block' 	=> true,
			'options' 		=> $wp_roles->get_names(),
			'condition'	=> $condition,
		];

	}

	/**
	 * Compare Condition value
	 *
	 * @param $settings
	 * @param $operator
	 * @param $value
	 * @return bool|void
	 */
	public function compare_value( $settings, $operator, $value) {
		$user = wp_get_current_user();
		//if $user and $value is equal it return true
		return hapro_compare( is_user_logged_in() && in_array( $value, $user->roles ), true, $operator );
	}
}
