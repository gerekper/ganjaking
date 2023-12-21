<?php
namespace Happy_Addons_Pro\Widget\Skins\Single_Product;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Skin_Base as Elementor_Skin_Base;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Happy_Addons_Pro\Traits\Post_Grid_Markup;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Skin_Base extends Elementor_Skin_Base {

	use Lazy_Query_Builder;
	use Post_Grid_Markup;

	/**
	 * @var string Save current permalink to avoid conflict with plugins the filters the permalink during the post render.
	 */
	protected $current_permalink;

	protected function _register_controls_actions() {

		add_action( 'elementor/element/ha-single-product/_section_feature_image/after_section_end', [ $this, 'register_controls'  ] );
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;

		$this->settings_content_section__tab();

		// Register Style controls
        $this->register_style_sections( $this->parent );

	}

	/**
	 * Settings content section tab
	 */
	protected function settings_content_section__tab() {

		$this->start_controls_section(
			'_section_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->settings_content_controls();

		$this->end_controls_section();

	}

	/**
	 * Settings content Control
	 */
	protected function settings_content_controls() {

		$this->add_control(
			'badge_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'label' => __( 'Badge Text', 'happy-addons-pro' ),
				'placeholder' => __( 'Your badge one text', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide badge text.', 'happy-addons-pro' ),
				'default' => __( 'cyber deal', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'discount_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'label' => __( 'Discount Text', 'happy-addons-pro' ),
				'placeholder' => __( 'Your discount text', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide discount text.', 'happy-addons-pro' ),
				'default' => __( '50% off', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_rating',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Rating', 'happy-addons-pro' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_cat',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Category', 'happy-addons-pro' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_price',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Price', 'happy-addons-pro' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_cart_button',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Cart Button', 'happy-addons-pro' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'add_to_cart_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __( 'Add To Cart Text', 'happy-addons-pro' ),
				'placeholder' => __( 'Your add to cart text', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				],
                'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
                ],
			]
		);

		$this->add_control(
			'show_quick_view_button',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __( 'Show Quick View Button', 'happy-addons-pro' ),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Excerpt Length', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide excerpt.', 'happy-addons-pro' ),
				'min'         => 0,
				'default'     => 15,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label' => __( 'Title HTML Tag', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				// 'separator' => 'before',
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

	}


	/**
	 * Register Style controls
	 */
	public function register_style_sections( $widget ) {

		//Item Box Style controls
		$this->box_style_tab_controls();

		$this->badge_discount_style_controls_section( $widget );

		$this->image_style_controls_section();

		$this->content_style_controls_section();

		$this->cart_and_qv_button_style_controls_section();

		$this->qv_modal_style_controls();
	}

	/**
	 * Item Box Style controls
	 */
	protected function box_style_tab_controls() {

		$this->start_controls_section(
			'_section_item_box_style',
			[
				'label' => __( 'Item Box', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
            'item_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-single-product__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
				'name' => 'item_border',
                'selector' => '{{WRAPPER}} .ha-single-product__item',
            ]
		);

		$this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __( 'Border Radius', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-single-product__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .ha-single-product__item'
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .ha-single-product__item',
            ]
		);

		$this->end_controls_section();
	}

	protected function badge_discount_style_controls_section( $widget ) {

		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_control_id( 'badge_text' ),
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => $this->get_control_id( 'discount_text' ),
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$this->badge__offset( $widget );

		$this->add_responsive_control(
			'badge_discount_spacing',
			[
				'label' => __( 'Space Between', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__badge span:nth-of-type(2)' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'and',
					'terms' => [
						[
							'name' => $this->get_control_id( 'badge_text' ),
							'operator' => '!=',
							'value' => '',
						],
						[
							'name' => $this->get_control_id( 'discount_text' ),
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$this->badge_style_controls();

		$this->discount_style_controls();

		$this->end_controls_section();
	}

	protected function badge__offset( $widget ) {

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __( 'Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$widget->start_popover();

		$this->add_responsive_control(
			'badge_offset_x',
			[
				'label' => __( 'Left', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					$this->get_control_id( 'badge_offset_toggle' ) => 'yes',
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__badge' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_offset_y',
			[
				'label' => __( 'Top', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					$this->get_control_id( 'badge_offset_toggle' ) => 'yes',
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__badge' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->end_popover();
	}

	protected function badge_style_controls( ) {

		$this->add_control(
			'_heading_badge_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'separator'   => 'before',
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'badge_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'badge_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} span.ha-single-product__badge-text',
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__badge-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} span.ha-single-product__badge-text',
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} span.ha-single-product__badge-text',
				'condition' => [
					$this->get_control_id( 'badge_text!' ) => '',
				],
			]
		);
	}

	protected function discount_style_controls( ) {

		$this->add_control(
			'_heading_discount_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Discount', 'happy-addons-pro' ),
				'separator'   => 'before',
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'discount_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'discount_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'discount_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_control(
			'discount_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_control(
			'discount_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'discount_border',
				'selector' => '{{WRAPPER}} span.ha-single-product__discount-text',
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'discount_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} span.ha-single-product__discount-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'discount_box_shadow',
				'selector' => '{{WRAPPER}} span.ha-single-product__discount-text',
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'discount_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} span.ha-single-product__discount-text',
				'condition' => [
					$this->get_control_id( 'discount_text!' ) => '',
				],
			]
		);
	}

	/**
	 * Image Style controls section tab
	 */
	protected function image_style_controls_section() {

		$this->start_controls_section(
			'_section_style_img',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->image_style_controls();

		$this->end_controls_section();
	}

	protected function image_style_controls() {

		$this->add_responsive_control(
			'img_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'img_width',
			[
				'label' => __( 'Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
					'em' => [
						'min' => .5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'img_height',
			[
				'label' => __( 'Height', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
					'em' => [
						'min' => .5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'img_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border',
				'selector' => '{{WRAPPER}} .ha-single-product__img img',
			]
		);

		$this->add_responsive_control(
			'img_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_box_shadow',
				'selector' => '{{WRAPPER}} .ha-single-product__img img',
			]
		);

		$this->start_controls_tabs( '_tabs_img_effects' );

		$this->start_controls_tab(
			'_tab_img_effects_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'img_opacity',
			[
				'label' => __( 'Opacity', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters',
				'selector' => '{{WRAPPER}} .ha-single-product__img img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_img_effects_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'img_hover_opacity',
			[
				'label' => __( 'Opacity', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__item:hover .ha-single-product__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_hover_css_filters',
				'selector' => '{{WRAPPER}} .ha-single-product__item:hover .ha-single-product__img img',
			]
		);

		$this->add_control(
			'img_hover_transition',
			[
				'label' => __( 'Transition Duration', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'default' => [
					'size' => .2
				],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__img img' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}


	/**
	 * Content Style controls section tab
	 */
	protected function content_style_controls_section() {

		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
            'content_padding',
            [
                'label' => __( 'Padding', 'happy-addons-pro' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .ha-single-product__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->rating_style_controls();

		$this->cat_style_controls();

		$this->title_style_controls();

		$this->excerpt_style_controls();

		$this->price_style_controls();


		$this->end_controls_section();
	}

	/**
	 * Ratting Style controls
	 */
	protected function rating_style_controls() {

		$this->add_control(
			'_heading_rating_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Rating', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_rating' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'rating_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__ratings' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_rating' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__ratings' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_rating' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__ratings .star-rating' => 'color: {{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'show_rating' ) => 'yes',
				],
			]
		);
	}

	/**
	 * Category Style controls
	 */
	protected function cat_style_controls() {

		$this->add_control(
			'_heading_cat_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Category', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cat_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cat_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cat_border',
				'label' => __( 'Border', 'happy-addons-pro' ),
				'exclude' => [
					'color'
				],
				'selector' => '{{WRAPPER}} .ha-single-product__category a',
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cat_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cat_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
				'selector' => '{{WRAPPER}} .ha-single-product__category a',
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'cat_tabs',
			[
				'condition' => [
					$this->get_control_id( 'show_cat' ) => 'yes',
				],
			]
		);
		$this->start_controls_tab(
			'cat_normal_tab',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cat_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cat_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-single-product__category a',
			]
		);

		$this->add_control(
			'cat_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'cat_hover_tab',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'cat_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'cat_hover_background',
				'label' => __( 'Background', 'happy-addons-pro' ),
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'image'
				],
				'selector' => '{{WRAPPER}} .ha-single-product__category a:hover',
			]
		);

		$this->add_control(
			'cat_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__category a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	/**
	 * Title Style controls
	 */
	protected function title_style_controls() {

		$this->add_control(
			'_heading_title_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Title', 'happy-addons-pro' ),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-single-product__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__title a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => __( 'HoverColor', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__title:hover a' => 'color: {{VALUE}}',
				],
			]
		);
	}

	/**
	 * Excerpt Style controls
	 */
	protected function excerpt_style_controls() {

		$this->add_control(
			'_heading_excerpt_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Excerpt', 'happy-addons-pro' ),
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__desc' => 'color: {{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'excerpt_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-single-product__desc',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					$this->get_control_id( 'excerpt_length!' ) => '',
				],
			]
		);

	}

	/**
	 * Price Style controls
	 */
	protected function price_style_controls() {

		$this->add_control(
			'_heading_price_style',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Price', 'happy-addons-pro' ),
				'separator' => 'before',
				'condition' => [
					$this->get_control_id( 'show_price' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'price_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_price' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-single-product__price' => 'color: {{VALUE}}',
				],
				'condition' => [
					$this->get_control_id( 'show_price' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-single-product__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					$this->get_control_id( 'show_price' ) => 'yes',
				],
			]
		);
	}

	protected function cart_and_qv_button_style_controls_section() {

		$this->start_controls_section(
			'_section_cart_and_qv_style_buttons',
			[
				'label' => __( 'Cart & Quick View', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => $this->get_control_id( 'show_cart_button' ),
							'operator' => '==',
							'value' => 'yes',
						],
						[
							'name' => $this->get_control_id( 'show_quick_view_button' ),
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->cart_button_style_controls();

		$this->qv_button_style_controls();

		$this->end_controls_section();

	}

	protected function cart_button_style_controls() {

		$this->add_control(
			'_heading_cart_btn',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Cart Button', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'cart_btn_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cart_btn_typography',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'cart_btn_border',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'_tabs_cart_btn_stat',
			[
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				]
			]
		);

		$this->start_controls_tab(
			'_tab_cart_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'color: {{VALUE}};',
					'{{WRAPPER}} .button i' => 'border-right-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cart_btn_box_shadow',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_cart_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .button:hover i' => 'border-right-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'cart_btn_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
					$this->get_control_id( 'cart_btn_border_border!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'cart_btn_hover_box_shadow',
				'selector' => '{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus, {{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus',
				'condition' => [
					$this->get_control_id( 'show_cart_button' ) => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

	}

	protected function qv_button_style_controls() {

		$this->add_control(
			'_heading_qv_button',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __( 'Quick View Button', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'qv_btn_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'qv_btn_typography',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'qv_btn_border',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'_tabs_qv_btn_stat',
			[
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
	 	);

		$this->start_controls_tab(
			'_tab_qv_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'qv_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_qv_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_color',
			[
				'label' => __( 'Text Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
					$this->get_control_id( 'qv_btn_border_border!' ) => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'qv_btn_hove_box_shadow',
				'selector' => '{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus',
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	/**
	 * Quick View Modal Style controls
	 */
	protected function qv_modal_style_controls() {

		$this->start_controls_section(
			'_section_style_qv_modal',
			[
				'label' => __( 'Quick View Modal', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'show_quick_view_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'_heading_qv_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'qv_title_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'qv_title_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'qv_title_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_rating',
			[
				'label' => __( 'Rating', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_rating_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'qv_rating_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_price',
			[
				'label' => __( 'Price', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_price_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'qv_price_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_price_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_summary',
			[
				'label' => __( 'Summary', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_summary_spacing',
			[
				'label' => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'qv_summary_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_summary_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_cart',
			[
				'label' => __( 'Add To Cart', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_cart_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'qv_cart_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'qv_cart_border',
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'qv_cart_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->start_controls_tabs( '_tab_qv_cart_stats' );
		$this->start_controls_tab(
			'_tab_qv_cart_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'qv_cart_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_qv_cart_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'qv_cart_hover_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_bg_color',
			[
				'label' => __( 'Background Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'qv_cart_border_border!' => '',
				]
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * adding woocommerce filter
	 *
	 * @return void
	 */
	public function __add_hooks() {
		add_filter( 'single_product_archive_thumbnail_size', [ $this, '__update_image_size' ] );
		add_filter( 'woocommerce_product_add_to_cart_text', [ $this, '__update_add_to_cart_text' ], 10, 2 );
	}

	/**
	 * removing woocommerce filter
	 *
	 * @return void
	 */
	public function __remove_hooks() {
		remove_filter( 'single_product_archive_thumbnail_size', [ $this, '__update_image_size' ] );
		remove_filter( 'woocommerce_product_add_to_cart_text', [ $this, '__update_add_to_cart_text' ], 10, 2 );
	}

	/**
	 * update woocommerce image size
	 *
	 * @param [string] $size
	 * @return void
	 */
	public function __update_image_size( $size ) {
		$settings = $this->parent->get_settings();
		return $settings['thumbnail_size'];
	}

	/**
	 * update add to cart text function
	 *
	 * @param [string] $text
	 * @param [object] $product
	 * @return void
	 */
	public function __update_add_to_cart_text( $text, $product ) {
		$add_to_cart_text = $this->get_instance_value( 'add_to_cart_text' );

		if ( $product->get_type() === 'simple' && $product->is_purchasable() && $product->is_in_stock() && ! empty( $add_to_cart_text ) ) {
			$text = $add_to_cart_text;
		}

		return $text;
	}

	/**
	 * Get feature image markup
	 *
	 * @return void
	 */
    protected function get_feature_image() {
		$settings = $this->parent->get_settings();
        ?>
			<a href="<?php the_permalink(); ?>" rel="bookmark" class="ha-single-product__feature_img">
				<?php
					if ( $settings['normal_image']['url'] && $settings['normal_image']['id'] ){
						echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'normal_image' );
					}else{
						woocommerce_template_loop_product_thumbnail();
					}
					echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'hover_image' );
				?>
			</a>
		<?php
	}


	/**
	 * print quick view button markup
	 *
	 * @param [init] $product_id
	 * @return void
	 */
	protected function print_quick_view_button( $product_id ) {
		$url = add_query_arg(
			[
				'action'     => 'ha_show_product_quick_view',
				'product_id' => $product_id,
				'nonce'      => wp_create_nonce( 'ha_show_product_quick_view' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		printf(
			'<a href="#" data-mfp-src="%s" class="ha-pqv-btn" title="%s" data-modal-class="ha-pqv--%s"><i class="fas fa-expand-alt"></i></a>',
			esc_url( $url ),
			__( 'Quick View', 'happy-addons-pro' ),
			$this->parent->get_id()
		);
	}

	/**
	 * get badge markup
	 *
	 * @return void
	 */
    protected function get_badge() {
		$badge_text = $this->get_instance_value( 'badge_text' );
		$discount_text = $this->get_instance_value( 'discount_text' );
        ?>
			<?php if ( $badge_text || $discount_text ) : ?>
				<div class="ha-single-product__badge">
					<?php
						if ( $badge_text ) {
							printf( '<span %1$s>%2$s</span>',
								'class="ha-single-product__badge-text"',
								esc_html( $badge_text )
							);
						}
						if ( $discount_text ) {
							printf( '<span %1$s>%2$s</span>',
								'class="ha-single-product__discount-text"',
								esc_html( $discount_text )
							);
						}
					?>
				</div>
			<?php endif; ?>
		<?php
    }

}
