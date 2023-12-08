<?php 
/*
Widget Name: Pricing List
Description: Pricing List
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
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Pricing_List extends Widget_Base {
		
	public function get_name() {
		return 'tp-pricing-list';
	}

    public function get_title() {
        return esc_html__('Pricing List', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-file-text theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

    protected function register_controls() {
		$this->start_controls_section(
				'Pricing_list',
				[
					'label' => esc_html__( 'Pricing List', 'theplus' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				]
		);
		$this->add_control(
			'menu_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1'  => esc_html__( 'Modern', 'theplus' ),
					'style_2' => esc_html__( 'Simple', 'theplus' ),
					'style_3' => esc_html__( 'Classic', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Italian Pizza', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'title_tag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'Small | Medium | Large', 'theplus' ),
				'placeholder' => esc_html__( 'Seprate by "|" ', 'theplus' ),
				'description' => esc_html__( 'Display multiple tag use separator e.g. Small | Medium | Large ', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'price',
			[
				'label' => esc_html__( 'Price', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( '$4.99', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_control(
			'content',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'Pricing_list_image_option',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'menu_style' => 'style_3',
				],
			]
		);
		$this->add_control(
			'icon_type',
			[
				'label' => esc_html__( 'Icon Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => esc_html__( 'Image', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
				'condition' => [
					'menu_style' => 'style_3',
				],
			]
		);
		$this->add_control(
			'image_option',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'dynamic' => ['active'   => true,],
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'image',
				],
			]
		);
		$this->add_responsive_control(
			'imglotti_right_space',
			[
				'label' => esc_html__( 'Right Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default'=>[
					'unit'=>'px',
					'size'=> '',
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-flex-img,{{WRAPPER}} .pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-flex-imgs' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'menu_style' => 'style_3',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image_option_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'image',
				],
			]
		);
		$this->add_control(
			'img_shape',
			[
				'label' => esc_html__( 'Image Shape', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => esc_html__( 'None', 'theplus' ),
					'img-rounded' => esc_html__( 'Rounded', 'theplus' ),
					'img-circle' => esc_html__( 'Circle', 'theplus' ),
				],
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'image',
				],
			]
		);
		$this->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'lottie',
				],
			]
		);
		$this->end_controls_section();
		$this->start_controls_section(
			'section_background',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_content_background' );
		$this->start_controls_tab(
			'tab_content_background_front',
			[
				'label' => esc_html__( 'Front', 'theplus' ),
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_bg_options',				
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-front,{{WRAPPER}} .pt-plus-food-menu.food-menu-style-1 .food-menu-box',
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
				'dynamic' => ['active'   => true,],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'front_bg_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-front,{{WRAPPER}} .pt-plus-food-menu.food-menu-style-1 .food-menu-box',
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
			]
		);
		$this->add_responsive_control(
			'front_bg_border_radious',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-flipbox-front,{{WRAPPER}} .pt-plus-food-menu.food-menu-style-1 .food-menu-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'front_bg_box_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-front,{{WRAPPER}} .pt-plus-food-menu.food-menu-style-1 .food-menu-box',
				
				'condition' => [
					'menu_style' => ['style_1','style_2'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_background_back',
			[
				'label' => esc_html__( 'Back', 'theplus' ),
				'condition' => [
					'menu_style' => ['style_2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'back_bg_options',				
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-back',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'menu_style' => ['style_2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'back_bg_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-back',
				'condition' => [
					'menu_style' => ['style_2'],
				],
			]
		);
		$this->add_responsive_control(
			'back_bg_border_radious',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-flipbox-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'menu_style' => ['style_2'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'back_bg_box_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-flipbox-back',
				
				'condition' => [
					'menu_style' => ['style_2'],
				],
			]
		);
		$this->end_controls_tab();		
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Start Pricing List Style */
		$this->start_controls_section(
				'Pricing_list_style',
				[
					'label' => esc_html__( 'Pricing List', 'theplus' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition'    => [
						'menu_style!' => [ 'style_3'],
					],
				]
		);
		$this->add_control(
			'box_align',
			[
				'label' => esc_html__( 'Box Align', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'text-left',
				'options' => [
					'text-left' => esc_html__( 'Left', 'theplus' ),
					'text-center'  => esc_html__( 'Center', 'theplus' ),
					'text-right'  => esc_html__( 'Right', 'theplus' ),
				],
				'condition'    => [
					'menu_style' => [ 'style_1' ],
				],
			]
		);
		$this->add_control(
				'box_align_top',
				[
					'label' => esc_html__( 'Box Align', 'theplus' ),
					'type' => Controls_Manager::SELECT,
					'default' => 'bottom-left',
					'options' => [
						'top-left' => esc_html__( 'Top Left', 'theplus' ),
						'top-right'  => esc_html__( 'Top Right', 'theplus' ),					
						'bottom-left'  => esc_html__( 'Bottom Left', 'theplus' ),
						'bottom-right'  => esc_html__( 'Bottom Right', 'theplus' ),
					],
					'condition'    => [
						'menu_style' => [ 'style_2' ],
					],
				]
			);
		
		$this->end_controls_section();
		/*End Pricing List Style */
		/*Start Pricing List Title */
		$this->start_controls_section(
				'Pricing_list_title_style',
				[
					'label' => esc_html__( 'Title', 'theplus' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title',
			]
		);
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#313131',
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'title_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title',
				
			]
		);
		$this->add_responsive_control(
			'title_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'title_border',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',	
				'condition'    => [					
					'menu_style' => [ 'style_3' ],
				],
			]
		);
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'separator' => 'before',
				'condition' => [
					'title_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'bd_title_height',
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
				'condition' => [
					'title_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],		
				'condition' => [
					'title_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'bd_title_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
				'condition' => [
					'title_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-title' => 'border-color: {{VALUE}};',
				],				
			]
		);
		$this->end_controls_section();
		/*End Pricing List Title */
		/*Start Pricing List Line */
		$this->start_controls_section(
				'Pricing_list_line_style',
				[
					'label' => esc_html__( 'Line', 'theplus' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition'    => [					
						'menu_style' => [ 'style_3' ],
					],
				]
		);
		$this->add_control(
			'border_line_style',
			[
				'label' => esc_html__( 'Line', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',	
				
			]
		);
		$this->add_control(
			'line_style',
			[
				'label' => esc_html__( 'Line', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'separator' => 'before',
				'condition' => [
					'border_line_style' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-menu-divider .menu-divider' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'bd_line_height',
			[
				'label' => esc_html__( 'Line Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'condition' => [
					'border_line_style' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-menu-divider .menu-divider' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_control(
			'bd_line_color',
			[
				'label' => esc_html__( 'Line Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#888',
				'condition' => [
					'border_line_style' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu.food-menu-style-3 .food-flex-line .food-menu-divider .menu-divider' => 'border-color: {{VALUE}};',
				],				
			]
		);
		$this->end_controls_section();
		/*End Pricing List Line */
		/*Start Pricing List Tag */
		$this->start_controls_section(
			'Pricing_list_tag_style',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tag_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag',
			]
		);
		$this->add_control(
			'tag_right_margin',
			[
				'label' => esc_html__( 'Tag Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px' ],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],				
				'range' => [					
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag' => 'margin-right: {{SIZE}}{{UNIT}};',
				],				
			]
		);
		$this->add_control(
			'tag_color',
			[
				'label' => esc_html__( 'Tag Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#313131',
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'tag_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag',
				
			]
		);
		$this->add_responsive_control(
			'tag_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],						
			]
		);
		$this->add_control(
			'tag_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);	
		$this->end_controls_section();
		/*End Pricing List Tag */
		/*Start Pricing List Price */
		$this->start_controls_section(
			'Pricing_list_price_style',
			[
				'label' => esc_html__( 'Price', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-price',
			]
		);
		$this->add_control(
			'price_color',
			[
				'label' => esc_html__( 'Price Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#313131',
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-price' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'price_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-price',
				
			]
		);
		$this->add_responsive_control(
			'price_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-price' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],						
			]
		);
		$this->add_control(
			'price_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-menu-price' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*End Pricing List Price */
		/*Start Pricing List desc */
		$this->start_controls_section(
			'Pricing_list_desc_style',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Description Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#313131',
				'global' => [
                    'default' => Global_Colors::COLOR_PRIMARY
                ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc,{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc p' => 'color: {{VALUE}}',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'desc_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc',
				
			]
		);
		$this->add_responsive_control(
			'dec_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],						
			]
		);
		$this->add_control(
			'dec_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*End Pricing List desc */
		/*Start Pricing List Image */
		$this->start_controls_section(
			'Pricing_list_img_style',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'image',
				],
			]
		);
		$this->add_responsive_control(
            'img_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Max Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 200,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs.food-flex-img' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
		);
		$this->add_responsive_control(
            'img_min_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Min Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 5,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs.food-flex-img' => 'min-width: {{SIZE}}{{UNIT}}',
				],
            ]
		);
		$this->add_control(
			'img_border',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control(
			'img_border_style',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'separator' => 'before',
				'condition' => [
					'img_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs .food-img img' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'border_height',
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
				'condition' => [
					'img_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs .food-img img' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_control(
			'border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#f5f5f5',
				'condition' => [
					'img_border' => 'yes',
				],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs .food-img img' => 'border-color: {{VALUE}};',
				],				
			]
		);
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs .food-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_shadow',
				'selector' => '{{WRAPPER}} .pt-plus-food-menu .food-menu-box .food-flex-imgs .food-img img',
			]
		);
		$this->end_controls_section();
		/*End Pricing List Image */
		/*lottie style*/
		$this->start_controls_section(
			'section_lottie_styling',
			[
				'label' => esc_html__('Lottie', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_style' => 'style_3',
					'icon_type' => 'lottie',
				],
			]
		);
		$this->add_control(
			'lottiedisplay', 
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Display', 'theplus'),
				'default' => 'inline-block',
				'options' => [
					'block'  => esc_html__( 'Block', 'theplus' ),
					'inline-block'  => esc_html__( 'Inline Block', 'theplus' ),
					'flex'  => esc_html__( 'Flex', 'theplus' ),
					'inline-flex'  => esc_html__( 'Inline Flex', 'theplus' ),
					'initial'  => esc_html__( 'Initial', 'theplus' ),
					'inherit'  => esc_html__( 'Inherit', 'theplus' ),
				],
			]
		);
		$this->add_responsive_control(
			'lottieMright',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Margin Right', 'theplus'),
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
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} lottie-player' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'render_type' => 'ui',
			]
		);
		$this->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 75,
				],
			]
		);
		$this->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 75,
				],
			]
		);
		$this->add_responsive_control(
			'lottieSpeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'lottieLoop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lottiehover',
			[
				'label' => esc_html__( 'Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*lottie style*/
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

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';

}	
	protected function render() {
		$settings = $this->get_settings_for_display();		
		$content = $settings['content'];
		$menu_style = $settings['menu_style'];
		$box_align_top = $settings['box_align_top'];
		$box_align = $settings['box_align'];
		$title = $settings['title'];
		$title_tag = $settings['title_tag'];
		$price = $settings['price'];
		$img_shape = $settings['img_shape'];
		
		/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-widget-wrapper";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		$style_class='';
			if($menu_style =="style_1"){
				$style_class = 'style-1';
			}else if($menu_style =="style_2"){
				$style_class = 'style-2';
			}else if($menu_style =="style_3"){
				$style_class = 'style-3';
			}

			/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$description=$food_title=$food_price=$food_img = $food_tag =$food_flex_img='';
		
		if(!empty($settings['image_option']['url'])){			
			$image_option=$settings['image_option']['id'];
			$image_src = tp_get_image_rander( $image_option,$settings['image_option_thumbnail_size']);
			$img = !empty($image_src) ? $image_src : '<img src="'.Utils::get_placeholder_image_src().'"/>';
			$food_img = '<div class="food-img '.esc_attr($img_shape).'">'.$img.'</div>';
			$food_flex_img = 'food-flex-img';
		}
		//lottie
		if(!empty($menu_style) && $menu_style == "style_3"){
			$icon_type = $settings["icon_type"];
			if(!empty($icon_type) && $icon_type == 'lottie'){
				$ext = pathinfo($settings['lottieUrl']['url'], PATHINFO_EXTENSION);
				if($ext!='json'){
					$food_img .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
				}else{
					$lottiedisplay = isset($settings['lottiedisplay']) ? $settings['lottiedisplay'] : 'inline-block';
					$lottieMright = isset($settings['lottieMright']['size']) ? $settings['lottieMright']['size'] : 20;
					$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 75;
					$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 75;
					$lottieSpeed = isset($settings['lottieSpeed']['size']) ? $settings['lottieSpeed']['size'] : 1;
					$lottieLoop = isset($settings['lottieLoop']) ? $settings['lottieLoop'] : 'no';
					$lottiehover = isset($settings['lottiehover']) ? $settings['lottiehover'] : 'no';
					$lottieLoopValue='';
					if(!empty($settings['lottieLoop']) && $settings['lottieLoop']=='yes'){
						$lottieLoopValue ='loop'; 
					}
					$lottieAnim='autoplay';
					if(!empty($settings['lottiehover']) && $settings['lottiehover']=='yes'){
						$lottieAnim ='hover'; 
					}
					$food_img .='<lottie-player src="'.esc_url($settings['lottieUrl']['url']).'" style="display: '.esc_attr($lottiedisplay).'; width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
				}
			}
		}	
			if(isset($bg_back_img) && !empty($bg_back_img)){
				$bg_back_img = wp_get_attachment_image_src($bg_back_img, "full");
				$img_bg_back_Src = isset($bg_back_img[0]) ? $bg_back_img[0] : Utils::get_placeholder_image_src();
			}else{$img_bg_back_Src = '';}

			if(isset($bg_img) && !empty($bg_img)){
				$bg_front_img = wp_get_attachment_image_src($bg_img, "full");
				$img_bg_Src = isset($bg_front_img[0]) ? $bg_front_img[0] : Utils::get_placeholder_image_src();
			}else{$img_bg_Src = '';}

			if(!empty($title_tag) ){
				$array = explode("|",$title_tag);
				if(!empty($array[1])){
					foreach($array as $value){
						$food_tag .='<h5 class="food-menu-tag" >'.esc_html($value).'</h5>';
					}
				}else{
					$food_tag ='<h5 class="food-menu-tag" >'.esc_html($title_tag).'</h5>';
				}
			}
			if(!empty($title) ){
				$food_title ='<h3 class="food-menu-title" >'.wp_kses_post($title).'</h3>';
			}
			if(!empty($price) ){
				$food_price ='<h4 class="food-menu-price" >'.esc_html($price).'</h4>';
			}
			
			if(!empty($content)){				
				$description = '<div class="food-desc" > '.wp_kses_post($content).' </div>';
			}
		
			$uid = uniqid('food_menu');
			if ($menu_style == 'style_1'){
				$box_align_1 = $box_align;
			}else{
				$box_align_1 = '';
			}
			
			if ($menu_style == 'style_2'){
				$box_align_top_1 = $box_align_top;
			}else{
				$box_align_top_1 = '';
			}
			
			$food_menu ='<div class="pt-plus-food-menu  '.esc_attr($box_align).' '.esc_attr($uid).'  food-menu-'.esc_attr($style_class).' '.esc_attr($animated_class).'" data-uid="'.esc_attr($uid).'" '.$animation_attr.'>';
			if ($menu_style == 'style_1'){
				$food_menu.='<div class="food-menu-box">';				
					$food_menu .= wp_kses_post($food_tag);
					$food_menu .= wp_kses_post($food_title);
					$food_menu .= wp_kses_post($description);
					$food_menu .= wp_kses_post($food_price);
				$food_menu.='</div>';
			}else if ($menu_style == 'style_2'){
				$food_menu.='<div class="food-menu-box '.esc_attr($box_align_top_1).'">';	
					$food_menu.='<div class="food-flipbox flip-horizontal flip-horizontal height-full">';
						$food_menu.='<div class="food-flipbox-holder height-full perspective bezier-1">';
							$food_menu.='<div class="food-flipbox-front bezier-1 no-backface origin-center">';
								$food_menu.='<div class="food-flipbox-content width-full">';
									$food_menu.= '<div class="food-menu-block">'.wp_kses_post($food_tag).'</div>';
									$food_menu.= '<div class="food-menu-block">'.wp_kses_post($food_title).'</div>';
									$food_menu.= wp_kses_post($food_price);
								$food_menu.='</div>';
							$food_menu.='</div>';
							$food_menu.='<div class="food-flipbox-back fold-back-horizontal no-backface bezier-1 origin-center">';
								$food_menu.='<div class="food-flipbox-content width-full ">';
									$food_menu.='<div class="text-center">';
										$food_menu.= wp_kses_post($description);		
									$food_menu.='</div>';
								$food_menu.='</div>';
							$food_menu.='</div>';
						$food_menu.='</div>';
					$food_menu.='</div>';
				$food_menu.='</div>';
			}else if ($menu_style == 'style_3'){
				$food_menu.='<div class="food-menu-box">';
					$food_menu.='<div class="food-menu-flex ">';
						$food_menu.='<div class="food-flex-line ">';
							$food_menu.='<div class="food-flex-imgs '.esc_attr($food_flex_img).'">';
								$food_menu.= $food_img;
							$food_menu.='</div>';		
							$food_menu.='<div class="food-flex-content">';
								$food_menu.= '<div class="food-menu-block">'.wp_kses_post($food_tag).'</div>';
								$food_menu.='<div class="food-title-price">';
									$food_menu.= wp_kses_post($food_title);
									$food_menu.= '<div class="food-menu-divider"><div class="menu-divider '.esc_attr($settings['border_line_style']).'"></div></div>';
									$food_menu.= wp_kses_post($food_price);
								$food_menu.='</div>';
								$food_menu.= wp_kses_post($description);
							$food_menu.='</div>';	
						$food_menu.='</div>';	
						
					$food_menu.='</div>';
				$food_menu.='</div>';
			}
			$food_menu.='</div>';
			
		echo $before_content.$food_menu.$after_content;	
	}
	
    protected function content_template() {}
}