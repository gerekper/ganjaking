<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;


/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 1/2/20
 */

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly.

class Extension_Pseudo_Elements
{

	/*
		 * Instance of this class
		 */
	private static $instance = null;


	public function __construct()
	{

		// Add new controls to advanced tab globally
		add_action(
			"elementor/element/after_section_end",
			array($this, 'jltma_section_add_pseudo_elements_controls'),
			20,
			3
		);
	}


	public function jltma_section_add_pseudo_elements_controls($widget, $section_id, $args)
	{

		if ('section' !== $widget->get_name()) {
			return;
		}

		$target_sections = array('section_custom_css');

		if (!defined('ELEMENTOR_PRO_VERSION')) {
			$target_sections[] = 'section_custom_css_pro';
		}

		if (!in_array($section_id, $target_sections)) {
			return;
		}

		//			if( in_array( $widget->get_name(), array('section') ) ){
		//				return;
		//			}

		// Adds general background options to pseudo elements
		// ---------------------------------------------------------------------
		$widget->start_controls_section(
			'ma_el_section_background_pseudo',
			array(
				'label'     => MA_EL_BADGE . __(' Pseudo Elements (Developers)', MELA_TD),
				'tab'       => Controls_Manager::TAB_ADVANCED
			)
		);

		$widget->add_control(
			'background_pseudo_description',
			array(
				'raw'  => __('Adds background to pseudo elements like ::before and ::after selectors. (developers only)', MELA_TD),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor'
			)
		);


		//Before Settings
		$widget->add_control(
			'background_pseudo_before_heading',
			array(
				'label'     => __('Background ::before', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			)
		);

		$widget->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'background_pseudo_before',
				'types'    => array('classic', 'gradient'),
				'selector' => '{{WRAPPER}} > .elementor-container:before'
			)
		);

		$widget->add_responsive_control(
			'ma_el_pseudo_before_width',
			array(
				'label'      => __('Width', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%', 'vw'),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'step' => 1
					),
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 0,
						'step' => 1
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					)
				),
				'selectors' => array(
					'{{WRAPPER}} > .elementor-container:before' => 'width:{{SIZE}}{{UNIT}};'
				)
			)
		);

		$widget->add_responsive_control(
			'ma_el_pseudo_before_height',
			array(
				'label'      => __('Height', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%', 'vw'),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'step' => 1
					),
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 0,
						'step' => 1
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					)
				),
				'selectors' => array(
					'{{WRAPPER}} > .elementor-container:before' => 'height:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} > .elementor-container:after' => 'content: ""; min-height: inherit;', // Hack for IE11
				)
			)
		);



		$widget->add_control(
			'ma_el_pseudo_before_content',
			[
				'label'         => __('Content', MELA_TD),
				'type'          => Controls_Manager::TEXT,
				'default'       => '',
				'selectors'     => [
					"{{WRAPPER}} > .elementor-container:before"  => "content:'{{VALUE}}';"
				],
			]
		);




		// After Settings
		$widget->add_control(
			'background_pseudo_after_heading',
			array(
				'label'     => __('Background ::after', MELA_TD),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			)
		);

		$widget->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'background_pseudo_after',
				'types'    => array('classic', 'gradient'),
				'selector' => '{{WRAPPER}} > .elementor-container:after'
			)
		);

		$widget->add_control(
			'ma_el_pseudo_after_content',
			[
				'label'         => __('Content', MELA_TD),
				'type'          => Controls_Manager::TEXT,
				'default'       => '',
				'selectors'     => [
					"{{WRAPPER}} > .elementor-container:after"  => "content:'{{VALUE}}';"
				],
			]
		);

		$widget->add_responsive_control(
			'ma_el_pseudo_after_width',
			array(
				'label'      => __('Width', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%', 'vw'),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'step' => 1
					),
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 0,
						'step' => 1
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					)
				),
				'selectors' => array(
					'{{WRAPPER}} > .elementor-container:after' => 'width:{{SIZE}}{{UNIT}};'
				)
			)
		);

		$widget->add_responsive_control(
			'ma_el_pseudo_after_height',
			array(
				'label'      => __('Height', MELA_TD),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', '%', 'vw'),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'step' => 1
					),
					'%' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					),
					'em' => array(
						'min'  => 0,
						'step' => 1
					),
					'vw' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1
					)
				),
				'selectors' => array(
					'{{WRAPPER}} > .elementor-container:after' => 'height:{{SIZE}}{{UNIT}};'
				)
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

Extension_Pseudo_Elements::get_instance();
