<?php

namespace MasterAddons\Inc\Controls;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;
use Elementor\Scheme_Color;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Custom transition group control
 * Date: 10/12/19
 */

class MA_Group_Control_Transition extends Group_Control_Base
{

	protected static $fields;
	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function __construct()
	{
		add_action('elementor/init', [$this, 'init'], 0);
	}

	public function init()
	{
		add_action('elementor/controls/controls_registered', [$this, 'register_controls']);
	}

	public function register_controls()
	{
		// Register control
		\Elementor\Plugin::instance()->controls_manager->add_group_control('ma-el-transition', new self());
	}


	public static function get_type()
	{
		return 'jltma-transitions';
	}

	public static function get_easings()
	{
		return [
			'linear' 		=> esc_html__('Linear', MELA_TD),
			'ease-in' 		=> esc_html__('Ease In', MELA_TD),
			'ease-out' 		=> esc_html__('Ease Out', MELA_TD),
			'ease-in-out' 	=> esc_html__('Ease In Out', MELA_TD)
		];
	}



	protected function init_fields()
	{
		$controls = [];

		$controls['property'] = [
			'label'			=> _x('Property', 'Transition Control', MELA_TD),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'all',
			'options'		=> [
				'all'		=> esc_html__('All', MELA_TD),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'transition-property: {{VALUE}}',
			],
		];

		$controls['easing'] = [
			'label'			=> _x('Easing', 'Transition Control', MELA_TD),
			'type' 			=> Controls_Manager::SELECT,
			'default' 		=> 'linear',
			'options'		=> self::get_easings(),
			'selectors' => [
				'{{SELECTOR}}' => 'transition-timing-function: {{VALUE}}'
			],
		];

		$controls['duration'] = [
			'label'			=> _x('Duration', 'Transition Control', MELA_TD),
			'type' 			=> Controls_Manager::NUMBER,
			'default' 		=> 0.3,
			'min' 			=> 0.05,
			'max' 			=> 2,
			'step' 			=> 0.05,
			'selectors' 	=> [
				'{{SELECTOR}}' => 'transition-duration: {{VALUE}}s;',
			],
			'separator' 	=> 'after',
		];

		return $controls;
	}


	protected function prepare_fields($fields)
	{

		array_walk(
			$fields,
			function (&$field, $field_name) {

				if (in_array($field_name, ['transition', 'popover_toggle'])) {
					return;
				}

				$field['condition']['transition'] = 'custom';
			}
		);

		return parent::prepare_fields($fields);
	}


	protected function get_default_options()
	{
		return [
			'popover' => [
				'starter_name' 	=> 'transition',
				'starter_title' => _x('Transition', 'Transition Control', MELA_TD),
			],
		];
	}
}
