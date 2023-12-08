<?php 
/*
Widget Name: Post Previous Next
Description: Post Previous Next
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Post_Navigation extends Widget_Base {
		
	public function get_name() {
		return 'tp-post-navigation';
	}

    public function get_title() {
        return esc_html__('Post Prev/Next', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-exchange theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-builder');
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
			'style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					"style-1" => esc_html__("Style 1", 'theplus'),					
					"style-2" => esc_html__("Style 2", 'theplus'),					
					"style-3" => esc_html__("Style 3", 'theplus'),					
					"style-4" => esc_html__("Style 4", 'theplus'),					
				],			
			]
		);
		$this->add_control(
            'showcsttexonomy',
            [
				'label' => esc_html__( 'Related', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',	
			]
        );
		$this->add_control(
			'showcsttexonomy_select',
			[
				'label' => esc_html__( 'Taxonomies', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'options' => theplus_get_post_taxonomies(),
				'default' => 'category',
				'dynamic' => ['active' => true,],
				'condition' => [
					'showcsttexonomy' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'st3minheight',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Min Height', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'condition' => [
					'style' => 'style-3',
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .tp-post-nav-hover-con' => 'min-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'prevText',
			[
				'label'       => esc_html__( 'Previous Post', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Previous Post',
				'placeholder' => 'Previous Post',
				'label_block' => true,	
			]
		);
		$this->add_control(
			'nextText',
			[
				'label'       => esc_html__( 'Next Post', 'theplus' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Next Post',
				'placeholder' => 'Next Post',
				'label_block' => true,
			]
		);
		$this->end_controls_section();
		/*End Content Section*/
		/*Prev/Next icon Style*/
		$this->start_controls_section(
            'section_np_icon_style',
            [
                'label' => esc_html__('Prev/Next Icon', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-2',
				],
            ]
        );
		$this->add_responsive_control(
			'np_icon_align',
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
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .tp-post-nav' => 'justify-content: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'np_icon_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
            'np_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 200,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_tab_np_icon' );
		$this->start_controls_tab(
			'tab_np_icon_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_np_icon_color_n',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'np_icon_background_n',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'np_icon_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i',
			]
		);
		$this->add_responsive_control(
			'np_icon_radius_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'np_icon_shadow_n',
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next a i',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tabs_np_icon_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_np_icon_h',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover a i' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'np_icon_background_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover a i',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'np_icon_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover a i',
			]
		);
		$this->add_responsive_control(
			'np_icon_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover a i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'np_icon_shadow_h',
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover a i,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover a i',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*Prev/Next icon Style*/
		/*Prev/Next content Style*/
		$this->start_controls_section(
            'section_next_prev_con_style',
            [
                'label' => esc_html__('Prev/Next Content', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => ['style-3','style-4'],
				],
            ]
        );
		$this->add_responsive_control(
			'np__con_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover .tp-post-nav-hover-con,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover .tp-post-nav-hover-con' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'np__con_background_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover .tp-post-nav-hover-con,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover .tp-post-nav-hover-con',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'np__con_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover .tp-post-nav-hover-con,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover .tp-post-nav-hover-con',
			]
		);
		$this->add_responsive_control(
			'np__con_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover .tp-post-nav-hover-con,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover .tp-post-nav-hover-con' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'np__con_shadow_h',
				'selector' => '{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-prev:hover .tp-post-nav-hover-con,{{WRAPPER}} .tp-post-navigation.tp-nav-style-2 .post-next:hover .tp-post-nav-hover-con',
			]
		);
		$this->end_controls_section();
		/*Prev/Next content Style*/
		/*Prev/Next Post Title Style*/
		$this->start_controls_section(
            'section_next_prev_title_style',
            [
                'label' => esc_html__('Prev/Next', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => ['style-4'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'navTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .prev-post-content b,{{WRAPPER}} .tp-post-navigation .next-post-content b',
			]
		);
		$this->start_controls_tabs( 'tabs_next_prev_style' );
		$this->start_controls_tab(
			'tab_next_prev_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'navNormalColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .prev-post-content b,{{WRAPPER}} .tp-post-navigation .next-post-content b' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_next_prev_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'navHoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev-post-content b,{{WRAPPER}} .tp-post-navigation .post-next:hover .next-post-content b' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Prev/Next Post Title Style*/
		/*Post Title Style*/
		$this->start_controls_section(
            'section_title_style',
            [
                'label' => esc_html__('Post Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => ['style-4'],
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'titleTypo',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .prev-post-content span,{{WRAPPER}} .tp-post-navigation .next-post-content span',
			]
		);
		$this->start_controls_tabs( 'tabs_title_style' );
		$this->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'titleNormalColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .prev-post-content span,{{WRAPPER}} .tp-post-navigation .next-post-content span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'titleHoverColor',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev-post-content span,{{WRAPPER}} .tp-post-navigation .post-next:hover .next-post-content span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Post Title Style*/
		/*Image Style*/
		$this->start_controls_section(
            'section_img_style',
            [
                'label' => esc_html__('Image', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style!' => ['style-3','style-4'],
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_img_style' );
		$this->start_controls_tab(
			'tab_img_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'imgBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation img',
			]
		);
		$this->add_responsive_control(
			'imgBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'imgBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-navigation img',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_img_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'imgBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev:hover img,{{WRAPPER}} .tp-post-navigation .post-next:hover img',
			]
		);
		$this->add_responsive_control(
			'imgBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation .post-prev:hover img,{{WRAPPER}} .tp-post-navigation .post-next:hover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'imgBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev:hover img,{{WRAPPER}} .tp-post-navigation .post-next:hover img',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Image Style*/
		/*Previous Box Style*/
		$this->start_controls_section(
            'section_prev_box_style',
            [
                'label' => esc_html__('Prev Box', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-1',
				],
            ]
        ); 
		$this->start_controls_tabs( 'tabs_prev_box_style' );
		$this->start_controls_tab(
			'tab_prev_box_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'prevBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev .prev',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'prevBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev .prev',	
			]
		);
		$this->add_responsive_control(
			'prevBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation .post-prev .prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'prevBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev .prev',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_prev_box_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'prevBgHover',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'prevBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev',	
			]
		);
		$this->add_responsive_control(
			'prevBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'prevBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Previous Box Style*/
		/*Next Box Style*/
		$this->start_controls_section(
            'section_next_box_style',
            [
                'label' => esc_html__('Next Box ', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-1',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_next_box_style' );
		$this->start_controls_tab(
			'tab_next_box_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'nextBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next .next',
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'nextBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next .next',		
			]
		);
		$this->add_responsive_control(
			'nextBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation .post-next .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nextBoxShadow',
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next .next',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_next_box_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'nextBgHover',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next:hover .next',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'nextBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next:hover .next',	
			]
		);
		$this->add_responsive_control(
			'nextBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-post-navigation .post-next:hover .next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nextBoxShadowHover',
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next:hover .next',
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*Next Box Style*/
		/*Content Background*/
		$this->start_controls_section(
            'section_content_bg_style',
            [
                'label' => esc_html__('Content Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-1',
				],
            ]
        );		
		$this->add_responsive_control(
			'padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default' => [
							'top' => '',
							'right' => '',
							'bottom' => '',
							'left' => '',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',	
			]
		);
		$this->start_controls_tabs( 'tabs_content_bg_style' );
		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'boxBg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}}',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorder',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}}',
			]
		);
		$this->add_responsive_control(
			'boxBorderRadius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);	
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadow',
				'selector' => '{{WRAPPER}}',
			]
		);	
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'boxBgHover',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}}:hover',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'boxBorderHover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}}:hover',	
			]
		);
		$this->add_responsive_control(
			'boxBorderRadiusHover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}}:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'boxBoxShadowHover',
				'selector' => '{{WRAPPER}}:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Content Background*/		
		/*image Style*/
		$this->start_controls_section(
            'section_image_bg_style',
            [
                'label' => esc_html__('Background Image ', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => 'style-3',
				],
            ]
        );
		$this->add_control(
			'column_bg_image_normal',
			[
				'label' => esc_html__( 'Normal Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .post_nav_link .tp-post-nav-hover-con:before' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'column_bg_image_hover',
			[
				'label' => esc_html__( 'Hover Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .post_nav_link:hover .tp-post-nav-hover-con:before' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
            'column_bg_image_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Image Position', 'theplus'),
				'default' => 'center center',
				'options' => theplus_get_image_position_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .tp-post-nav-hover-con' => 'background-position: {{VALUE}} !important;',
				],
			]
        );
		$this->add_control(
            'column_bg_img_attach', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Attachment', 'theplus'),
				'default' => 'fixed',
				'options' => theplus_get_image_attachment_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .tp-post-nav-hover-con' => 'background-attachment: {{VALUE}} !important;',
				],
			]
        );
		$this->add_control(
            'column_bg_img_repeat', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Repeat', 'theplus'),
				'default' => 'no-repeat',
				'options' => theplus_get_image_reapeat_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .tp-post-nav-hover-con' => 'background-repeat: {{VALUE}} !important;',
				],
			]
        );
		$this->add_control(
            'column_bg_image_size', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Background Size', 'theplus'),
				'default' => 'cover',
				'options' => theplus_get_image_size_options(),
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-trans.tp-nav-style-3 .tp-post-nav-hover-con' => 'background-size: {{VALUE}} !important;',
				],
			]
        );
		$this->end_controls_section();
		/*Icon Style*/
		$this->start_controls_section(
            'section_st4_icon_style',
            [
                'label' => esc_html__('Icon ', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => ['style-4'],
				],
            ]
        );
		$this->add_control(
			'st4_icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .tp-post-nav-hover-arrow' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'st4_icon_color_hover',
			[
				'label' => esc_html__( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .post-prev:hover .tp-post-nav-hover-arrow,
					{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .post-next:hover .tp-post-nav-hover-arrow' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'st4_icon_bg',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .tp-post-nav-hover-arrow' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'st4_icon_bg_hover',
			[
				'label' => esc_html__( 'Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .post-prev:hover .tp-post-nav-hover-arrow,{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .post-next:hover .tp-post-nav-hover-arrow' => 'background: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*Icon Style*/
		/*Post Content Style*/
		$this->start_controls_section(
            'section_st4_post_con_style',
            [
                'label' => esc_html__('Post Content ', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'style' => ['style-4'],
				],
            ]
        );
		$this->add_responsive_control(
			'st4_post_con_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .prev-post-content,
					{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .next-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'st4_post_con_bg',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .prev-post-content,
					{{WRAPPER}} .tp-post-navigation.tp-nav-style-4 .next-post-content' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'st4titleTypo',
				'label' => esc_html__( 'Post Title Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .post-next:hover .next-post-content span,{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev-post-content span',
			]
		);
		$this->add_control(
			'st4titleNormalColor',
			[
				'label' => esc_html__( 'Post Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .post-next:hover .next-post-content span,{{WRAPPER}} .tp-post-navigation .post-prev:hover .prev-post-content span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'st4labelTypo',
				'label' => esc_html__( 'Post Label Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-post-navigation .prev-post-content b,{{WRAPPER}} .tp-post-navigation .next-post-content b',
			]
		);
		$this->add_control(
			'st4labelNormalColor',
			[
				'label' => esc_html__( 'Post Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-post-navigation .prev-post-content b,{{WRAPPER}} .tp-post-navigation .next-post-content b' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*Post Content Style*/
	}

    protected function render() {

       $settings = $this->get_settings_for_display();

        $post_id = get_queried_object_id();
		$post = get_queried_object();
		$uid_psnav=uniqid('tp-nav');
		$style = (!empty($settings['style'])) ? $settings['style'] : 'style-1';
		$prevText = (!empty($settings['prevText'])) ? $settings['prevText'] : '';
		$nextText = (!empty($settings['nextText'])) ? $settings['nextText'] : '';
		$showcsttexonomy = isset($settings['showcsttexonomy']) ? $settings['showcsttexonomy'] : '';

		if(isset($showcsttexonomy) && $showcsttexonomy == 'yes'){			
			$showcsttexonomy_select = !empty($settings['showcsttexonomy_select']) ? $settings['showcsttexonomy_select'] : '';			
			if($showcsttexonomy_select){				
				$prev_post = get_previous_post(true, '', $showcsttexonomy_select);
				$next_post = get_next_post(true, '', $showcsttexonomy_select);
			}
		}else{			
			$prev_post = get_previous_post();
			$next_post = get_next_post();
		}

		$prevnav =$img=$prevpostimg =$prevpostcon='';
		if (!empty( $prev_post )){
			
			$prevpostcon .= '<div class="prev-post-content">';
				$prevpostcon .= '<b>'.esc_html($prevText).'</b>';
				$prevpostcon .= '<span>'.esc_html($prev_post->post_title).'</span>';
			$prevpostcon .= '</div>';
			
			if (has_post_thumbnail( $prev_post->ID ) ){
				$prevpostimg .= '<div class="post-image">';					
					$prevpostimg .= tp_get_image_rander( $prev_post->ID, 'thumbnail', [ 'class' => 'tp-nav-trans' ], 'post' );
				$prevpostimg .= '</div>';
			}else{
				$prevpostimg .= '<div class="post-image">';
					$prevpostimg .= '<img src="'.THEPLUS_URL .'/assets/images/placeholder-grid.jpg" class="tp-nav-trans" />';
				$prevpostimg .= '</div>';
			}
			
			if(!empty($style)){
				$lazyclass='';
				if($style=='style-1'){	
					$prevnav .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'" class="post_nav_link prev tp-nav-trans" rel="'.esc_attr__('prev','theplus').'">';	
						$prevnav .=$prevpostimg;
						$prevnav .=$prevpostcon;						
					$prevnav .= '</a>';
				}else if($style=='style-2'){				
					$prevnav .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'" class="post_nav_link prev tp-nav-trans" rel="'.esc_attr__('prev','theplus').'"><i aria-hidden="true" class="far fa-arrow-alt-circle-left"></i>';
					$prevnav .='<div class="tp-post-nav-hover-con">'.$prevpostimg.$prevpostcon.'</div></a>';
				}else if($style=='style-3'){				
					$img = wp_get_attachment_image_src( get_post_thumbnail_id( $prev_post->ID ),'full');
					if(tp_has_lazyload()){
						$lazyclass=' lazy-background';
					}
					$prevnav .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'" class="post_nav_link prev tp-nav-trans" rel="'.esc_attr__('prev','theplus').'">';
					$prevnav .='<div class="tp-post-nav-hover-con '.esc_attr($lazyclass).'" style="background-image: url('.esc_url(!empty($img[0]) ? $img[0] : '').');background-size: cover;background-attachment: fixed;background-position: center center;background-repeat:no-repeat;">'.$prevpostcon.'</div></a>';
				}else if($style=='style-4'){				
					$prevnav .= '<a href="'.esc_url(get_permalink( $prev_post->ID )).'" class="post_nav_link prev tp-nav-trans" rel="'.esc_attr__('prev','theplus').'">';
						$prevnav .=$prevpostimg;
						$prevnav .='<div class="tp-post-nav-hover-arrow"></div>';
						$prevnav .=$prevpostcon;
					$prevnav .='</a>';
				}
				
			}
		}

		
		$nextnav =$img1=$nextpostcon= $nextpostimg='';
		if (!empty( $next_post )){
			$nextpostcon .= '<div class="next-post-content">';
				$nextpostcon .= '<b>'.esc_html($nextText).'</b>';
				$nextpostcon .= '<span>'.esc_html($next_post->post_title).'</span>';
			$nextpostcon .= '</div>';
			
			if (has_post_thumbnail( $next_post->ID ) ){
				$nextpostimg .= '<div class="post-image">';
					$nextpostimg .= tp_get_image_rander( $next_post->ID, 'thumbnail', [ 'class' => 'tp-nav-trans' ], 'post' );
				$nextpostimg .= '</div>';
			}else{
				$nextpostimg .= '<div class="post-image">';
					$nextpostimg .= '<img src="'.THEPLUS_URL .'/assets/images/placeholder-grid.jpg" class="tp-nav-trans" />';
				$nextpostimg .= '</div>';
			}			
			
			if(!empty($style)){
				$lazyclass='';
				if($style=='style-1'){					
					$nextnav .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'" class="post_nav_link next tp-nav-trans" rel="'.esc_attr__('next','theplus').'">';
					$nextnav .=$nextpostcon;
					$nextnav .=$nextpostimg;				
					$nextnav .= '</a>';
				}else if($style=='style-2'){
					$nextnav .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'" class="post_nav_link next tp-nav-trans" rel="'.esc_attr__('next','theplus').'"><i aria-hidden="true" class="far fa-arrow-alt-circle-right"></i>';
					$nextnav .='<div class="tp-post-nav-hover-con">'.$nextpostimg.$nextpostcon.'</div></a>';
				}else if($style=='style-3'){
					$img1 = wp_get_attachment_image_src( get_post_thumbnail_id( $next_post->ID ),'full');
					if(tp_has_lazyload()){
						$lazyclass=' lazy-background';
					}
					$nextnav .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'" class="post_nav_link next tp-nav-trans" rel="'.esc_attr__('next','theplus').'">';
					$nextnav .='<div class="tp-post-nav-hover-con '.esc_attr($lazyclass).'" style="background-image: url('.esc_url($img1[0]).');background-size: cover;background-attachment: fixed;background-position: center center;background-repeat:no-repeat;">'.$nextpostcon.'</div></a>';
				}else if($style=='style-4'){				
					$nextnav .= '<a href="'.esc_url(get_permalink( $next_post->ID )).'" class="post_nav_link next tp-nav-trans" rel="'.esc_attr__('next','theplus').'">';
						$nextnav .=$nextpostimg;
						$nextnav .='<div class="tp-post-nav-hover-arrow"></div>';
						$nextnav .=$nextpostcon;
					$nextnav .='</a>';
				}				
			}			
		}
		
		 $output = '<div class="tp-post-navigation tp-nav-trans tp-widget-'.esc_attr($uid_psnav).' tp-nav-'.$style.'">';
			$output .= '<div class="tp-post-nav tp-row">';
				$colclass='';
				if($style!='style-2'){
					$colclass = 'tp-col tp-col-md-6 tp-col-sm-6 tp-col-xs-12';
				}
				$output .= '<div class="post-prev '.esc_attr($colclass).'">';
					$output .= $prevnav;
				$output .= '</div>';
				$output .= '<div class="post-next '.esc_attr($colclass).'">';
					$output .= $nextnav;
				$output .= '</div>';
			$output .= '</div>';
		$output .= "</div>";
       
        echo $output;
	}
	
    protected function content_template() {
	
    }
}