<?php

/**
 * Product grid widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons_Pro\Traits\Lazy_Query_Builder;

defined('ABSPATH') || die();

class Product_Grid_New extends Base {

	use Lazy_Query_Builder;

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @access public
	 *
	 */
	public function get_title() {
		return _x('Product Grid', 'Widget name', 'happy-addons-pro');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-Product-Grid';
	}

	public function get_keywords() {
		return ['woocommerce', 'product', 'grid', 'ha-skin'];
	}

	/**
	 * Overriding default function to add custom html class.
	 *
	 * @return string
	 */
	public function get_html_wrapper_class() {
		$html_class = parent::get_html_wrapper_class();
		$html_class .= ' ' . str_replace('-new', '', $this->get_name());
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
				'label' => __('Layout', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'skin',
			[
				'label' => __('Skin', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'classic' => __('Classic', 'happy-addons-pro'),
					'hover' => __('Hover', 'happy-addons-pro'),
				],
				'default' => 'classic',
				'prefix_class' => 'ha-product-grid--',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label' => __('Columns', 'happy-addons-pro'),
				'type' => Controls_Manager::SELECT,
				'desktop_default' => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'options' => [
					'1' => 1,
					'2' => 2,
					'3' => 3,
					'4' => 4,
					'5' => 5,
					'6' => 6,
				],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-column: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'show_badge',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Badge', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_rating',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Rating', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_price',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Price', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_cart_button',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Cart Button', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'show_quick_view_button',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Quick View Button', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'separator' => 'before',
				'exclude' => ['custom'],
				'default' => 'woocommerce_thumbnail',
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	protected function register_query_controls_section() {
		$this->start_controls_section(
			'_section_query',
			[
				'label' => __('Query', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_controls();

		$this->update_control(
			'posts_post_type',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'product'
			]
		);

		$this->remove_control('posts_selected_ids');

		$this->update_control(
			'posts_include_by',
			[
				'options' => [
					'terms' => __('Terms', 'happy-addons-pro'),
					'featured' => __('Featured Products', 'happy-addons-pro'),
				]
			]
		);

		$this->remove_control('posts_include_author_ids');

		$this->update_control(
			'posts_exclude_by',
			[
				'options' => [
					'current_post'      => __('Current Product', 'happy-addons-pro'),
					'manual_selection'  => __('Manual Selection', 'happy-addons-pro'),
					'terms'             => __('Terms', 'happy-addons-pro'),
				]
			]
		);

		$this->remove_control('posts_exclude_author_ids');

		$this->update_control(
			'posts_include_term_ids',
			[
				'description' => __('Select product categories and tags', 'happy-addons-pro'),
			]
		);

		$this->update_control(
			'posts_exclude_term_ids',
			[
				'description' => __('Select product categories and tags', 'happy-addons-pro'),
			]
		);

		$this->update_control(
			'posts_select_date',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => 'anytime'
			]
		);

		$this->remove_control('posts_date_before');

		$this->remove_control('posts_date_after');

		$this->update_control(
			'posts_orderby',
			[
				'options' => [
					'comment_count' => __('Review Count', 'happy-addons-pro'),
					'date'          => __('Date', 'happy-addons-pro'),
					'ID'            => __('ID', 'happy-addons-pro'),
					'menu_order'    => __('Menu Order', 'happy-addons-pro'),
					'rand'          => __('Random', 'happy-addons-pro'),
					'title'         => __('Title', 'happy-addons-pro'),
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

		$this->remove_control('posts_ignore_sticky_posts');

		$this->update_control(
			'posts_only_with_featured_image',
			[
				'type' => Controls_Manager::HIDDEN,
				'default' => false
			]
		);

		$this->add_control(
			'posts_per_page',
			[
				'label' => __('Number Of Products', 'happy-addons-pro'),
				'description' => __('Only visible products will be shown in the products grid. Hence number of products in the grid may differ from number of products setting.', 'happy-addons-pro'),
				'type' => Controls_Manager::NUMBER,
				'default' => 9,
			]
		);

		$this->end_controls_section();
	}

	protected function register_advance_controls_section() {
		$this->start_controls_section(
			'_section_content_advance',
			[
				'label' => __('Advance', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'add_to_cart_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __('Add To Cart Text', 'happy-addons-pro'),
				'placeholder' => __('Your add to cart text', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'quick_view_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __('Quick View Text', 'happy-addons-pro'),
				'default' => __('Quick View', 'happy-addons-pro'),
				'placeholder' => __('Your quick view text', 'happy-addons-pro'),
				'separator' => 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'show_load_more',
			[
				'type' => Controls_Manager::SWITCHER,
				'label' => __('Show Load More Button', 'happy-addons-pro'),
				'default' => 'yes',
				'return_value' => 'yes',
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'load_more_text',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __('Button Text', 'happy-addons-pro'),
				'default' => __('More products', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_load_more' => 'yes',
				]
			]
		);

		$this->add_control(
			'load_more_link',
			[
				'type' => Controls_Manager::URL,
				'label' => __('Button URL', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_load_more' => 'yes',
				],
				'default' => [
					'url' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#'
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
				'label' => __('Layout', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_layout_grid',
			[
				'label' => __('Grid', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'layout_row_gap',
			[
				'label' => __('Row Gap', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-row-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'layout_column_gap',
			[
				'label' => __('Column Gap', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}}' => '--grid-column-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'_heading_layout_item',
			[
				'label' => __('Product', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'item_align',
			[
				'label' => __('Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__rating' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-product-grid__title' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ha-product-grid__price' => 'text-align: {{VALUE}};',
					'{{WRAPPER}}.ha-product-grid--classic .ha-product-grid__btns' => 'text-align: {{VALUE}};',
				],
				'prefix_class' => 'ha-product-grid--',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%', 'rem'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'item_border',
				'selector' => '{{WRAPPER}} .ha-product-grid__item',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-grid__item',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_background',
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .ha-product-grid__item'
			]
		);

		$this->end_controls_section();
	}

	protected function register_badge_style_controls_section() {
		$this->start_controls_section(
			'_section_style_badge',
			[
				'label' => __('Badge', 'happy-addons-pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __('Offset', 'happy-addons-pro'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_offset_x',
			[
				'label' => __('Left', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_offset_y',
			[
				'label' => __('Top', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_responsive_control(
			'badge_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'badge_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'badge_border',
				'selector' => '{{WRAPPER}} .ha-product-grid__badge',
			]
		);

		$this->add_responsive_control(
			'badge_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-grid__badge',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'badge_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'exclude' => [
					'line_height'
				],
				'default' => [
					'font_size' => ['']
				],
				'selector' => '{{WRAPPER}} .ha-product-grid__badge',
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
				'label' => __('Image', 'happy-addons-pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'img_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'img_height',
			[
				'label' => __('Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
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
					'{{WRAPPER}} .ha-product-grid__img img' => 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'img_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__img img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'img_border',
				'selector' => '{{WRAPPER}} .ha-product-grid__img img',
			]
		);

		$this->add_responsive_control(
			'img_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'img_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-grid__img img',
			]
		);

		$this->start_controls_tabs('_tabs_img_effects');

		$this->start_controls_tab(
			'_tab_img_effects_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'img_opacity',
			[
				'label' => __('Opacity', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_css_filters',
				'selector' => '{{WRAPPER}} .ha-product-grid__img img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_img_effects_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'img_hover_opacity',
			[
				'label' => __('Opacity', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__item:hover .ha-product-grid__img img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'img_hover_css_filters',
				'selector' => '{{WRAPPER}} .ha-product-grid__item:hover .ha-product-grid__img img',
			]
		);

		$this->add_control(
			'img_hover_transition',
			[
				'label' => __('Transition Duration', 'happy-addons-pro'),
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
					'{{WRAPPER}} .ha-product-grid__img img' => 'transition-duration: {{SIZE}}s;',
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
				'label' => __('Content', 'happy-addons-pro'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_rating',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Rating', 'happy-addons-pro'),
			]
		);

		$this->add_responsive_control(
			'rating_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_size',
			[
				'label' => __('Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__rating' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__rating' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'_heading_title',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Title', 'happy-addons-pro'),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-product-grid__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'_heading_price',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Price', 'happy-addons-pro'),
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'price_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__price' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'price_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'price_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '{{WRAPPER}} .ha-product-grid__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_cart_button_style_controls_section() {
		$this->start_controls_section(
			'_section_style_buttons',
			[
				'label' => __('Cart & Quick View', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'btn_offset_toggle',
			[
				'label' => __('Offset', 'happy-addons-pro'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'condition' => [
					'_skin' => 'hover',
				],
			]
		);

		$this->add_control(
			'btns_align',
			[
				'label' => __('Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}}.ha-product-grid--classic .ha-product-grid__btns' => 'text-align: {{VALUE}};',
				],
				'prefix_class' => 'ha-product-grid--btns-',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'btn_offset_x',
			[
				'label' => __('Position X', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'btn_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__btns' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => 'hover',
				],
			]
		);

		$this->add_responsive_control(
			'btn_offset_y',
			[
				'label' => __('Position Y', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'btn_offset_toggle' => 'yes'
				],
				'range' => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__btns' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => 'hover',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'_heading_button_cart',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Cart Button', 'happy-addons-pro'),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'btn_typography',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'btn_border',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('_tabs_btn_stat');

		$this->start_controls_tab(
			'_tab_btn_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'btn_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'btn_box_shadow',
				'selector' => '{{WRAPPER}} .button, {{WRAPPER}} .added_to_cart',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_btn_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'btn_hover_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_hover_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'btn_hover_box_shadow',
				'selector' => '{{WRAPPER}} .button:hover, {{WRAPPER}} .button:focus, {{WRAPPER}} .added_to_cart:hover, {{WRAPPER}} .added_to_cart:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'_heading_button_qv',
			[
				'type' => Controls_Manager::HEADING,
				'label' => __('Quick View Button', 'happy-addons-pro'),
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_btn_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'qv_btn_border',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
			]
		);

		$this->add_control(
			'qv_btn_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('_tabs_qv_btn_stat');

		$this->start_controls_tab(
			'_tab_qv_btn_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'qv_btn_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'qv_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-pqv-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_qv_btn_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'qv_btn_hover_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-pqv-btn:hover, {{WRAPPER}} .ha-pqv-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'qv_btn_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
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
				'name' => 'qv_btn_hove_box_shadow',
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
				'label' => __('Load More Button', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'load_more_btn_align',
			[
				'label' => __('Alignment', 'happy-addons-pro'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'happy-addons-pro'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'load_more_btn_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'load_more_btn_typography',
				'selector' => '{{WRAPPER}} .ha-product-grid__load-more-btn',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'load_more_btn_border',
				'selector' => '{{WRAPPER}} .ha-product-grid__load-more-btn',
			]
		);

		$this->add_control(
			'load_more_btn_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('_tabs_load_more_btn_stat');

		$this->start_controls_tab(
			'_tab_load_more_btn_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'load_more_btn_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_btn_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-grid__load-more-btn',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'_tab_load_more_btn_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'load_more_btn_hover_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-product-grid__load-more-btn:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-product-grid__load-more-btn:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_more_btn_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'btn_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-product-grid__load-more-btn:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'load_more_btn_hove_box_shadow',
				'selector' => '{{WRAPPER}} .ha-product-grid__load-more-btn:hover, {{WRAPPER}} .ha-product-grid__load-more-btn:focus',
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
				'label' => __('Quick View Modal', 'happy-addons-pro'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'_heading_qv_title',
			[
				'label' => __('Title', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'qv_title_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
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
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_control(
			'qv_title_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__title' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_rating',
			[
				'label' => __('Rating', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_rating_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'qv_rating_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__rating' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_price',
			[
				'label' => __('Price', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_price_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
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
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_price_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__price' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_summary',
			[
				'label' => __('Summary', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'qv_summary_spacing',
			[
				'label' => __('Bottom Spacing', 'happy-addons-pro'),
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
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'qv_summary_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__summary' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'_heading_qv_cart',
			[
				'label' => __('Add To Cart', 'happy-addons-pro'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'qv_cart_padding',
			[
				'label' => __('Padding', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'qv_cart_border_radius',
			[
				'label' => __('Border Radius', 'happy-addons-pro'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
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
				'label' => __('Typography', 'happy-addons-pro'),
				'selector' => '.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->start_controls_tabs('_tab_qv_cart_stats');
		$this->start_controls_tab(
			'_tab_qv_cart_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'qv_cart_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
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
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'qv_cart_hover_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'.ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:hover, .ha-pqv.ha-pqv--{{ID}} .ha-pqv__cart .button:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'qv_cart_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
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

	public function get_products_query_args() {
		$settings = $this->get_settings_for_display();
		$args = $this->get_query_args();

		$args['posts_per_page'] = $settings['posts_per_page'];

		if (
			isset($settings['posts_include_by']) &&
			is_array($settings['posts_include_by']) &&
			in_array('featured', $settings['posts_include_by'])
		) {

			$args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			];
		}

		return $args;
	}

	public function get_query() {
		return get_posts($this->get_products_query_args());
	}

	public static function show_wc_missing_alert() {
		if (current_user_can('activate_plugins')) {
			printf(
				'<div %s>%s</div>',
				'style="margin: 1rem;padding: 1rem 1.25rem;border-left: 5px solid #f5c848;color: #856404;background-color: #fff3cd;"',
				__('WooCommerce is missing! Please install and activate WooCommerce.', 'happy-addons-pro')
			);
		}
	}

	public function get_load_more_button() {
		$settings = $this->get_settings_for_display();

		if ($settings['show_load_more'] !== 'yes') {
			return;
		}

		$this->add_link_attributes('load_more', $settings['load_more_link']);
		$this->add_render_attribute('load_more', 'class', 'ha-product-grid__load-more-btn');
		?>
		<div class="ha-product-grid__load-more">
			<a <?php $this->print_render_attribute_string('load_more'); ?>><?php echo esc_html($settings['load_more_text']); ?></a>
		</div>
		<?php
	}

	public function __add_hooks() {
		add_filter('single_product_archive_thumbnail_size', [$this, '__update_image_size']);
		add_filter('woocommerce_product_add_to_cart_text', [$this, '__update_add_to_cart_text'], 10, 2);

		add_filter('woocommerce_loop_add_to_cart_link', [$this, '__update_add_to_cart'], 10, 3);
	}

	public function __remove_hooks() {
		remove_filter('single_product_archive_thumbnail_size', [$this, '__update_image_size']);
		remove_filter('woocommerce_product_add_to_cart_text', [$this, '__update_add_to_cart_text'], 10, 2);

		remove_filter('woocommerce_loop_add_to_cart_link', [$this, '__update_add_to_cart'], 10, 3);
	}

	public function __update_image_size($size) {
		return $this->get_settings_for_display('thumbnail_size');
	}

	public function __update_add_to_cart_text($text, $product) {
		$add_to_cart_text = $this->get_settings_for_display('add_to_cart_text');

		if ($product->get_type() === 'simple' && $product->is_purchasable() && $product->is_in_stock() && !empty($add_to_cart_text)) {
			$text = $add_to_cart_text;
		}

		return $text;
	}

	public function __update_add_to_cart($html, $product, $args) {

		$add_to_cart_text_icons = ('classic' == $this->get_settings_for_display('skin')) ? esc_html($product->add_to_cart_text()) : '<i class="fas fa-shopping-cart"></i><span class="ha-screen-reader-text">' . esc_html($product->add_to_cart_text()) . '</span>';
		return sprintf(
			'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
			esc_url($product->add_to_cart_url()),
			esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
			esc_attr(isset($args['class']) ? $args['class'] : 'button'),
			isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
			$add_to_cart_text_icons
		);
	}

	protected function print_quick_view_button($product_id) {
		$url = add_query_arg(
			[
				'action'     => 'ha_show_product_quick_view',
				'product_id' => $product_id,
				'nonce'      => wp_create_nonce('ha_show_product_quick_view'),
			],
			admin_url('admin-ajax.php')
		);

		$quick_view_text_icons = ('classic' == $this->get_settings_for_display('skin')) ? $this->get_settings_for_display('quick_view_text') : '<i class="fas fa-eye"></i><span class="ha-screen-reader-text">' . $this->get_settings_for_display('quick_view_text') . '</span>';

		printf(
			'<a href="#" data-mfp-src="%s" class="ha-pqv-btn" data-modal-class="ha-pqv--%s">%s</a>',
			esc_url($url),
			$this->get_id(),
			$quick_view_text_icons
		);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if (!function_exists('WC')) {
			$this->show_wc_missing_alert();

			return;
		}

		// Add WC hooks
		$this->__add_hooks();

		$products = (array) $this->get_query();

		global $post;

		foreach ($products as $post) : setup_postdata($post);

			global $product;

			// Ensure visibility.
			if (empty($product) || !$product->is_visible()) {
				continue;
			}
		?>

			<article <?php wc_product_class('ha-product-grid__item', $product); ?>>
				<div role="figure" class="ha-product-grid__img">
					<a href="<?php the_permalink(); ?>" rel="bookmark">
						<?php woocommerce_template_loop_product_thumbnail(); ?>
					</a>

					<?php if ($settings['show_badge'] === 'yes' && $product->is_on_sale()) : ?>
						<div class="ha-product-grid__badge"><?php woocommerce_show_product_loop_sale_flash(); ?></div>
					<?php endif; ?>
					<?php if ($settings['skin'] === 'hover') : ?>
						<?php if ($settings['show_cart_button'] === 'yes' || $settings['show_quick_view_button'] === 'yes') : ?>
							<div class="ha-product-grid__btns">
								<?php

								if ($settings['show_quick_view_button'] === 'yes') :
									$this->print_quick_view_button($product->get_id());
								endif;

								if ($settings['show_cart_button'] === 'yes') :
									woocommerce_template_loop_add_to_cart();
								endif;

								?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>

				<?php if ($settings['show_rating'] === 'yes' && $product->get_average_rating()) : ?>
					<div class="ha-product-grid__rating"><?php woocommerce_template_loop_rating();  ?></div>
				<?php endif; ?>

				<h2 class="ha-product-grid__title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

				<?php if ($settings['show_price'] === 'yes') : ?>
					<div class="ha-product-grid__price"><?php woocommerce_template_loop_price(); ?></div>
				<?php endif; ?>

				<?php if ($settings['skin'] === 'classic') : ?>
					<?php if ($settings['show_cart_button'] === 'yes' || $settings['show_quick_view_button'] === 'yes') : ?>
						<div class="ha-product-grid__btns">
							<?php

							if ($settings['show_cart_button'] === 'yes') :
								woocommerce_template_loop_add_to_cart();
							endif;

							if ($settings['show_quick_view_button'] === 'yes') :
								$this->print_quick_view_button($product->get_id());
							endif;

							?>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</article>

<?php
		endforeach;

		wp_reset_postdata();

		$this->get_load_more_button();

		// Remove WC hooks
		$this->__remove_hooks();
	}
}
