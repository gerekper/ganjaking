<?php 
/*
Widget Name: TP Shape Divider
Description: Separator of Row and Column shape divider
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Tp_Shape_Divider extends Widget_Base {
		
	public function get_name() {
		return 'tp-shape-divider';
	}

    public function get_title() {
        return esc_html__('Advanced Separators', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-cloud theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'shape_divider',
			[
				'label' => esc_html__( 'Shape Divider', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'wave'  => esc_html__( 'Wave', 'theplus' ),
					'shape-1'  => esc_html__( 'Shape 1', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'shape_divider_type',
			[
				'label' => esc_html__( 'Row/Column', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => [
					'row'  => esc_html__( 'Section/Row', 'theplus' ),
					'column'  => esc_html__( 'Column', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'divider_position',
			[
				'label' => esc_html__( 'Divider Position', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'top' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
				],
				'default' => 'bottom',
				'toggle' => false,
				'label_block' => false,
				'condition' => [
					'shape_divider!' => 'none',
					'shape_divider_type' => 'row',
				],	
			]
		);
		$this->add_control(
			'divider_position_column',
			[
				'label' => esc_html__( 'Divider Position', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
				],
				'default' => 'right',
				'toggle' => false,
				'label_block' => false,
				'condition' => [
					'shape_divider!' => 'none',
					'shape_divider_type' => 'column',
				],	
			]
		);
		$this->add_control(
			'position_flip',
			[
				'label' => esc_html__( 'Flip Divider', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Flip', 'theplus' ),
				'label_off' => esc_html__( 'Default', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_responsive_control(
			'divider_shape_height',
			[
				'label' => esc_html__( 'Shape Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'selectors' => [
					'.shape{{ID}}.tp-plus-shape-divider.shape-wave,.shape{{ID}}.tp-plus-shape-divider .wave-items' => 'height: {{SIZE}}{{UNIT}};',					
				],
				'condition' => [
					'shape_divider' => 'wave',
					'shape_divider_type' => 'row',
				],
			]
		);
		
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'shape_color_type',
			[
				'label' => __( 'Shape Color Type', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => __( 'Solid', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => __( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'default' => 'solid',
				'toggle' => false,
			]
		);
		$repeater->add_control(
			'shape_color',[
				'label' => esc_html__('Shape Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#8072fc',
				'selectors' => [
					'.tp-plus-shape-divider .wave-items{{CURRENT_ITEM}}.classic-color path' => 'fill: {{VALUE}}'
				],				
			]
		);
		$repeater->add_control(
			'shape_color_2',[
				'label' => esc_html__('Shape Color 2', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#6fc784',
				'condition' => [
					'shape_color_type' => 'gradient',
				],
			]
		);
		$repeater->add_control(
			'gradient_x1',
			[	
				'label' => esc_html__( 'Gradient X1', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'condition' => [
					'shape_color_type' => 'gradient',
				],
			]
		);
		$repeater->add_control(
			'gradient_x2',
			[	
				'label' => esc_html__( 'Gradient X2', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 70,
				],
				'condition' => [
					'shape_color_type' => 'gradient',
				],
			]
		);
		$repeater->add_control(
			'gradient_y1',
			[	
				'label' => esc_html__( 'Gradient Y1', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'condition' => [
					'shape_color_type' => 'gradient',
				],
			]
		);
		$repeater->add_control(
			'gradient_y2',
			[	
				'label' => esc_html__( 'Gradient Y2', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'condition' => [
					'shape_color_type' => 'gradient',
				],
			]
		);
		$repeater->add_control(
			'shape_height',
			[	
				'label' => esc_html__( 'Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 80,
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'shape_bones',
			[	
				'label' => esc_html__( 'Bones', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 4,
				],
			]
		);
		$repeater->add_control(
			'shape_amplitude',
			[	
				'label' => esc_html__( 'Amplitude', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => '',
				'range' => [
					'' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => '',
					'size' => 60,
				],
			]
		);
		$repeater->add_control(
			'shape_speed',
			[	
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,				
				'range' => [
					'' => [
						'min' => 0,
						'max' => 1.5,
						'step' => 0.01,
					],
				],
				'default' => [
					'size' => 0.15,
				],
			]
		);
		$this->add_control(
            'wave_loop_shape',
            [
				'label' => esc_html__( 'Wave Loop', 'theplus' ),
                'type' => Controls_Manager::REPEATER,				
                'default' => [
                    [
                        'shape_color' => '#8072fc',
                        'shape_height' => [ 'size' => '80' ],
                        'shape_bones' =>  [ 'size' => '4' ],
                        'shape_amplitude' => [ 'size' => '60' ],
                        'shape_speed' => [ 'size' => '0.15' ],
                    ],
					[
                        'shape_color' => 'rgba(128, 114, 252, 0.8)',
						'shape_height' => [ 'size' => '60' ],
                        'shape_bones' =>  [ 'size' => '3' ],
                        'shape_amplitude' => [ 'size' => '40' ],
                        'shape_speed' => [ 'size' => '0.25' ],
					],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ shape_color }}}',
				'condition' => [
					'shape_divider' => 'wave',
				],				
            ]
        );
		$this->add_control(
			'shape_fill_color',[
				'label' => esc_html__('Fill Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '#8072fc',
				'selectors' => [
					'.shape{{ID}}.shape-shape-1 path' => 'stroke: {{VALUE}};fill: {{VALUE}}'
				],
				'condition' => [
					'shape_divider' => 'shape-1',
				],
			]
		);
		$this->add_control(
			'icon_font_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'shape_divider' => 'shape-1',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-angle-down',
				'condition' => [
					'shape_divider' => 'shape-1',
					'icon_font_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-angle-down',
					'library' => 'solid',
				],
				'condition' => [
					'shape_divider' => 'shape-1',
					'icon_font_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'shape_divider' => 'shape-1',
					'icon_font_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'icon_color',[
				'label' => esc_html__('Icon Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.shape{{ID}}.shape-shape-1 .shape-1-icon' => 'color: {{VALUE}}',
					'.shape{{ID}}.shape-shape-1 .shape-1-icon svg' => 'fill: {{VALUE}}'
				],
				'condition' => [
					'shape_divider' => 'shape-1',
				],
			]
		);
		$this->add_control(
			'icon_size',
			[	
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,				
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'.shape{{ID}}.shape-shape-1 .shape-1-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'.shape{{ID}}.shape-shape-1 .shape-1-icon svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'shape_divider' => 'shape-1',
				],
			]
		);
		$this->add_control(
			'shape_1_url',
			[
				'label' => esc_html__( 'Url', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => true,
					'nofollow' => true,
				],
				'dynamic' => ['active'   => true,],
				'separator' => 'before',
				'condition' => [
					'shape_divider' => 'shape-1',
				],
			]
		);
		$this->end_controls_section();
	}
	
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$shape_divider = $settings["shape_divider"];
		$divider_position = (!empty($settings["divider_position"])) ? $settings["divider_position"] : 'bottom';
		if(!empty($settings["shape_divider_type"]) && $settings["shape_divider_type"]=='column'){
			$divider_position = (!empty($settings["divider_position_column"])) ? $settings["divider_position_column"] : 'right';
		}
		
		$uid='shape'.$this->get_id();
		$data_class = 'shape-'.esc_attr($shape_divider);
		$data_class .= ' '.esc_attr($uid);
		$data_class .= ' shape-'.esc_attr($divider_position);
		if(!empty($settings["position_flip"]) && $settings["position_flip"]=='yes'){
			$data_class .= ' flip-'.esc_attr($divider_position);
		}
		
		$data_attr ='data-id="'.esc_attr($uid).'"';
		$data_attr .=' data-position="'.esc_attr($divider_position).'"';
		$data_attr .=' data-section-type="'.esc_attr($settings["shape_divider_type"]).'"';
		
		$output ='<div id="'.esc_attr($uid).'" class="tp-plus-shape-divider '.esc_attr($data_class).' " '.$data_attr.'>';
			if($shape_divider=='wave' && !empty($settings['wave_loop_shape'])){
				
				foreach($settings['wave_loop_shape'] as $item) {
					$shape_color =(!empty($item['shape_color'])) ? 'data-color="'.esc_attr($item['shape_color']).'"' : '#8072fc';
					$shape_height =(isset($item['shape_height']["size"])) ? 'data-height="'.esc_attr($item['shape_height']["size"]).'"' : '80';
					$shape_bones =(isset($item['shape_bones']["size"])) ? 'data-bones="'.esc_attr($item['shape_bones']["size"]).'"' : '4';
					$shape_amplitude =(isset($item['shape_amplitude']["size"])) ? 'data-amplitude="'.esc_attr($item['shape_amplitude']["size"]).'"' : '60';
					$shape_speed =(isset($item['shape_speed']["size"])) ? 'data-speed="'.esc_attr($item['shape_speed']["size"]).'"' : 0.15;
					
					$wave_gradient = $wave_gradient_attr = ''; 
					$wave_class = "classic-color";
					if(!empty($item['shape_color_type']) && $item['shape_color_type']=='gradient'){
						$color_1 = (!empty($item['shape_color'])) ? $item['shape_color'] : '#8072fc';
						$color_2 = (!empty($item['shape_color_2'])) ? $item['shape_color_2'] : '#6fc784';
						$gradient_x1 = (isset($item['gradient_x1']["size"])) ? $item['gradient_x1']["size"].$item['gradient_x1']["unit"] : '0';
						$gradient_x2 = (isset($item['gradient_x2']["size"])) ? $item['gradient_x2']["size"].$item['gradient_x2']["unit"] : '70%';
						$gradient_y1 = (isset($item['gradient_y1']["size"])) ? $item['gradient_y1']["size"].$item['gradient_y1']["unit"] : '0';
						$gradient_y3 = (isset($item['gradient_y2']["size"])) ? $item['gradient_y2']["size"].$item['gradient_y2']["unit"] : '50%';
						$wave_gradient = '<linearGradient id="gradient_' . esc_attr($item['_id']) . '" x1="'.esc_attr($gradient_x1).'" x2="'.esc_attr($gradient_x2).'" y1="'.esc_attr($gradient_y1).'" y2="'.esc_attr($gradient_y3).'"><stop offset="0%" stop-color="'.esc_attr($color_1).'"  /><stop offset="100%" stop-color="'.esc_attr($color_2).'" /></linearGradient>';
						$wave_gradient_attr = 'data-gradient-id="#gradient_' . esc_attr($item['_id']) . '"';
						$wave_class = "gradient-color";
					}
					$output .='<svg class="wave-items '.esc_attr($wave_class).' elementor-repeater-item-' . esc_attr($item['_id']) . '" width="100%" height="'.esc_attr($item['shape_height']["size"]).'" version="1.1" xmlns="http://www.w3.org/2000/svg" class="wave" '.$shape_color.' '.$shape_height.' '.$shape_bones.' '.$shape_amplitude.' '.$shape_speed.' '.$wave_gradient_attr.'><defs>'.$wave_gradient.'</defs><path id="wave-' . esc_attr($item['_id']) . '" d="" /></svg>';
				}
			}
			
			//shape-1			
			if($shape_divider=='shape-1'){
				if($settings["icon_font_style"]=='font_awesome'){
					$icons=$settings["icon_fontawesome"];
				}else if($settings["icon_font_style"]=='font_awesome_5'){
					ob_start();
					\Elementor\Icons_Manager::render_icon( $settings['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
					$icons = ob_get_contents();
					ob_end_clean();
				}else if($settings["icon_font_style"]=='icon_mind'){
					$icons=$settings["icons_mind"];				
				}else{
					$icons='fa fa-angle-down';
				}
				if(!empty($settings["icon_font_style"]) && $settings["icon_font_style"]=='font_awesome_5' && !empty($settings['icon_fontawesome_5'])){
					$service_icon = '<span class="shape-1-icon">'.$icons.'</span>';
				}else{
					$service_icon = '<i class=" '.esc_attr($icons).' shape-1-icon"></i>';
				}
				
				
				if ( ! empty( $settings['shape_1_url']['url'] ) ) {
					$this->add_render_attribute( 'shape_link', 'href', $settings['shape_1_url']['url'] );
					if ( $settings['shape_1_url']['is_external'] ) {
						$this->add_render_attribute( 'shape_link', 'target', '_blank' );
					}
					if ( $settings['shape_1_url']['nofollow'] ) {
						$this->add_render_attribute( 'shape_link', 'rel', 'nofollow' );
					}
					$this->add_render_attribute( 'shape_link', 'class', 'shape-1-url' );
				}
				
				$output .='<svg class="shape-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="44px" height="200px" viewBox="0 0 44 200" preserveAspectRatio="none"><g><path fill="rgba(255,255,255,1)" stroke="#ffffff" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" opacity="1" d="M 45.703505156528365 200.19885384998346 C 45.73301315307617 190.79171752929688 45.365841356073965 101.6338238086374 45.662750244140625 0.25295501947402954 C 45.52727381388346 10.878458460172016 41.63068771362305 22.985551555951435 34.041473388671875 36.57423400878906 C 24.181148529052734 56.518985748291016 1.116410493850708 75.28781127929688 0.9567615985870361 100.19087982177734 C 1.1245900392532349 125.75543975830078 23.82019805908203 145.14585876464844 34.17644691467285 165.19732666015625 C 41.07911682128906 178.5620574951172 45.73301315307617 190.79171752929688 45.703505156528365 200.19885384998346 Z" transform="matrix(1 0 0 1 -0.729369 -0.220138)"></path></g></svg>';
				$output .=$service_icon;
				$output .='<a '.$this->get_render_attribute_string( "shape_link" ).'></a>';
			}
		$output .='</div>';
			
		echo $output;
	}
	
    protected function content_template() {
	
    }

}
