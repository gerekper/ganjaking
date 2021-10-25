<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/2/20
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

class Extension_Entrance_Animation
{

	/*
	 * Instance of this class
	 */
	private static $instance = null;


	public function __construct()
	{

		// Add new controls to advanced tab globally
		add_action("elementor/element/after_section_end", array($this, 'jltma_section_add_transition_controls'), 18, 3);
	}


	public function jltma_section_add_transition_controls($widget, $section_id, $args)
	{

		// Anchor element sections
		$target_sections = array('section_custom_css');

		if (!defined('ELEMENTOR_PRO_VERSION')) {
			$target_sections[] = 'section_custom_css_pro';
		}

		if (!in_array($section_id, $target_sections)) {
			return;
		}

		// Adds transition options to all elements
		// ---------------------------------------------------------------------
		$widget->start_controls_section(
			'ma_el_section_common_inview_transition',
			array(
				'label'     => MA_EL_BADGE . __(' Entrance Animation', MELA_TD),
				'tab'       => Controls_Manager::TAB_ADVANCED
			)
		);

		$widget->add_control(
			'ma_el_animation_name',
			array(
				'label'   => __('Animation', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => Master_Addons_Helper::jltma_animation_options(),
				'default'            => '',
				'prefix_class'       => 'jltma-appear-watch-animation ',
				'label_block'        => false
			)
		);


		$widget->add_control(
			'ma_el_animation_duration',
			array(
				'label'     => __('Duration', MELA_TD) . ' (ms)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => '',
				'min'       => 0,
				'step'      => 100,
				'selectors'    => array(
					'{{WRAPPER}}' => 'animation-duration:{{SIZE}}ms;'
				),
				'condition' => array(
					'ma_el_animation_name!' => ''
				),
				'render_type' => 'template'
			)
		);

		$widget->add_control(
			'ma_el_animation_delay',
			array(
				'label'     => __('Delay', MELA_TD) . ' (ms)',
				'type'      => Controls_Manager::NUMBER,
				'default'   => '',
				'min'       => 0,
				'step'      => 100,
				'selectors' => array(
					'{{WRAPPER}}' => 'animation-delay:{{SIZE}}ms;'
				),
				'condition' => array(
					'ma_el_animation_name!' => ''
				)
			)
		);

		$widget->add_control(
			'ma_el_animation_easing',
			array(
				'label'   => __('Easing', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''                       =>  esc_html__('Default', MELA_TD),
					'initial'                =>  esc_html__('Initial', MELA_TD),

					'linear'                 =>  esc_html__('Linear', MELA_TD),
					'ease-in' 				 =>  esc_html__('Ease In', MELA_TD),
					'ease-out'               =>  esc_html__('Ease Out', MELA_TD),
					'0.19,1,0.22,1'          =>  esc_html__('Ease In Out', MELA_TD),

					'0.47,0,0.745,0.715'     =>  esc_html__('Sine In', MELA_TD),
					'0.39,0.575,0.565,1'     =>  esc_html__('Sine Out', MELA_TD),
					'0.445,0.05,0.55,0.95'   =>  esc_html__('Sine In Out', MELA_TD),

					'0.55,0.085,0.68,0.53'   =>  esc_html__('Quad In', MELA_TD),
					'0.25,0.46,0.45,0.94'    =>  esc_html__('Quad Out', MELA_TD),
					'0.455,0.03,0.515,0.955' =>  esc_html__('Quad In Out', MELA_TD),

					'0.55,0.055,0.675,0.19'  =>  esc_html__('Cubic In', MELA_TD),
					'0.215,0.61,0.355,1'     =>  esc_html__('Cubic Out', MELA_TD),
					'0.645,0.045,0.355,1'    =>  esc_html__('Cubic In Out', MELA_TD),

					'0.895,0.03,0.685,0.22'  =>  esc_html__('Quart In', MELA_TD),
					'0.165,0.84,0.44,1'      =>  esc_html__('Quart Out', MELA_TD),
					'0.77,0,0.175,1'         =>  esc_html__('Quart In Out', MELA_TD),

					'0.895,0.03,0.685,0.22'  =>  esc_html__('Quint In', MELA_TD),
					'0.895,0.03,0.685,0.22'  =>  esc_html__('Quint Out', MELA_TD),
					'0.895,0.03,0.685,0.22'  =>  esc_html__('Quint In Out', MELA_TD),

					'0.95,0.05,0.795,0.035'  =>  esc_html__('Expo In', MELA_TD),
					'0.19,1,0.22,1'          =>  esc_html__('Expo Out', MELA_TD),
					'1,0,0,1'                =>  esc_html__('Expo In Out', MELA_TD),

					'0.6,0.04,0.98,0.335'    =>  esc_html__('Circ In', MELA_TD),
					'0.075,0.82,0.165,1'     =>  esc_html__('Circ Out', MELA_TD),
					'0.785,0.135,0.15,0.86'  =>  esc_html__('Circ In Out', MELA_TD),

					'0.6,-0.28,0.735,0.045'  =>  esc_html__('Back In', MELA_TD),
					'0.175,0.885,0.32,1.275' =>  esc_html__('Back Out', MELA_TD),
					'0.68,-0.55,0.265,1.55'  =>  esc_html__('Back In Out', MELA_TD)
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'animation-timing-function:cubic-bezier({{VALUE}});'
				),
				'condition' => array(
					'ma_el_animation_name!' => ''
				),
				'default'      => '',
				'return_value' => ''
			)
		);

		$widget->add_control(
			'ma_el_animation_count',
			array(
				'label'   => esc_html__('Repeat Count', MELA_TD),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					''  => esc_html__('Default', MELA_TD),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'infinite' => esc_html__('Infinite', MELA_TD)
				),
				'selectors' => array(
					'{{WRAPPER}}' => 'animation-iteration-count:{{VALUE}};opacity:1;' // opacity is required to prevent flick between repetitions
				),
				'condition' => array(
					'ma_el_animation_name!' => ''
				),
				'default'      => ''
			)
		);

		$widget->end_controls_section();
	}


	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Extension_Entrance_Animation::get_instance();
