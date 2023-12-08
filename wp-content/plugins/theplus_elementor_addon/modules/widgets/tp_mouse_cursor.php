<?php 
/*
Widget Name: Mouse Cursor
Description: 
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Mouse_Cursor extends Widget_Base {
		
	public function get_name() {
		return 'tp-mouse-cursor';
	}

    public function get_title() {
        return esc_html__('Mouse Cursor', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-mouse-pointer theplus_backend_icon';
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
			'mc_wid_Note',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note: This widget works only at frontend.',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'cursor_effect',
			[
				'label' => esc_html__( 'Cursor Area', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mc-column',
				'options' => [
					'mc-column'  => esc_html__( 'Column', 'theplus' ),
					'mc-section'  => esc_html__( 'Section', 'theplus' ),
					'mc-container'  => esc_html__( 'Container', 'theplus' ),
					'mc-widget'  => esc_html__( 'Widget', 'theplus' ),
					'mc-body'  => esc_html__( 'Body', 'theplus' ),
				],				
			]
		);	
		$this->add_control(
			'cursor_effect_Note',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Cursor will be changed to in the area of the exact above widget in the Editor.',
				'content_classes' => 'tp-widget-description',
				'condition' => [					
					'cursor_effect' => 'mc-widget',
				],	
			]
		);
		$this->add_control(
			'mouse_cursor_type',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mouse-cursor-icon',
				'options' => [
					'mouse-cursor-icon'  => esc_html__( 'Cursor Icon', 'theplus' ),
					'mouse-follow-image' => esc_html__( 'Follow Image', 'theplus' ),
					'mouse-follow-text' => esc_html__( 'Follow Text', 'theplus' ),
				    'mouse-follow-circle' => esc_html__( 'Follow Circle', 'theplus' ),
				],
			]
		);	
		$this->add_control(
			'Icon_cursor_type',
			[
				'label' => esc_html__( 'Icon Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cursor-Icon-custom',
				'options' => [
					'cursor-Icon-predefine'  => esc_html__( 'Predefined', 'theplus' ),
					'cursor-Icon-custom' => esc_html__( 'Custom', 'theplus' ),
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-cursor-icon',
				],				
			]
		);
		$this->add_control(
			'mc_cursor_icon_symbol',
			[
				'label' => esc_html__( 'Select', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'crosshair',
				'options' => [
					'none' => esc_html__( 'none ','theplus' ),
					'alias' => esc_html__( 'alias ','theplus' ),
					'all-scroll' => esc_html__( 'all-scroll ','theplus' ),
					'auto' => esc_html__( 'auto ','theplus' ),
					'cell' => esc_html__( 'cell','theplus' ),
					'context-menu' => esc_html__( 'context-menu ','theplus' ),
					'col-resize' => esc_html__( 'col-resize ','theplus' ),
					'copy' => esc_html__( 'copy ','theplus' ),
					'crosshair' => esc_html__( 'crosshair ','theplus' ),
					'default' => esc_html__( 'default ','theplus' ),
					'e-resize' => esc_html__( 'e-resize ','theplus' ),
					'ew-resize' => esc_html__( 'ew-resize ','theplus' ),
					'grab' => esc_html__( 'grab ','theplus' ),
					'grabbing' => esc_html__( 'grabbing ','theplus' ),
					'help' => esc_html__( 'help ','theplus' ),
					'move' => esc_html__( 'move ','theplus' ),
					'n-resize' => esc_html__( 'n-resize ','theplus' ),
					'ne-resize' => esc_html__( 'ne-resize ','theplus' ),
					'nesw-resize' => esc_html__( 'nesw-resize ','theplus' ),
					'ns-resize' => esc_html__( 'ns-resize ','theplus' ),
					'nw-resize' => esc_html__( 'nw-resize ','theplus' ),
					'nwse-resize' => esc_html__( 'nwse-resize ','theplus' ),
					'no-drop' => esc_html__( 'no-drop ','theplus' ),
					'not-allowed' => esc_html__( 'not-allowed ','theplus' ),
					'pointer' => esc_html__( 'pointer ','theplus' ),
					'progress' => esc_html__( 'progress ','theplus' ),
					'row-resize' => esc_html__( 'row-resize ','theplus' ),
					's-resize' => esc_html__( 's-resize ','theplus' ),
					'se-resize' => esc_html__( 'se-resize ','theplus' ),
					'sw-resize' => esc_html__( 'sw-resize ','theplus' ),
					'text' => esc_html__( 'text ','theplus' ),
					'w-resize' => esc_html__( 'w-resize ','theplus' ),
					'wait' => esc_html__( 'wait ','theplus' ),
					'zoom-in' => esc_html__( 'zoom-in ','theplus' ),
					'zoom-out' => esc_html__( 'zoom-out ','theplus' ),
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-cursor-icon',
					'Icon_cursor_type' => 'cursor-Icon-predefine',
				],
			]
		);
		$this->add_control(
			'circle_cursor_type',
			[
				'label' => esc_html__( 'Pointer Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cursor-predefine',
				'options' => [
					'cursor-predefine'  => esc_html__( 'Predefined', 'theplus' ),
					'cursor-custom' => esc_html__( 'Custom', 'theplus' ),
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',
				],
				
			]
		);
		$this->add_control(
			'mc_cursor_symbol',
			[
				'label' => esc_html__( 'Select Pointer', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'crosshair',
				'options' => [
					'none' => esc_html__( 'none ','theplus' ),
					'alias' => esc_html__( 'alias ','theplus' ),
					'all-scroll' => esc_html__( 'all-scroll ','theplus' ),
					'auto' => esc_html__( 'auto ','theplus' ),
					'cell' => esc_html__( 'cell','theplus' ),
					'context-menu' => esc_html__( 'context-menu ','theplus' ),
					'col-resize' => esc_html__( 'col-resize ','theplus' ),
					'copy' => esc_html__( 'copy ','theplus' ),
					'crosshair' => esc_html__( 'crosshair ','theplus' ),
					'default' => esc_html__( 'default ','theplus' ),
					'e-resize' => esc_html__( 'e-resize ','theplus' ),
					'ew-resize' => esc_html__( 'ew-resize ','theplus' ),
					'grab' => esc_html__( 'grab ','theplus' ),
					'grabbing' => esc_html__( 'grabbing ','theplus' ),
					'help' => esc_html__( 'help ','theplus' ),
					'move' => esc_html__( 'move ','theplus' ),
					'n-resize' => esc_html__( 'n-resize ','theplus' ),
					'ne-resize' => esc_html__( 'ne-resize ','theplus' ),
					'nesw-resize' => esc_html__( 'nesw-resize ','theplus' ),
					'ns-resize' => esc_html__( 'ns-resize ','theplus' ),
					'nw-resize' => esc_html__( 'nw-resize ','theplus' ),
					'nwse-resize' => esc_html__( 'nwse-resize ','theplus' ),
					'no-drop' => esc_html__( 'no-drop ','theplus' ),
					'not-allowed' => esc_html__( 'not-allowed ','theplus' ),
					'pointer' => esc_html__( 'pointer ','theplus' ),
					'progress' => esc_html__( 'progress ','theplus' ),
					'row-resize' => esc_html__( 'row-resize ','theplus' ),
					's-resize' => esc_html__( 's-resize ','theplus' ),
					'se-resize' => esc_html__( 'se-resize ','theplus' ),
					'sw-resize' => esc_html__( 'sw-resize ','theplus' ),
					'text' => esc_html__( 'text ','theplus' ),
					'w-resize' => esc_html__( 'w-resize ','theplus' ),
					'wait' => esc_html__( 'wait ','theplus' ),
					'zoom-in' => esc_html__( 'zoom-in ','theplus' ),
					'zoom-out' => esc_html__( 'zoom-out ','theplus' ),
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-predefine',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'cursor: {{VALUE}};',
				],
				
			]
		);
		$this->add_control(
			'circle_style',
			[
				'label' => esc_html__( 'Follow Circle Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mc-cs1',
				'options' => [
					'mc-cs1'  => esc_html__( 'Border Cursor', 'theplus' ),
					'mc-cs2' => esc_html__( 'Progress Cursor (Body)', 'theplus' ),
					'mc-cs3' => esc_html__( 'Blend Cursor', 'theplus' ),
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
				],
			]
		);
		$this->add_control(
			'circle_style_mc_cs2_nottice',
			[				
				'type' => Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'Note : Style-2 only works for Body option selected in the Cursor Area.', 'theplus' ),
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'mc_pointer_icon',
			[
				'label' => esc_html__( 'Cursor Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => ['url' => '',],
				'separator' => 'before',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => '===', 'value' => 'mouse-cursor-icon'],
								['name' => 'Icon_cursor_type', 'operator' => '===', 'value' => 'cursor-Icon-custom']
							]
						],
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => '===', 'value' => 'mouse-follow-image']
							]
						],
					]
				],
			]
		);
		$this->add_control(
			'mc_pointer_icon_cir_cst',
			[
				'label' => esc_html__( 'Cursor Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => ['url' => '',],
				'separator' => 'before',
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-custom',
				],
			]
		);
		$this->add_control(
			'mc_pointer_icon_width',
			[
				'label' => esc_html__( 'Icon Max Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-pointer-follow' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					
					'mouse_cursor_type' => ['mouse-follow-image'],
				],
			]
		);
        $this->add_control(
			'mc_follow_circle_width',
			[
				'label' => esc_html__( 'Circle Max Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'max-width: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					 'mouse_cursor_type' => 'mouse-follow-circle',	
					 'circle_style' => ['mc-cs1','mc-cs3'],
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mc_follow_circle_height',
			[
				'label' => esc_html__( 'Circle Max Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'max-height: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					 'mouse_cursor_type' => 'mouse-follow-circle',
					 'circle_style' => ['mc-cs1','mc-cs3'],
				],
			]
		);
		$this->add_control(
			'mc_2_first_circle_size',
			[
				'label' => esc_html__( 'First Circle Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 90,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 80,
				],
				'condition' => [					
					 'mouse_cursor_type' => 'mouse-follow-circle',
					 'circle_style' => 'mc-cs2',
				],
				'separator' => 'before',	
			]
		);
		$this->add_control(
			'mc_2_second_circle_size',
			[
				'label' => esc_html__( 'Second Circle Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 90,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 80,
				],
				'condition' => [					
					 'mouse_cursor_type' => 'mouse-follow-circle',
					 'circle_style' => 'mc-cs2',
				],
			]
		);
	    $this->add_control(
			'mc_pointer_text',
			[
				'label' => esc_html__( 'Follow Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Follow Text', 'theplus' ),
				'placeholder' => esc_html__( 'Follow Text', 'theplus' ),
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-text',
				],
			]
		);
		$this->add_control(
			'mc_pointer_left_offset',
			[
				'label' => esc_html__( 'Cursor Left Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],						
				'condition' => [					
					'mouse_cursor_type' => ['mouse-follow-text','mouse-follow-image','mouse-follow-circle'],
				],
			]
		);
		$this->add_control(
			'mc_pointer_top_offset',
			[
				'label' => esc_html__( 'Cursor Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition' => [					
					'mouse_cursor_type' => ['mouse-follow-text','mouse-follow-image','mouse-follow-circle'],
				],
			]
		);
		$this->add_responsive_control(
            'mc_circle_z_index',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Circle Z-Index', 'theplus'),
				'size_units' => [ 'px' ],				
				'range' => [
					'px' => [
						'min'	=> 0,
						'max'	=> 99999,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle ' => 'z-index: {{SIZE}};',
				],
				'condition' => [
					'mouse_cursor_type' => ['mouse-follow-circle'],
				],
            ]
        );
		$this->add_control(
			'mc_click_cursor',
			[
				'label'        =>  esc_html__( 'Mouse Cursor Click', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     =>  esc_html__( 'Yes', 'theplus' ),
				'label_off'    =>  esc_html__( 'No', 'theplus' ),					
				'default'      => 'no',	
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => '===', 'value' => 'mouse-cursor-icon'],
								['name' => 'Icon_cursor_type', 'operator' => '===', 'value' => 'cursor-Icon-custom']
							]
						],
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => 'in', 'value' => ['mouse-follow-image','mouse-follow-text']]
							]
						],
					]
				],	
			]
		);
		$this->add_control(
			'mc_pointer_click_icon',
			[
				'label' => esc_html__( 'Cursor Click Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => '===', 'value' => 'mouse-cursor-icon'],
								['name' => 'Icon_cursor_type', 'operator' => '===', 'value' => 'cursor-Icon-custom'],
								['name' => 'mc_click_cursor', 'operator' => '===', 'value' => 'yes']
							]
						],
						[
						'terms' => [
								['name' => 'mouse_cursor_type', 'operator' => '===', 'value' => 'mouse-follow-image'],
								['name' => 'mc_click_cursor', 'operator' => '===', 'value' => 'yes']
							]
						],
					]
				],	
			]
		);
		$this->add_control(
			'mc_pointer_click_text',
			[
				'label' => esc_html__( 'Click Follow Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'See More', 'theplus' ),
				'placeholder' => esc_html__( 'See More', 'theplus' ),
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-text',
					'mc_click_cursor' => 'yes',
				],
			]
		);
		$this->add_control(
			'mc_click_cursor_cir_cst',
			[
				'label'        =>  esc_html__( 'Mouse Cursor Click', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     =>  esc_html__( 'Yes', 'theplus' ),
				'label_off'    =>  esc_html__( 'No', 'theplus' ),					
				'default'      => 'no',	
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-custom',
				],
			]
		);
		$this->add_control(
			'mc_pointer_click_icon_cst',
			[
				'label' => esc_html__( 'Cursor Click Icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-custom',
					'mc_click_cursor_cir_cst' => 'yes',
				],
			]
		);
		$this->add_control(
			'stl_tag_select',
			[
				'label' => esc_html__( 'List of Tags for Hover Effect', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'a', 'theplus' ),
				'placeholder' => esc_html__( 'h1 to h6 tage Separate by "," ', 'theplus' ),
				'description' => esc_html__( 'Note: You Can Change the Cursor On Above Mentioned Tags. E.g. a,h1,h2,etc', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' 	=> [
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-predefine',
				],
			]
		);
		$this->end_controls_section();
		/*End Content Section*/	
		/*Start Style Section*/
		$this->start_controls_section(
            'section_follow_text_styling',
            [
                'label' => esc_html__('Follow Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                	'cursor_effect!' => 'mc-widget',
					'mouse_cursor_type' => 'mouse-follow-text',
				],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mc_follow_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .plus-cursor-pointer-follow-text',
			]
		);
		$this->add_control(
			'mc_follow_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-pointer-follow-text' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'mc_follow_text_padding',
			[
				'label' => esc_html__( 'Text Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' =>[
					'top' => '10',
					'right' => '15',
					'bottom' => '10',
					'left' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-pointer-follow-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',							
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mc_follow_text_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-cursor-pointer-follow-text',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'mc_follow_text_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .plus-cursor-pointer-follow-text',
			]
		);
		$this->add_control(
			'mc_follow_text_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-pointer-follow-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'mc_follow_text_box_shadow',
				'label' => esc_html__( 'Text Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-cursor-pointer-follow-text',
			]
		);
        $this->end_controls_section();
          /*End Style Section*/
          /*Start Widget Follow text Style Section*/
		$this->start_controls_section(
            'section_widget_follow_text_styling',
            [
                'label' => esc_html__('Follow Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                	'cursor_effect' => 'mc-widget',
					'mouse_cursor_type' => 'mouse-follow-text',
				],
            ]
        );
        $this->add_responsive_control(
			'widget_follow_text_size',
			[
				'label' => esc_html__( 'Text Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
			]
		);
        $this->add_control(
			'widget_follow_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
			]
		);
         $this->end_controls_section();
        /*End Widget Follow text Section*/		
        /*Start Follow Circle Style*/
		$this->start_controls_section(
            'section_follow_circle_styling',
            [
                'label' => esc_html__('Follow Circle', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
               'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
				],
            ]
        );
        $this->add_control(
			'circle_stltwo_mxbndmd',
			[
				'label' => esc_html__( 'Mix Blend Mode', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'difference',
				'options' => [
					'normal' => esc_html__( 'normal ','theplus' ),
					'multiply' => esc_html__( 'multiply ','theplus' ),
					'screen' => esc_html__( 'screen ','theplus' ),
					'overlay' => esc_html__( 'overlay ','theplus' ),
					'darken' => esc_html__( 'darken','theplus' ),
					'lighten' => esc_html__( 'lighten ','theplus' ),
					'color-dodge' => esc_html__( 'color-dodge ','theplus' ),
					'color-burn' => esc_html__( 'color-burn ','theplus' ),
					'difference' => esc_html__( 'difference ','theplus' ),
					'exclusion' => esc_html__( 'exclusion ','theplus' ),
					'hue' => esc_html__( 'hue ','theplus' ),
					'saturation' => esc_html__( 'saturation ','theplus' ),
					'color' => esc_html__( 'color ','theplus' ),
					'luminosity' => esc_html__( 'luminosity ','theplus' ),
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs3',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'mix-blend-mode: {{VALUE}};',
				],
				
			]
		);
		$this->start_controls_tabs( 'circle_tabs_style' );
		$this->start_controls_tab(
			'circle_stl_Nml',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-predefine',
				],
			]
		);
		$this->add_control(
			'circle_stltwo_background',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'background-color: {{VALUE}};',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => ['mc-cs1','mc-cs3'],
				],
			]
		);	
		$this->add_control(
			'circle_opacity',
			[
				'label' => esc_html__( 'Opacity', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'opacity: {{VALUE}}',
				],
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => ['mc-cs1'],
				],
			]
		);	
		$this->add_control(
			'circle_stlone_stroke',
			[
				'label' => esc_html__( 'Circle Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'stroke: {{VALUE}};',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_width',
			[
				'label' => esc_html__( 'Circle Stroke Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_fill',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'fill: {{VALUE}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_opacity',
			[
				'label' => esc_html__( 'Opacity', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.1,
				'selectors' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'opacity: {{VALUE}}',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);	
		$this->add_control(
			'circle_stlone_stroke_progress',
			[
				'label' => esc_html__( 'Circle Progress Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-progress-bar' => 'stroke: {{VALUE}};',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_progress_width',
			[
				'label' => esc_html__( 'Circle Stroke Progress Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-progress-bar' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_transformNml',
			[
				'label' => esc_html__( 'Transform CSS', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],			
			]
		);
		$this->add_responsive_control(
			'circle_transition_Nml',
			[
				'label'   => esc_html__( 'Transition Duration', 'theplus' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '.5',
				],
				'range' => [
					'px' => [
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 3,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle' => 'transition: transform {{SIZE}}s ease;-webkit-transition: transform {{SIZE}}s ease;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'circle_stl_Hvr',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-circle',
					'circle_cursor_type' => 'cursor-predefine',
				],
			]
		);
		$this->add_control(
			'circle_scale',
			[
				'label' => esc_html__( 'Circle Scale ( 0.5 - 2 )', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0.5,
				'max' => 2,
				'step' => 0.1,
				'selectors' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle' => 'transform: scale({{VALUE}})',
				],
				'condition' => [
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stltwo_background_h',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-cursor-follow-circle' => 'background-color: {{VALUE}};',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => ['mc-cs1','mc-cs3'],
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_h',
			[
				'label' => esc_html__( 'Circle Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'stroke: {{VALUE}} !important;',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_width_h',
			[
				'label' => esc_html__( 'Circle Stroke Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_fill_h',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-st1' => 'fill: {{VALUE}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_progress_h',
			[
				'label' => esc_html__( 'Circle Progress Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selector' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-progress-bar' => 'stroke: {{VALUE}};',
				],
				'condition' => [
				    'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_stlone_stroke_progress_width_h',
			[
				'label' => esc_html__( 'Circle Stroke Progress Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'.tp-mouse-hover-active {{WRAPPER}} .plus-mc-svg-circle .plus-mc-circle-progress-bar' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [					
					'mouse_cursor_type' => 'mouse-follow-circle',					
					'circle_cursor_type' => 'cursor-predefine',
					'circle_style' => 'mc-cs2',
				],
			]
		);
		$this->add_control(
			'circle_transformHvr',
			[
				'label' => esc_html__( 'Transform CSS', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle:hover' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],		
			]
		);
		$this->add_responsive_control(
			'circle_transition_Hvr',
			[
				'label'   => esc_html__( 'Transition Duration', 'theplus' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '.5',
				],
				'range' => [
					'px' => [
						'step' => 0.1,
						'min'  => 0.1,
						'max'  => 3,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-cursor-follow-circle:hover' => 'transition: transform {{SIZE}}s ease;-webkit-transition: transform {{SIZE}}s ease;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();
        /*End Follow Circle Style */
	}
	
    protected function render() {

        $settings = $this->get_settings_for_display();	
        $uid_mcursor=uniqid("tp-mc");
        $widget_follow_text_size = (!empty($settings['widget_follow_text_size']['size'])) ? $settings['widget_follow_text_size']['size'] : 20;
        $widget_follow_text_color = (!empty($settings['widget_follow_text_color'])) ? $settings['widget_follow_text_color'] : '#000';
        if (!empty($settings['cursor_effect']) && $settings['cursor_effect'] =='mc-widget' && !empty($settings['mouse_cursor_type']) && $settings['mouse_cursor_type'] =='mouse-follow-text') {
	        $wdgetcls = '.elementor-element'.$this->get_unique_selector();
	        ?>
	        <script type="text/javascript">
	        jQuery(document).ready(function(){		  
	        	var wdgetcls = jQuery("<?php echo $wdgetcls; ?>").prev();
	        	var wdgetid = wdgetcls.data('id');        	
	        	var wdgetflwtxt = ".elementor-element-"+wdgetid+" .plus-cursor-pointer-follow-text";
	        	setTimeout(function() {
	               jQuery(wdgetflwtxt).css("color","<?php echo $widget_follow_text_color; ?>").css("font-size","<?php echo $widget_follow_text_size; ?>px");
	            }, 100);
	        	
			});
	  		</script>
	  		<?php    
  		}    			
    	$mouse_cursor_attr = array();
    	$mouse_cursor_attr['effect'] = (!empty($settings['cursor_effect'])) ? $settings['cursor_effect'] : '';
    	if ($settings['cursor_effect'] =='mc-column' || $settings['cursor_effect'] =='mc-section' || $settings['cursor_effect'] =='mc-widget' || $settings['cursor_effect'] =='mc-body' || $settings['cursor_effect'] =='mc-container') {
        	$mouse_cursor_attr['type'] = (!empty($settings['mouse_cursor_type'])) ? $settings['mouse_cursor_type'] : '';
        	if($settings['mouse_cursor_type'] =='mouse-cursor-icon'){
        	  $mouse_cursor_attr['icon_type'] = (!empty($settings['Icon_cursor_type'])) ? $settings['Icon_cursor_type'] : 'cursor-Icon-custom';
            }
			if($settings['mouse_cursor_type'] =='mouse-cursor-icon' && $settings['Icon_cursor_type'] =='cursor-Icon-predefine'){
				$mouse_cursor_attr['mc_cursor_icon'] = (!empty($settings["mc_cursor_icon_symbol"])) ? $settings["mc_cursor_icon_symbol"] : 'crosshair';
			}			
        	if($settings['mouse_cursor_type'] =='mouse-cursor-icon' && $settings['Icon_cursor_type'] =='cursor-Icon-custom'){	
        		$mouse_cursor_attr['mc_cursor_icon'] = (!empty($settings['mc_pointer_icon']['url'])) ? $settings['mc_pointer_icon']['url'] : '';
				
				if($settings['cursor_effect'] =='mc-widget'){
					$mouse_cursor_attr['mc_cursor_adjust_width'] = (!empty($settings["mc_pointer_icon_width"]["size"])) ? $settings["mc_pointer_icon_width"]["size"] : 0;
				}
				
				$mouse_cursor_attr['mc_cursor_adjust_left'] = (isset($settings["mc_pointer_left_offset"]["size"])) ? $settings["mc_pointer_left_offset"]["size"] : 0;
				$mouse_cursor_attr['mc_cursor_adjust_top'] = (isset($settings["mc_pointer_top_offset"]["size"])) ? $settings["mc_pointer_top_offset"]["size"] : 0;

        		if( !empty($settings['mc_click_cursor']) && $settings['mc_click_cursor'] == 'yes' && !empty($settings['mc_pointer_click_icon']['url']) ){
        			$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
        			$mouse_cursor_attr['mc_cursor_see_icon'] = (!empty($settings['mc_pointer_click_icon']['url'])) ? $settings['mc_pointer_click_icon']['url'] : '';					
        		}

        	}
        	else if(!empty($settings['mouse_cursor_type']) && $settings['mouse_cursor_type'] =='mouse-follow-image'){
        		$mouse_cursor_attr['mc_cursor_icon'] = (!empty($settings['mc_pointer_icon']['url'])) ? $settings['mc_pointer_icon']['url'] : '';
				
				if($settings['cursor_effect'] =='mc-widget'){
					$mouse_cursor_attr['mc_cursor_adjust_width'] = (!empty($settings["mc_pointer_icon_width"]["size"])) ? $settings["mc_pointer_icon_width"]["size"] : 0;
				}
				
				$mouse_cursor_attr['mc_cursor_adjust_left'] = (isset($settings["mc_pointer_left_offset"]["size"])) ? $settings["mc_pointer_left_offset"]["size"] : 0;
				$mouse_cursor_attr['mc_cursor_adjust_top'] = (isset($settings["mc_pointer_top_offset"]["size"])) ? $settings["mc_pointer_top_offset"]["size"] : 0;
					
        		if( !empty($settings['mc_click_cursor']) && $settings['mc_click_cursor'] == 'yes' && !empty($settings['mc_pointer_click_icon']['url']) ){
        			$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
        			$mouse_cursor_attr['mc_cursor_see_icon'] = (!empty($settings['mc_pointer_click_icon']['url'])) ? $settings['mc_pointer_click_icon']['url'] : '';					
        		}

        	}
	    	else if(!empty($settings['mouse_cursor_type']) && $settings['mouse_cursor_type'] =='mouse-follow-text'){
					$mouse_cursor_attr['mc_cursor_text'] = (!empty($settings['mc_pointer_text'])) ? $settings['mc_pointer_text'] : '';
				
					$mouse_cursor_attr['mc_cursor_adjust_left'] = (isset($settings["mc_pointer_left_offset"]["size"])) ? $settings["mc_pointer_left_offset"]["size"] : 0;
					$mouse_cursor_attr['mc_cursor_adjust_top'] = (isset($settings["mc_pointer_top_offset"]["size"])) ? $settings["mc_pointer_top_offset"]["size"] : 0;	
					
				if( !empty($settings['mc_click_cursor']) && $settings['mc_click_cursor'] == 'yes' && !empty($settings['mc_pointer_click_text']) ){				
					$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
					$mouse_cursor_attr['mc_cursor_see_text'] = (!empty($settings['mc_pointer_click_text'])) ? $settings['mc_pointer_click_text'] : '';									
				}
					
			}else if(!empty($settings['mouse_cursor_type']) && $settings['mouse_cursor_type'] =='mouse-follow-circle') {	
			   $mouse_cursor_attr['circle_type'] = (!empty($settings['circle_cursor_type'])) ? $settings['circle_cursor_type'] : 'cursor-predefine';
			    if($settings['circle_cursor_type'] == 'cursor-predefine'){
			      $mouse_cursor_attr['mc_cursor_adjust_symbol'] = (!empty($settings["mc_cursor_symbol"])) ? $settings["mc_cursor_symbol"] : 'crosshair';
			      $mouse_cursor_attr['mc_cursor_adjust_style'] = (!empty($settings["circle_style"])) ? $settings["circle_style"] : 'mc-cs1';
			          $mouse_cursor_attr['circle_style_tag_selector'] = (!empty($settings["stl_tag_select"])) ? $settings["stl_tag_select"] : 'a';
			          $mouse_cursor_attr['mc_circle_transformNml'] = (!empty($settings["circle_transformNml"])) ? $settings["circle_transformNml"] : '';
			          $mouse_cursor_attr['mc_circle_transformHvr'] = (!empty($settings["circle_transformHvr"])) ? $settings["circle_transformHvr"] : '';
			          $mouse_cursor_attr['mc_circle_transitionNml'] = (!empty($settings["circle_transition_Nml"]["size"])) ? $settings["circle_transition_Nml"]["size"] : '';
			          $mouse_cursor_attr['mc_circle_transitionHvr'] = (!empty($settings["circle_transition_Hvr"]["size"])) ? $settings["circle_transition_Hvr"]["size"] : '';

			        if($settings["circle_style"] == 'mc-cs2'){
				      $mouse_cursor_attr['style_one_crcle_bg'] = (!empty($settings["circle_stlone_stroke"])) ? $settings["circle_stlone_stroke"] : '';
				      $mouse_cursor_attr['style_one_crcle_prog_bg'] = (!empty($settings["circle_stlone_stroke_progress"])) ? $settings["circle_stlone_stroke_progress"] : '';
					  $mouse_cursor_attr['style_one_crcle_bgh'] = (!empty($settings["circle_stlone_stroke_h"])) ? $settings["circle_stlone_stroke_h"] : '';
				      $mouse_cursor_attr['style_one_crcle_prog_bgh'] = (!empty($settings["circle_stlone_stroke_progress_h"])) ? $settings["circle_stlone_stroke_progress_h"] : '';
			        }
			        if($settings["circle_style"] == 'mc-cs3'){
				      $mouse_cursor_attr['style_two_blend_mode'] = (!empty($settings["circle_stltwo_mxbndmd"])) ? $settings["circle_stltwo_mxbndmd"] : 'difference';
			        }
			        if($settings["circle_style"] == 'mc-cs1'|| $settings["circle_style"] == 'mc-cs3'){
			          $mouse_cursor_attr['style_two_bg'] = (!empty($settings["circle_stltwo_background"])) ? $settings["circle_stltwo_background"] : '';
					  $mouse_cursor_attr['style_two_bgh'] = (!empty($settings["circle_stltwo_background_h"])) ? $settings["circle_stltwo_background_h"] : '';
			        }		       
			    }else if($settings['circle_cursor_type'] == 'cursor-custom'){		
					$mouse_cursor_attr['mc_cursor_icon'] = (!empty($settings['mc_pointer_icon_cir_cst']['url'])) ? $settings['mc_pointer_icon_cir_cst']['url'] : '';
					
					$mouse_cursor_attr['mc_cursor_adjust_left'] = (isset($settings["mc_pointer_left_offset"]["size"])) ? $settings["mc_pointer_left_offset"]["size"] : 0;
					$mouse_cursor_attr['mc_cursor_adjust_top'] = (isset($settings["mc_pointer_top_offset"]["size"])) ? $settings["mc_pointer_top_offset"]["size"] : 0;
						
	        		if( !empty($settings['mc_click_cursor_cir_cst']) && $settings['mc_click_cursor_cir_cst'] == 'yes' && !empty($settings['mc_pointer_click_icon_cst']['url']) ){
	        			$mouse_cursor_attr['mc_cursor_see_more'] = 'yes';
	        			$mouse_cursor_attr['mc_cursor_see_icon'] = (!empty($settings['mc_pointer_click_icon_cst']['url'])) ? $settings['mc_pointer_click_icon_cst']['url'] : '';						
	        		}
        	    }				
				if((!empty($settings['mouse_cursor_type']) && $settings['mouse_cursor_type'] == 'mouse-follow-circle') && (!empty($settings['circle_style']) && $settings['circle_style'] == 'mc-cs2')){
					$mouse_cursor_attr['mc_2_first_circle_size'] = (!empty($settings["mc_2_first_circle_size"]["size"])) ? $settings["mc_2_first_circle_size"]["size"] : 80;
					$mouse_cursor_attr['mc_2_second_circle_size'] = (!empty($settings["mc_2_second_circle_size"]["size"])) ? $settings["mc_2_second_circle_size"]["size"] : 80;
				}
			}
	    }      
		$output='';
        if(!empty($settings['mouse_cursor_type'])){
			$output .= "<div class='tp-mouse-cursor-wrapper ".esc_attr($uid_mcursor)."' data-plus-mc-settings='".json_encode($mouse_cursor_attr)."'></div>";
		}		
		echo $output;		
	}		
	protected function content_template() {
	
	}
}