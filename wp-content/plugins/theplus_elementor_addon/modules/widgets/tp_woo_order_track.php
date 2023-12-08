<?php 
/*
Widget Name: Woo Order Track
Description: Woo Order Track
Author: Posimyth
Author URI: http://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Woo_Order_Track extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-order-track';
	}

    public function get_title() {
        return esc_html__('Woo Order Track', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-truck theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['track', 'order' , 'order track' , 'order track page' , 'WooCommerce'];
	}
	
    protected function register_controls() {	
		$this->start_controls_section(
			'section_order_track_page',
			[
				'label' => esc_html__( 'Layout', 'theplus' ),
			]
		);
		$this->add_control(
			'ot_layout',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_ot_l1',
				'options' => [
					'tp_ot_l1'  => esc_html__( 'Style 1', 'theplus' ),
					'tp_ot_l2'  => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->add_control(
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
				],
				'default' => 'left',
				'toggle' => true,
				'separator' => 'before',
				'condition' => [
					'ot_layout' => 'tp_ot_l2',
				],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order,
					{{WRAPPER}} .tp-order-track-page-wrapper .track_order,
					{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'text-align:{{VALUE}};',
				],				
			]
		);
		$this->end_controls_section();
		
		/*icon option start*/
		$this->start_controls_section(
			'section_icon_opt',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
			]
		);
		$this->add_control(
			'updateIcons',
			[
				'label' => esc_html__( 'Update Icons', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'updateIconsnote',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'Note : Replace default icons with another font awesome icon using below options.<a href="https://fontawesome.com/v4.7.0/icons" target="_blank">( Get Font Awesome Icon Id. )</a>',
				'content_classes' => 'tp-widget-description',
			]
		);
		$this->add_control(
			'ot_icon',
			[
				'label' => esc_html__( 'Order Track', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( '\f02b', 'theplus' ),
				'selectors' => [
                    '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:before' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->end_controls_section();
		/*icon option end*/
		
		/*style start*/		
		/*order traking box content start*/
		$this->start_controls_section(
            'checkout_box_content_styling',
            [
                'label' => esc_html__('Order Track Form', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ot_box_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ot_box_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ot_box_bg',
				'types'     => [ 'classic', 'gradient' ],
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .tp-order-track-page-wrapper',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ot_box_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper',
			]
		);
		$this->add_responsive_control(
			'ot_box_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-order-track-page-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ot_box_shadow',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper',
			]
		);
		$this->end_controls_section();
		/*order traking box content end*/
		
		/*ot heading description start*/
		$this->start_controls_section(
            'ot_head_desc_styling',
            [
                'label' => esc_html__('Heading/Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ot_head_desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p:not(.form-row)',				
			]
		);
		$this->add_control(
			'ot_head_desc_color',
			[
				'label' => esc_html__( 'Heading/Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p:not(.form-row)' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*ot heading description end*/
		
		/*ot label start*/
		$this->start_controls_section(
            'ot_label_styling',
            [
                'label' => esc_html__('Label', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ot_label_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p.form-row.form-row-first,
				{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p.form-row.form-row-last',				
			]
		);
		$this->add_control(
			'ot_label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p.form-row.form-row-first,
				{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order p.form-row.form-row-last' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*ot label end*/
		
		/*Input Fields start*/
		$this->start_controls_section(
            'ot_input_styling',
            [
                'label' => esc_html__('Input Field', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ot_input_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ot_input_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
            'ot_input_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ot_input_typography',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]',
				'separator' => 'before',
			]
		);		
		$this->add_control(
			'ot_input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_ot_input_field_style' );
		$this->start_controls_tab(
			'tab_ot_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'ot_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ot_input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ot_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]',
			]
		);		
		
		$this->add_responsive_control(
			'ot_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ot_box_norml_shadow',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_ot_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'ot_input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ot_input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]:focus',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ot_box_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]:focus',				
			]
		);		
		$this->add_responsive_control(
			'ot_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ot_box_active_shadow',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row input[type="text"]:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*Input Fields end*/
		
		/*track btn start*/
		$this->start_controls_section(
            'tb_btn_styling',
            [
                'label' => esc_html__('Track Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'ot_track_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'ot_track_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'ot_track_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ot_track_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'separator' => 'before',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button',
				
			]
		);		
		$this->start_controls_tabs( 'ot_track_tabs' );
			$this->start_controls_tab(
				'ot_track_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),					
				]
			);
			$this->add_control(
				'ot_track_n_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ot_track_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ot_track_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button',
				]
			);
			$this->add_responsive_control(
				'ot_track_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ot_track_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button',
				]
			);	
			$this->end_controls_tab();
			$this->start_controls_tab(
				'ot_track_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),					
				]
			);
			$this->add_control(
				'ot_track_h_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:hover' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'ot_track_h_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:hover',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'ot_track_h_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:hover',
				]
			);
			$this->add_responsive_control(
				'ot_track_h_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ot_track_h_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-form-track-order .form-row .button:hover',
				]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*track btn end*/
		
		/*order info start*/
		$this->start_controls_section(
            'ot_od_info_styling',
            [
                'label' => esc_html__('Order Info', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ot_od_info_typography',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .order-info,{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark',				
			]
		);
		$this->add_control(
			'ot_od_info_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .order-info,{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark' => 'color: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'ot_od_info_highlight_heading',
			[
				'label' => esc_html__( 'Highlight Text Option', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator'	=> 'before',
			]
		);
		$this->add_responsive_control(
            'ot_od_info_ht_in_gap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Inner Gap', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'ot_od_info_ht_gap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Outer Gap', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'ot_od_info_ht_color',
			[
				'label' => esc_html__( 'Highlight Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ot_od_info_ht_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark',
			]
		);				
		$this->add_responsive_control(
			'ot_od_info_ht_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .order-info mark' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->end_controls_section();
		/*order info end*/
		
		/*o info heading title start*/
		$this->start_controls_section(
            'oi_ht_styling',
            [
                'label' => esc_html__('Order Info Heading Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'oi_ht_typography',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details .woocommerce-column__title,{{WRAPPER}}  .tp-order-track-page-wrapper .woocommerce-order-details__title',				
			]
		);
		$this->add_control(
			'oi_ht_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details .woocommerce-column__title,{{WRAPPER}}  .tp-order-track-page-wrapper .woocommerce-order-details__title' => 'color: {{VALUE}};',
				],
			]
		);		
		$this->end_controls_section();
		/*order info end*/
		
		/*o product and billing start*/
		$this->start_controls_section(
            'oi_pbi_styling',
            [
                'label' => esc_html__('Product list and Billing', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'oi_pbi_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-table--order-details,
					{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'oi_pbi_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table td,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table td a',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'oi_pbi_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table td,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table td a' => 'color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'oi_pbi_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-table--order-details,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oi_pbi_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-table--order-details,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table',
			]
		);
		$this->add_responsive_control(
			'oi_pbi_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-table--order-details,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oi_pbi_shadow',
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-table--order-details,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table',
			]
		);
		$this->add_control(
			'oi_pbin_border_heading',
			[
				'label' => esc_html__( 'Inner Border', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oi_pbin_border',
				'label' => esc_html__( 'Inner Border', 'theplus' ),				
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table thead tr th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table thead tr td,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tbody tr th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tbody tr td,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tfoot tr:not(:last-child) th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tfoot tr:not(:last-child) td',
			]
		);
		$this->add_control(
			'oi_pbin_tot_border_heading',
			[
				'label' => esc_html__( 'Total Border', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oi_pbin_tot_border',
				'label' => esc_html__( 'Total Border', 'theplus' ),				
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tfoot tr:last-child th,{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce table.shop_table tfoot tr:last-child td',
			]
		);
		$this->end_controls_section();
		/*product list and billing end*/
		
		/*billing and shipping address start*/
		$this->start_controls_section(
            'oi_bas_add_styling',
            [
                'label' => esc_html__('Billing & Shipping address', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'oi_bas_add_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address',				
			]
		);
		$this->add_control(
				'oi_bas_add_color',
				[
					'label' => esc_html__( 'Color', 'theplus' ),
					'type' => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address' => 'color: {{VALUE}}',
					],					
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name' => 'oi_bas_add_n_bg',
					'label' => esc_html__( 'Background', 'theplus' ),
					'types' => [ 'classic', 'gradient'],
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address',
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name' => 'oi_bas_add_n_border',
					'label' => esc_html__( 'Border', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address',
				]
			);
			$this->add_responsive_control(
				'oi_bas_add_n_br',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'oi_bas_add_n_shadow',
					'label' => esc_html__( 'Box Shadow', 'theplus' ),
					'selector' => '{{WRAPPER}} .tp-order-track-page-wrapper .woocommerce-customer-details address',
				]
			);	
		$this->end_controls_section();
		/*billing and shipping address end*/
	}
	private function get_shortcode() {
		$settings = $this->get_settings();		
		$this->add_render_attribute( 'shortcode', 'woocommerce_order_tracking' );
		$shortcode   = [];
		$shortcode[] = sprintf( '[%s]', $this->get_render_attribute_string( 'shortcode' ) );
		return implode("", $shortcode);
	}
	public function render() {
		$settings = $this->get_settings_for_display();
		$output ='<div class="tp-order-track-page-wrapper '.esc_attr($settings['ot_layout']).'">';
			$output .= do_shortcode($this->get_shortcode());
		$output .= '</div>';
		
		echo $output;
	}
	
}	