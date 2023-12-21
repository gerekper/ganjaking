<?php

/**
 * Product grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;
use Elementor\Utils;

defined( 'ABSPATH' ) || die();

class Edd_Product_Grid extends Base {

	use Lazy_Query_Builder;

	/**
	 * Get widget title.
	 *
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'EDD Product Grid', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-Product-Grid';
	}

	public function get_keywords() {
		return ['edd', 'product', 'grid', 'ha-skin'];
	}

	/**
	 * Overriding default function to add custom html class.
	 *
	 * @return string
	 */
	public function get_html_wrapper_class() {
		$html_class  = parent::get_html_wrapper_class();
		$html_class .= ' ' . str_replace( '-new', '', $this->get_name() );
		return $html_class;
	}

	protected function register_content_controls() {
		$this->register_layout_controls_section();
		$this->register_query_controls_section();
		$this->register_advance_controls_section();
	}

	protected function register_layout_controls_section() {
		$this->start_controls_section(
			'_section_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'skin',
			[
				'label'        => __( 'Skin', 'happy-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => [
					'classic' => __( 'Classic', 'happy-addons-pro' ),
					'hover'   => __( 'Hover', 'happy-addons-pro' ),
				],
				'default'      => 'classic',
				'prefix_class' => 'ha-edd-product-grid--',
				'render_type'  => 'template',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'           => __( 'Columns', 'happy-addons-pro' ),
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => 4,
				'tablet_default'  => 3,
				'mobile_default'  => 2,
				'options'         => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				],
				'selectors'       => [
					'{{WRAPPER}}' => '--grid-column: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_badge',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Badge', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'badge_label',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Badge label', 'happy-addons-pro' ),
				'placeholder' => __( 'Sale!', 'happy-addons-pro' ),
				'default'     => 'Sale!',
				'condition'   => [
					'show_badge' => 'yes',
				],
			]
		);

		// $this->add_control(
		//     'show_rating',
		//     [
		//         'type'           => Controls_Manager::SWITCHER,
		//         'label'          => __( 'Show Rating', 'happy-addons-pro' ),
		//         'default'        => 'yes',
		//         'return_value'   => 'yes',
		//         'style_transfer' => true,
		//     ]
		// );

		$this->add_control(
			'show_price',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Price', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_cart_button',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Cart Button', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		// $this->add_control(
		//     'cart_btn_label',
		//     [
		//         'type'        => Controls_Manager::TEXT,
		//         'label_block' => true,
		//         'label'       => __( 'Button label', 'happy-addons-pro' ),
		//         'placeholder' => __( 'Add to Cart', 'happy-addons-pro' ),
		//         'default'     => 'Add to Cart',
		//         'condition'   => [
		//             'show_cart_button' => 'yes',
		//         ],
		//     ]
		// );

		$this->add_control(
			'show_sale_count',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Sale Count', 'happy-addons-pro' ),
				'default'        => '',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_quick_view_button',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Quick View Button', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'           => 'thumbnail',
				'separator'      => 'before',
				'exclude'        => ['custom'],
				'default'        => 'medium',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_query_controls_section() {
		$this->start_controls_section(
			'_section_query',
			[
				'label' => __( 'Query', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->update_control(
			'posts_post_type',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'download',
			]
		);

		$this->remove_control( 'posts_selected_ids' );

		$this->update_control(
			'posts_include_by',
			[
				'options' => [
					'terms'    => __( 'Terms', 'happy-addons-pro' ),
					'featured' => __( 'Featured Products', 'happy-addons-pro' ),
				],
			]
		);

		$this->remove_control( 'posts_include_author_ids' );

		$this->update_control(
			'posts_exclude_by',
			[
				'options' => [
					'current_post'     => __( 'Current Product', 'happy-addons-pro' ),
					'manual_selection' => __( 'Manual Selection', 'happy-addons-pro' ),
					'terms'            => __( 'Terms', 'happy-addons-pro' ),
				],
			]
		);

		$this->remove_control( 'posts_exclude_author_ids' );

		$this->update_control(
			'posts_include_term_ids',
			[
				'description' => __( 'Select product categories and tags', 'happy-addons-pro' ),
			]
		);

		$this->update_control(
			'posts_exclude_term_ids',
			[
				'description' => __( 'Select product categories and tags', 'happy-addons-pro' ),
			]
		);

		$this->update_control(
			'posts_select_date',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => 'anytime',
			]
		);

		$this->remove_control( 'posts_date_before' );

		$this->remove_control( 'posts_date_after' );

		$this->update_control(
			'posts_orderby',
			[
				'options' => [
					'comment_count' => __( 'Review Count', 'happy-addons-pro' ),
					'date'          => __( 'Date', 'happy-addons-pro' ),
					'ID'            => __( 'ID', 'happy-addons-pro' ),
					'menu_order'    => __( 'Menu Order', 'happy-addons-pro' ),
					'rand'          => __( 'Random', 'happy-addons-pro' ),
					'title'         => __( 'Title', 'happy-addons-pro' ),
				],
				'default' => 'title',
			]
		);

		$this->update_control(
			'posts_order',
			[
				'default' => 'asc',
			]
		);

		$this->remove_control( 'posts_ignore_sticky_posts' );

		$this->update_control(
			'posts_only_with_featured_image',
			[
				'type'    => Controls_Manager::HIDDEN,
				'default' => false,
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label'       => __( 'Number Of Products', 'happy-addons-pro' ),
				'description' => __( 'Only visible products will be shown in the products grid. Hence number of products in the grid may differ from number of products setting.', 'happy-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 8,
			]
		);

		$this->end_controls_section();
	}

	protected function register_advance_controls_section() {
		$this->start_controls_section(
			'_section_content_advance',
			[
				'label' => __( 'Advanced', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'cart_btn_label',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Add To Cart Text', 'happy-addons-pro' ),
				'placeholder' => __( 'Your add to cart text', 'happy-addons-pro' ),
				'default'     => 'Add to Cart',
				'condition'   => [
					'show_cart_button' => 'yes',
				],
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'skin!' => 'hover',
				],
			]
		);

		$this->add_control(
			'quick_view_text',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Quick View Text', 'happy-addons-pro' ),
				'default'     => __( 'Quick View', 'happy-addons-pro' ),
				'placeholder' => __( 'Your quick view text', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'skin!' => 'hover',
				],
			]
		);

		$this->add_control(
			'show_cat',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Category', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Excerpt', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Excerpt Length', 'happy-addons-pro' ),
				'description' => __( 'Leave it blank to hide excerpt.', 'happy-addons-pro' ),
				'separator'   => 'after',
				'min'         => 0,
				'default'     => 15,
				'condition' => [
					'show_excerpt' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_load_more',
			[
				'type'           => Controls_Manager::SWITCHER,
				'label'          => __( 'Show Load More Button', 'happy-addons-pro' ),
				'default'        => 'yes',
				'return_value'   => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'label'       => __( 'Button Text', 'happy-addons-pro' ),
				'default'     => __( 'More products', 'happy-addons-pro' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'show_load_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'load_more_link',
			[
				'type'      => Controls_Manager::URL,
				'label'     => __( 'Button URL', 'happy-addons-pro' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'show_load_more' => 'yes',
				],
				'default'   => [
					'url' => '#',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_controls() {
		$this->register_layout_style_controls_section();
		$this->register_badge_style_controls_section();
		$this->register_image_style_controls_section();
		$this->register_content_style_controls_section();
		$this->register_cart_button_style_controls_section();
		$this->register_load_more_button_style_controls_section();
		$this->register_qv_modal_style_controls();
	}

	protected function register_layout_style_controls_section() {
		$this->start_controls_section(
			'_section_style_layout',
			[
				'label' => __( 'Layout', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_layout_grid',
			[
				'label' => __( 'Grid', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'layout_row_gap',
			[
				'label'      => __( 'Row Gap', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_column_gap',
			[
				'label'      => __( 'Column Gap', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'_heading_layout_item',
			[
				'label'     => __( 'Product', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'item_align',
			[
				'label'        => __( 'Alignment', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'    => [
					// '{{WRAPPER}} .ha-edd-product-grid__rating'                        => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__title'                         => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__price'                         => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid--classic .ha-edd-product-grid__btns' => 'text-align: {{VALUE}};',
				],
				'prefix_class' => 'ha-edd-product-grid--',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%', 'rem'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'item_border',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__item',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_background',
				'types'    => ['classic', 'gradient'],
				'exclude'  => ['image'],
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__item',
			]
		);

		$this->end_controls_section();
	}

	protected function register_badge_style_controls_section() {
		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __( 'Badge', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label'        => __( 'Offset', 'happy-addons-pro' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_offset_x',
			[
				'label'      => __( 'Left', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition'  => [
					'badge_offset_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_offset_y',
			[
				'label'      => __( 'Top', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition'  => [
					'badge_offset_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'exclude'  => [
					'line_height',
				],
				'default'  => [
					'font_size' => [''],
				],
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__badge',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_image_style_controls_section() {
		$this->start_controls_section(
			'_section_style_img',
			[
				'label' => __( 'Image', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'img_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'img_height',
			[
				'label'      => __( 'Height', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'range'      => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
					'em' => [
						'min' => .5,
						'max' => 50,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 300,
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__img img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'img_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__img img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'img_border',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__img img',
			]
		);

		$this->add_responsive_control(
			'img_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'img_box_shadow',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__img img',
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
				'label'     => __( 'Opacity', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'img_css_filters',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__img img',
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
				'label'     => __( 'Opacity', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__item:hover .ha-edd-product-grid__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'img_hover_css_filters',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__item:hover .ha-edd-product-grid__img img',
			]
		);

		$this->add_control(
			'img_hover_transition',
			[
				'label'     => __( 'Transition Duration', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max'  => 3,
						'step' => 0.1,
					],
				],
				'default'   => [
					'size' => .2,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__img img' => 'transition-duration: {{SIZE}}s;',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_content_style_controls_section() {
		$this->start_controls_section(
			'_section_style_content',
			[
				'label' => __( 'Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->cat_style_controls();

		// $this->add_control(
		//     '_heading_rating',
		//     [
		//         'type'  => Controls_Manager::HEADING,
		//         'label' => __( 'Rating', 'happy-addons-pro' ),
		//     ]
		// );

		// $this->add_responsive_control(
		//     'rating_spacing',
		//     [
		//         'label'      => __( 'Bottom Spacing', 'happy-addons-pro' ),
		//         'type'       => Controls_Manager::SLIDER,
		//         'size_units' => ['px', 'em'],
		//         'selectors'  => [
		//             '{{WRAPPER}} .ha-edd-product-grid__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};',
		//         ],
		//     ]
		// );

		// $this->add_responsive_control(
		//     'rating_size',
		//     [
		//         'label'      => __( 'Size', 'happy-addons-pro' ),
		//         'type'       => Controls_Manager::SLIDER,
		//         'size_units' => ['px'],
		//         'selectors'  => [
		//             '{{WRAPPER}} .ha-edd-product-grid__rating' => 'font-size: {{SIZE}}{{UNIT}};',
		//         ],
		//     ]
		// );

		// $this->add_control(
		//     'rating_color',
		//     [
		//         'label'     => __( 'Color', 'happy-addons-pro' ),
		//         'type'      => Controls_Manager::COLOR,
		//         'selectors' => [
		//             '{{WRAPPER}} .ha-edd-product-grid__rating' => 'color: {{VALUE}}',
		//         ],
		//     ]
		// );

		$this->add_control(
			'_heading_title',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Title', 'happy-addons-pro' ),
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => __( 'Text Color Hover', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__title:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'_heading_excerpt',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Excerpt', 'happy-addons-pro' ),
				'separator' => 'before',
				'condition' => [
					'show_excerpt' => 'yes',
			   ],
			]
		);

		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__desc' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_excerpt' => 'yes',
			   ],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__desc' => 'color: {{VALUE}}',
				],
				'condition' => [
					'show_excerpt' => 'yes',
			   ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__desc',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'condition' => [
					'show_excerpt' => 'yes',
			   ],
			]
		);

		$this->add_control(
			'_heading_price',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Price', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'price_spacing',
			[
				'label'      => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();
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
				'separator' => 'after',
				'condition' => [
					 'show_cat' => 'yes',
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
					'{{WRAPPER}} .ha-edd-product-grid__category' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					 'show_cat' => 'yes',
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
					'{{WRAPPER}} .ha-edd-product-grid__category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					 'show_cat' => 'yes',
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
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__category a',
				'condition' => [
					 'show_cat' => 'yes',
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
					'{{WRAPPER}} .ha-edd-product-grid__category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					 'show_cat' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cat_typography',
				'label' => __( 'Typography', 'happy-addons-pro' ),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__category a',
				'condition' => [
					 'show_cat' => 'yes',
				],
			]
		);

		$this->start_controls_tabs(
			'cat_tabs',
			[
				'condition' => [
					 'show_cat' => 'yes',
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
					'{{WRAPPER}} .ha-edd-product-grid__category a' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__category a',
			]
		);

		$this->add_control(
			'cat_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__category a' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .ha-edd-product-grid__category a:hover' => 'color: {{VALUE}}',
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
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__category a:hover',
			]
		);

		$this->add_control(
			'cat_hover_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__category a:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	protected function register_cart_button_style_controls_section() {
		$this->start_controls_section(
			'_section_style_buttons',
			[
				'label' => __( 'Cart & Quick View', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_offset_toggle',
			[
				'label'        => __( 'Offset', 'happy-addons-pro' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition'    => [
					'_skin' => 'hover',
				],
			]
		);

		$this->add_control(
			'btns_align',
			[
				'label'        => __( 'Alignment', 'happy-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'    => [
					'{{WRAPPER}} .ha-edd-product-grid--classic .ha-edd-product-grid__btns' => 'text-align: {{VALUE}};',
				],
				'prefix_class' => 'ha-edd-product-grid--btns-',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'btn_offset_x',
			[
				'label'      => __( 'Position X', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition'  => [
					'btn_offset_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__btns' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'_skin' => 'hover',
				],
			]
		);

		$this->add_responsive_control(
			'btn_offset_y',
			[
				'label'      => __( 'Position Y', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition'  => [
					'btn_offset_toggle' => 'yes',
				],
				'range'      => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__btns' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'_skin' => 'hover',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'_heading_button_cart',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Cart Button', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'btn_typography',
				'selector' => '
				{{WRAPPER}} .button,
				{{WRAPPER}} .added_to_cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_border',
				'selector' => '
				{{WRAPPER}} .button,
				{{WRAPPER}} .added_to_cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue',
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_btn_stat' );

		$this->start_controls_tab(
			'_tab_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_box_shadow',
				'selector' => '
				{{WRAPPER}} .button,
				{{WRAPPER}} .added_to_cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'btn_hover_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus'               => 'color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus'               => 'background: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:focus' => 'background: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:focus' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus'               => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:hover, {{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_hover_box_shadow',
				'selector' => '{{WRAPPER}} .button:hover,
				{{WRAPPER}} .button:focus,
				{{WRAPPER}} .added_to_cart:hover,
				{{WRAPPER}} .added_to_cart:focus,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-add-to-cart:hover,
				{{WRAPPER}} .ha-edd-product-grid__btns .edd-submit.button.blue:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'_heading_button_qv',
			[
				'type'      => Controls_Manager::HEADING,
				'label'     => __( 'Quick View Button', 'happy-addons-pro' ),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_btn_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-pqv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qv_btn_typography',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'qv_btn_border',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
			]
		);

		$this->add_control(
			'qv_btn_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-pqv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_qv_btn_stat' );

		$this->start_controls_tab(
			'_tab_qv_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'qv_btn_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qv_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_qv_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'qv_btn_hover_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'qv_btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'qv_btn_hove_box_shadow',
				'selector' => '{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_load_more_button_style_controls_section() {
		$this->start_controls_section(
			'_section_style_load_more_button',
			[
				'label' => __( 'Load More Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'load_more_btn_align',
			[
				'label'     => __( 'Alignment', 'happy-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'happy-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_btn_spacing',
			[
				'label'      => __( 'Top Spacing', 'happy-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_btn_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'load_more_btn_typography',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__load-more-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'load_more_btn_border',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__load-more-btn',
			]
		);

		$this->add_control(
			'load_more_btn_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( '_tabs_load_more_btn_stat' );

		$this->start_controls_tab(
			'_tab_load_more_btn_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'load_more_btn_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'load_more_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__load-more-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_load_more_btn_hover',
			[
				'label' => __( 'Hover', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'load_more_btn_hover_color',
			[
				'label'     => __( 'Text Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-edd-product-grid__load-more-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-edd-product-grid__load-more-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-edd-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-edd-product-grid__load-more-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'load_more_btn_hove_box_shadow',
				'selector' => '{{WRAPPER}} .ha-edd-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-edd-product-grid__load-more-btn:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_qv_modal_style_controls() {
		$this->start_controls_section(
			'_section_style_qv_modal',
			[
				'label' => __( 'Quick View Modal', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_qv_title',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'qv_title_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qv_title_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'qv_title_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__title' => 'color: {{VALUE}};',
				],
			]
		);

		// $this->add_control(
		//     '_heading_qv_rating',
		//     [
		//         'label'     => __( 'Rating', 'happy-addons-pro' ),
		//         'type'      => Controls_Manager::HEADING,
		//         'separator' => 'before',
		//     ]
		// );

		// $this->add_responsive_control(
		//     'qv_rating_spacing',
		//     [
		//         'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
		//         'type'      => Controls_Manager::SLIDER,
		//         'selectors' => [
		//             '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};',
		//         ],
		//     ]
		// );

		// $this->add_control(
		//     'qv_rating_color',
		//     [
		//         'label'     => __( 'Color', 'happy-addons-pro' ),
		//         'type'      => Controls_Manager::COLOR,
		//         'selectors' => [
		//             '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__rating' => 'color: {{VALUE}};',
		//         ],
		//     ]
		// );

		$this->add_control(
			'_heading_qv_price',
			[
				'label'     => __( 'Price', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_price_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qv_price_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_price_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_qv_summary',
			[
				'label'     => __( 'Summary', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_summary_spacing',
			[
				'label'     => __( 'Bottom Spacing', 'happy-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__summary' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qv_summary_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__summary',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_summary_color',
			[
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__summary' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'_heading_qv_cart',
			[
				'label'     => __( 'Add To Cart', 'happy-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_cart_padding',
			[
				'label'      => __( 'Padding', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'qv_cart_border_radius',
			[
				'label'      => __( 'Border Radius', 'happy-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'qv_cart_border',
				'selector' => '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'qv_cart_typography',
				'label'    => __( 'Typography', 'happy-addons-pro' ),
				'selector' => '.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button',
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
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button' => 'background-color: {{VALUE}}',
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
				'label'     => __( 'Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:hover, .ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_bg_color',
			[
				'label'     => __( 'Background Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:hover, .ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_border_color',
			[
				'label'     => __( 'Border Color', 'happy-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:hover, .ha-pqv-edd.ha-pqv-edd--{{ID}} .ha-pqv-edd__cart .button:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'qv_cart_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * @return mixed
	 */
	public function get_products_query_args() {
		$settings = $this->get_settings_for_display();
		$args     = $this->get_query_args();

		$args['posts_per_page'] = $settings['posts_per_page'];

		// if (
		// isset($settings['posts_include_by']) &&
		// is_array($settings['posts_include_by']) &&
		// in_array('featured', $settings['posts_include_by'])
		// ) {

		// $args['tax_query'][] = [
		// 'taxonomy' => 'product_visibility',
		// 'field'    => 'name',
		// 'terms'    => 'featured',
		// 'operator' => 'IN',
		// ];
		// }

		return $args;
	}

	public function get_query() {
		return get_posts( $this->get_products_query_args() );
	}

	public static function show_edd_missing_alert() {
		if ( current_user_can( 'activate_plugins' ) ) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__( 'Easy Digital Downloads is missing! Please install and activate Easy Digital Downloads.', 'happy-addons-pro' )
			);
		}
	}

	/**
	 * @return null
	 */
	public function get_load_more_button() {
		$settings = $this->get_settings_for_display();

		if ( $settings['show_load_more'] !== 'yes' ) {
			return;
		}

		$this->add_link_attributes( 'load_more', $settings['load_more_link'] );
		$this->add_render_attribute( 'load_more', 'class', 'ha-edd-product-grid__load-more-btn' );
		?>
		<div class="ha-edd-product-grid__load-more">
			<a <?php $this->print_render_attribute_string( 'load_more' ); ?>><?php echo esc_html( $settings['load_more_text'] ); ?></a>
		</div>
		<?php
	}

	public function __add_hooks() {
		add_filter( 'edd_purchase_link_defaults', [ $this, 'hide_button_prices'] );
		add_filter( 'edd_purchase_download_form', [ $this, 'pw_edd_remove_free' ], 10, 2 );

	}

	public function __remove_hooks() {

	}

	public function pw_edd_remove_free( $form, $args ) {

		// $form = str_replace( 'Free&nbsp;&ndash;&nbsp;Add to Cart', 'Add to Cart' , $form );
		$form = preg_replace( '/Free/i', '', $form );

		return $form;
	}

	/**
	 * @param  $args
	 * @return mixed
	 */
	public function hide_button_prices( $args ) {
		$args['price'] = (bool) false;

		return $args;
	}

	public function print_custom_add_to_cart_button( $product_id ) {
		$url = add_query_arg(
			[
				'edd_action'  => 'add_to_cart',
				'download_id' => $product_id,
			]
		);

		printf(
			'<a href="%s" class="button"><i class="fas fa-shopping-cart"></i></a>',
			$url
		);
	}

	public function ha_edd_ajax_add_to_cart_link( $product_id ) {
		$url = add_query_arg(
			[
				'action'  		=> 'ha_edd_ajax_add_to_cart_link',
				'download_id' => $product_id,
				'nonce'       => wp_create_nonce( 'ha_edd_ajax_add_to_cart_link' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		printf(
			'<a href="%s" data-download-id="%s" nonce="%s" class="button ha_edd_ajax_btn"><i class="fas fa-shopping-cart"></i></a>',
			'#',
			$product_id,
			wp_create_nonce( 'ha_edd_ajax_add_to_cart_link' )
		);
	}

	/**
	 * @param $product_id
	 */
	protected function print_quick_view_button( $product_id ) {
		$url = add_query_arg(
			[
				'action'      => 'ha_show_edd_product_quick_view',
				'download_id' => $product_id,
				'nonce'       => wp_create_nonce( 'ha_show_edd_product_quick_view' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		$quick_view_text_icons = ( 'classic' == $this->get_settings_for_display( 'skin' ) ) ? $this->get_settings_for_display( 'quick_view_text' ) : '<i class="fas fa-eye"></i><span class="ha-screen-reader-text">' . $this->get_settings_for_display( 'quick_view_text' ) . '</span>';

		printf(
			'<a href="#" data-mfp-src="%s" class="ha-pqv-btn" data-modal-class="ha-pqv-edd--%s">%s</a>',
			esc_url( $url ),
			$this->get_id(),
			$quick_view_text_icons
		);
	}

	/**
	 * @return null
	 */
	public function render() {
		$settings = $this->get_settings_for_display();

		if ( ! function_exists( 'EDD' ) ) {
			$this->show_edd_missing_alert();

			return;
		}

		// Add WC hooks
		$this->__add_hooks();
		echo '<div class="ha-edd-product-grid__wrapper">';
		$downloads = (array) $this->get_query();

		global $post;

		foreach ( $downloads as $post ) :
			setup_postdata( $post );

			$img_src = '';

			if ( get_post_thumbnail_id( $post ) ) {
				$img_src = Group_Control_Image_Size::get_attachment_image_src( get_post_thumbnail_id( $post ), 'thumbnail', $settings );
			} else {
				$img_src = Utils::get_placeholder_image_src();
			}

			// global $product;

			// Ensure visibility.
			// if (empty($product) || !$product->is_visible()) {
			// continue;
			// }

			?>

				<article class="ha-edd-product-grid__item">
					<div role="figure" class="ha-edd-product-grid__img">
						<a href="<?php the_permalink(); ?>" rel="bookmark">
							<img src="<?php echo esc_url( $img_src ); ?>" alt="<?php the_title(); ?>" />
						</a>
						<?php if ( $settings['show_badge'] === 'yes' ) : ?>
							<div class="ha-edd-product-grid__badge"><?php echo esc_html( $settings['badge_label'] ); ?></div>
						<?php endif; ?>
					<?php if ( $settings['skin'] === 'hover' ) : ?>
						<?php if ( $settings['show_cart_button'] === 'yes' || $settings['show_quick_view_button'] === 'yes' ) : ?>
							<div class="ha-edd-product-grid__btns">
								<?php

								if ( $settings['show_quick_view_button'] === 'yes' ) :
									$this->print_quick_view_button( $post->ID );
								endif;

								if ( $settings['show_cart_button'] === 'yes' ) :
									if ( edd_has_variable_prices( $post->ID ) ) {
										printf('<a href="%s" class="button">%s</a>',
											esc_url( get_the_permalink( $post->ID ) ),
											'<i class="fas fa-list-ul"></i>'
										);
									} else {
										// echo edd_get_purchase_link( [ 'download_id' => $post->ID, 'text' => 'ADD' ] );
										$this->ha_edd_ajax_add_to_cart_link( $post->ID );
									}
								endif;

								?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<?php if ( $settings[ 'show_cat' ] === 'yes' ) : ?>
					<div class="ha-edd-product-grid__category">
						<?php echo ha_pro_the_first_taxonomy( $post->ID, 'download_category', ['class'=>'ha-edd-product-grid__category_inner'] ); ?>
					</div>
				<?php endif; ?>
				<h2 class="ha-edd-product-grid__title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<?php if ( $settings[ 'show_excerpt' ] == 'yes' && !empty( $settings[ 'excerpt_length' ] ) ) : ?>
					<p class="ha-edd-product-grid__desc">
						<?php echo ha_pro_get_excerpt( $post->ID, $settings[ 'excerpt_length' ] ); ?>
					</p>
				<?php endif; ?>
				<?php if ( $settings['show_price'] === 'yes' ) : ?>
					<div class="ha-edd-product-grid__price"><?php edd_price( $post->ID ); ?></div>
				<?php endif; ?>


				<?php if ( $settings['skin'] === 'classic' ) : ?>
					<?php if ( $settings['show_cart_button'] === 'yes' || $settings['show_quick_view_button'] === 'yes' ) : ?>
						<div class="ha-edd-product-grid__btns">
							<?php

							if ( $settings['show_cart_button'] === 'yes' ) :
								if ( edd_has_variable_prices( $post->ID ) ) {
									printf('<a href="%s" class="button">%s</a>',
										esc_url( get_the_permalink( $post->ID ) ),
										__( 'Select Options', 'happy-addons-pro' )
									);
								} else {
									echo edd_get_purchase_link( [ 'download_id' => $post->ID, 'text' => $settings['cart_btn_label'] ] );

								}

								endif;

							if ( $settings['show_quick_view_button'] === 'yes' ) :
								$this->print_quick_view_button( $post->ID );
								endif;

							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</article>

			<?php
		endforeach;

		wp_reset_postdata();
		echo '</div>';
		$this->get_load_more_button();

	}
}
