<?php 
/*
Widget Name: Woo Single Basic
Description: Woo Single Basic
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

class ThePlus_Woo_Single_Basic extends Widget_Base {
		
	public function get_name() {
		return 'tp-woo-single-basic';
	}

    public function get_title() {
        return esc_html__('Woo Single Basic', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-info-circle theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-woo-builder');
    }
	public function get_keywords() {
		return ['Single Basic, woocomerce','title','short description','badge','rating','nextprevious','next previous','post navigation','post','product'];
	}
    protected function register_controls() {
		/*content start*/
		$this->start_controls_section(
			'section_woo_single_basic',
			[
				'label' => esc_html__( 'Woo Single Basic', 'theplus' ),
			]
		);
		$repeater = new \Elementor\Repeater();		
		$repeater->add_control(
			'select',
			[
				'label' => esc_html__( 'Select', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'title',
				'options' => [
					'title'  => esc_html__( 'Title', 'theplus' ),
					'short_description'  => esc_html__( 'Short Description', 'theplus' ),
					'sale_badge'  => esc_html__( 'Badge', 'theplus' ),
					'rating'  => esc_html__( 'Rating', 'theplus' ),
					'next_previous_product'  => esc_html__( 'Next/Previous Product', 'theplus' ),
					
				],
			]
		);		
		$repeater->add_control(
			'nxt_text',
			[
				'label' => esc_html__( 'Next Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Next', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'select' => 'next_previous_product',					
				],
			]
		);
		$repeater->add_control(
			'nxt_icon',
			[
				'label' => esc_html__( 'Next Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-long-arrow-alt-right',
					'library' => 'solid',
				],
				'condition' => [
					'select' => 'next_previous_product',					
				],	
			]
		);
		$repeater->add_control(
			'previous_text',
			[
				'label' => esc_html__( 'Previous Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Previous', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'select' => 'next_previous_product',					
				],
			]
		);
		$repeater->add_control(
			'previous_icon',
			[
				'label' => esc_html__( 'Previous Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-long-arrow-alt-left',
					'library' => 'solid',
				],
				'condition' => [
					'select' => 'next_previous_product',					
				],	
			]
		);
		$repeater->add_control(
			'nxt_prev_specific',
			[
				'label' => esc_html__( 'Current Category', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Enable', 'theplus' ),
				'label_off' => __( 'Disable', 'theplus' ),				
				'condition' => [
					'select' => 'next_previous_product',					
				],	
			]
		);
		$repeater->add_control(
			'select_title_tag',
			[
				'label' => esc_html__( 'Title Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => theplus_get_tags_options(),
				'separator' => 'before',
				'condition'		=> [
					'select' => 'title',
				],
			]
		);
		$repeater->add_control(
			'sale_badge_oos_text',
			[
				'label' => esc_html__( 'Out of Stock Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => esc_html__( 'Out of Stock', 'theplus' ),
				'placeholder' => esc_html__( 'Out of Stock', 'theplus' ),
				'condition'		=> [
					'select' => 'sale_badge',
				],
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Woo Single Basic', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'select' => 'title',
                    ],
					[
                        'select' => 'short_description',
                    ],
					[
                        'select' => 'sale_badge',
                    ],
					[
                        'select' => 'rating',
                    ],
					[
                        'select' => 'next_previous_product',
                    ],
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ select }}}',				
            ]
        );
		$this->add_control(
			'nxt_prev_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'flex-start',
				'separator' => 'before',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic' => 'align-items: {{VALUE}};',					
				],	
			]
		);
		$this->end_controls_section();
		/*content end*/
		
		/*style start*/
		/*title style start*/
		$this->start_controls_section(
            'title_styling',
            [
                'label' => esc_html__('Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp_product_title',				
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp_product_title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*title style end*/
		
		/*short description style start*/
		$this->start_controls_section(
            'short_desc_styling',
            [
                'label' => esc_html__('Short Description', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sd_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-details__short-description,{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-details__short-description p',
			]
		);
		$this->add_control(
			'sd_color',
			[
				'label' => esc_html__( 'Short Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-details__short-description,{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-details__short-description p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*description style end*/
		
		/*sale badge style start*/
		$this->start_controls_section(
            'sale_badge_styling',
            [
                'label' => esc_html__('Sale Badge', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'sale_badge',
			[
				'label' => esc_html__( 'Sale Badge', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'sale_badge_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Badge Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic span.onsale' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sale_badge_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-basic span.onsale',
			]
		);
		$this->add_control(
			'sale_badge_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .onsale' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'sale_badge_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .onsale',
			]
		);		
		$this->add_responsive_control(
			'sale_badge_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'sale_badge_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .onsale',
			]
		);
		$this->add_control(
			'out_of_stock_badge',
			[
				'label' => esc_html__( 'Out Of stock Badge', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'oos_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'oos_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock',
				
			]
		);
		$this->add_control(
			'oos_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'oos_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oos_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock',
			]
		);
		$this->add_responsive_control(
			'oos_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic span.badge.out-of-stock' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*sale badge style end*/
		
		/*product rating style start*/
		$this->start_controls_section(
            'product_rating_styling',
            [
                'label' => esc_html__('Rating', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'rating_icon_heading',
			[
				'label' => esc_html__( 'Rating Icon', 'theplus' ),
				'type' => Controls_Manager::HEADING,		
			]
		);
		$this->add_responsive_control(
            'rating_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Rating Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .star-rating,
					{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .star-rating::before' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'rating_icon_color_ck',
			[
				'label' => esc_html__( 'Rating Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .star-rating' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_control(
			'rating_icon_color_empty',
			[
				'label' => esc_html__( 'Empty Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .star-rating::before' => 'color: {{VALUE}}',
				],
				
			]
		);
		$this->add_control(
			'rating_count_heading',
			[
				'label' => esc_html__( 'Rating Count', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'rating_count_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .woocommerce-review-link',
				
			]
		);
		$this->add_control(
			'rating_count_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .woocommerce-product-rating .woocommerce-review-link' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*product rating style end*/
		
		/*next previous navigation start*/
		$this->start_controls_section(
            'nxt_prev_styling',
            [
                'label' => esc_html__('Next/Previous', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'np_title_icn_heading',
			[
				'label' => esc_html__( 'Title/Icon', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_responsive_control(
			'nxt_prev_t_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'nxt_prev_t_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
            'nxt_prev_gap',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gap', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb.tp-wsb-next' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'nxt_prev_t_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong',				
			]
		);
		$this->add_responsive_control(
			'nxt_prev_t_svg_icon',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Svg Icon Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=>20,
				],			
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link svg,{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->start_controls_tabs( 'tabs_nxt_prev_t_style' );
		$this->start_controls_tab(
			'tab_nxt_prev_t_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'nxt_prev_t_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
					{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link svg,
					{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'nxt_prev_t_bg',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'nxt_prev_t_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong',
			]
		);
		$this->add_responsive_control(
			'nxt_prev_t_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'nxt_prev_t_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link strong',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_nxt_prev_t_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'nxt_prev_t_color_a',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link,
					{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link svg,
					{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong svg' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'nxt_prev_t_bg_a',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'nxt_prev_t_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong',
			]
		);
		$this->add_responsive_control(
			'nxt_prev_t_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'nxt_prev_t_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link,
				{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb:hover .post_nav_link strong',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'np_product_infobox_heading',
			[
				'label' => esc_html__( 'Product Info Box', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'p_info_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'p_info_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'p_info_offset',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 150,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'top: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_responsive_control(
			'pro_info_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'min-width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_control(
			'pro_info_overflow',
			[
				'label' => esc_html__( 'Overflow Hidden', 'theplus' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __( 'Enable', 'theplus' ),
				'label_off' => __( 'Disable', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'overflow: hidden;',
				],
				'default' => 'no',				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'p_info_bg_a',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'p_info_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner',
			]
		);
		$this->add_responsive_control(
			'p_info_br_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'p_info_shadow_a',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner',
			]
		);
		$this->add_control(
			'p_info_up_arrow_heading',
			[
				'label' => esc_html__( 'Up Arrow', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'p_info_up_arrow_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .post_nav_link:hover+.tp-wsb-next-prev-inner:before' => 'border-bottom: 5px solid {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'p_info_previous_offset',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Previous Offset', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb.tp-wsb-prev .post_nav_link:hover+.tp-wsb-next-prev-inner:before' => 'left: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_responsive_control(
			'p_info_next_offset',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Next Offset', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 250,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb.tp-wsb-next .post_nav_link:hover+.tp-wsb-next-prev-inner:before' => 'right: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_control(
			'np_product_image_heading',
			[
				'label' => esc_html__( 'Product Image', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pro_image_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],				
				'render_type' => 'ui',				
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-image img' => 'max-width: {{SIZE}}{{UNIT}};min-width: {{SIZE}}{{UNIT}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pro_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-image img',
			]
		);
		$this->add_responsive_control(
			'pro_image_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pro_image_shadow',
				'label' => esc_html__( 'Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-image img',
			]
		);
		$this->add_control(
			'np_product_title_heading',
			[
				'label' => esc_html__( 'Product Title', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pro_title_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pro_title_typography',
				'selector' => '{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-content',
			]
		);
		$this->add_control(
			'pro_title_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-woo-single-basic .tp-wsb-next-prev .tp-wsb .tp-wsb-next-prev-inner .post-content' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*next previous product title end*/		
		/*style end*/
		
	}
	
	
	public function render() {
		$settings = $this->get_settings_for_display();		
		$loop_content=$settings["loop_content"];
		if ( class_exists('woocommerce') ) {
			global $post,$product;
			$product = wc_get_product();
			if ( ! $product ) {
				return '';
			}
			if(!empty($loop_content)) {
				$output='<div class="tp-woo-single-basic">';
				
				foreach($loop_content as $item){
					$select = $item['select'];
					$select_title_tag = $item['select_title_tag'];
					/*woo short desc*/					
					$short_description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );	
					/*woo short desc*/
					
					/*next previous*/
					$nxt_icon=$previous_icon='';
					$nxt_text=$item["nxt_text"];
					$previous_text=$item["previous_text"];
					
					if(!empty($item["nxt_icon"])){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $item['nxt_icon'], [ 'aria-hidden' => 'true' ]);
						$nxt_icon = ob_get_contents();
						ob_end_clean();						
					}
					if(!empty($item["previous_icon"])){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $item['previous_icon'], [ 'aria-hidden' => 'true' ]);
						$previous_icon = ob_get_contents();
						ob_end_clean();						
					}
					/*next previous*/
					
					/*get title start*/
					if(!empty($select) && $select=='title'){				
						$output .= '<'.theplus_validate_html_tag($select_title_tag).' class="tp_product_title entry-title">'.get_the_title().'</'.theplus_validate_html_tag($select_title_tag).'>';
					}
					/*get title end*/
					
					/*get short description start*/
					if(!empty($select) && $select=='short_description' && !empty($short_description)){						
						$output .='<div class="woocommerce-product-details__short-description">'.$short_description.'</div>';
					}
					/*get short description end*/
					
					/*get sale badge start*/
					if(!empty($select) && $select=='sale_badge'){				
						if(tp_out_of_stock()) {
							$output .= '<span class="badge out-of-stock">'.esc_html($item['sale_badge_oos_text']).'</span>';
						}else if($product->is_on_sale()){
							if ('discount' == 'discount') {
								if($product->get_type() == 'simple' || $product->get_type() == 'external'){	
									$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
									$output .= apply_filters('woocommerce_sale_flash', '<span class="badge onsale perc">&darr; '.$percentage.'%</span>', $post, $product);
								}else{
									$output .= apply_filters('woocommerce_sale_flash', '<span class="badge onsale">'.esc_html__( 'Sale','theplus' ).'</span>', $post, $product);
								}
							} 
						}
					}
					/*get sale badge end*/
					
					/*get rating start*/
					if(!empty($select) && $select=='rating' && wc_review_ratings_enabled()){
						$rating_count = $product->get_rating_count();
						$review_count = $product->get_review_count();
						$average      = $product->get_average_rating();
						
						if ( $rating_count > 0 ) { 
							$output .='<div class="woocommerce-product-rating">';
								$output .= wc_get_rating_html( $average, $rating_count );
								if ( comments_open() ) {							
									$output .='<a href="#reviews" class="woocommerce-review-link" rel="nofollow">('.$review_count.' customer reviews)</a>';
								}
							$output .='</div>';
						}
					  
					}
					/*get rating end*/
					
					/*next previous product start*/
					if(!empty($select) && $select=='next_previous_product'){	
						if(isset($item['nxt_prev_specific']) && $item['nxt_prev_specific'] == 'yes'){
							$next_post = get_next_post(true, '', 'product_cat');
							$prev_post = get_previous_post(true, '', 'product_cat');
						}else{
							$next_post = get_next_post();
							$prev_post = get_previous_post();
						}
						
						$output .= '<div class="tp-wsb-next-prev">';
							if (!empty( $prev_post )){
								$output .= '<div class="tp-wsb tp-wsb-prev">';
									$output .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'" class="post_nav_link prev" rel="'.esc_attr__('prev','theplus').'">';
										$output .= '<strong class="tp-wsb-title">'.$previous_icon.'&nbsp;'.esc_html__($previous_text).'</strong>';							
									$output .= '</a>';
										$output .= '<div class="tp-wsb-next-prev-inner">';									
											if (has_post_thumbnail( $prev_post->ID ) ){ 
												$output .= '<div class="post-image">';											
													$output .= get_the_post_thumbnail( $prev_post->ID, 'thumbnail' );										
												$output .= '</div>';
											 }
											$output .= '<div class="post-content">';								
												$output .= '<span>'.theplus_remove_wpautop($prev_post->post_title).'</span>';
											$output .= '</div>';
										$output .= '</div>';
								$output .= '</div>';								
							}
							if (!empty( $next_post )){
								$output .= '<div class="tp-wsb tp-wsb-next">';
									$output .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'" class="post_nav_link next" rel="'.esc_attr__('next','theplus').'">';
										$output .= '<strong class="tp-wsb-title">'.esc_html__($nxt_text).'&nbsp;'.$nxt_icon.'</strong>';
									$output .= '</a>';
									$output .= '<div class="tp-wsb-next-prev-inner">';									
										if (has_post_thumbnail( $next_post->ID ) ){
											$output .= '<div class="post-image">';
												$output .= get_the_post_thumbnail( $next_post->ID, 'thumbnail' );							
											$output .= '</div>';
										 }
										$output .= '<div class="post-content">';								
											$output .= '<span>'.theplus_remove_wpautop($next_post->post_title).'</span>';
										$output .= '</div>';
									$output .= '</div>';
								
							$output .= '</div>';
							}
						$output .= '</div>';
					}
					/*next previous product start*/
				}
				$output .= '</div>';
				echo $output;
			}
		}
	}	
}