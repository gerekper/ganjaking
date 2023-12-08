<?php 
/*
Widget Name: Button
Description: creative of button style.
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Button extends Widget_Base {
		
	public function get_name() {
		return 'tp-button';
	}

    public function get_title() {
        return esc_html__('TP Button', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-link theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
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
            'button_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'style-1',
                'options' => [
                    'style-1' => esc_html__('Style 1', 'theplus'),
                    'style-2' => esc_html__('Style 2', 'theplus'),
                    'style-3' => esc_html__('Style 3', 'theplus'),
                    'style-4' => esc_html__('Style 4', 'theplus'),
                    'style-5' => esc_html__('Style 5', 'theplus'),
                    'style-6' => esc_html__('Style 6', 'theplus'),
                    'style-7' => esc_html__('Style 7', 'theplus'),
					'style-8' => esc_html__('Style 8', 'theplus'),
					'style-9' => esc_html__('Style 9', 'theplus'),
					'style-10' => esc_html__('Style 10', 'theplus'),
					'style-11' => esc_html__('Style 11', 'theplus'),
					'style-12' => esc_html__('Style 12', 'theplus'),
					'style-13' => esc_html__('Style 13', 'theplus'),
					'style-14' => esc_html__('Style 14', 'theplus'),
					'style-15' => esc_html__('Style 15', 'theplus'),
					'style-16' => esc_html__('Style 16', 'theplus'),
					'style-17' => esc_html__('Style 17', 'theplus'),
					'style-18' => esc_html__('Style 18', 'theplus'),
					'style-19' => esc_html__('Style 19', 'theplus'),
					'style-20' => esc_html__('Style 20', 'theplus'),
					'style-21' => esc_html__('Style 21', 'theplus'),
					'style-22' => esc_html__('Style 22', 'theplus'),
					'style-24' => esc_html__('Style 23', 'theplus'),
                ],
            ]
        );
		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Read More', 'theplus' ),
				'placeholder' => esc_html__( 'Read More', 'theplus' ),
			]
		);
		$this->add_control(
			'button_24_text',
			[
				'label' => esc_html__( 'Button Tag Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Click Here', 'theplus' ),
				'placeholder' => esc_html__( 'Click Here', 'theplus' ),
				'condition' => [
					'button_style' => ['style-24'],
				],
			]
		);
		$this->add_control(
			'button_hover_text',
			[
				'label' => esc_html__( 'Hover Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Click Here', 'theplus' ),
				'placeholder' => esc_html__( 'Click Here', 'theplus' ),
				'condition' => [
					'button_style' => ['style-4','style-11','style-14'],
				],
			]
		);
		$this->add_control(
			'button_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
			]
		);	
		$this->add_control(
			'button_custom_attributes',
			[
				'label'     => __( 'Add Custom Attributes', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'default' => 'no',
			]
		);

		$this->add_control(
			'custom_attributes',
			[
				'label' => __( 'Custom Attributes', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key=value', 'theplus' ),				
				'condition' => [
					'button_custom_attributes' => 'yes'
				]
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Layout', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_responsive_control(
			'button_align',
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
				],
			]
		);
				
		$this->add_control(
            'btn_hover_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Button Style', 'theplus'),
                'default' => 'hover-left',
                'options' => [
                    'hover-left' => esc_html__('On Left', 'theplus'),
                    'hover-right' => esc_html__('On Right', 'theplus'),
                    'hover-top' => esc_html__('On Top', 'theplus'),
                    'hover-bottom' => esc_html__('On Bottom', 'theplus'),
                ],
				'condition' => [
					'button_style' => ['style-11','style-13'],
				],
            ]
        );
		$this->end_controls_section();
		$this->start_controls_section(
            'section_button_icon_styling',
            [
                'label' => esc_html__('Icon Settings', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'button_style!' => ['style-3','style-6','style-7','style-9'],
				],
				
            ]
        );
		$this->add_control(
            'icon_hover_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Icon Hover Style', 'theplus'),
                'default' => 'hover-top',
                'options' => [
                    'hover-top' => esc_html__('On Top', 'theplus'),
                    'hover-bottom' => esc_html__('On Bottom', 'theplus'),
                ],
				'condition' => [
					'button_style' => ['style-17'],
				],
            ]
        );
		$this->add_control(
			'button_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'button_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'label_block' => true,
				'default' => 'fa fa-chevron-right',
				'condition' => [					
					'button_icon_style' => 'font_awesome',
				],
			]
		);
		$this->add_control(
			'button_icon_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [					
					'button_icon_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'button_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [					
					'button_icon_style' => 'icon_mind',
				],
			]
		);
		$this->add_control(
			'before_after',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'after',
				'options' => [
					'after' => esc_html__( 'After', 'theplus' ),
					'before' => esc_html__( 'Before', 'theplus' ),
				],
				'condition' => [
					'button_style!' => ['style-17'],
					'button_icon_style!' => '',
				],
			]
		);
		$this->add_control(
			'icon_spacing',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'button_style!' => ['style-17'],
					'button_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button-link-wrap .button-after' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-link-wrap .button-before' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap .btn-icon.button-before' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap .btn-icon.button-after' => 'padding-right: {{SIZE}}{{UNIT}};',
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
						'max' => 200,
					],
				],
				'separator' => 'before',
				'condition' => [
					'button_style!' => ['style-17'],
					'button_icon_style!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button-link-wrap .btn-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .button-link-wrap .btn-icon svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
			]
		);
		$this->add_responsive_control( 'vertical_button_align_24',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Top', 'theplus' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-v-align-middle',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'theplus' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap ' => 'align-items: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-24'],
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_extra_styling',
            [
                'label' => esc_html__('Extra', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'button_css_id',
			[
				'label' => esc_html__( 'Button ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'theplus' ),
				'label_block' => false,
				'description' => esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'theplus' ),
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__('Typography and Cosmetics', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );		
		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],			
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button:not(.button-style-11):not(.button-style-17) .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap > span,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);			
		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],
				'default' => [
							'top' => '15',
							'right' => '30',
							'bottom' => '15',
							'left' => '30',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button:not(.button-style-11):not(.button-style-17) .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap > span,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_button .button-link-wrap',
			]
		);
		$this->add_control(
            'button_svg_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Svg Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		
		$this->add_control(
			'btn_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button .button-link-wrap svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-3 .button-link-wrap .arrow *' => 'fill: {{VALUE}};stroke: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-7 .button-link-wrap:after' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-2 .button-link-wrap i,
								{{WRAPPER}} .pt_plus_button.button-style-3 a.button-link-wrap:before,
								{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-15 .button-link-wrap::before,
								{{WRAPPER}} .pt_plus_button.button-style-15 .button-link-wrap::after,
								{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap::after,
								{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap::after,
								{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,
								{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap',				
				'condition' => [
					'button_style!' => ['style-1','style-6','style-7','style-9','style-12','style-13'],
				],
			]
		);
		$this->add_control(
			'button_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap' => 'border-style: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'button_style' => ['style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-19','style-20','style-21','style-22','style-24'],
				],
			]
		);

		$this->add_responsive_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-19','style-20','style-21','style-22','style-24'],
					'button_border_style!' => 'none',
				]
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap' => 'background: {{VALUE}};',
				],
				'condition' => [
					'button_style' => ['style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-24'],
					'button_border_style!' => 'none'
				],
			]
		);

		$this->add_responsive_control(
			'button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap::after,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-4','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-19','style-20','style-21','style-22','style-24'],
				],
			]
		);
		$this->add_responsive_control(
			'button_radius_18',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap::after,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => 'style-18',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-2 .button-link-wrap i,
							   {{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-15 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap,
							   {{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap',
				'condition' => [
					'button_style' => ['style-2','style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-15','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-24'],
				],
			]
		);
		$this->add_control(
			'btn_bottom_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_style' => 'style-1',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap .button_line' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
            'bottom_border_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Height', 'theplus'),
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min'	=> 1,
						'max'	=> 20,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'condition' => [
					'button_style' => 'style-1',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap .button_line' => 'height: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'btn_text_hover_color',
			[
				'label' => esc_html__( 'Text Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap .btn-icon,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap .btn-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover svg,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap .btn-icon svg,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap .btn-icon svg,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap svg,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap::after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-3 .button-link-wrap:hover .arrow-1 *' => 'fill: {{VALUE}};stroke: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_button.button-style-2 .button-link-wrap:hover i,
								{{WRAPPER}} .pt_plus_button.button-style-3 .button-link-wrap:hover:before,
								{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap::after,
								{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap:before,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap:after,
								{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,
								{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap:hover,
								{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap::before,
								{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap::before,
								{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap::after,
								{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap:hover,
								{{WRAPPER}} .pt_plus_button.button-style-15 .button-link-wrap:hover::after,
								{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap::before,
								{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap::before,
								{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap:hover::after,
								{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap:after,
								{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap:after,
								{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap:after,
								{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap:hover,
								{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap:hover',
				'condition' => [
					'button_style!' => ['style-1','style-6','style-7','style-9'],
				],
			]
		);
		$this->add_control(
			'button_border_hover_color',
			[
				'label'     => esc_html__( 'Hover Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#313131',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap::before,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap:hover' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap::before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'button_style' => ['style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-24'],
					'button_border_style!' => 'none'
				],
			]
		);
		$this->add_responsive_control(
			'button_radius_hover_18',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap:hover::after,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap:hover::before,{{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => 'style-18',
				],
			]
		);
		$this->add_responsive_control(
			'button_hover_radius',
			[
				'label'      => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap:hover,{{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_style' => ['style-4','style-8','style-10','style-11','style-12','style-13','style-14','style-16','style-17','style-19','style-20','style-21','style-22','style-24'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-2 .button-link-wrap:hover i,
							   {{WRAPPER}} .pt_plus_button.button-style-4 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-5 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-8 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-10 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-11 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-12 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-13 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-14 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-15 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-16 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-17 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-18 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-19 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-20 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-21 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-22 .button-link-wrap:hover,
							   {{WRAPPER}} .pt_plus_button.button-style-24 .button-link-wrap:hover',
				'condition' => [
					'button_style' => ['style-2','style-4','style-5','style-8','style-10','style-11','style-12','style-13','style-14','style-15','style-16','style-17','style-18','style-19','style-20','style-21','style-22','style-24'],
				],
			]
		);
		$this->add_control(
			'btn_bottom_border_hover_color',
			[
				'label' => esc_html__( 'Border Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_style' => 'style-1',
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_button .button-link-wrap:hover .button_line' => 'background: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_tag_24_heading',
			[
				'label' => esc_html__( 'Button Tag Text', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'button_style' => ['style-24'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_tag_typography',
				'selector' => '{{WRAPPER}} .pt_plus_button.button-style-24 .button-tag-hint',
				'condition' => [
					'button_style' => 'style-24',
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_extra_effect_styling',
            [
                'label' => esc_html__('Special', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'btn_magic_scroll',
			[
				'label'        => esc_html__( 'Magic Scroll', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Theplus_Magic_Scroll_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Scroll Options', 'theplus' ),
				'name'           => 'scroll_option',
				'render_type'  => 'template',
				'condition'    => [
					'btn_magic_scroll' => [ 'yes' ],
				],
			)
		);
		$this->start_controls_tabs( 'tabs_magic_scroll' );
		$this->start_controls_tab(
			'tab_scroll_from',
			[
				'label' => esc_html__( 'Initial', 'theplus' ),
				'condition'    => [
					'btn_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$this->add_group_control(
			\Theplus_Magic_Scroll_From_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Initial Position', 'theplus' ),
				'name'           => 'scroll_from',
				'condition'    => [
					'btn_magic_scroll' => [ 'yes' ],
				],
			)
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_scroll_to',
			[
				'label' => esc_html__( 'Final', 'theplus' ),
				'condition'    => [
					'btn_magic_scroll' => [ 'yes' ],
				],
			]
		);
		$this->add_group_control(
			\Theplus_Magic_Scroll_To_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Final Position', 'theplus' ),
				'name'           => 'scroll_to',
				'condition'    => [
					'btn_magic_scroll' => [ 'yes' ],
				],
			)
		);
		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'plus_tooltip',
			[
				'label'        => esc_html__( 'Tooltip', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'plus_tooltip_tabs' );

		$this->start_controls_tab(
			'plus_tooltip_content_tab',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'render_type'  => 'template',
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_tooltip_content_type',
			[
				'label' => esc_html__( 'Content Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal_desc',
				'options' => [
					'normal_desc'  => esc_html__( 'Content Text', 'theplus' ),
					'content_wysiwyg'  => esc_html__( 'Content WYSIWYG', 'theplus' ),
				],
				'render_type'  => 'template',
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_tooltip_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 5,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis', 'theplus' ),
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_tooltip_content_wysiwyg',
			[
				'label' => esc_html__( 'Tooltip Content', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'theplus' ),
				'render_type'  => 'template',
				'condition' => [
					'plus_tooltip_content_type' => 'content_wysiwyg',
					'plus_tooltip' => 'yes',
				],
			]				
		);
		$this->add_control(
			'plus_tooltip_content_align',
			[
				'label'   => esc_html__( 'Text Alignment', 'theplus' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'plus_tooltip_content_type' => 'normal_desc',
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'plus_tooltip_content_typography',
				'selector' => '{{WRAPPER}} .tippy-tooltip .tippy-content',
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);

		$this->add_control(
			'plus_tooltip_content_color',
			[
				'label'  => esc_html__( 'Text Color', 'theplus' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tippy-tooltip .tippy-content,{{WRAPPER}} .tippy-tooltip .tippy-content p' => 'color: {{VALUE}}',
				],
				'condition' => [
					'plus_tooltip_content_type' => ['normal_desc','content_wysiwyg'],
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'plus_tooltip_styles_tab',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'condition' => [
					'plus_tooltip' => 'yes',
				],
			]
		);
		$this->add_group_control(
			\Theplus_Tooltips_Option_Group::get_type(),
			array(
				'label' => esc_html__( 'Tooltip Options', 'theplus' ),
				'name'           => 'tooltip_opt',
				'render_type'  => 'template',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$this->add_group_control(
			\Theplus_Tooltips_Option_Style_Group::get_type(),
			array(
				'label' => esc_html__( 'Style Options', 'theplus' ),
				'name'           => 'tooltip_style',
				'render_type'  => 'template',
				'condition'    => [
					'plus_tooltip' => [ 'yes' ],
				],
			)
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
            'btn_special_effect',
            [
				'label'   => esc_html__( 'Special Effect', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Theplus_Overlay_Special_Effect_Group::get_type(),
			array(
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'name'           => 'plus_overlay_spcial',
				'condition'    => [
					'btn_special_effect' => [ 'yes' ],
				],
			)
		);
		$this->add_control(
			'plus_mouse_move_parallax',
			[
				'label'        => esc_html__( 'Mouse Move Parallax', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),			
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			\Theplus_Mouse_Move_Parallax_Group::get_type(),
			array(
				'label' => esc_html__( 'Parallax Options', 'theplus' ),
				'name'           => 'plus_mouse_parallax',
				'render_type'  => 'template',
				'condition'    => [
					'plus_mouse_move_parallax' => [ 'yes' ],
				],
			)
		);
		$this->add_control(
			'plus_continuous_animation',
			[
				'label'        => esc_html__( 'Continuous Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'plus_animation_effect',
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
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_animation_hover',
			[
				'label'        => esc_html__( 'Hover Animation', 'theplus' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'theplus' ),
				'label_off'    => esc_html__( 'No', 'theplus' ),					
				'render_type'  => 'template',
				'condition' => [
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_animation_duration',
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
					'{{WRAPPER}} .pt-plus-button-wrapper .animted-content-inner' => 'animation-duration: {{SIZE}}{{UNIT}};-webkit-animation-duration: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'plus_continuous_animation' => 'yes',
				],
			]
		);
		$this->add_control(
			'plus_transform_origin',
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
					'{{WRAPPER}} .pt-plus-button-wrapper .animted-content-inner' => '-webkit-transform-origin: {{VALUE}};-moz-transform-origin: {{VALUE}};-ms-transform-origin: {{VALUE}};-o-transform-origin: {{VALUE}};transform-origin: {{VALUE}};',
				],
				'render_type'  => 'template',
				'condition' => [
					'plus_continuous_animation' => 'yes',
					'plus_animation_effect' => 'rotating',
				],
			]
		);
		$this->add_control(
            'full_width_btn',
            [
				'label'   => esc_html__( 'Full-Width Button', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'btn_hover_effects',
			[
				'label'   => esc_html__( 'Button Hover Effects', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'separator' => 'before',
				'options' => theplus_get_content_hover_effect_options(),
			]
		);
		$this->add_control(
            'hover_shadow_color',
            [
                'label' => esc_html__('Shadow Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'rgba(0, 0, 0, 0.6)',
				'condition'    => [
					'btn_hover_effects' => ['float_shadow','grow_shadow','shadow_radial'],
				],
            ]
        );
		$this->add_control(
            'shake_animate',
            [
				'label'   => esc_html__( 'Interval Shake Animate', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'shake_animate_duration',
			[
				'label' => esc_html__( 'Interval Shake Duration', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 5,
				'selectors' => [
                    '{{WRAPPER}} .pt-plus-button-wrapper .button-link-wrap.shake_animate' => ' animation-duration: {{VALUE}}s;-o-animation-duration: {{VALUE}}s;
					-ms-animation-duration: {{VALUE}}s;-moz-animation-duration: {{VALUE}}s;-webkit-animation-duration: {{VALUE}}s;',
				],
				'condition' => [
					'shake_animate' => 'yes',
				],
			]
		);
		$this->end_controls_section();


		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv_hs',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/
		
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

	}
	 protected function render() {

        $settings = $this->get_settings_for_display();
		$hover_class=$full_button_width=$hover_attr=$data_class=$button_hover_text='';
		
		$magic_class = $magic_attr = $parallax_scroll = '';
		if (!empty($settings['btn_magic_scroll']) && $settings['btn_magic_scroll'] == 'yes') {
			if($settings["scroll_option_popover_toggle"]==''){
				$scroll_offset=0;
				$scroll_duration=300;
			}else{
				$scroll_offset	 = (isset($settings['scroll_option_scroll_offset']) ? $settings['scroll_option_scroll_offset'] : 0 );
				$scroll_duration = (isset($settings['scroll_option_scroll_duration']) ? $settings['scroll_option_scroll_duration'] : 300 );
			}
			
			if($settings["scroll_from_popover_toggle"]==''){
				$scroll_x_from=0;
				$scroll_y_from=0;
				$scroll_opacity_from=1;
				$scroll_scale_from=1;
				$scroll_rotate_from=0;
			}else{
				$scroll_x_from = (isset($settings['scroll_from_scroll_x_from']) ? $settings['scroll_from_scroll_x_from'] : 0 );
				$scroll_y_from = (isset($settings['scroll_from_scroll_y_from']) ? $settings['scroll_from_scroll_y_from'] : 0 );
				$scroll_opacity_from = (isset($settings['scroll_from_scroll_opacity_from']) ? $settings['scroll_from_scroll_opacity_from'] : 1 );
				$scroll_scale_from 	= (isset($settings['scroll_from_scroll_scale_from']) ? $settings['scroll_from_scroll_scale_from'] : 1 );
				$scroll_rotate_from = (isset($settings['scroll_from_scroll_rotate_from']) ? $settings['scroll_from_scroll_rotate_from'] : 0 );
			}
			
			if(empty($settings["scroll_to_popover_toggle"])){
				$scroll_x_to=0;
				$scroll_y_to=-50;
				$scroll_opacity_to=1;
				$scroll_scale_to=1;
				$scroll_rotate_to=0;
			}else{
				$scroll_x_to = (isset($settings['scroll_to_scroll_x_to']) ? $settings['scroll_to_scroll_x_to'] : 0 );
				$scroll_y_to = (isset($settings['scroll_to_scroll_y_to']) ? $settings['scroll_to_scroll_y_to'] : -50 );
				$scroll_opacity_to = (isset($settings['scroll_to_scroll_opacity_to']) ? $settings['scroll_to_scroll_opacity_to'] : 1 );
				$scroll_scale_to = (isset($settings['scroll_to_scroll_scale_to']) ? $settings['scroll_to_scroll_scale_to'] : 1 );
				$scroll_rotate_to = (isset($settings['scroll_to_scroll_rotate_to']) ? $settings['scroll_to_scroll_rotate_to'] : 0 );
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
		
		$reveal_effects=$effect_attr='';
		if(!empty($settings["btn_special_effect"]) && $settings["btn_special_effect"]=='yes'){
			$effect_rand_no =uniqid('reveal');
			$color_1=(!empty($settings["plus_overlay_spcial_effect_color_1"])) ? $settings["plus_overlay_spcial_effect_color_1"] : '#313131';
			$color_2=(!empty($settings["plus_overlay_spcial_effect_color_2"])) ? $settings["plus_overlay_spcial_effect_color_2"] : '#ff214f';
			$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
			$effect_attr .=' data-effect-color-1="'.esc_attr($color_1).'" ';
			$effect_attr .=' data-effect-color-2="'.esc_attr($color_2).'" ';
			$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
		}
		
		$move_parallax=$move_parallax_attr=$parallax_move='';
		if(!empty($settings['plus_mouse_move_parallax']) && $settings['plus_mouse_move_parallax']=='yes'){
			$move_parallax='pt-plus-move-parallax';
			$parallax_move='parallax-move';
			$parallax_speed_x=(isset($settings["plus_mouse_parallax_speed_x"]["size"])) ? $settings["plus_mouse_parallax_speed_x"]["size"] : 30;
			$parallax_speed_y=(isset($settings["plus_mouse_parallax_speed_y"]["size"])) ? $settings["plus_mouse_parallax_speed_y"]["size"] : 30;
			$move_parallax_attr .= ' data-move_speed_x="' . esc_attr($parallax_speed_x) . '" ';
			$move_parallax_attr .= ' data-move_speed_y="' . esc_attr($parallax_speed_y) . '" ';
		}
		
		$hover_class  = $hover_attr = '';
		$hover_uniqid = uniqid('hover-effect');
		if ($settings["btn_hover_effects"] == "float_shadow" || $settings["btn_hover_effects"] == "grow_shadow" || $settings["btn_hover_effects"] == "shadow_radial") {
			$hover_attr .= 'data-hover_uniqid="' . esc_attr($hover_uniqid) . '" ';
			$hover_attr .= ' data-hover_shadow="' . esc_attr($settings["hover_shadow_color"]) . '" ';
			$hover_attr .= ' data-content_hover_effects="' . esc_attr($settings["btn_hover_effects"]) . '" ';
		}
		$btn_hover_effects=$settings["btn_hover_effects"];
		if ($btn_hover_effects == "grow") {
			$hover_class .= 'content_hover_grow';
		} elseif ($btn_hover_effects == "push") {
			$hover_class .= 'content_hover_push';
		} elseif ($btn_hover_effects == "bounce-in") {
			$hover_class .= 'content_hover_bounce_in';
		} elseif ($btn_hover_effects == "float") {
			$hover_class .= 'content_hover_float';
		} elseif ($btn_hover_effects == "wobble_horizontal") {
			$hover_class .= 'content_hover_wobble_horizontal';
		} elseif ($btn_hover_effects == "wobble_vertical") {
			$hover_class .= 'content_hover_wobble_vertical';
		} elseif ($btn_hover_effects == "float_shadow") {
			$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_float_shadow';
		} elseif ($btn_hover_effects == "grow_shadow") {
			$hover_class .= ' ' . esc_attr($hover_uniqid) . ' content_hover_grow_shadow';
		} elseif ($btn_hover_effects == "shadow_radial") {
			$hover_class .= '' . esc_attr($hover_uniqid) . ' content_hover_radial';
		}
		
		if ( ! empty( $settings['button_link']['url'] ) ) {
			$this->add_link_attributes( 'button', $settings['button_link'] );			
		}
		
		$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['button_background_image'],$settings['button_hover_background_image']) : '';
		
		if(!empty($settings['shake_animate'] && $settings['shake_animate']=='yes')){
			$this->add_render_attribute( 'button', 'class', 'button-link-wrap shake_animate '.$lz1 );
		}else{
			$this->add_render_attribute( 'button', 'class', 'button-link-wrap '.$lz1 );
		}
		
		$this->add_render_attribute( 'button', 'role', 'button' );
		
		if ( ! empty( $settings['button_css_id'] ) ) {
			$this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
		}
			
		if(!empty($settings['button_hover_text'])){
			$this->add_render_attribute( 'button', 'data-hover', $settings['button_hover_text'] );
		}else{
			$this->add_render_attribute( 'button', 'data-hover', $settings['button_text'] );
		}
		$button_style = $settings['button_style'];
		$button_align=' text-'.$settings['button_align'];
		$button_align .=(!empty($settings['button_align_tablet'])) ? ' text--tablet'.$settings['button_align_tablet'] : '';
		$button_align .=(!empty($settings['button_align_mobile'])) ? ' text--mobile'.$settings['button_align_mobile'] : '';
		$btn_hover_style = $settings['btn_hover_style'];
		$icon_hover_style = $settings['icon_hover_style'];
		$button_text = $settings['button_text'];
		$button_hover_text = $settings['button_hover_text'];
		$uid=uniqid('btn');
		$data_class= $uid;
		$data_class .=' button-'.$button_style.' ';
		
		if($button_style=='style-11' || $button_style=='style-13'){
			$data_class .=' '.$btn_hover_style.' ';
		}
		if($button_style=='style-17'){
			$data_class .=' '.$icon_hover_style.' ';
		}

		if(!empty($settings['full_width_btn']) && $settings['full_width_btn']=='yes'){
			$data_class .=' full-button ';
			$full_button_width=' full-button ';
		}
		if(!empty($settings['transition_hover']) && $settings['transition_hover']=='yes'){
			$data_class .=' trnasition_hover ';
		}
		
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$PlusExtra_Class="plus-adv-text-widget";
		include THEPLUS_PATH.'modules/widgets/theplus-widgets-extra.php';
		
		if( $settings['plus_tooltip'] == 'yes' ) {
		
			$this->add_render_attribute( '_tooltip', 'data-tippy', '', true );

			if (!empty($settings['plus_tooltip_content_type']) && $settings['plus_tooltip_content_type']=='normal_desc') {
				$this->add_render_attribute( '_tooltip', 'title', $settings['plus_tooltip_content_desc'], true );
			}else if (!empty($settings['plus_tooltip_content_type']) && $settings['plus_tooltip_content_type']=='content_wysiwyg') {
				$tooltip_content=$settings['plus_tooltip_content_wysiwyg'];
				$this->add_render_attribute( '_tooltip', 'title', $tooltip_content, true );
			}
			$plus_tooltip_position=(!empty($settings["tooltip_opt_plus_tooltip_position"])) ? $settings["tooltip_opt_plus_tooltip_position"] : 'top';
			$this->add_render_attribute( '_tooltip', 'data-tippy-placement', $plus_tooltip_position, true );
			
			$tooltip_interactive = (isset($settings["tooltip_opt_plus_tooltip_interactive"]) && $settings["tooltip_opt_plus_tooltip_interactive"] == 'yes' ? 'true' : 'false');
			$this->add_render_attribute( '_tooltip', 'data-tippy-interactive', $tooltip_interactive, true );
			
			$plus_tooltip_theme = (!empty($settings["tooltip_opt_plus_tooltip_theme"])) ? $settings["tooltip_opt_plus_tooltip_theme"] : 'dark';
			$this->add_render_attribute( '_tooltip', 'data-tippy-theme', $plus_tooltip_theme, true );
			
			
			$tooltip_arrow =($settings["tooltip_opt_plus_tooltip_arrow"]!='none' || empty($settings["tooltip_opt_plus_tooltip_arrow"])) ? 'true' : 'false';
			$this->add_render_attribute( '_tooltip', 'data-tippy-arrow', $tooltip_arrow , true );
			
			$plus_tooltip_arrow=(!empty($settings["tooltip_opt_plus_tooltip_arrow"])) ? $settings["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
			$this->add_render_attribute( '_tooltip', 'data-tippy-arrowtype', $plus_tooltip_arrow, true );
			
			$plus_tooltip_animation=(!empty($settings["tooltip_opt_plus_tooltip_animation"])) ? $settings["tooltip_opt_plus_tooltip_animation"] : 'shift-toward';
			$this->add_render_attribute( '_tooltip', 'data-tippy-animation', $plus_tooltip_animation, true );
			
			$plus_tooltip_x_offset=(isset($settings["tooltip_opt_plus_tooltip_x_offset"])) ? $settings["tooltip_opt_plus_tooltip_x_offset"] : 0;
			$plus_tooltip_y_offset=(isset($settings["tooltip_opt_plus_tooltip_y_offset"])) ? $settings["tooltip_opt_plus_tooltip_y_offset"] : 0;
			$this->add_render_attribute( '_tooltip', 'data-tippy-offset', $plus_tooltip_x_offset .','. $plus_tooltip_y_offset, true );
			
			$tooltip_duration_in =(isset($settings["tooltip_opt_plus_tooltip_duration_in"])) ? $settings["tooltip_opt_plus_tooltip_duration_in"] : 250;
			$tooltip_duration_out =(isset($settings["tooltip_opt_plus_tooltip_duration_out"])) ? $settings["tooltip_opt_plus_tooltip_duration_out"] : 200;
			$tooltip_trigger =(!empty($settings["tooltip_opt_plus_tooltip_triggger"])) ? $settings["tooltip_opt_plus_tooltip_triggger"] : 'mouseenter';
			$tooltip_arrowtype =(!empty($settings["tooltip_opt_plus_tooltip_arrow"])) ? $settings["tooltip_opt_plus_tooltip_arrow"] : 'sharp';
		}
		
		$continuous_animation='';
		if(!empty($settings["plus_continuous_animation"]) && $settings["plus_continuous_animation"]=='yes'){
			if($settings["plus_animation_hover"]=='yes'){
				$animation_class='hover_';
			}else{
				$animation_class='image-';
			}
			$continuous_animation=$animation_class.$settings["plus_animation_effect"];
		}
		
		$uid_button=uniqid("button");
			
		$button_custom_attributes=$settings['button_custom_attributes'];
		$custom_attributes=$settings['custom_attributes'];
		
		$cst_att='';
		if((!empty($button_custom_attributes) && $button_custom_attributes=='yes') && !empty($custom_attributes)){
			$cst_att = $custom_attributes;
		}
		$the_button ='<div class="pt-plus-button-wrapper  '.esc_attr($button_align).' '.esc_attr($magic_class).'  ">';
			$the_button .='<div class="button_parallax '.esc_attr($parallax_scroll).' '.esc_attr($move_parallax).' '.esc_attr($full_button_width).'" '.$magic_attr.'>';
			if($settings['plus_mouse_move_parallax']=='yes'){
				$the_button .='<div class="'.esc_attr($parallax_move).'" '.$move_parallax_attr.'>';			
			}
				$the_button .='<div id="'.esc_attr($uid_button).'"  class="'.esc_attr($button_align).' ts-button content_hover_effect ' . esc_attr($hover_class) . ' '.esc_attr($full_button_width).' '.$this->get_render_attribute_string( '_tooltip' ).'" ' . $hover_attr . '>';
					$the_button .='<div class="pt_plus_button '.esc_attr($data_class).' '.$animated_class.' '.esc_attr($reveal_effects).'" '.$effect_attr.' '.$animation_attr.'>';
						$the_button .= '<div class="animted-content-inner '.esc_attr($continuous_animation).'">';
							$the_button .='<a '.$this->get_render_attribute_string( "button" ).' '.$cst_att.' >';
							$the_button .= $this->render_text();
							$the_button .='</a>';
						$the_button .='</div>';
					$the_button .='</div>';
				$the_button .='</div>';
			if($settings['plus_mouse_move_parallax']=='yes'){
				$the_button .='</div>';
			}
			$the_button .='</div>';
		$the_button .='</div>';
		$inline_tippy_js='';
		if($settings['plus_tooltip'] == 'yes'){
			$inline_tippy_js ='jQuery( document ).ready(function() {
			"use strict";
				if(typeof tippy === "function"){
					tippy( "#'.esc_attr($uid_button).'" , {
						arrowType : "'.esc_attr($tooltip_arrowtype).'",
						duration : ['.esc_attr($tooltip_duration_in).','.esc_attr($tooltip_duration_out).'],
						trigger : "'.esc_attr($tooltip_trigger).'",
						appendTo: document.querySelector("#'.esc_attr($uid_button).'")
					});
				}
			});';
			$the_button .= wp_print_inline_script_tag($inline_tippy_js);
		}
		echo $before_content.$the_button.$after_content;
	}
    protected function content_template() {
	
    }
	
	protected function render_text() {	
		$icons_after=$icons_before=$button_text=$style_content='';
		$settings = $this->get_settings_for_display();		
		$button_style = $settings['button_style'];
		$before_after = $settings['before_after'];
		$button_text = $settings['button_text'];
		
		$icons='';
		if($settings["button_icon_style"]=='font_awesome'){
			$icons=$settings["button_icon"];
		}else if($settings["button_icon_style"]=='icon_mind'){
			$icons=$settings["button_icons_mind"];
		}else if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
			ob_start();
			\Elementor\Icons_Manager::render_icon( $settings['button_icon_5'], [ 'aria-hidden' => 'true' ]);
			$icons = ob_get_contents();
			ob_end_clean();		
		}
		
		if($before_after=='before' && !empty($icons)){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				$icons_before = '<span class="btn-icon button-before">'.$icons.'</span>';
			}else{
				$icons_before = '<i class="btn-icon button-before '.esc_attr($icons).'"></i>';
			}
		}
		if($before_after=='after' && !empty($icons)){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				$icons_after = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				$icons_after = '<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}
		}
		
		if($button_style=='style-1'){
			$button_text =$icons_before.esc_html($button_text) . $icons_after;
			$style_content='<div class="button_line"></div>';
		}
		if($button_style=='style-2' || $button_style=='style-5' || $button_style=='style-8' || $button_style=='style-10'){
			$button_text =$icons_before . esc_html($button_text) . $icons_after;
		}
		if($button_style=='style-3'){
			$button_text =esc_html($button_text).'<svg class="arrow" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg><svg class="arrow-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid" width="48" height="9" viewBox="0 0 48 9"><path d="M48.000,4.243 L43.757,8.485 L43.757,5.000 L0.000,5.000 L0.000,4.000 L43.757,4.000 L43.757,0.000 L48.000,4.243 Z" class="cls-1"></path></svg>';
		}
		if($button_style=='style-4'){
			$button_text =$icons_before.esc_html($button_text) . $icons_after;
		}
		if($button_style=='style-6'){
			$button_text =esc_html($button_text);
		}
		if($button_style=='style-7'){
			$button_text =esc_html($button_text).'<span class="btn-arrow"></span>';
		}
		if($button_style=='style-9'){
			$button_text =esc_html($button_text).'<span class="btn-arrow"><i class="fa-show fas fa-chevron-right" aria-hidden="true"></i><i class="fa-hide fas fa-chevron-right" aria-hidden="true"></i></span>';
		}
		if($button_style=='style-11'){
			$button_text ='<span>'.$icons_before . esc_html($button_text) . $icons_after.'</span>';
		}
		if($button_style=='style-12' || $button_style=='style-15' || $button_style=='style-16'){
			$button_text ='<span>'.$icons_before . esc_html($button_text) . $icons_after.'</span>';
		}
		if($button_style=='style-13'){
			$button_text ='<span>'.$icons_before . esc_html($button_text) . $icons_after.'</span>';			
		}
		if($button_style=='style-14'){
			$button_text ='<span>'.$icons_before . esc_html($button_text) . $icons_after.'</span>';
		}
		if($button_style=='style-17'){
			if(!empty($settings["button_icon_style"]) && $settings["button_icon_style"]=='font_awesome_5'){
				ob_start();
				\Elementor\Icons_Manager::render_icon( $settings['button_icon_5'], [ 'aria-hidden' => 'true' ]);
				$icons = ob_get_contents();
				ob_end_clean();
				$icons_before = '<span class="btn-icon button-after">'.$icons.'</span>';
			}else{
				$icons_before='<i class="btn-icon button-after '.esc_attr($icons).'"></i>';
			}			
			$button_text =$icons_before .'<span>'. esc_html($button_text) .'</span>';		
		}
		if($button_style=='style-18' || $button_style=='style-19' || $button_style=='style-20' || $button_style=='style-21' || $button_style=='style-22'){
			$button_text =$icons_before .'<span>'. esc_html($button_text) .'</span>'. $icons_after;
		}
		if($button_style=='style-24'){
			$button_24_tag='';
			if(!empty($settings["button_24_text"])){
			$button_24_tag='<span class="button-tag-hint">'.esc_html($settings["button_24_text"]).'</span>';
			}
			$button_text =$icons_before .'<span>'.$button_24_tag. esc_html($button_text) .'</span>'. $icons_after;
		}
		return $button_text.$style_content;
	}
}