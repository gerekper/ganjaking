<?php 
/*
Widget Name: Design Tool
Description: Design Tool
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

use TheplusAddons\Theplus_Element_Load;
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


class ThePlus_Design_Tool extends Widget_Base {
		
	public function get_name() {
		return 'tp-design-tool';
	}

    public function get_title() {
        return __('Design Tool', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-align-justify theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

    protected function register_controls() {		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Design Tool', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'design_tool_opt',
			[
				'label' => esc_html__( 'Design Tool Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'grid_stystem',
				'options' => [
					'grid_stystem'  => esc_html__( 'Grid System', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'grid_stystem_opt',
			[
				'label' => esc_html__( 'Grid Stystem', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'gs_default',
				'options' => [
					'gs_default'  => esc_html__( 'Default', 'theplus' ),
					'gs_custom'  => esc_html__( 'Custom', 'theplus' ),
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
				],
			]
		);
		$this->add_control(
			'direction',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'ltr',				
				'options' => [
					'ltr'  => esc_html__( 'Left to Right', 'theplus' ),
					'ttb'  => esc_html__( 'Top to Bottom', 'theplus' ),
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'tp_grid_cont_max_width',
			[
				'label' => esc_html__( 'Maximum Width', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
				'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1140,
				],
				'separator' => 'before',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_cont_max_width: {{SIZE}}{{UNIT}};',				                    
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
				'render_type' => 'ui',
			]
		);
		$this->add_responsive_control(
			'tp_grid_columns',
			[
				'label' => esc_html__( 'Grid System Columns', 'theplus' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 150,
				'step'      => 1,
				'default'   => 12,
				'separator' => 'before',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_columns: {{VALUE}};',
				],	
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
			]
		);
		$this->add_control(
			'tp_grid_color',
			[
				'label' => esc_html__( 'Grid System Columns Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(128, 114, 252, 0.25)',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_color: {{VALUE}};',
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
			]
		);
		$this->add_responsive_control(
			'tp_grid_alley',
			[
				'label' => esc_html__( 'Alley Space', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'em' => [
						'min'  => 0,
						'max'  => 20,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'separator' => 'before',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_alley: {{SIZE}}{{UNIT}};',
					],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
				'render_type' => 'ui',
			]
		);		
		$this->add_control(
			'tp_grid_alley_color',
			[
				'label' => esc_html__( 'Alley Background Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_alley_color: {{VALUE}};',
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
				],
			]
		);
		$this->add_responsive_control(
			'tp_grid_left_right_offset',
			[
				'label' => esc_html__( 'Offset', 'theplus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 1,
					],
					'em' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'separator' => 'before',
				'selectors' => [
					'html.elementor-html,html' => '--tp_grid_left_right_offset: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'design_tool_opt' => 'grid_stystem',
					'grid_stystem_opt' => 'gs_custom',
					'direction' => 'ltr',
				],
				'render_type' => 'ui',
			]
		);
		$this->add_control(
			'tp_grid_front_side',
			[
				'label' => esc_html__( 'Display Grid System on Front', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'selectors' => [
					'html' => 'content: "";',
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
	}
	
	protected function render() {
	$settings = $this->get_settings_for_display();
	$design_tool_opt=$settings['design_tool_opt'];
	$grid_stystem_opt=$settings['grid_stystem_opt'];
		$design_tool='';
		if(!empty($design_tool_opt) && $design_tool_opt=='grid_stystem'){
			$design_tool .='<style>';
				if(!empty($grid_stystem_opt) && $grid_stystem_opt=='gs_default'){
					$design_tool .=':root{--tp_grid_repeate-columns-width: calc(100% / var(--tp_grid_columns));--tp_grid_column-width: calc((100% / var(--tp_grid_columns)) - var(--tp_grid_alley));--tp_grid_background-width-opt: calc(100% + var(--tp_grid_alley));--tp_grid_background-col-opt: repeating-linear-gradient(to right,var(--tp_grid_color), var(--tp_grid_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_repeate-columns-width));}
					html.elementor-html, html {--tp_grid_cont_max_width: 1140px;--tp_grid_columns: 12;--tp_grid_color: rgba(128, 114, 252, 0.25);--tp_grid_alley: 30px;
					--tp_grid_alley_color: transparent;--tp_grid_left_right_offset:0px;}
					@media (max-width: 1024px){html.elementor-html, html {--tp_grid_columns: 6;--tp_grid_alley:15px;}}
					@media (max-width: 767px){html.elementor-html, html {--tp_grid_columns: 4;--tp_grid_alley:10px;}}
					';
				}else if(!empty($grid_stystem_opt) && $grid_stystem_opt=='gs_custom'){
					$design_tool .=':root {--tp_grid_repeate-columns-width: calc(100% / var(--tp_grid_columns));--tp_grid_column-width: calc((100% / var(--tp_grid_columns)) - var(--tp_grid_alley));--tp_grid_background-width-opt: calc(100% + var(--tp_grid_alley));--tp_grid_background-col-opt: repeating-linear-gradient(';
					$direction ='';
					if(!empty($settings['direction']) && $settings['direction']=='ltr'){
						$direction ='to right,';
					}else if(!empty($settings['direction']) && $settings['direction']=='ttb'){
						$direction ='';
					}
					$design_tool .=$direction . 'var(--tp_grid_color), var(--tp_grid_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_column-width), var(--tp_grid_alley_color) var(--tp_grid_repeate-columns-width) );}';
				}
				if(!empty($settings['tp_grid_front_side']) && $settings['tp_grid_front_side']=='yes'){
					$design_tool .='html.elementor-html::before,html::before {';
				}else{
					$design_tool .='html.elementor-html::before {';
				}
				
				$design_tool .='content: "" !important;position:fixed;pointer-events:none;top:0;right:0;bottom:0;left:0;margin-right:auto;margin-left:auto;width: calc(100% - (2 * var(--tp_grid_left_right_offset)));max-width: var(--tp_grid_cont_max_width);min-height: 100vh;background-image: var(--tp_grid_background-col-opt);background-size: var(--tp_grid_background-width-opt) 100%;z-index:999;}';
			$design_tool .='</style>';
			
		}
		echo $design_tool;
		
	}
    protected function content_template() {	
    }
}