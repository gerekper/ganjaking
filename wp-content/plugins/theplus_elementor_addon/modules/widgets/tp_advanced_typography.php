<?php 
/*
Widget Name: Text Typography
Description: Different Style Of Text Typography Layouts 
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;
 
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly
 
class ThePlus_Advanced_Typography extends Widget_Base {
 
	public function get_name() {
		return 'tp-advanced-typography';
	}
 
    public function get_title() {
        return esc_html__('Advanced Typography', 'theplus');
    }
 
    public function get_icon() {
        return 'fa fa-underline theplus_backend_icon';
    }
 
    public function get_categories() {
        return array('plus-essential');
    }
 
    protected function register_controls() {
		/*start advanced typography*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Advanced Typography', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
 
		$this->add_control(
			'typography_listing',
			[
				'label' => esc_html__( 'Select Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Normal', 'theplus' ),
					'listing' => esc_html__( 'Multiple', 'theplus' ),
				],
			]
		);
 
		$repeater = new \Elementor\Repeater();
 
		$repeater->add_control(
			'typo_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 3,
				'default' => esc_html__( 'Default Text', 'theplus' ),
				'placeholder' => esc_html__( 'Type your text here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'text_link',
			[
				'label' => esc_html__( 'Url/Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'dynamic' => [
					'active'   => true,
				],
				'separator' => ['before','after'],
			]
		);
		$repeater->add_control(
			'typo_extra_options',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'list_typo_stroke',
			[
				'label' => esc_html__( 'Stroke/Fill Options', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
			]
		);
		$repeater->add_responsive_control(
			'typo_stroke_width',
			[
				'label' => esc_html__( 'Stroke Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				],				
				'condition' => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
		$repeater->start_controls_tabs('text_border_strock_tabs');
		$repeater->start_controls_tab('text_border_strock_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'list_typo_stroke' => 'yes',
				],
			]
		);		
		$repeater->add_control(
			'strock_color_option',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
				'condition' => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'strock_color',
			[
				'label' => esc_html__( 'Stroke Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke' => '-webkit-text-stroke-color: {{VALUE}}',
				],
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'solid',
				],
			]
		);
		$repeater->add_control(
            'strock_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke' =>
					'background: -webkit-linear-gradient({{SIZE}}{{UNIT}}, {{strock_gradient_color1.VALUE}} {{strock_gradient_color1_control.SIZE}}{{strock_gradient_color1_control.UNIT}}, {{strock_gradient_color2.VALUE}} {{strock_gradient_color2_control.SIZE}}{{strock_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-stroke-color: transparent;',
				],
				'condition'    => [
					'list_typo_stroke' => 'yes',
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
			]
        );
		$repeater->add_control(
			't_strock_fill',
			[
				'label' => esc_html__( 'Text Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
		$repeater->add_control(
			't_strock_fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'of_type' => 'gradient',
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke' => 'color: {{VALUE}};-webkit-text-fill-color: {{VALUE}}',
				],
				'condition'    => [
					'list_typo_stroke' => 'yes',
					't_strock_fill' => 'yes',
				],
			]
		);	
 
		$repeater->end_controls_tab();
		$repeater->start_controls_tab('text_border_strock_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition'    => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
 
		$repeater->add_control(
			'strock_color_option_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
				'condition'    => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'strock_color_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke:hover' => '-webkit-text-stroke-color: {{VALUE}}',
				],
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'solid',
				],
			]
		);	
		$repeater->add_control(
            'strock_gradient_color1_hover',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color1_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color2_hover',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_color2_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'strock_gradient_angle_hover', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke:hover' =>
					'background: -webkit-linear-gradient({{SIZE}}{{UNIT}}, {{strock_gradient_color1_hover.VALUE}} {{strock_gradient_color1_control_hover.SIZE}}{{strock_gradient_color1_control_hover.UNIT}}, {{strock_gradient_color2_hover.VALUE}} {{strock_gradient_color2_control_hover.SIZE}}{{strock_gradient_color2_control_hover.UNIT}});-webkit-background-clip: text;-webkit-text-stroke-color: transparent;',
				],
				'condition'    => [
					'list_typo_stroke' => 'yes',
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
			]
        );
		$repeater->add_control(
			't_strock_fill_hover',
			[
				'label' => esc_html__( 'Text Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
				'condition'    => [
					'list_typo_stroke' => 'yes',
				],
			]
		);
		$repeater->add_control(
			't_strock_fill_color_hover',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.list_typo_stroke:hover' => 'color: {{VALUE}};-webkit-text-fill-color:{{VALUE}}',
				],
				'condition' => [
					'list_typo_stroke' => 'yes',
					't_strock_fill_hover' => 'yes',
				],
			]
		);	
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'bg_based_text_switch',
			[
				'label' => esc_html__( 'Background based Blend Mode', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'bg_based_text_style',
			[
				'label' => esc_html__( 'Variations', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'color'  => esc_html__( 'Color', 'theplus' ),
					'color-burn' => esc_html__( 'Color Burn', 'theplus' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'theplus' ),			
					'darken' => esc_html__( 'Darken', 'theplus' ),			
					'difference' => esc_html__( 'Difference', 'theplus' ),			
					'exclusion' => esc_html__( 'Exclusion', 'theplus' ),			
					'hard-light' => esc_html__( 'Hard Light', 'theplus' ),			
					'hue' => esc_html__( 'Hue', 'theplus' ),			
					'inherit' => esc_html__( 'Inherit', 'theplus' ),
					'initial' => esc_html__( 'Initial', 'theplus' ),			
					'lighten' => esc_html__( 'Lighten', 'theplus' ),
					'luminosity' => esc_html__( 'Luminosity', 'theplus' ),
					'multiply' => esc_html__( 'Multiply', 'theplus' ),
					'normal' => esc_html__( 'Normal', 'theplus' ),
					'overlay' => esc_html__( 'Overlay', 'theplus' ),
					'saturation' => esc_html__( 'Saturation', 'theplus' ),
					'screen' => esc_html__( 'Screen', 'theplus' ),
					'soft-light' => esc_html__( 'Soft Light', 'theplus' ),					
					'unset' => esc_html__( 'Unset', 'theplus' ),
				],
				'condition' => [
					'bg_based_text_switch' => 'yes',	
				],
			]
		);
		$repeater->add_control(
			'img_gif_ovly_txtimg_switch',
			[
				'label' => esc_html__( 'Knockout Text', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
 
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'img_gif_ovly_txtimg_option',
				'label' => esc_html__( 'Text Background', 'theplus' ),
				'types' => [ 'classic' ],
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.typo_gif_based_text',
				'condition' => [
					'img_gif_ovly_txtimg_switch' => 'yes',	
				],
			]
		);
		$repeater->add_control(
			'on_hover_img_reveal_switch',
			[
				'label' => esc_html__( 'On Hover Image Reveal', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'on_hover_img_source',
			[
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Hover Image', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'on_hover_img_reveal_switch' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'adv_hover_img_reveal_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'on_hover_img_reveal_switch' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'marquee_switch',
			[
				'label' => esc_html__( 'Marquee', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'marquee_type',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),					
					'on_transition' => esc_html__( 'On Transition', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'marquee_direction',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right'  => esc_html__( 'Right', 'theplus' ),
					'up'  => esc_html__( 'Up', 'theplus' ),
					'down'  => esc_html__( 'Down', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'marquee_behavior',
			[
				'label' => esc_html__( 'Behavior', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'initial',
				'options' => [
					'initial'  => esc_html__( 'Scroll', 'theplus' ),
					'normal'  => esc_html__( 'Slide', 'theplus' ),
					'alternate'  => esc_html__( 'Alternate', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$repeater->add_control(
			'marquee_loop',
			[
				'label' => esc_html__( 'Loop', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => -1,
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$repeater->add_control('marquee_scrollamount',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'default' => 6,
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$repeater->add_control(
			'marquee_scrolldelay',
			[
				'label' => esc_html__( 'Animation Duration', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,				
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$repeater->add_control(
			'marquee_scrolldelay_t',
			[
				'label' => esc_html__( 'Animation Duration', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,				
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'on_transition',
				],
			]
		);
		$repeater->add_responsive_control('marquee_text_width',
			[	
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
					'vw' => [
                        'min' => 0,
                        'max' => 100,
						'step' => 1,
                    ],
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} marquee' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};white-space: nowrap;',
				],
				'condition' => [
					'marquee_switch' => 'yes',	
					'marquee_type' => 'default',
				],
			]
		);
		$repeater->add_control(
			'marquee_text_width_t',
			[	
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
					'vw' => [
                        'min' => 0,
                        'max' => 100,
						'step' => 1,
                    ],
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};display: inline-block;',
				],
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'on_transition',
				],
			]
		);
		$repeater->add_control(
			'loop_magic_scroll',[
				'label'   => esc_html__( 'Magic Scroll', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_Option_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Scroll Options', 'theplus' ),
				'name'           => 'loop_scroll_option',
				'render_type'  => 'template',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->start_controls_tabs( 'loop_tab_magic_scroll' );
		$repeater->start_controls_tab(
			'loop_tab_scroll_from',
			[
				'label' => esc_html__( 'Initial', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_From_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Initial Position', 'theplus' ),
				'name'           => 'loop_scroll_from',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'loop_tab_scroll_to',
			[
				'label' => esc_html__( 'Final', 'theplus' ),
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->add_group_control(
			\Theplus_Magic_Scroll_To_Style_Group::get_type(),
			[
				'label' => esc_html__( 'Final Position', 'theplus' ),
				'name'           => 'loop_scroll_to',
				'condition'    => [
					'loop_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'plus_mouse_move_parallax',
			[
				'label'        => esc_html__( 'Mouse Move Parallax', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_group_control(
			\Theplus_Mouse_Move_Parallax_Group::get_type(),
			array(
				'label' => esc_html__( 'Parallax Options', 'theplus' ),
				'name'           => 'plus_mouse_parallax',
				'condition'    => [
					'plus_mouse_move_parallax' => [ 'yes' ],
				],
			)
		);
		$repeater->add_control(
			'text_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'text_animation_effect',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => [
					'pulse'  => esc_html__( 'Pulse', 'theplus' ),
					'floating'  => esc_html__( 'Floating', 'theplus' ),
					'tossing'  => esc_html__( 'Tossing', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'text_animation_hover',
			[
				'label'        => esc_html__( 'Hover animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'text_animation_duration',
			[	
				'label' => esc_html__( 'Duration Time', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 's',
				'range' => [
					's' => [
						'min' => 0.5,
						'max' => 50,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 2.5,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
				'separator' => 'after',
			]
		);
		//Under Line Style
		$repeater->add_control(
			'typo_underline_style',
			[
				'label' => esc_html__( 'Advance Underline Options', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'under_classic'  => esc_html__( 'Classic', 'theplus' ),
					'under_overlay'  => esc_html__( 'Overlay', 'theplus' ),					
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'typo_overlay_under_style',
			[
				'label' => esc_html__( 'Overlay Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
					'style-4'  => esc_html__( 'Style 4', 'theplus' ),
					'style-5'  => esc_html__( 'Style 5', 'theplus' ),
					'style-6'  => esc_html__( 'Style 6', 'theplus' ),
					'style-7'  => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$repeater->start_controls_tabs('text_under_tabs');
		$repeater->start_controls_tab('text_under_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'typo_underline_style!' => 'none',
				],
			]
		);
		$repeater->add_control(
			'typo_classic_under',
			[
				'label' => esc_html__( 'Underline Line', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'underline'  => esc_html__( 'Underline', 'theplus' ),
					'overline'  => esc_html__( 'Overline', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text' => 'text-decoration-line: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$repeater->add_control(
			'typo_classic_under_style',
			[
				'label' => esc_html__( 'Underline Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'solid', 'theplus' ),
					'double'  => esc_html__( 'Double', 'theplus' ),
					'dotted'  => esc_html__( 'Dotted', 'theplus' ),
					'dashed'  => esc_html__( 'Dashed', 'theplus' ),
					'wavy'  => esc_html__( 'Wavy', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text' => 'text-decoration-style: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$repeater->add_control(
			'text_under_color',
			[
				'label' => esc_html__( 'Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text' => 'text-decoration-color: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$repeater->add_control(
			'text_under_overlay_bottom_off',
			[
				'label' => esc_html__( 'Bottom Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-1:before' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style' => 'style-1',
				],
			]
		);
		$repeater->add_control(
			'text_under_overlay_height',
			[
				'label' => esc_html__( 'Line Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-1:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-2:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-3:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:after,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-5:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:after,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-7:before' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'text_under_overlay_bg',
				'label' => esc_html__( 'Underline Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-1:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-2:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-3:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:after,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-5:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:after,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-7:before',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab('text_under_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'typo_underline_style!' => 'none',
				],
			]
		);
		$repeater->add_control(
			'typo_classic_under_hover',
			[
				'label' => esc_html__( 'Classic Hover', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'underline'  => esc_html__( 'Underline', 'theplus' ),
					'overline'  => esc_html__( 'Overline', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text:hover' => 'text-decoration-line: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$repeater->add_control(
			'typo_classic_under_hover_style',
			[
				'label' => esc_html__( 'Underline Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'solid', 'theplus' ),
					'double'  => esc_html__( 'Double', 'theplus' ),
					'dotted'  => esc_html__( 'Dotted', 'theplus' ),
					'dashed'  => esc_html__( 'Dashed', 'theplus' ),
					'wavy'  => esc_html__( 'Wavy', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text:hover' => 'text-decoration-style: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$repeater->add_control(
			'text_under_hover_color',
			[
				'label' => esc_html__( 'Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_classic .listing-typo-text:hover' => 'text-decoration-color: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);		
		$repeater->add_control(
			'text_under_overlay_hover_height',
			[
				'label' => esc_html__( 'Hover Line Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-1:hover:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-2:hover:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-3:hover:before,
					{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:hover:after,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-5:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:hover:after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style!' => 'style-7',
				],
			]
		);
 
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'text_under_overlay_hover_bg',
				'label' => esc_html__( 'Underline Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-1:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-2:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-3:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-4:hover:after,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-5:hover:before,{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.under_overlay.overlay-style-6:after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style!' => 'style-7',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'typo_styling_options',
			[
				'label' => esc_html__( 'Typography Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$repeater->start_controls_tabs( 'tabs_color_loop_style' );
		$repeater->start_controls_tab(
			'tab_loop_style_typo',
			[
				'label' => esc_html__( 'Typography', 'theplus' ),
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typogrophy_font',
				'label' => esc_html__( 'Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text',
			]
		);
		$repeater->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);		
		$repeater->add_control(
			'transform_css',
			[
				'label' => esc_html__( 'Transform Normal', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.plus-adv-text-typo' => '-webkit-transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;display: inline-block;'
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'transform_css_hover',
			[
				'label' => esc_html__( 'Transform Hover', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.plus-adv-text-typo:hover' => '-webkit-transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;display: inline-block;'
				],
			]
		);
		$repeater->add_control(
			'typo_advanced_style',
			[
				'label' => esc_html__( 'Advanced Style', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_responsive_control(
			'text_max_width',
			[
				'label' => esc_html__( 'Text Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 700,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.plus-adv-text-typo' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text' => 'white-space: nowrap;',
				],
				'condition' => [
					'typo_advanced_style' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'text_max_left',
			[
				'label' => esc_html__( 'Horizontal Alignment', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.plus-adv-text-typo' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'typo_advanced_style' => 'yes',
				],
			]
		);
		$repeater->add_responsive_control(
			'text_max_bottom',
			[
				'label' => esc_html__( 'Vertical Alignment', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}}.plus-adv-text-typo' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'typo_advanced_style' => 'yes',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'tab_loop_color_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$repeater->add_control(
			'adv_typ_text_color_option',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$repeater->add_control(
			'adv_typ_text_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.bg_based_text' => '-webkit-text-fill-color:{{VALUE}}',
				],
				'condition' => [
					'adv_typ_text_color_option' => 'solid',
				],
			]
		);
		$repeater->add_control(
            'adv_typ_text_color_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'typo_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{adv_typ_text_color_gradient_color1.VALUE}} {{adv_typ_text_color_gradient_color1_control.SIZE}}{{adv_typ_text_color_gradient_color1_control.UNIT}}, {{adv_typ_text_color_gradient_color2.VALUE}} {{adv_typ_text_color_gradient_color2_control.SIZE}}{{adv_typ_text_color_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'adv_typ_text_color_option' => 'gradient',
					'adv_typ_text_color_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$repeater->add_control(
            'typo_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{adv_typ_text_color_gradient_color1.VALUE}} {{adv_typ_text_color_gradient_color1_control.SIZE}}{{adv_typ_text_color_gradient_color1_control.UNIT}}, {{adv_typ_text_color_gradient_color2.VALUE}} {{adv_typ_text_color_gradient_color2_control.SIZE}}{{adv_typ_text_color_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
					'adv_typ_text_color_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'typo_shadow',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text',
			]
		);
		$repeater->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'typo_filters',
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text',
				'separator' => 'before',
			]
		);
		$repeater->end_controls_tab();
		$repeater->start_controls_tab(
			'tab_loop_color_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$repeater->add_control(
			'adv_typ_text_color_option_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$repeater->add_control(
			'adv_typ_text_color_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text.bg_based_text:hover' => '-webkit-text-fill-color:{{VALUE}}',
				],
				'condition' => [
					'adv_typ_text_color_option_hover' => 'solid',
				],
			]
		);
		$repeater->add_control(
            'adv_typ_text_color_gradient_color1_hover',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color1_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color2_hover',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_color2_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'adv_typ_text_color_gradient_style_hover', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$repeater->add_control(
            'typo_gradient_angle_hover', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text:hover' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{adv_typ_text_color_gradient_color1_hover.VALUE}} {{adv_typ_text_color_gradient_color1_control_hover.SIZE}}{{adv_typ_text_color_gradient_color1_control_hover.UNIT}}, {{adv_typ_text_color_gradient_color2_hover.VALUE}} {{adv_typ_text_color_gradient_color2_control_hover.SIZE}}{{adv_typ_text_color_gradient_color2_control_hover.UNIT}})',
				],
				'condition'    => [
					'adv_typ_text_color_option_hover' => 'gradient',
					'adv_typ_text_color_gradient_style_hover' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$repeater->add_control(
            'typo_gradient_position_hover', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text:hover' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{adv_typ_text_color_gradient_color1_hover.VALUE}} {{adv_typ_text_color_gradient_color1_control_hover.SIZE}}{{adv_typ_text_color_gradient_color1_control_hover.UNIT}}, {{adv_typ_text_color_gradient_color2_hover.VALUE}} {{adv_typ_text_color_gradient_color2_control_hover.SIZE}}{{adv_typ_text_color_gradient_color2_control_hover.UNIT}})',
				],
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
					'adv_typ_text_color_gradient_style_hover' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'typo_shadow_hover',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text:hover',
			]
		);
		$repeater->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'typo_filters_hover',
				'selector' => '{{WRAPPER}} .plus-list-adv-typo-block {{CURRENT_ITEM}} .listing-typo-text:hover',
				'separator' => 'before',
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
 
		$this->add_control(
            'listing_content',
            [
				'label' => esc_html__( 'Text Listing', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'typo_text' => 'This Default',                       
                    ],
					[
                        'typo_text' => ' Text',
                    ],
                ],                
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ typo_text }}}',
				'condition' => [
					'typography_listing' => 'listing',
				],
            ]
        );
 
		$this->add_control(
			'advanced_typography_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Default Text', 'theplus' ),
				'placeholder' => esc_html__( 'Type your text here', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'span_replace_tag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'span',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);	
		$this->add_responsive_control(
			'text_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'prefix_class' => 'text-%s',
				'default' => 'center',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*end advanced typography*/
 
		/*start Text Direction*/
		$this->start_controls_section(
			'text_direction_hv',
			[
				'label' => esc_html__( 'Text Direction', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'text_write_mode',
			[
				'label' => esc_html__( 'Vertical Text', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'unset',
				'options' => [
					'unset' => esc_html__( 'Normal', 'theplus' ),
					'vertical-lr' => esc_html__( 'Left to Right', 'theplus' ),
					'vertical-rl' => esc_html__( 'Right to Left', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'text_orientation',
			[
				'label' => esc_html__( 'Vertical Letters', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
				'condition' => [
					'typography_listing' => 'default',
					'text_write_mode!' => 'unset',	
				],
			]
		);
		$this->add_control(
			'text_direction_ltr',
			[
				'label' => esc_html__( 'Vertical Direction', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'initial' => [
						'title' => esc_html__( 'Initial', 'theplus' ),
						'icon' => 'fa fa-bars',
					],
					'ltr' => [
						'title' => esc_html__( 'LTR', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'rtl' => [
						'title' => esc_html__( 'RTL', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
 
				],
				'label_block' => false,
				'default' => 'initial',
				'toggle' => false,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'transform_css',
			[
				'label' => esc_html__( 'Transform Normal', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block' => '-webkit-transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'separator' => 'before',
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'transform_css_hover',
			[
				'label' => esc_html__( 'Transform Hover', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover' => '-webkit-transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'transform_origin_css',
			[
				'label' => esc_html__( 'Transform Origin', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'top left'  => esc_html__( 'Top Left', 'theplus' ),
					'top center' => esc_html__( 'Top Center', 'theplus' ),
					'top right' => esc_html__( 'Top Right', 'theplus' ),
					'center left' => esc_html__( 'Center Left', 'theplus' ),
					'center' => esc_html__( 'Center', 'theplus' ),
					'center right' => esc_html__( 'Center Right', 'theplus' ),
					'bottom left' => esc_html__( 'Bottom Left', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),
					'bottom right' => esc_html__( 'Bottom Right', 'theplus' ),
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block' => 'transform-origin: {{VALUE}};'
				],
			]
		);
		$this->end_controls_section();
		/*end Text Direction*/
 
		/*start text border(strock)*/
		$this->start_controls_section(
			'text_border_stroke',
			[
				'label' => esc_html__( 'Stroke/Fill Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'stroke_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->end_controls_section();
		/*end advanced typography*/
 
		/*start circular text*/
		$this->start_controls_section(
			'circular_text',
			[
				'label' => esc_html__( 'Circular Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'circular_text_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
 
		$this->add_control(
			'circular_text_custom',
			[
				'label' => esc_html__( 'Custom Radius', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'circular_text_switch' => 'yes',	
				],
			]
		);
		$this->add_control(
            'circular_radious',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Select Value', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 800,
						'step' => 1,
					],
				],			
				'condition' => [
					'circular_text_switch' => 'yes',	
					'circular_text_custom' => 'yes',	
				],
            ]
        );
 
		$this->add_control(
			'circular_text_reversed',
			[
				'label' => esc_html__( 'Reverse Direction', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'circular_text_switch' => 'yes',	
				],
			]
		);
 
		$this->add_control(
			'circular_text_resized',
			[
				'label' => esc_html__( 'Auto Responsive', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'circular_text_switch' => 'yes',	
				],
			]
		);
		$this->end_controls_section();
		/*end circular text*/
 
		/*start background based text*/
		$this->start_controls_section(
			'background_based_text',
			[
				'label' => esc_html__( 'Background based Blend Mode', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'background_based_text_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'background_based_text_style',
			[
				'label' => esc_html__( 'Variations', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'color'  => esc_html__( 'Color', 'theplus' ),
					'color-burn' => esc_html__( 'Color Burn', 'theplus' ),
					'color-dodge' => esc_html__( 'Color Dodge', 'theplus' ),			
					'darken' => esc_html__( 'Darken', 'theplus' ),			
					'difference' => esc_html__( 'Difference', 'theplus' ),			
					'exclusion' => esc_html__( 'Exclusion', 'theplus' ),			
					'hard-light' => esc_html__( 'Hard Light', 'theplus' ),			
					'hue' => esc_html__( 'Hue', 'theplus' ),			
					'inherit' => esc_html__( 'Inherit', 'theplus' ),
					'initial' => esc_html__( 'Initial', 'theplus' ),			
					'lighten' => esc_html__( 'Lighten', 'theplus' ),
					'luminosity' => esc_html__( 'Luminosity', 'theplus' ),
					'multiply' => esc_html__( 'Multiply', 'theplus' ),
					'normal' => esc_html__( 'Normal', 'theplus' ),
					'overlay' => esc_html__( 'Overlay', 'theplus' ),
					'saturation' => esc_html__( 'Saturation', 'theplus' ),
					'screen' => esc_html__( 'Screen', 'theplus' ),
					'soft-light' => esc_html__( 'Soft Light', 'theplus' ),					
					'unset' => esc_html__( 'Unset', 'theplus' ),
				],
				'condition' => [
					'background_based_text_switch' => 'yes',	
				],
			]
		);		
 
		$this->end_controls_section();
		/*end background based text*/
 
		/*start image or gif overlay text image*/
		$this->start_controls_section(
			'img_gif_ovly_txtimg',
			[
				'label' => esc_html__( 'Knockout Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'img_gif_ovly_txtimg_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'img_gif_ovly_txtimg_option',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic'],
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,
				{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover',
				'condition' => [
					'img_gif_ovly_txtimg_switch' => 'yes',	
				],
			]
		);
		$this->end_controls_section();
		/*end image or gif overlay text image*/
 
		/*on hover image reveal start*/
		$this->start_controls_section(
			'on_hover_img_reveal',
			[
				'label' => esc_html__( 'On Hover Image Reveal', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'on_hover_img_reveal_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
            'on_hover_img_source', [
				'type' => Controls_Manager::MEDIA,
				'label' => esc_html__('Hover Image', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'on_hover_img_reveal_switch' => 'yes',
				],
			]
        );		
		$this->add_control(
			'adv_hover_img_reveal_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
					'style-4' => esc_html__( 'Style 4', 'theplus' ),
					'style-5' => esc_html__( 'Style 5', 'theplus' ),
					'style-6' => esc_html__( 'Style 6', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'on_hover_img_reveal_switch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*on hover image reveal end*/
 
		/*start marquee*/
		$this->start_controls_section(
			'marquee_section',
			[
				'label' => esc_html__( 'Marquee', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);		
		$this->add_control(
			'marquee_switch',
			[
				'label' => esc_html__( 'Enable/Disable', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);
		$this->add_control(
			'marquee_type',
			[
				'label' => esc_html__( 'Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),					
					'on_transition' => esc_html__( 'On Transition', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'marquee_direction',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'theplus' ),
					'right'  => esc_html__( 'Right', 'theplus' ),
					'up'  => esc_html__( 'Up', 'theplus' ),
					'down'  => esc_html__( 'Down', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'marquee_behavior',
			[
				'label' => esc_html__( 'Behavior', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'initial',
				'options' => [
					'initial'  => esc_html__( 'Scroll', 'theplus' ),
					'normal'  => esc_html__( 'Slide', 'theplus' ),
					'alternate'  => esc_html__( 'Alternate', 'theplus' ),
				],
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$this->add_control(
			'marquee_loop',
			[
				'label' => esc_html__( 'Loop', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => -1,
				'max' => 100,
				'step' => 1,
				'default' => -1,
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$this->add_control('marquee_scrollamount',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'default' => 6,
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$this->add_control(
			'marquee_scrolldelay',
			[
				'label' => esc_html__( 'Animation Duration', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 5,
				'default' => 85,				
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'default',
				],
			]
		);
		$this->add_control(
			'marquee_scrolldelay_t',
			[
				'label' => esc_html__( 'Animation Duration', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,				
				'condition' => [
					'marquee_switch' => 'yes',
					'marquee_type' => 'on_transition',
				],
			]
		);
		$this->add_responsive_control('marquee_width_default',
			[	
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
					'vw' => [
                        'min' => 0,
                        'max' => 100,
						'step' => 1,
                    ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block marquee' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};white-space: nowrap;',
				],
				'condition' => [
					'typography_listing' => 'default',
					'marquee_switch' => 'yes',	
					'marquee_type' => 'default',
				],
			]
		);
		$this->add_control('marquee_width_transition',
			[	
				'label' => esc_html__( 'Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'vw'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
					'vw' => [
                        'min' => 0,
                        'max' => 100,
						'step' => 1,
                    ],
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block' => 'width: {{SIZE}}{{UNIT}};max-width: {{SIZE}}{{UNIT}};display: inline-block;',
				],
				'condition' => [
					'typography_listing' => 'default',
					'marquee_switch' => 'yes',
					'marquee_type' => 'on_transition',
				],
			]
		);
		$this->end_controls_section();
		/*end marquee*/
		/*start marquee*/
		$this->start_controls_section(
			'advanced_options_section',
			[
				'label' => esc_html__( 'Advanced Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->add_control(
			'text_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'dynamic' => [
					'active'   => true,
				],
				'separator' => 'before',
				'condition' => [
					'typography_listing' => 'default',
				],
			]
		);
		$this->end_controls_section();
		/*end Advanced Options*/
		/*start style section*/
		/*adv typography style */
		$this->start_controls_section(
            'adv_typo_mainstyle',
            [
                'label' => esc_html__('Advanced Typography', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'adv_typography_text',
				'label' => esc_html__( 'Typography', 'theplus' ),				
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text',
			]
		);
		$this->add_responsive_control(
			'text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs('adv_typ_text_tabs');
		$this->start_controls_tab('adv_typ_text_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'adv_typ_text_color_option',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'adv_typ_text_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text' => 'color: {{VALUE}}',
				],
				'condition' => [
					'adv_typ_text_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'adv_typ_text_color_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{adv_typ_text_color_gradient_color1.VALUE}} {{adv_typ_text_color_gradient_color1_control.SIZE}}{{adv_typ_text_color_gradient_color1_control.UNIT}}, {{adv_typ_text_color_gradient_color2.VALUE}} {{adv_typ_text_color_gradient_color2_control.SIZE}}{{adv_typ_text_color_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'adv_typ_text_color_option' => 'gradient',
					'adv_typ_text_color_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{adv_typ_text_color_gradient_color1.VALUE}} {{adv_typ_text_color_gradient_color1_control.SIZE}}{{adv_typ_text_color_gradient_color1_control.UNIT}}, {{adv_typ_text_color_gradient_color2.VALUE}} {{adv_typ_text_color_gradient_color2_control.SIZE}}{{adv_typ_text_color_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'adv_typ_text_color_option' => 'gradient',
					'adv_typ_text_color_gradient_style' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'adv_typ_text_shadow',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text',
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'adv_typcss_filters_normal',
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
 
		$this->start_controls_tab('adv_typ_text_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'adv_typ_text_color_option_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'adv_typ_text_color_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text:hover' => 'color: {{VALUE}}',
				],
				'condition' => [
					'adv_typ_text_color_option_hover' => 'solid',
				],
			]
		);
		$this->add_control(
            'adv_typ_text_color_gradient_color1_hover',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color1_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color2_hover',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_color2_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_style_hover', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_angle_hover', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text:hover' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{adv_typ_text_color_gradient_color1_hover.VALUE}} {{adv_typ_text_color_gradient_color1_control_hover.SIZE}}{{adv_typ_text_color_gradient_color1_control_hover.UNIT}}, {{adv_typ_text_color_gradient_color2_hover.VALUE}} {{adv_typ_text_color_gradient_color2_control_hover.SIZE}}{{adv_typ_text_color_gradient_color2_control_hover.UNIT}})',
				],
				'condition'    => [
					'adv_typ_text_color_option_hover' => 'gradient',
					'adv_typ_text_color_gradient_style_hover' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'adv_typ_text_color_gradient_position_hover', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text:hover' => 'background-color: transparent;-webkit-background-clip: text;-webkit-text-fill-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{adv_typ_text_color_gradient_color1_hover.VALUE}} {{adv_typ_text_color_gradient_color1_control_hover.SIZE}}{{adv_typ_text_color_gradient_color1_control_hover.UNIT}}, {{adv_typ_text_color_gradient_color2_hover.VALUE}} {{adv_typ_text_color_gradient_color2_control_hover.SIZE}}{{adv_typ_text_color_gradient_color2_control_hover.UNIT}})',
				],
				'condition' => [
					'adv_typ_text_color_option_hover' => 'gradient',
					'adv_typ_text_color_gradient_style_hover' => 'radial',
			],
			'of_type' => 'gradient',
			]
        );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'adv_typ_text_shadow_hover',
				'label' => esc_html__( 'Text Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'adv_typcss_filters_hover',
				'selector' => '{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block:hover span,{{WRAPPER}} .plus-list-adv-typo-block .listing-typo-text:hover',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*end adv typography style */
 
		/*start text border strock */
		$this->start_controls_section(
            'text_border_strock',
            [
                'label' => esc_html__('Stroke/Fill Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'typography_listing' => 'default',
					'stroke_switch' => 'yes',					
				],
            ]
        );
		$this->add_responsive_control(
            'strock_width_normal',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Stroke Width', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke span' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs('text_border_strock_tabs');
		$this->start_controls_tab('text_border_strock_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);		
		$this->add_control(
			'strock_color_option',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'strock_color',
			[
				'label' => esc_html__( 'Stroke Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke span' => '-webkit-text-stroke-color: {{VALUE}}',
				],
				'condition' => [
					'strock_color_option' => 'solid',
				],
			]
		);
		$this->add_control(
            'strock_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition' => [
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition' => [
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke span' =>
					'background: -webkit-linear-gradient({{SIZE}}{{UNIT}}, {{strock_gradient_color1.VALUE}} {{strock_gradient_color1_control.SIZE}}{{strock_gradient_color1_control.UNIT}}, {{strock_gradient_color2.VALUE}} {{strock_gradient_color2_control.SIZE}}{{strock_gradient_color2_control.UNIT}});-webkit-background-clip: text;-webkit-text-stroke-color: transparent;',
				],
				'condition'    => [
					'strock_color_option' => 'gradient',
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
			't_strock_fill',
			[
				'label' => esc_html__( 'Text Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			't_strock_fill_color',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'of_type' => 'gradient',
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke span' => 'color: {{VALUE}};-webkit-text-fill-color: {{VALUE}}',
				],
				'condition'    => [
					't_strock_fill' => 'yes',
				],
			]
		);	
 
		$this->end_controls_tab();
		$this->start_controls_tab('text_border_strock_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
 
		$this->add_control(
			'strock_color_option_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Classic', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
			]
		);
		$this->add_control(
			'strock_color_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover span' => '-webkit-text-stroke-color: {{VALUE}}',
				],
				'condition' => [
					'strock_color_option_hover' => 'solid',
				],
			]
		);	
		$this->add_control(
            'strock_gradient_color1_hover',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'condition' => [
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color1_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition' => [
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color2_hover',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'transparent',
				'condition' => [
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_color2_control_hover',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
					],
				'render_type' => 'ui',
				'condition' => [
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'strock_gradient_angle_hover', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover span' =>
					'background: -webkit-linear-gradient({{SIZE}}{{UNIT}}, {{strock_gradient_color1_hover.VALUE}} {{strock_gradient_color1_control_hover.SIZE}}{{strock_gradient_color1_control_hover.UNIT}}, {{strock_gradient_color2_hover.VALUE}} {{strock_gradient_color2_control_hover.SIZE}}{{strock_gradient_color2_control_hover.UNIT}});-webkit-background-clip: text;-webkit-text-stroke-color: transparent;',
				],
				'condition'    => [
					'strock_color_option_hover' => 'gradient',
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
			't_strock_fill_hover',
			[
				'label' => esc_html__( 'Text Fill Color', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			't_strock_fill_color_hover',
			[
				'label' => esc_html__( 'Fill Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover,{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block.typo_stroke:hover span' => 'color: {{VALUE}};-webkit-text-fill-color:{{VALUE}}',
				],
				'condition' => [
					't_strock_fill_hover' => 'yes',
				],
			]
		);	
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*end text border strock */
		/*Continuous Animation start*/
		$this->start_controls_section(
            'section_continuous_anim_options',
            [
                'label' => esc_html__('Continuous Animation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'typography_listing' => 'default',
				],
            ]
        );
		$this->add_control(
			'text_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			'text_animation_effect',
			[
				'label' => esc_html__( 'Animation Effect', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'pulse',
				'options' => [
					'pulse'  => esc_html__( 'Pulse', 'theplus' ),
					'floating'  => esc_html__( 'Floating', 'theplus' ),
					'tossing'  => esc_html__( 'Tossing', 'theplus' ),
					'rotating'  => esc_html__( 'Rotating', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'text_animation_hover',
			[
				'label'        => esc_html__( 'Hover animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'text_transform_origin',
			[
				'label' => esc_html__( 'Transform Origin', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'top left'  => esc_html__( 'Top Left', 'theplus' ),
					'top center"'  => esc_html__( 'Top Center', 'theplus' ),
					'top right'  => esc_html__( 'Top Right', 'theplus' ),
					'center left'  => esc_html__( 'Center Left', 'theplus' ),
					'center center'  => esc_html__( 'Center Center', 'theplus' ),
					'center right'  => esc_html__( 'Center Right', 'theplus' ),
					'bottom left'  => esc_html__( 'Bottom Left', 'theplus' ),
					'bottom center'  => esc_html__( 'Bottom Center', 'theplus' ),
					'bottom right'  => esc_html__( 'Bottom Right', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
				],
				'render_type'  => 'template',
				'condition' => [
					'text_continuous_animation' => 'yes',
					'text_animation_effect' => 'rotating',
				],
			]
		);
		$this->add_responsive_control(
			'text_animation_duration',
			[
				'label' => esc_html__( 'Duration Time', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 50,
				'step' => 0.1,
				'default' => 2.5,
				'condition' => [
					'text_continuous_animation' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block .text-content-block' => 'animation-duration: {{VALUE}}s;-webkit-animation-duration: {{VALUE}}s;',
				],
				'separator' => 'after',
			]
		);		
		$this->end_controls_section();
		/*end Continuous Animation */
 
		/*Advance Underline start*/
		$this->start_controls_section(
            'section_adv_underline_options',
            [
                'label' => esc_html__('Advance Underline Options', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'typography_listing' => 'default',
				],
            ]
        );
		//Under Line Style
		$this->add_control(
			'typo_underline_style',
			[
				'label' => esc_html__( 'Underline Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'under_classic'  => esc_html__( 'Classic', 'theplus' ),
					'under_overlay'  => esc_html__( 'Overlay', 'theplus' ),					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'typo_overlay_under_style',
			[
				'label' => esc_html__( 'Overlay Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
					'style-3'  => esc_html__( 'Style 3', 'theplus' ),
					'style-4'  => esc_html__( 'Style 4', 'theplus' ),
					'style-5'  => esc_html__( 'Style 5', 'theplus' ),
					'style-6'  => esc_html__( 'Style 6', 'theplus' ),
					'style-7'  => esc_html__( 'Style 7', 'theplus' ),
				],
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$this->start_controls_tabs('text_under_tabs');
		$this->start_controls_tab('text_under_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'typo_underline_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'typo_classic_under',
			[
				'label' => esc_html__( 'Underline Line', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'underline'  => esc_html__( 'Underline', 'theplus' ),
					'overline'  => esc_html__( 'Overline', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block' => 'text-decoration-line: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$this->add_control(
			'typo_classic_under_style',
			[
				'label' => esc_html__( 'Underline Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'solid', 'theplus' ),
					'double'  => esc_html__( 'Double', 'theplus' ),
					'dotted'  => esc_html__( 'Dotted', 'theplus' ),
					'dashed'  => esc_html__( 'Dashed', 'theplus' ),
					'wavy'  => esc_html__( 'Wavy', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block' => 'text-decoration-style: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$this->add_control(
			'text_under_color',
			[
				'label' => esc_html__( 'Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block' => 'text-decoration-color: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$this->add_control(
			'text_under_overlay_bottom_off',
			[
				'label' => esc_html__( 'Bottom Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-1:before' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'text_under_overlay_height',
			[
				'label' => esc_html__( 'Line Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-1:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-2:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-3:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:after,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-5:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:after,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-7:before' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'text_under_overlay_bg',
				'label' => esc_html__( 'Underline Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-1:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-2:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-3:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:after,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-5:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:after,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-7:before',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab('text_under_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'typo_underline_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'typo_classic_under_hover',
			[
				'label' => esc_html__( 'Classic Hover', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'underline'  => esc_html__( 'Underline', 'theplus' ),
					'overline'  => esc_html__( 'Overline', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
					'line-through'  => esc_html__( 'Line Through', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block:hover' => 'text-decoration-line: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$this->add_control(
			'typo_classic_under_hover_style',
			[
				'label' => esc_html__( 'Underline Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'solid', 'theplus' ),
					'double'  => esc_html__( 'Double', 'theplus' ),
					'dotted'  => esc_html__( 'Dotted', 'theplus' ),
					'dashed'  => esc_html__( 'Dashed', 'theplus' ),
					'wavy'  => esc_html__( 'Wavy', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block:hover' => 'text-decoration-style: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);
		$this->add_control(
			'text_under_hover_color',
			[
				'label' => esc_html__( 'Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_classic .text-content-block:hover' => 'text-decoration-color: {{VALUE}}',
				],
				'condition' => [
					'typo_underline_style' => 'under_classic',
				],
			]
		);		
		$this->add_control(
			'text_under_overlay_hover_height',
			[
				'label' => esc_html__( 'Hover Line Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 0.5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-1:hover:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-2:hover:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-3:hover:before,
					{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:hover:after,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-5:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:hover:after' => 'height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style!' => 'style-7',
				],
			]
		);
 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'text_under_overlay_hover_bg',
				'label' => esc_html__( 'Underline Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-1:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-2:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-3:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-4:hover:after,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-5:hover:before,{{WRAPPER}} .pt_plus_adv_typo_block.under_overlay.overlay-style-6:after',
				'condition' => [
					'typo_underline_style' => 'under_overlay',
					'typo_overlay_under_style!' => 'style-7',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*Advance Underline End*/
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_animation_styling',
            [
                'label' => esc_html__('On Scroll View Animation', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
 
		$this->add_control(
			'animation_effects',
			[
				'label'   => esc_html__( 'Choose Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_animation_options(),
			]
		);
		$this->add_control(
            'animation_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_duration_default',
            [
				'label'   => esc_html__( 'Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animate_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_duration_default' => 'yes',
				],
            ]
        );
		$this->add_control(
			'animation_out_effects',
			[
				'label'   => esc_html__( 'Out Animation Effect', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'no-animation',
				'options' => theplus_get_out_animation_options(),
				'separator' => 'before',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_delay',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Out Animation Delay', 'theplus'),
				'default' => [
					'unit' => '',
					'size' => 50,
				],
				'range' => [
					'' => [
						'min'	=> 0,
						'max'	=> 4000,
						'step' => 15,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
            ]
        );
		$this->add_control(
            'animation_out_duration_default',
            [
				'label'   => esc_html__( 'Out Animation Duration', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
				],
			]
		);
		$this->add_control(
            'animation_out_duration',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Duration Speed', 'theplus'),
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'range' => [
					'px' => [
						'min'	=> 100,
						'max'	=> 10000,
						'step' => 100,
					],
				],
				'condition' => [
					'animation_effects!' => 'no-animation',
					'animation_out_effects!' => 'no-animation',
					'animation_out_duration_default' => 'yes',
				],
            ]
        );
		$this->add_control(
			'animation_hidden',
			[
				'label' => esc_html__( 'Overflow Hidden', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hidden', 'theplus' ),
				'label_off' => esc_html__( 'Visible', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'animation_effects!' => 'no-animation',
				],
			]
		);
		$this->end_controls_section();
	}
 
	protected function render() {
        $settings = $this->get_settings_for_display();
		$advanced_typography_text = $settings['advanced_typography_text'];		
		$background_based_text_style = $settings['background_based_text_style'];
		$circular_text_switch = ($settings['circular_text_switch']=='yes') ? "typo_circular" : "";		
		$stroke_switch = ($settings['stroke_switch']=='yes') ? "typo_stroke" : "";
		$background_based_text_switch = ($settings['background_based_text_switch']=='yes') ? "typo_bg_based_text" : "";
		$img_gif_ovly_txtimg_switch = ($settings['img_gif_ovly_txtimg_switch']=='yes') ? "typo_gif_based_text" : "";
		$on_hover_img_reveal_switch = ($settings['on_hover_img_reveal_switch']=='yes') ? "typo_on_hover_img_reveal" : "";
 
		$typo_underline_style = ($settings["typo_underline_style"]!='none') ? $settings["typo_underline_style"] : '';		
		if($typo_underline_style!='' && $typo_underline_style=='under_overlay'){
			$typo_underline_style .= (isset($settings['typo_overlay_under_style'])) ? ' overlay-'.esc_attr($settings['typo_overlay_under_style']) : '';
		}
 
		$text_continuous_animation='';
		if(!empty($settings["text_continuous_animation"]) && $settings["text_continuous_animation"]=='yes'){
			if($settings["text_animation_hover"]=='yes'){
				$text_animation_class='hover_';
			}else{
				$text_animation_class='image-';
			}
			$text_continuous_animation=$text_animation_class.$settings["text_animation_effect"];
		}
 
		$animation_effects=$settings["animation_effects"];
		$animation_delay= isset($settings["animation_delay"]["size"]) ? $settings["animation_delay"]["size"] : 50;
		if($animation_effects=='no-animation'){
			$animated_class = '';
			$animation_attr = '';
			$animation_hidden = '';
		}else{
			$animate_offset = theplus_scroll_animation();
			$animated_class = 'animate-general';
			$animation_hidden = (!empty($settings["animation_hidden"]) && $settings["animation_hidden"]=='yes') ? 'animate-hidden' : '';
			$animation_attr = ' data-animate-type="'.esc_attr($animation_effects).'" data-animate-delay="'.esc_attr($animation_delay).'"';
			$animation_attr .= ' data-animate-offset="'.esc_attr($animate_offset).'"';
			if($settings["animation_duration_default"]=='yes'){
				$animate_duration=$settings["animate_duration"]["size"];
				$animation_attr .= ' data-animate-duration="'.esc_attr($animate_duration).'"';
			}
			if(!empty($settings["animation_out_effects"]) && $settings["animation_out_effects"]!='no-animation'){
				$animation_attr .= ' data-animate-out-type="'.esc_attr($settings["animation_out_effects"]).'" data-animate-out-delay="'.esc_attr($settings["animation_out_delay"]["size"]).'"';					
				if($settings["animation_out_duration_default"]=='yes'){						
					$animation_attr .= ' data-animate-out-duration="'.esc_attr($settings["animation_out_duration"]["size"]).'"';
				}
			}
		}
 
		$circular_attr='';
		if($settings['circular_text_switch']=='yes'){
			if($settings['circular_text_custom'] == 'yes'){
				$circular_radious = (isset($settings['circular_radious']['size'])) ? $settings['circular_radious']['size'] : '360';
				$circular_attr .= ' data-custom-radius="' . esc_attr($circular_radious) . '" ';
			}
			if($settings['circular_text_reversed'] == 'yes' && !empty($circular_text_reversed)){				
				$circular_attr .= ' data-custom-reversed="yes" ';
			}
			if($settings['circular_text_resized'] == 'yes' && !empty($circular_text_resized)){				
				$circular_attr .= ' data-custom-resize="yes" ';
			}			
		}
 
		$mix_blend_attr=$data_attr='';
		if($settings['background_based_text_switch']=='yes'){
			$mix_blend_attr .= 'mix-blend-mode:'.esc_attr($background_based_text_style).';';
			$data_attr .='data-blend-mode="'.esc_attr($background_based_text_style).'"';
		}

		$adv_typ_text=$loop_typo_text='';				
		if(!empty($settings["typography_listing"]) && $settings["typography_listing"]=='listing'){
			//Loop Listing Text Typo
			if(!empty($settings["listing_content"])) {
				$i=0;
				foreach($settings["listing_content"] as $item) {
					if ( ! empty( $item['text_link']['url'] ) ) {
						$this->add_render_attribute( 'loop_typo_link'.$i, 'href', $item['text_link']['url'] );
						if ( $item['text_link']['is_external'] ) {
							$this->add_render_attribute( 'loop_typo_link'.$i, 'target', '_blank' );
						}
						if ( $item['text_link']['nofollow'] ) {
							$this->add_render_attribute( 'loop_typo_link'.$i, 'rel', 'nofollow' );
						}
						$loop_tag='a';
					}else{
						$loop_tag='span';								
					}

					$data_class = $mix_blend_style ='';
					if(!empty($item['list_typo_stroke']) && $item['list_typo_stroke']=='yes'){
						$data_class .= ' list_typo_stroke';
					}
					if(!empty($item['bg_based_text_switch']) && $item['bg_based_text_switch']=='yes'){
						$data_class .= ' bg_based_text';
						$mix_blend_style .= 'mix-blend-mode:'.esc_attr($item['bg_based_text_style']).';';
					}
					if(!empty($item['img_gif_ovly_txtimg_switch']) && $item['img_gif_ovly_txtimg_switch']=='yes'){
						$data_class .= ' typo_gif_based_text';
					}
					if(!empty($item['on_hover_img_reveal_switch']) && $item['on_hover_img_reveal_switch']=='yes'){
						$data_class .= ' typo_on_hover_img_reveal';
					}

					if(!empty($item["text_continuous_animation"]) && $item["text_continuous_animation"]=='yes'){
						if($item["text_animation_hover"]=='yes'){
							$text_animation_class='hover_';
						}else{
							$text_animation_class='image-';
						}
						$data_class .= $text_animation_class.$item["text_animation_effect"];
					}

					$typo_underline_style = ($item["typo_underline_style"]!='none') ? $item["typo_underline_style"] : '';

					if($typo_underline_style!='' && $typo_underline_style=='under_overlay'){
						$typo_underline_style .= ($item['typo_overlay_under_style']!='') ? ' overlay-'.$item['typo_overlay_under_style'] : '';
					}
					$marquee_attr=$marquee_class='';
					if($item['marquee_switch']=='yes'){
						$marquee_attr .= (!empty($item['marquee_direction'])) ? ' direction="'.esc_attr($item['marquee_direction']).'"' : '';
						$marquee_attr .= (!empty($item['marquee_behavior'])) ? ' behavior="'.esc_attr($item['marquee_behavior']).'"' : '';						
						$marquee_attr .= (!empty($item['marquee_loop'])) ? ' loop="'.esc_attr($item['marquee_loop']).'"' : '';
						$marquee_attr .= (!empty($item['marquee_scrollamount'])) ? ' scrollamount="'.esc_attr($item['marquee_scrollamount']).'"' : '';
						$marquee_attr .= (!empty($item['marquee_scrolldelay'])) ? ' scrolldelay="'.esc_attr($item['marquee_scrolldelay']).'"' : '';
						if(!empty($item['marquee_type']) && $item['marquee_type']=='on_transition'){
							$loop_tag='span';
							$marquee_class ='tp_adv_typo_'.esc_attr($item['marquee_direction']);
							$marquee_attr = '';
						}else{
							$loop_tag='marquee';
						}
					}

					$loop_img_reveal_open = $loop_img_reveal_close= '';
					if(!empty($item['on_hover_img_reveal_switch']) && $item['on_hover_img_reveal_switch']=='yes'){
						if(!empty($item['adv_hover_img_reveal_style'])){
							$on_hover_img_source='';
							if(!empty($item['on_hover_img_source']['url'])){
								$on_hover_img_source=$item['on_hover_img_source']['url'];
							}
							$loop_img_reveal_open .='<div class="tp-block" style="display:inline-flex;cursor:pointer;" data-fx="';
							if($item['adv_hover_img_reveal_style']=='style-1'){
								$loop_img_reveal_open .='1';
							}else if($item['adv_hover_img_reveal_style']=='style-2'){
								$loop_img_reveal_open .='2';
							}else if($item['adv_hover_img_reveal_style']=='style-3'){
								$loop_img_reveal_open .='3';
							}else if($item['adv_hover_img_reveal_style']=='style-4'){
								$loop_img_reveal_open .='4';
							}else if($item['adv_hover_img_reveal_style']=='style-5'){
								$loop_img_reveal_open .='15';
							}else if($item['adv_hover_img_reveal_style']=='style-6'){
								$loop_img_reveal_open .='22';
							}
							$loop_img_reveal_open .='">';
							if(! empty( $item['text_link']['url'] )){ 
								$loop_img_reveal_open .='<a '.$this->get_render_attribute_string( "loop_typo_link".$i ).' class="block__title" data-img="'.esc_attr($on_hover_img_source).'">';
							}else{
								$loop_img_reveal_open .='<a class="block__title" data-img="'.esc_attr($on_hover_img_source).'">';
							}

							$loop_img_reveal_close .='</a></div>';
						}
					}

					$magic_class = $magic_attr = $parallax_scroll = '';
					if (!empty($item['loop_magic_scroll']) && $item['loop_magic_scroll'] == 'yes') {
						if($item["loop_scroll_option_popover_toggle"]==''){
							$scroll_offset=0;
							$scroll_duration=300;
						}else{
							$scroll_offset=$item['loop_scroll_option_scroll_offset'];
							$scroll_duration=$item['loop_scroll_option_scroll_duration'];
						}
						if($item["loop_scroll_from_popover_toggle"]==''){
							$scroll_x_from=0;
							$scroll_y_from=0;
							$scroll_opacity_from=1;
							$scroll_scale_from=1;
							$scroll_rotate_from=0;
						}else{
							$scroll_x_from=$item['loop_scroll_from_scroll_x_from'];
							$scroll_y_from=$item['loop_scroll_from_scroll_y_from'];
							$scroll_opacity_from=$item['loop_scroll_from_scroll_opacity_from'];
							$scroll_scale_from=$item['loop_scroll_from_scroll_scale_from'];
							$scroll_rotate_from=$item['loop_scroll_from_scroll_rotate_from'];
						}
						if($item["loop_scroll_to_popover_toggle"]==''){
							$scroll_x_to=0;
							$scroll_y_to=-50;
							$scroll_opacity_to=1;
							$scroll_scale_to=1;
							$scroll_rotate_to=0;
						}else{
							$scroll_x_to=$item['loop_scroll_to_scroll_x_to'];
							$scroll_y_to=$item['loop_scroll_to_scroll_y_to'];
							$scroll_opacity_to=$item['loop_scroll_to_scroll_opacity_to'];
							$scroll_scale_to=$item['loop_scroll_to_scroll_scale_to'];
							$scroll_rotate_to=$item['loop_scroll_to_scroll_rotate_to'];
						}
						$magic_attr .= ' data-scroll_type="position" ';
						$magic_attr .= ' data-scroll_offset="' . esc_attr($scroll_offset) . '" ';
						$magic_attr .= ' data-scroll_duration="' . esc_attr($scroll_duration) . '" ';

						$magic_attr .= ' data-scroll_x_from="' . esc_attr($scroll_x_from) . '" ';
						$magic_attr .= ' data-scroll_x_to="' . esc_attr($scroll_x_to) . '" ';
						$magic_attr .= ' data-scroll_y_from="' . esc_attr($scroll_y_from) . '" ';
						$magic_attr .= ' data-scroll_y_to="' . esc_attr($scroll_y_to) . '" ';
						$magic_attr .= ' data-scroll_opacity_from="' . esc_attr($scroll_opacity_from) . '" ';
						$magic_attr .= ' data-scroll_opacity_to="' . esc_attr($scroll_opacity_to) . '" ';
						$magic_attr .= ' data-scroll_scale_from="' . esc_attr($scroll_scale_from) . '" ';
						$magic_attr .= ' data-scroll_scale_to="' . esc_attr($scroll_scale_to) . '" ';
						$magic_attr .= ' data-scroll_rotate_from="' . esc_attr($scroll_rotate_from) . '" ';
						$magic_attr .= ' data-scroll_rotate_to="' . esc_attr($scroll_rotate_to) . '" ';

						$parallax_scroll .= ' parallax-scroll ';
						$magic_class .= ' magic-scroll ';
					}

					$move_parallax=$move_parallax_attr=$parallax_move='';
					if(!empty($item['plus_mouse_move_parallax']) && $item['plus_mouse_move_parallax']=='yes'){
						$move_parallax='pt-plus-move-parallax';
						$parallax_move='parallax-move';
						$parallax_speed_x=(isset($item["plus_mouse_parallax_speed_x"]["size"])) ? $item["plus_mouse_parallax_speed_x"]["size"] : 30;
						$parallax_speed_y=(isset($item["plus_mouse_parallax_speed_y"]["size"])) ? $item["plus_mouse_parallax_speed_y"]["size"] : 30;
						$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($parallax_speed_x) . '" ';
						$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($parallax_speed_y) . '" ';
					}

					if(!empty($magic_class) || !empty($move_parallax)){
						$loop_typo_text .= '<span class="plus-typo-magic-scroll '.esc_attr($magic_class).' '.esc_attr($move_parallax).'">';
					}
					if(!empty($item['typo_text'])){

						$loop_typo_text .= '<span class=" plus-adv-text-typo elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($typo_underline_style).' '.esc_attr($animated_class).' ' . esc_attr($parallax_scroll) . ' '.esc_attr($parallax_move).'" ' . $magic_attr . ' '.$move_parallax_attr.' '.$animation_attr.'>';

							$loop_typo_text .= $loop_img_reveal_open;
							if ( ! empty( $item['text_link']['url'] ) && (!empty($item['on_hover_img_reveal_switch']) && $item['on_hover_img_reveal_switch']=='yes')) {
								$loop_typo_text .='<div  class="'.$marquee_class.' listing-typo-text '.esc_attr($data_class).' " '.$marquee_attr.'  style="display:inline;'.$mix_blend_style.'">';
									$loop_typo_text .= htmlspecialchars_decode($item['typo_text']);
								$loop_typo_text .= '</div>';
							}else{
								$loop_typo_text .='<'.$loop_tag.'  '.$this->get_render_attribute_string( "loop_typo_link".$i ).' class="'.$marquee_class.' listing-typo-text '.esc_attr($data_class).' " '.$marquee_attr.'  style="'.$mix_blend_style.'">';
								$loop_typo_text .= htmlspecialchars_decode($item['typo_text']);
								$loop_typo_text .= '</'.$loop_tag.'>';
							}

							$loop_typo_text .= $loop_img_reveal_close;

						$loop_typo_text .= '</span>';
					}
					if(!empty($magic_class) || !empty($move_parallax)){
						$loop_typo_text .= '</span>';
					}
					$i++;
				}
			}
		}else{
			//Default Text Typo
			$uidat=uniqid('advtypo');

			if ( ! empty( $settings['text_link']['url'] ) ) {
				$this->add_render_attribute( 'typo_link_a', 'href', $settings['text_link']['url'] );
				if ( $settings['text_link']['is_external'] ) {
					$this->add_render_attribute( 'typo_link_a', 'target', '_blank' );
				}
				if ( $settings['text_link']['nofollow'] ) {
					$this->add_render_attribute( 'typo_link_a', 'rel', 'nofollow' );
				}
			}

			//vertical text writing-mode
			$text_write_mode='';
			if($settings['text_write_mode']!='unset'){
				$text_write_mode .= 'max-block-size: max-content;writing-mode: '.esc_attr($settings['text_write_mode']).';-webkit-writing-mode: '.esc_attr($settings['text_write_mode']).';-ms-writing-mode: '.esc_attr($settings['text_write_mode']).';';
			}
			if($settings["text_orientation"]=='yes'){
				$text_write_mode .= 'text-orientation: upright;';
			}
			if($settings['text_direction_ltr']!='initial'){
				$text_write_mode .= 'unicode-bidi: bidi-override;direction: '.esc_attr($settings['text_direction_ltr']).';';
			}
			if(!empty($advanced_typography_text)){
				$span_replace_tag = !empty($settings['span_replace_tag']) ? $settings['span_replace_tag'] : 'span';
				if(!empty($span_replace_tag) && $span_replace_tag != 'span'){
					echo '<style>.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h1,.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h2,.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h3,.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h4,.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h5,.pt-plus-adv-typo-wrapper .pt_plus_adv_typo_block > h6 {padding: 0;margin: 0;}</style>';
				}
				$loop_text= $advanced_typography_text;	
				if($settings['marquee_switch']=='yes'){
					if(!empty($settings['marquee_type']) && $settings['marquee_type']=='on_transition'){
						$adv_typ_text = '<'.esc_attr(theplus_validate_html_tag($span_replace_tag)).' id="'.esc_attr($uidat).'" class="tp_adv_typo_'.esc_attr($settings['marquee_direction']).' text-content-block  tp-adv-typ-marquee '.esc_attr($circular_text_switch).' '.esc_attr($stroke_switch).' '.esc_attr($background_based_text_switch).' '.esc_attr($img_gif_ovly_txtimg_switch).' '.esc_attr($on_hover_img_reveal_switch).' '.esc_attr($text_continuous_animation).' '.esc_attr($animated_class).'" '.$data_attr.' style="white-space: nowrap;'.$text_write_mode.$mix_blend_attr.'" '.$circular_attr.' '.$animation_attr.'>'.htmlspecialchars_decode($loop_text).'</'.esc_attr(theplus_validate_html_tag($span_replace_tag)).'>';
					}else{
						$marquee_attr='';
						$marquee_attr .= (!empty($settings['marquee_direction'])) ? ' direction="'.esc_attr($settings['marquee_direction']).'"' : '';
						$marquee_attr .= (!empty($settings['marquee_behavior'])) ? ' behavior="'.esc_attr($settings['marquee_behavior']).'"' : '';						
						$marquee_attr .= (!empty($settings['marquee_loop'])) ? ' loop="'.esc_attr($settings['marquee_loop']).'"' : '';
						$marquee_attr .= (!empty($settings['marquee_scrollamount'])) ? ' scrollamount="'.esc_attr($settings['marquee_scrollamount']).'"' : '';
						$marquee_attr .= (!empty($settings['marquee_scrolldelay'])) ? ' scrolldelay="'.esc_attr($settings['marquee_scrolldelay']).'"' : '';

						$adv_typ_text = '<marquee '.$marquee_attr.' id="'.esc_attr($uidat).'" class="text-content-block  '.esc_attr($circular_text_switch).' '.esc_attr($stroke_switch).' '.esc_attr($background_based_text_switch).' '.esc_attr($img_gif_ovly_txtimg_switch).' '.esc_attr($text_continuous_animation).' '.esc_attr($animated_class).'" '.$data_attr.' style="white-space: nowrap;'.$text_write_mode.$mix_blend_attr.'" '.$circular_attr.' '.$animation_attr.'>'.htmlspecialchars_decode($loop_text).'</marquee>';
					}

				}else{
					if (!empty($settings['text_link']['url'])){								
						$adv_typ_text=$adv_typ_text1 =$adv_typ_text2='';
						if(!empty($settings['on_hover_img_reveal_switch']) && $settings['on_hover_img_reveal_switch']=='yes'){
							if(!empty($settings['adv_hover_img_reveal_style'])){
								$adv_typ_text .='<div class="tp-block" data-fx="';										
								if($settings['adv_hover_img_reveal_style']=='style-1'){
									$adv_typ_text .='1';
								}else if($settings['adv_hover_img_reveal_style']=='style-2'){
									$adv_typ_text .='2';
								}else if($settings['adv_hover_img_reveal_style']=='style-3'){
									$adv_typ_text .='3';
								}else if($settings['adv_hover_img_reveal_style']=='style-4'){
									$adv_typ_text .='4';
								}else if($settings['adv_hover_img_reveal_style']=='style-5'){
									$adv_typ_text .='15';
								}else if($settings['adv_hover_img_reveal_style']=='style-6'){
									$adv_typ_text .='22';
								}
								$adv_typ_text .='">';
							}									

							$on_hover_img_source='';
							if(!empty($settings['on_hover_img_source']['url'])){
								$on_hover_img_source=$settings['on_hover_img_source']['url'];
							}

							$adv_typ_text1 .='block__title';									
							$adv_typ_text2 .=$on_hover_img_source;
						}

						$adv_typ_text .= '<a id="'.esc_attr($uidat).'" '.$this->get_render_attribute_string( "typo_link_a" ).' class="text-content-block '.esc_attr($adv_typ_text1).' '.esc_attr($circular_text_switch).' '.esc_attr($stroke_switch).' '.esc_attr($background_based_text_switch).' '.esc_attr($img_gif_ovly_txtimg_switch).' '.esc_attr($on_hover_img_reveal_switch).' '.esc_attr($text_continuous_animation).' '.esc_attr($animated_class).'" '.$data_attr.' style="'.$text_write_mode.$mix_blend_attr.' " '.$circular_attr.' '.$animation_attr.' data-img="'.esc_url($adv_typ_text2).'">'.htmlspecialchars_decode($loop_text).'</a>';						
					}else{
						$adv_typ_text ='';
						if(!empty($settings['on_hover_img_reveal_switch']) && $settings['on_hover_img_reveal_switch']=='yes'){
							if(!empty($settings['adv_hover_img_reveal_style'])){
								$adv_typ_text .='<div class="tp-block" data-fx="';
								if($settings['adv_hover_img_reveal_style']=='style-1'){
									$adv_typ_text .='1';
								}else if($settings['adv_hover_img_reveal_style']=='style-2'){
									$adv_typ_text .='2';
								}else if($settings['adv_hover_img_reveal_style']=='style-3'){
									$adv_typ_text .='3';
								}else if($settings['adv_hover_img_reveal_style']=='style-4'){
									$adv_typ_text .='4';
								}else if($settings['adv_hover_img_reveal_style']=='style-5'){
									$adv_typ_text .='15';
								}else if($settings['adv_hover_img_reveal_style']=='style-6'){
									$adv_typ_text .='22';
								}
								$adv_typ_text .='">';
							}

							$on_hover_img_source='';
							if(!empty($settings['on_hover_img_source']['url'])){
								$on_hover_img_source=$settings['on_hover_img_source']['url'];
							}									
							$adv_typ_text .='<a class="block__title" data-img="'.esc_url($on_hover_img_source).'">';
						}

						$adv_typ_text .= '<'.esc_attr(theplus_validate_html_tag($span_replace_tag)).' id="'.esc_attr($uidat).'" class="text-content-block  '.esc_attr($circular_text_switch).' '.esc_attr($stroke_switch).' '.esc_attr($background_based_text_switch).' '.esc_attr($img_gif_ovly_txtimg_switch).' '.esc_attr($on_hover_img_reveal_switch).' '.esc_attr($text_continuous_animation).' '.esc_attr($animated_class).'" '.$data_attr.' style="'.$text_write_mode.$mix_blend_attr.'" '.$circular_attr.' '.$animation_attr.'>'.htmlspecialchars_decode($loop_text).'</'.esc_attr(theplus_validate_html_tag($span_replace_tag)).'>';

						if(!empty($settings['on_hover_img_reveal_switch']) && $settings['on_hover_img_reveal_switch']=='yes'){
							if(!empty($settings['adv_hover_img_reveal_style'])){
								$adv_typ_text .='</a></div>';
							}
						}
					}						
				}
			}
		}
 
		/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-adv-typo-widget";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';				
		/*--Plus Extra ---*/
 
		$id = $this->get_id();
		$advanced_typography ='<div id="at'.$id.'" class="pt-plus-adv-typo-wrapper">';
		if(!empty($settings["typography_listing"]) && $settings["typography_listing"]=='listing'){
			$advanced_typography .= '<div class="plus-list-adv-typo-block '.esc_attr($animation_hidden).'">';
			$advanced_typography .= $loop_typo_text;
			$advanced_typography .= '</div>';
		}else{
			$advanced_typography .='<div class="pt_plus_adv_typo_block  '.esc_attr($animation_hidden).' '.esc_attr($typo_underline_style).'">';
				$advanced_typography .= $adv_typ_text;
			$advanced_typography .='</div>';
		}
		$advanced_typography .='</div>';
		/*normal marquee css*/
 
		if(!empty($settings['marquee_switch']) && $settings['marquee_switch'] =='yes'){
			if(!empty($settings['marquee_type']) && $settings['marquee_type'] =='on_transition'){
				if(!empty($settings['marquee_scrolldelay_t'])){				
					if(!empty($settings['marquee_direction']) && $settings['marquee_direction']=='left'){
						$advanced_typography .='<style>#at'.esc_attr($id).'.pt-plus-adv-typo-wrapper .tp_adv_typo_left{
						-moz-animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite !important;
						-webkit-animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite !important;
						animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite !important;}
	
						@-moz-keyframes tp_adv_typo_left-at'.esc_attr($id).' {
						0%   { -moz-transform: translateX(100%); }
						100% { -moz-transform: translateX(-100%); }
						}
						@-webkit-keyframes tp_adv_typo_left-at'.esc_attr($id).' {
						0%   { -webkit-transform: translateX(100%); }
						100% { -webkit-transform: translateX(-100%); }
						}
						@keyframes tp_adv_typo_left-at'.esc_attr($id).' {
						0%   { 
						-moz-transform: translateX(100%); 
						-webkit-transform: translateX(100%); 
						transform: translateX(100%); 		
						}
						100% { 
						-moz-transform: translateX(-100%); 
						-webkit-transform: translateX(-100%);
						transform: translateX(-100%); 
						}
						}</style>';
					}
	
					if(!empty($settings['marquee_direction']) && $settings['marquee_direction']=='right'){
						$advanced_typography .='<style>#at'.esc_attr($id).'.pt-plus-adv-typo-wrapper .tp_adv_typo_right{
						-moz-animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						-webkit-animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;}
	
						@-moz-keyframes tp_adv_typo_right-at'.esc_attr($id).' {
						0%   { -moz-transform: translateX(-100%); }
						100% { -moz-transform: translateX(100%); }
						}
						@-webkit-keyframes tp_adv_typo_right-at'.esc_attr($id).' {
						0%   { -webkit-transform: translateX(-100%); }
						100% { -webkit-transform: translateX(100%); }
						}
						@keyframes tp_adv_typo_right-at'.esc_attr($id).' {
						0%   { 
						-moz-transform: translateX(-100%); 
						-webkit-transform: translateX(-100%); 
						transform: translateX(-100%); 		
						}
						100% { 
						-moz-transform: translateX(100%); 
						-webkit-transform: translateX(100%);
						transform: translateX(100%); 
						}
						}</style>';
					}
	
					if(!empty($settings['marquee_direction']) && $settings['marquee_direction']=='up'){
						$advanced_typography .='<style>#at'.esc_attr($id).'.pt-plus-adv-typo-wrapper .tp_adv_typo_up{
						-moz-animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						-webkit-animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;}
	
						@-moz-keyframes tp_adv_typo_up-at'.esc_attr($id).' {
						0%   { -moz-transform: translateY(100%); }
						100% { -moz-transform: translateY(-100%); }
						}
						@-webkit-keyframes tp_adv_typo_up-at'.esc_attr($id).' {
						0%   { -webkit-transform: translateY(100%); }
						100% { -webkit-transform: translateY(-100%); }
						}
						@keyframes tp_adv_typo_up-at'.esc_attr($id).' {
						0%   { 
						-moz-transform: translateY(100%);
						-webkit-transform: translateY(100%);
						transform: translateY(100%); 		
						}
						100% { 
						-moz-transform: translateY(-100%);
						-webkit-transform: translateY(-100%);
						transform: translateY(-100%); 
						}
						}</style>';
					}
	
					if(!empty($settings['marquee_direction']) && $settings['marquee_direction']=='down'){
						$advanced_typography .='<style>#at'.esc_attr($id).'.pt-plus-adv-typo-wrapper .tp_adv_typo_down{
						-moz-animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						-webkit-animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;
						animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($settings['marquee_scrolldelay_t']).'s linear infinite;}
	
						@-moz-keyframes tp_adv_typo_down-at'.esc_attr($id).' {
						0%   { -moz-transform: translateY(-100%); }
						100% { -moz-transform: translateY(100%); }
						}
						@-webkit-keyframes tp_adv_typo_down-at'.esc_attr($id).' {
						0%   { -webkit-transform: translateY(-100%); }
						100% { -webkit-transform: translateY(100%); }
						}
						@keyframes tp_adv_typo_down-at'.esc_attr($id).' {
						0%   { 
						-moz-transform: translateY(-100%); 
						-webkit-transform: translateY(-100%);
						transform: translateY(-100%); 		
						}
						100% { 
						-moz-transform: translateY(100%);
						-webkit-transform: translateY(100%);
						transform: translateY(100%); 
						}
						}</style>';
					}
				}
			}
		}
		/*normal marquee css end*/
 
		/*repeater marquee css start*/
		if(!empty($settings["listing_content"])) {
			$i=0;
			foreach($settings["listing_content"] as $item) {
				if(!empty($item['marquee_switch']) && $item['marquee_switch'] =='yes'){
		
						if(!empty($item['marquee_scrolldelay_t'])){
		
							if(!empty($item['marquee_direction']) && $item['marquee_direction']=='left'){
								$advanced_typography .='<style>.pt-plus-adv-typo-wrapper .elementor-repeater-item-'.esc_attr($item['_id']).' .tp_adv_typo_left.listing-typo-text{
								-moz-animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite !important;
								-webkit-animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite !important;
								animation: tp_adv_typo_left-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite !important;}
			
								@-moz-keyframes tp_adv_typo_left-at'.esc_attr($id).' {
								0%   { -moz-transform: translateX(100%); }
								100% { -moz-transform: translateX(-100%); }
								}
								@-webkit-keyframes tp_adv_typo_left-at'.esc_attr($id).' {
								0%   { -webkit-transform: translateX(100%); }
								100% { -webkit-transform: translateX(-100%); }
								}
								@keyframes tp_adv_typo_left-at'.esc_attr($id).' {
								0%   { 
								-moz-transform: translateX(100%); 
								-webkit-transform: translateX(100%); 
								transform: translateX(100%); 		
								}
								100% { 
								-moz-transform: translateX(-100%); 
								-webkit-transform: translateX(-100%);
								transform: translateX(-100%); 
								}
								}</style>';
							}
			
							if(!empty($item['marquee_direction']) && $item['marquee_direction']=='right'){
								$advanced_typography .='<style>.pt-plus-adv-typo-wrapper .elementor-repeater-item-'.esc_attr($item['_id']).' .tp_adv_typo_right.listing-typo-text{
								-moz-animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								-webkit-animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								animation: tp_adv_typo_right-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;}
			
								@-moz-keyframes tp_adv_typo_right-at'.esc_attr($id).' {
								0%   { -moz-transform: translateX(-100%); }
								100% { -moz-transform: translateX(100%); }
								}
								@-webkit-keyframes tp_adv_typo_right-at'.esc_attr($id).' {
								0%   { -webkit-transform: translateX(-100%); }
								100% { -webkit-transform: translateX(100%); }
								}
								@keyframes tp_adv_typo_right-at'.esc_attr($id).' {
								0%   { 
								-moz-transform: translateX(-100%); 
								-webkit-transform: translateX(-100%); 
								transform: translateX(-100%); 		
								}
								100% { 
								-moz-transform: translateX(100%); 
								-webkit-transform: translateX(100%);
								transform: translateX(100%); 
								}
								}</style>';
							}
			
							if(!empty($item['marquee_direction']) && $item['marquee_direction']=='up'){
								$advanced_typography .='<style>.pt-plus-adv-typo-wrapper .elementor-repeater-item-'.esc_attr($item['_id']).' .tp_adv_typo_up.listing-typo-text{
								-moz-animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								-webkit-animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								animation: tp_adv_typo_up-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;}
			
								@-moz-keyframes tp_adv_typo_up-at'.esc_attr($id).' {
								0%   { -moz-transform: translateY(100%); }
								100% { -moz-transform: translateY(-100%); }
								}
								@-webkit-keyframes tp_adv_typo_up-at'.esc_attr($id).' {
								0%   { -webkit-transform: translateY(100%); }
								100% { -webkit-transform: translateY(-100%); }
								}
								@keyframes tp_adv_typo_up-at'.esc_attr($id).' {
								0%   { 
								-moz-transform: translateY(100%);
								-webkit-transform: translateY(100%);
								transform: translateY(100%); 		
								}
								100% { 
								-moz-transform: translateY(-100%);
								-webkit-transform: translateY(-100%);
								transform: translateY(-100%); 
								}
								}</style>';
							}
			
							if(!empty($item['marquee_direction']) && $item['marquee_direction']=='down'){
								$advanced_typography .='<style>.pt-plus-adv-typo-wrapper .elementor-repeater-item-'.esc_attr($item['_id']).' .tp_adv_typo_down.listing-typo-text{
								-moz-animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								-webkit-animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;
								animation: tp_adv_typo_down-at'.esc_attr($id).' '.esc_attr($item['marquee_scrolldelay_t']).'s linear infinite;}
			
								@-moz-keyframes tp_adv_typo_down-at'.esc_attr($id).' {
								0%   { -moz-transform: translateY(-100%); }
								100% { -moz-transform: translateY(100%); }
								}
								@-webkit-keyframes tp_adv_typo_down-at'.esc_attr($id).' {
								0%   { -webkit-transform: translateY(-100%); }
								100% { -webkit-transform: translateY(100%); }
								}
								@keyframes tp_adv_typo_down-at'.esc_attr($id).' {
								0%   { 
								-moz-transform: translateY(-100%); 
								-webkit-transform: translateY(-100%);
								transform: translateY(-100%); 		
								}
								100% { 
								-moz-transform: translateY(100%);
								-webkit-transform: translateY(100%);
								transform: translateY(100%); 
								}
								}</style>';
							}
						}
				}
				$i++;
			}
		}
		/*repeater marquee css end*/
 
		echo $before_content.$advanced_typography.$after_content;		
	}
 
    protected function content_template() {
 
    }
}