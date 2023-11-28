<?php

namespace Essential_Addons_Elementor\Pro\Elements;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Essential_Addons_Elementor\Traits\Helper;
use Essential_Addons_Elementor\Classes\Helper as HelperClass;

class Woo_Cross_Sells extends Widget_Base {
	use Helper;

	public function get_name() {
		return 'eael-woo-cross-sells';
	}

	public function get_title() {
		return esc_html__( 'Woo Cross Sells', 'essential-addons-elementor' );
	}

	public function get_icon() {
		return 'eaicon-woo-cross-sells';
	}

	public function get_style_depends() {
		return [
			'font-awesome-5-all',
			'font-awesome-4-shim',
		];
	}

	public function get_categories() {
		return [ 'essential-addons-elementor', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [
			'cart',
			'woo cart',
			'cross sells',
			'woo cart cross sells',
			'ea cross sells',
			'woocommerce',
			'woocommerce cross sells',
			'ea',
			'essential addons',
			'cross-sells'
		];
	}

	public function get_custom_help_url() {
		return 'https://essential-addons.com/elementor/docs/ea-woo-cross-sells/';
	}

	protected function register_controls() {
		if ( ! class_exists( 'woocommerce' ) ) {
			$this->start_controls_section(
				'eael_global_warning',
				[
					'label' => __( 'Warning!', 'essential-addons-elementor' ),
				]
			);

			$this->add_control(
				'eael_global_warning_text',
				[
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => __( '<strong>WooCommerce</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=woocommerce&tab=search&type=term" target="_blank">WooCommerce</a> first.',
						'essential-addons-elementor' ),
					'content_classes' => 'eael-warning',
				]
			);

			$this->end_controls_section();

			return;
		}

		/**
		 * General Settings
		 */
		$this->start_controls_section(
			'ea_section_woo_cross_sells_general_settings',
			[
				'label' => esc_html__( 'General', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_dynamic_template_layout',
			[
				'label'   => esc_html__( 'Layout', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => $this->get_template_list_for_dropdown( true ),
			]
		);

		$this->add_responsive_control(
			'eael_cross_sales_column',
			[
				'label'           => esc_html__( 'Columns', 'essential-addons-elementor' ),
				'type'            => Controls_Manager::SELECT,
				'default'         => '4',
				'desktop_default' => '4',
				'tablet_default'  => '3',
				'mobile_default'  => '1',
				'options'         => [
					'1' => esc_html__( '1', 'essential-addons-elementor' ),
					'2' => esc_html__( '2', 'essential-addons-elementor' ),
					'3' => esc_html__( '3', 'essential-addons-elementor' ),
					'4' => esc_html__( '4', 'essential-addons-elementor' ),
					'5' => esc_html__( '5', 'essential-addons-elementor' ),
					'6' => esc_html__( '6', 'essential-addons-elementor' ),
				],
				'prefix_class'    => 'eael-cross-sales-column%s-',
				'condition'       => [
					'eael_dynamic_template_layout' => [ 'style-1', 'style-2' ],
				],
				'selectors'       => [
					'{{WRAPPER}} .eael-cs-products-container' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'        => 'eael_cross_sales_image_size',
				'exclude'     => [ 'custom' ],
				'default'     => 'medium',
				'label_block' => true,
			]
		);

		$this->add_control( 'orderby', [
			'label'   => __( 'Order By', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'none'       => __( 'None', 'essential-addons-elementor' ),
				'title'      => __( 'Title', 'essential-addons-elementor' ),
				'id'         => __( 'ID', 'essential-addons-elementor' ),
				'date'       => __( 'Date', 'essential-addons-elementor' ),
				'modified'   => __( 'Modified', 'essential-addons-elementor' ),
				'menu_order' => __( 'Menu Order', 'essential-addons-elementor' ),
				'price'      => __( 'Price', 'essential-addons-elementor' ),
			],
			'default' => 'none',

		] );

		$this->add_control( 'order', [
			'label'   => __( 'Order', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'asc'  => __( 'Ascending', 'essential-addons-elementor' ),
				'desc' => __( 'Descending', 'essential-addons-elementor' ),
			],
			'default' => 'desc',
		] );

		$this->add_control( 'products_count', [
			'label'   => __( 'Products Count', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 4,
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
		] );

		$this->add_control( 'product_offset', [
			'label'   => __( 'Offset', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 0,
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
		] );

		$this->add_control(
			'eael_cross_sales_custom_size_img',
			[
				'label'        => esc_html__( 'Custom Image Area?', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		$this->add_control(
			'eael_cross_sales_img_render_type',
			[
				'label'     => esc_html__( 'Image Render Type', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fill',
				'options'   => [
					'contain' => esc_html__( 'Contain', 'essential-addons-elementor' ),
					'fill'    => esc_html__( 'Stretched', 'essential-addons-elementor' ),
					'cover'   => esc_html__( 'Cropped', 'essential-addons-elementor' ),
				],
				'selectors' => [
					'{{WRAPPER}} .eael-cs-product-image img' => 'height: 100%; width: 100%; object-fit: {{VALUE}};',
				],
				'condition' => [
					'eael_cross_sales_custom_size_img' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'eael_cross_sales_custom_height',
			[
				'label'      => __( 'Height', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'size' => 100,
					'unit' => '%',
				],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-product-image'         => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-cs-products-container.style-2 .eael-cs-product-image' => 'max-height: calc(100% - 78px);',
				],
				'condition'  => [
					'eael_cross_sales_custom_size_img' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_cross_sales_excerpt_length',
			[
				'label'     => __( 'Excerpt Words', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '40',
				'condition' => [
					'eael_dynamic_template_layout' => 'style-3',
				],
			]
		);

		$this->add_control(
			'eael_cross_sales_excerpt_expanison_indicator',
			[
				'label'       => esc_html__( 'Expansion Indicator', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( '...', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_dynamic_template_layout' => 'style-3',
				],
				'dynamic'     => [
					'active' => true,
				],
				'ai'          => [
					'active' => false,
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_woo_cross_sells_content_visibility_settings',
			[
				'label' => esc_html__( 'Card Components', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_cross_sales_visibility_heading',
			[
				'label'        => esc_html__( 'Heading', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_woo_cross_sales_heading',
			[
				'label'       => esc_html__( 'Heading', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'You may be interested in…', 'essential-addons-elementor' ),
				'ai'          => [
					'active' => false,
				],
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_cross_sales_visibility_title',
			[
				'label'        => esc_html__( 'Title', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_cross_sales_visibility_price',
			[
				'label'        => esc_html__( 'Price', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_cross_sales_visibility_buttons',
			[
				'label'        => esc_html__( 'Buttons', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'eael_cross_sales_visibility_description',
			[
				'label'        => esc_html__( 'Description', 'essential-addons-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Show',
				'label_off'    => 'Hide',
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'eael_dynamic_template_layout' => 'style-3',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'eael_woo_cross_sells_label_settings',
			[
				'label'     => esc_html__( 'Labels', 'essential-addons-elementor' ),
				'condition' => [
					'eael_dynamic_template_layout!' => 'style-2'
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sales_view_product_label',
			[
				'label'       => esc_html__( 'View Product', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'View Product', 'essential-addons-elementor' ),
				'ai'          => [
					'active' => false,
				],
				'dynamic'     => [
					'active' => true,
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sales_add_to_cart_label',
			[
				'label'       => esc_html__( 'Add to Cart', 'essential-addons-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => false,
				'default'     => __( 'Add to Cart', 'essential-addons-elementor' ),
				'ai'          => [
					'active' => false,
				],
				'dynamic'     => [
					'active' => true,
				]
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'ea_section_woo_cross_sells_general_style',
			[
				'label' => esc_html__( 'General', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_cross_sales_column_gap',
			[
				'label'      => __( 'Horizontal Gap', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container:not(.style-3),
					{{WRAPPER}} .eael-cs-products-container.style-3 .eael-cs-single-product' => 'column-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'eael_cross_sales_row_gap',
			[
				'label'      => __( 'Vertical Gap', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container' => 'row-gap: {{SIZE}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Heading', 'essential-addons-elementor' ),
				'separator' => 'before',
				'condition' => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_heading_tag',
			[
				'label'     => __( 'Heading Tag', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h2',
				'options'   => [
					'h1'   => __( 'H1', 'essential-addons-elementor' ),
					'h2'   => __( 'H2', 'essential-addons-elementor' ),
					'h3'   => __( 'H3', 'essential-addons-elementor' ),
					'h4'   => __( 'H4', 'essential-addons-elementor' ),
					'h5'   => __( 'H5', 'essential-addons-elementor' ),
					'h6'   => __( 'H6', 'essential-addons-elementor' ),
					'span' => __( 'Span', 'essential-addons-elementor' ),
					'p'    => __( 'P', 'essential-addons-elementor' ),
					'div'  => __( 'Div', 'essential-addons-elementor' ),
				],
				'condition' => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'eael_woo_cross_sells_heading_align',
			[
				'label'     => esc_html__( 'Alignment', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'    => [
						'title' => esc_html__( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'  => [
						'title' => esc_html__( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'   => [
						'title' => esc_html__( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .eael-woo-cross-sells-heading' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_heading_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-woo-cross-sells-heading' => 'color: {{VALUE}};'
				],
				'condition' => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_cross_sells_heading_typography',
				'selector'  => '{{WRAPPER}} .eael-woo-cross-sells-heading',
				'condition' => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'heading_padding',
			[
				'label'      => __( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-woo-cross-sells-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'heading_margin',
			[
				'label'      => __( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-woo-cross-sells-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'eael_cross_sales_visibility_heading' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_single_item_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Single Item', 'essential-addons-elementor' ),
				'separator' => 'before',
				'condition' => [
					'eael_dynamic_template_layout!' => 'style-1'
				]
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "eael_woo_cross_sells_single_item_border",
			'selector'  => '{{WRAPPER}} .eael-cs-single-product',
			'condition' => [
				'eael_dynamic_template_layout!' => 'style-1'
			]
		] );

		$this->add_control( "eael_woo_cross_sells_single_item_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .eael-cs-single-product' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'eael_dynamic_template_layout!' => 'style-1'
			]
		] );

		$this->add_responsive_control(
			'single_item_padding',
			[
				'label'      => __( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-single-product' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
				'condition'  => [
					'eael_dynamic_template_layout!' => 'style-1'
				]
			]
		);

		$this->add_responsive_control(
			'single_item_margin',
			[
				'label'      => __( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-single-product' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
				'condition'  => [
					'eael_dynamic_template_layout!' => 'style-1'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ea_section_woo_cross_sells_thumbnail_style',
			[
				'label' => esc_html__( 'Thumbnail', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_thumbnail_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eael-cs-product-image',
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "eael_woo_cross_sells_thumnbail_border",
			'selector' => '{{WRAPPER}} .eael-cs-product-image',
		] );

		$this->add_control( "eael_woo_cross_sells_thumnbail_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .eael-cs-product-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_responsive_control(
			'eael_cross_sales_thumbnail_width',
			[
				'label'      => __( 'Thumbnail Width', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'default'    => [
					'size' => 25,
					'unit' => '%',
				],
				'range'      => [
					'%' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container.style-3 .eael-cs-single-product' => 'grid-template-columns: {{SIZE}}% auto;',
				],
				'condition'  => [
					'eael_dynamic_template_layout' => 'style-3'
				]
			]
		);

		$this->add_responsive_control(
			'thumbnail_padding',
			[
				'label'      => __( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-product-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'thumbnail_margin',
			[
				'label'      => __( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-product-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ea_section_woo_cross_sells_product_details_style',
			[
				'label' => esc_html__( 'Product Details', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_product_details_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eael-cs-product-info,
				{{WRAPPER}} .style-2 .eael-cs-single-product',
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "eael_woo_cross_sells_product_details_border",
			'selector' => '{{WRAPPER}} .eael-cs-product-info',
		] );

		$this->add_control( "eael_woo_cross_sells_product_details_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .eael-cs-product-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_control(
			'eael_woo_cross_sells_product_details_divider_color',
			[
				'label'     => esc_html__( 'Divider Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-products-container.style-1 .eael-cs-single-product .eael-cs-product-info .eael-cs-product-buttons'        => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .eael-cs-products-container.style-1 .eael-cs-single-product .eael-cs-product-info .eael-cs-product-buttons::after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'style-1'
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_title_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Title', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_title_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-product-info .eael-cs-product-title,
					{{WRAPPER}} .eael-cs-product-info .eael-cs-product-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_title_typography',
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-product-info .eael-cs-product-title',
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_price_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Price', 'essential-addons-elementor' ),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_price_color',
			[
				'label'     => esc_html__( 'Regular Price Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-product-info .eael-cs-product-price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_price_color_sale',
			[
				'label'     => esc_html__( 'Sale Price Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-product-info .eael-cs-product-price ins' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_price_typography',
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-product-info .eael-cs-product-price',
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_description_heading',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => esc_html__( 'Description', 'essential-addons-elementor' ),
				'separator' => 'before',
				'condition' => [
					'eael_dynamic_template_layout' => 'style-3',
				]
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_description_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-product-info .eael-cs-product-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_dynamic_template_layout' => 'style-3',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_woo_cross_sells_description_typography',
				'selector'  => '{{WRAPPER}} .eael-cs-products-container .eael-cs-product-info .eael-cs-product-excerpt',
				'condition' => [
					'eael_dynamic_template_layout' => 'style-3',
				]
			]
		);

		$this->add_responsive_control(
			'product_details_padding',
			[
				'label'      => __( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-product-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'product_details_margin',
			[
				'label'      => __( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-product-info' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
				'condition'  => [
					'eael_dynamic_template_layout' => 'style-3'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ea_section_woo_cross_sells_button_style',
			[
				'label' => esc_html__( 'Buttons', 'essential-addons-elementor' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'eael_woo_cross_sells_style_tabs'
		);

		$this->start_controls_tab(
			'style_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_button_color',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a,
				{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_button_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_button_typography',
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a,
				{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a i',
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "eael_woo_cross_sells_button_border",
			'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a',
		] );

		$this->add_control( "eael_woo_cross_sells_button_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_button_box_shadow',
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'style_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_woo_cross_sells_button_color_hover',
			[
				'label'     => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover,
				{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_button_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover',
			]
		);

		$this->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "eael_woo_cross_sells_button_border_hover",
			'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover',
		] );

		$this->add_control( "eael_woo_cross_sells_button_border_radius_hover", [
			'label'      => __( 'Border Radius', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		] );

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_woo_cross_sells_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-buttons a:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'buttons_padding',
			[
				'label'      => __( 'Padding', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-info .eael-cs-product-buttons a,
					{{WRAPPER}} .eael-cs-products-container.style-2 .eael-cs-single-product .eael-cs-product-image .eael-cs-product-buttons a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'buttons_margin',
			[
				'label'      => __( 'Margin', 'essential-addons-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eael-cs-products-container .eael-cs-single-product .eael-cs-product-info .eael-cs-product-buttons a,
					{{WRAPPER}} .eael-cs-products-container.style-2 .eael-cs-single-product .eael-cs-product-image .eael-cs-product-buttons a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$settings = $this->get_settings_for_display();
		$orderby  = $settings['orderby'];
		$order    = $settings['order'];
		$offset   = absint( $settings['product_offset'] );
		$limit    = absint( $settings['products_count'] );

		// Handle product query.
		$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );
		$cross_sells = wc_products_array_orderby( $cross_sells, $orderby, $order );
		$cross_sells = $limit > 0 ? array_slice( $cross_sells, $offset, $limit ) : $cross_sells;

		if ( empty( $cross_sells ) ) {
			if ( Plugin::instance()->editor->is_edit_mode() ) {
				printf( '<center>%s</center>', __( 'To view the <strong>Woo Cross Sells</strong>, you must add products to the cart that has cross-selling items with it.', 'essential-addons-elementor' ) );
			}

			return;
		}

		$this->add_render_attribute( 'container', [
			'class' => [
				'eael-cs-products-container',
				$settings['eael_dynamic_template_layout'],
				$settings['eael_cross_sales_custom_size_img'] === 'yes' ? 'eael-custom-image-area' : ''
			]
		] );

		$image_size = $settings['eael_cross_sales_image_size_size'];
		$template   = $this->get_template( $settings['eael_dynamic_template_layout'] );
		$heading    = $settings['eael_woo_cross_sales_heading'];

		if ( ! empty( $heading ) ) {
			printf( '<%1$s class="eael-woo-cross-sells-heading">%2$s</%1$s>', HelperClass::eael_validate_html_tag( $settings['eael_woo_cross_sells_heading_tag'] ), esc_html( $heading ) );
		} ?>

        <div <?php $this->print_render_attribute_string( 'container' ); ?>>
			<?php
			if ( file_exists( $template ) ) {
				foreach ( $cross_sells as $cs_product ) {
					$is_purchasable = $cs_product->is_purchasable() && $cs_product->is_in_stock() && ! $cs_product->is_type( 'variable' );
					$parent_id      = $cs_product->get_parent_id();
					include( $template );
				}
			} else {
				_e( '<p class="eael-no-layout-found">No layout found!</p>', 'essential-addons-elementor' );
			} ?>
        </div>
		<?php
	}
}