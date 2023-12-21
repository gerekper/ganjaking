<?php
namespace Happy_Addons_Pro\Extension\Conditions;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Condition
 * @package Happy_Addons_Pro\Extension\Conditions
 */
abstract class Condition  {

	/**
	 * Get Condition Key
	 *
	 * @return string|void
	 */
	public function get_key_name() {}

	/**
	 * Get Condition Title
	 *
	 * @return string|void
	 */
	public function get_title() {}

	/**
	 * Get Repeater Control Field Value
	 *
	 * @param array $condition
	 * @return array|void
	 */
	public function get_repeater_control(array $condition) {}

	/**
	 * Compare Condition value
	 *
	 * @param $settings
	 * @param $operator
	 * @param $value
	 * @return bool|void
	 */
	public function compare_value ( $settings, $operator, $value ) {}

}
