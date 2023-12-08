<?php
use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
class Theplus_Glass_Morphism extends Elementor\Widget_Base {
	public function __construct() {
		$theplus_options=get_option('theplus_options');
		$plus_extras=theplus_get_option('general','extras_elements');		
		
		if((isset($plus_extras) && empty($plus_extras) && empty($theplus_options)) || (!empty($plus_extras) && in_array('plus_glass_morphism',$plus_extras))){
			
			add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'tp_glass_morphism_controls' ], 10, 2 );
			add_action( 'elementor/element/column/_section_responsive/after_section_end', [ $this, 'tp_glass_morphism_controls' ], 10, 2 );
			add_action( 'elementor/element/common/section_custom_css_pro/after_section_end', [ $this, 'tp_glass_morphism_controls' ], 10, 2 );
			
			$experiments_manager = Plugin::$instance->experiments;		
			if($experiments_manager->is_feature_active( 'container' )){
				add_action( 'elementor/element/container/section_layout/after_section_end', [ $this, 'tp_glass_morphism_controls' ], 10, 2  );
			}

			add_action( 'elementor/frontend/before_render', [ $this, 'tp_glass_morphism_before_render'], 10, 1 );
		}		
	}
	
	public function get_name() {
		return 'plus-glass-morphism';
	}
	
	public function tp_glass_morphism_controls($element) {		
		$element->start_controls_section(
			'plus_glass_morphism_section',
			[
				'label' => esc_html__( 'Plus Extras : Glass Morphism', 'theplus' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);
		$element->add_control(
			'scwbf_options',
			[
				'label' => esc_html__( 'Glass Morphism', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
			]
		);
		$element->add_control(
			'scwbf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition' => [
					'scwbf_options' => 'yes',
				],
			]
		);
		$element->add_control(
			'scwbf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} > .elementor-container,{{WRAPPER}} > .elementor-column-wrap,{{WRAPPER}} > .elementor-widget-wrap,{{WRAPPER}} > .elementor-widget-container,{{WRAPPER}}.e-container,{{WRAPPER}}.e-con' => '-webkit-backdrop-filter:grayscale({{scwbf_grayscale.SIZE}})  blur({{scwbf_blur.SIZE}}{{scwbf_blur.UNIT}}) !important;backdrop-filter:grayscale({{scwbf_grayscale.SIZE}})  blur({{scwbf_blur.SIZE}}{{scwbf_blur.UNIT}}) !important;',
				 ],
				 'condition' => [
					'scwbf_options' => 'yes',
				],
			]
		);
		$element->end_controls_section();
	}
	
	public function tp_glass_morphism_before_render($element) {		
		$settings = $element->get_settings();
		//$settings = $element->get_settings_for_display();
				
	}
}