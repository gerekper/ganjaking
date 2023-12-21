<?php
namespace Happy_Addons_Pro\Extension\Conditions;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Login_status
 * contain all element of login status condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
class Login_status  extends Condition {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {
		return 'login_status';
	}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {
		return __( 'Login Status', 'happy-addons-pro' );
	}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {
			return [
				'label' 		=> $this->get_title(),
				'show_label' 	=> false,
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'login',
				'label_block' 	=> true,
				'options' 		=> [
					'login' 		=> __( 'Logged In', 'happy-addons-pro' ),
				],
				'condition' 		=> $condition,
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
		return hapro_compare( is_user_logged_in(), true, $operator );
	}
}
