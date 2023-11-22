<?php

namespace ElementPack\Modules\WcProducts\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use ElementPack\Utils;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Modules\WcProducts\Skins;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class WC_Products extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	private $_query = null;
	public function get_name() {
		return 'bdt-wc-products';
	}

	public function get_title() {
		return BDTEP . esc_html__('WC - Products', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-wc-products';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['product', 'woocommerce', 'table', 'wc'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-wc-products', 'datatables-uikit', 'datatables', 'ep-font'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-woocommerce', 'datatables', 'datatables-uikit', 'ep-wc-products', 'ep-scripts'];
		} else {
			return ['ep-woocommerce', 'datatables', 'datatables-uikit', 'ep-wc-products'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/3VkvEpVaNAM';
	}

	public function get_query() {
		return $this->_query;
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Table($this));
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_woocommerce_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'		 => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-products-wrapper.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-products-wrapper.bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'row_gap',
			[
				'label'   => esc_html__('Row Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-products-wrapper.bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-products-wrapper.bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_responsive_control(
			'alignment',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner' => 'text-align: {{VALUE}}',
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .star-rating' => 'text-align: {{VALUE}}; display: inline-block !important',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'hide_header',
			[
				'label'   => esc_html__('Hide Header', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'return'  => 'yes',
				'condition'    => [
					'_skin'      => 'bdt-table',
				],

			]
		);

		$this->add_control(
			'table_header_alignment',
			[
				'label'   => esc_html__('Header Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table th' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'_skin!' => '',
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'table_data_alignment',
			[
				'label'   => esc_html__('Data Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table td' => 'text-align: {{VALUE}}',
				],
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'label'     => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'   => ['custom'],
				'default'   => 'medium',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => esc_html__('Show Filter', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition'    => [
					'_skin'      => '',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'active_hash',
			[
				'label'       => esc_html__('Hash Location', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'no',
				'condition' => [
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'hash_top_offset',
			[
				'label'     => esc_html__('Top Offset ', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => ['px', ''],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 5,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'condition' => [
					'active_hash' => 'yes',
					'show_filter_bar' => 'yes',
				],
			]
		);

		$this->add_control(
			'hash_scrollspy_time',
			[
				'label'     => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => ['ms', ''],
				'range' => [
					'px' => [
						'min' => 500,
						'max' => 5000,
						'step' => 1000,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition' => [
					'active_hash' => 'yes',
					'show_filter_bar' => 'yes',
				],
			]
		);
		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Show Pagination', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'wc_products_enable_ajax_loadmore',
			[
				'label' => esc_html__('Ajax Load More', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_pagination!' => 'yes',
					'_skin' => '',
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'wc_products_enable_ajax_loadmore_items',
			[
				'label' => esc_html__('Load More Items', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::NUMBER,
				'default' => 4,
				'condition' => [
					'show_pagination!' => 'yes',
					'wc_products_enable_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				// 'frontend_available' => true,
			]
		);
		$this->add_control(
			'wc_products_show_loadmore',
			[
				'label' => esc_html__('Load More Button', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_pagination!' => 'yes',
					'wc_products_enable_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'wc_products_show_infinite_scroll',
			[
				'label' => esc_html__('Infinite Scroll', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'show_pagination!' => 'yes',
					'wc_products_show_loadmore!' => 'yes',
					'wc_products_enable_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'masonry',
			[
				'label'       => esc_html__('Masonry', 'bdthemes-element-pack'),
				'description' => esc_html__('Masonry will not work if you not set filter.', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => [
					'columns!' => '1',
					'_skin'		=> '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'show_info',
			[
				'label'   => esc_html__('Footer Info', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition'    => [
					'_skin'           => 'bdt-table',
					'show_pagination' => 'yes',
				],
			]
		);

		$this->end_controls_section();
		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack') . BDTEP_NC,
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->register_query_builder_controls();
		$this->register_wc_query_additional('8');
		$this->end_controls_section();


		$this->start_controls_section(
			'section_woocommerce_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'match_height',
			[
				'label' => __('Item Match Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_badge',
			[
				'label'     => esc_html__('Show Badge', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'show_change_length',
			[
				'label'   => esc_html__('Show Change Length', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'_skin'           => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'show_searching',
			[
				'label'   => esc_html__('Search', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'_skin'           => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'show_ordering',
			[
				'label'   => esc_html__('Ordering', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'after',
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'show_thumb',
			[
				'label'   => esc_html__('Show Thumbnail', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'_skin'      => 'bdt-table',
				],
			]
		);

		$this->add_control(
			'open_thumb_in_lightbox',
			[
				'label'      => esc_html__('Open Thumb in Lightbox', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'show_thumb',
							'value'    => 'yes',
						],
						[
							'name'  => '_skin',
							'value' => 'bdt-table',
						],
					],
				],
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => element_pack_title_tags(),
				'condition' => [
					'show_title' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_control(
			'excerpt_limit',
			[
				'label'      => esc_html__('Excerpt Limit', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 10,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'show_excerpt',
							'value' => 'yes',
						],
						[
							'name'     => '_skin',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'   => esc_html__('Rating', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_price',
			[
				'label'   => esc_html__('Price', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_categories',
			[
				'label'     => esc_html__('Categories', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin' => 'bdt-table',
				],
			]
		);


		$this->add_control(
			'show_tags',
			[
				'label'     => esc_html__('Tags', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin' => 'bdt-table',
				],
			]
		);

		$this->add_control(
			'show_quantity',
			[
				'label'   => esc_html__('Quantity', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin' => 'bdt-table',
				],
			]
		);

		$this->add_control(
			'show_cart',
			[
				'label'   => esc_html__('Add to Cart', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'cart_hide_mobile',
			[
				'label'   => esc_html__('Cart Hide On Mobile ?', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_cart' => 'yes',
					'_skin!' => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'show_quick_view',
			[
				'label'   => esc_html__('Quick View', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'quick_view_hide_mobile',
			[
				'label'   => esc_html__('Quick View Hide On Mobile ?', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_quick_view' => 'yes',
					'_skin!' => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'thumb_hide_on_mobile',
			[
				'label'        => esc_html__('Thumb Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-thumb-hide-on-mobile-',
				'condition'    => [
					'show_thumb' => 'yes',
					'_skin'      => 'bdt-table',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_hide_on_mobile',
			[
				'label'        => esc_html__('Title Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-title-hide-on-mobile-',
				'condition'    => [
					'show_title' => 'yes',
					'_skin'      => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'excerpt_hide_on_mobile',
			[
				'label'        => esc_html__('Description Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-excerpt-hide-on-mobile-',
				'condition'    => [
					'show_excerpt' => 'yes',
					'_skin'        => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'price_hide_on_mobile',
			[
				'label'        => esc_html__('Price Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-price-hide-on-mobile-',
				'condition'    => [
					'show_price' => 'yes',
					'_skin'      => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'categories_hide_on_mobile',
			[
				'label'        => esc_html__('Categories Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-categories-hide-on-mobile-',
				'condition'    => [
					'show_categories' => 'yes',
					'_skin'           => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'tags_hide_on_mobile',
			[
				'label'        => esc_html__('Tags Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-tags-hide-on-mobile-',
				'condition'    => [
					'show_tags' => 'yes',
					'_skin'     => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'rating_hide_on_mobile',
			[
				'label'        => esc_html__('Rating Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-rating-hide-on-mobile-',
				'condition'    => [
					'show_rating' => 'yes',
					'_skin'       => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'cart_hide_on_mobile',
			[
				'label'        => esc_html__('Cart Hide on mobile ?', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-cart-hide-on-mobile-',
				'condition'    => [
					'show_cart' => 'yes',
					'_skin'     => 'bdt-table',
				]
			]
		);


		$this->add_control(
			'grid_animation_type',
			[
				'label'   => esc_html__('Grid Entrance Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => element_pack_transition_options(),
				'separator' => 'before',
				'condition'    => [
					'_skin!'     => 'bdt-table',
				]
			]
		);

		$this->add_control(
			'grid_anim_delay',
			[
				'label'      => esc_html__('Animation delay', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['ms', ''],
				'range'      => [
					'ms' => [
						'min'  => 0,
						'max'  => 1000,
						'step' => 5,
					],
				],
				'default'    => [
					'unit' => 'ms',
					'size' => 300,
				],
				'condition' => [
					'grid_animation_type!' => '',
				],
			]
		);




		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_item',
			[
				'label'     => esc_html__('Item', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => esc_html__('Border Color', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#eee',
					],
				],
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner',
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => esc_html__('Item Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'desc_padding',
			[
				'label'      => esc_html__('Description Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'item_hover_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-inner:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_table',
			[
				'label'     => esc_html__('Table', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->start_controls_tabs('tabs_table_style');

		$this->start_controls_tab(
			'tab_table_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'table_header_typography',
				'label'    => esc_html__('Header Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-products table th',
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'table_heading_background',
			[
				'label'     => esc_html__('Heading Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table th' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'table_heading_color',
			[
				'label'     => esc_html__('Heading Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table th' => 'color: {{VALUE}};',
				],
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'cell_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table td'                  => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-wc-products table th'                  => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-wc-products table.dataTable.no-footer' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'cell_border' => 'yes',
				],
			]
		);

		$this->add_control(
			'table_odd_row_background',
			[
				'label'     => esc_html__('Odd Row Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table.dataTable.stripe tbody tr.odd' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'stripe' => 'yes',
				],
			]
		);

		$this->add_control(
			'table_even_row_background',
			[
				'label'     => esc_html__('Even Row Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table.dataTable.stripe tbody tr.even' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'stripe' => 'yes',
				],
			]
		);

		$this->add_control(
			'cell_border',
			[
				'label'     => esc_html__('Cell Border', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_control(
			'stripe',
			[
				'label'     => esc_html__('stripe', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label'     => esc_html__('Hover Effect', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'table_cell_padding',
			[
				'label'      => esc_html__('Cell Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products table.bdt-wc-product td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sorting_style',
			[
				'label'     => esc_html__('Sorting Style', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->add_control(
			'sorting_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products.bdt-wc-products-skin-table table.dataTable thead th:before, {{WRAPPER}} .bdt-wc-products.bdt-wc-products-skin-table table.dataTable thead th:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'hide_header!' => 'yes'
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_table_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'table_odd_row_hover_background',
			[
				'label'     => esc_html__('Odd Row Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products table.dataTable.stripe tbody tr:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'stripe' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_search_field_style',
			[
				'label' => esc_html__('Search Field', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_searching' => 'yes',
					'_skin'           => 'bdt-table',
				],
			]
		);

		$this->start_controls_tabs('tabs_search_field_style');

		$this->start_controls_tab(
			'tab_search_field_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'search_field_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'search_field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-products input[type*="search"], {{WRAPPER}} .bdt-wc-products select',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'search_field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_control(
			'search_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .dataTables_filter label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'search_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'search_text_typography',
				'label'     => esc_html__('Text Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-wc-products .dataTables_filter label',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_search_field_focus',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'search_field_focus_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]:focus' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_field_focus_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_field_focus_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'search_field_focus_border_width',
			[
				'label'   => __('Border Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]:focus' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'search_field_focus_border_radius',
			[
				'label'   => __('Border Radius', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products input[type*="search"]:focus' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_select_field_style',
			[
				'label'     => esc_html__('Select Field', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes',
					'_skin'           => 'bdt-table',
				],
			]
		);

		$this->start_controls_tabs('tabs_select_field_style');

		$this->start_controls_tab(
			'tab_select_field_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'select_field_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products select'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'select_field_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'select_field_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-products select',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'select_field_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'select_field_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'select_text_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'select_field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .dataTables_length label' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'select_text_typography',
				'label'     => esc_html__('Text Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-wc-products .dataTables_length label',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_select_field_focus',
			[
				'label' => esc_html__('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'select_field_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'select_field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products select:focus'   => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'label'    => esc_html__('Image Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product-image',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'image_shadow',
				'exclude' => [
					'shadow_position',
				],
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product-image',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_title_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_excerpt',
			[
				'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product-excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .star-rating:before' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .star-rating span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .star-rating span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_price',
			[
				'label'     => esc_html__('Price', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'regular_price',
			[
				'label'     => __('Regular Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'old_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-price del .amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'old_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-price del' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'old_price_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-price del .amount',
			]
		);

		$this->add_control(
			'sale_price_heading',
			[
				'label'     => esc_html__('Sale Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sale_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-price .amount,
					{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-wc-product-price ins .amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'sale_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product-price, {{WRAPPER}} .bdt-wc-products .bdt-wc-product-price ins' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sale_price_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product-price,
				{{WRAPPER}} .bdt-wc-products .bdt-wc-product-price ins .amount,
				{{WRAPPER}} .bdt-wc-products .bdt-wc-product-price .amount',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cart' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_fullwidth',
			[
				'label'     => esc_html__('Fullwidth Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'width: 100%;',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'      => esc_html__('Width(%)', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a' => 'width: {{SIZE}}%;'
				],
				'condition' => [
					'button_fullwidth' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-add-to-cart a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_quick_view',
			[
				'label'     => esc_html__('Quick View Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quick_view' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_quick_view_style');

		$this->start_controls_tab(
			'tab_quick_view_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quick_view_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-products .bdt-quick-view a',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'quick_view_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quick_view_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-quick-view a',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'quick_view_typography',
				'selector'  => '{{WRAPPER}} .bdt-wc-products .bdt-quick-view a i',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_quick_view_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a:hover i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'quick_view_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-quick-view a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_quick_view_modal',
			[
				'label'     => esc_html__('Quick View Modal', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_quick_view' => 'yes',
				],
			]
		);


		$this->add_control(
			'quick_view_modal_body_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view.bdt-modal-container .bdt-modal-dialog' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_title_heading',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .product_title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_title_spacing',
			[
				'label'      => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'.bdt-product-quick-view .product .product_title' => 'padding-bottom: {{SIZE}}px;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_title_typography',
				'selector' => '.bdt-product-quick-view .product .product_title',
			]
		);

		$this->add_control(
			'quick_view_modal_excerpt_heading',
			[
				'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .woocommerce-product-details__short-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_excerpt_spacing',
			[
				'label'      => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'.bdt-product-quick-view .product .woocommerce-product-details__short-description' => 'padding-bottom: {{SIZE}}px;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_excerpt_typography',
				'selector' => '.bdt-product-quick-view .product .woocommerce-product-details__short-description',
			]
		);

		$this->add_control(
			'quick_view_modal_rating_heading',
			[
				'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'.bdt-product-quick-view .product .star-rating:before' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'quick_view_modal_active_rating_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'.bdt-product-quick-view .product .star-rating span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_rating_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .woocommerce-product-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_price_heading',
			[
				'label'     => esc_html__('Old Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_old_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product del .amount' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_old_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .product del' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_old_price_typography',
				'selector' => '.bdt-product-quick-view .product del',
			]
		);

		$this->add_control(
			'quick_view_modal_sale_price_heading',
			[
				'label'     => esc_html__('Sale Price', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_sale_price_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product ins .amount, .bdt-product-quick-view .product .price' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_sale_price_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .product ins, .bdt-product-quick-view .product .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_sale_price_typography',
				'selector' => '.bdt-product-quick-view .product ins, .bdt-product-quick-view .product .price',
			]
		);

		$this->add_control(
			'quick_view_modal_badge_heading',
			[
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_badge_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.bdt-product-quick-view .product .onsale' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_badge_bg_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .onsale' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_badge_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100
					],
				],
				'selectors' => [
					'.bdt-product-quick-view .product .onsale' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quick_view_modal_badge_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '.bdt-product-quick-view .product .onsale',
			]
		);

		$this->add_control(
			'quick_view_modal_badge_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .product .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'quick_view_modal_badge_typography',
				'selector'  => '.bdt-product-quick-view .product .onsale',
			]
		);

		$this->add_control(
			'quick_view_modal_meta_heading',
			[
				'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_meta_color',
			[
				'label'     => esc_html__('Type Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .product_meta>span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_meta_typography',
				'label'     => esc_html__('Type Typography', 'bdthemes-element-pack'),
				'selector' => '.bdt-product-quick-view .product .product_meta>span',
			]
		);

		$this->add_control(
			'quick_view_modal_tag_color',
			[
				'label'     => esc_html__('Category/Tags Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .product_meta a, .bdt-product-quick-view .product .product_meta>span span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'quick_view_modal_tag_typography',
				'label'     => esc_html__('Category/Tags Typography', 'bdthemes-element-pack'),
				'selector' => '.bdt-product-quick-view .product .product_meta a,  .bdt-product-quick-view .product .product_meta>span span',
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_meta_top_spacing',
			[
				'label'      => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'.bdt-product-quick-view .product .product_meta' => 'padding-top: {{SIZE}}px;'
				]
			]
		);

		$this->add_control(
			'quick_view_modal_close_heading',
			[
				'label'     => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_close_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .bdt-modal-dialog .bdt-close svg' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_close_size',
			[
				'label'      => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50
					],
				],
				'selectors' => [
					'.bdt-product-quick-view .bdt-modal-dialog .bdt-close svg' => 'width: {{SIZE}}px;'
				]
			]
		);

		$this->add_control(
			'quick_view_modal_quantity_heading',
			[
				'label'     => esc_html__('Quantity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'quick_view_modal_quantity_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_quantity_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quick_view_modal_quantity_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-product-quick-view .cart .quantity .qty',
			]
		);

		$this->add_control(
			'quick_view_modal_quantity_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_quantity_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quick_view_modal_quantity_shadow',
				'selector' => '.bdt-product-quick-view .cart .quantity .qty',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'quick_view_modal_quantity_typography',
				'selector'  => '.bdt-product-quick-view .cart .quantity .qty',
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_quantity_width',
			[
				'label'      => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'width: {{SIZE}}px;'
				]
			]
		);

		$this->add_responsive_control(
			'quick_view_modal_quantity_spacing',
			[
				'label'      => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 50
					],
				],
				'selectors' => [
					'.bdt-product-quick-view .cart .quantity .qty' => 'margin-right: {{SIZE}}px;'
				]
			]
		);

		$this->add_control(
			'quick_view_modal_button_heading',
			[
				'label'     => esc_html__('Add To Cart', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('tabs_quick_view_modal_style');

		$this->start_controls_tab(
			'tab_quick_view_modal_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_modal_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .cart .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .cart .button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'quick_view_modal_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.bdt-product-quick-view .product .cart .button',
			]
		);

		$this->add_control(
			'quick_view_modal_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .product .cart .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'.bdt-product-quick-view .product .cart .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'quick_view_modal_shadow',
				'selector' => '.bdt-product-quick-view .product .cart .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'quick_view_modal_typography',
				'selector'  => '.bdt-product-quick-view .product .cart .button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_quick_view_modal_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'quick_view_modal_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .cart .button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.bdt-product-quick-view .product .cart .button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'quick_view_modal_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'quick_view_modal_border_border!' => '',
				],
				'selectors' => [
					'.bdt-product-quick-view .product .cart .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'badge_bg_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'badge_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'badge_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-wc-products .bdt-wc-product .bdt-badge',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__('Footer', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_datatable_footer_style');

		$this->start_controls_tab(
			'tab_datatable_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'pagination_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination'    => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .dataTables_paginate' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a'    => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .paginate_button'          => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'active_pagination_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .paginate_button.current'          => 'color: {{VALUE}} !important;',
				],
			]
		);


		$this->add_control(
			'pagination_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a'    => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'bdt-table'
				]
			]
		);

		$this->add_control(
			'active_pagination_background',
			[
				'label'     => esc_html__('Active Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'bdt-table'
				]
			]
		);

		$this->add_responsive_control(
			'pagination_margin',
			[
				'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a'    => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} ul.bdt-pagination li span' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} .paginate_button'          => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_arrow_size',
			[
				'label'     => esc_html__('Arrow Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span, {{WRAPPER}} .dataTables_paginate',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_datatable_info',
			[
				'label' => __('Page Info', 'bdthemes-element-pack'),
				'condition' => [
					'_skin' => 'bdt-table'
				]
			]
		);

		$this->add_responsive_control(
			'info_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .dataTables_info' => 'margin-top: {{SIZE}}px;',
				],
				'condition' => [
					'_skin' => 'bdt-table'
				]
			]
		);

		$this->add_control(
			'info_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_info' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-table'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'info_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .dataTables_info',
				'condition' => [
					'_skin' => 'bdt-table'
				]
			]
		);

		$this->end_controls_tab();


		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_categories',
			[
				'label'      => esc_html__('Categories', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => '_skin',
							'value' => 'bdt-table',
						],
						[
							'name'  => 'show_categories',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'categories_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-product-categories a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-wc-product-categories'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'categories_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-product-categories, {{WRAPPER}} .bdt-wc-product-categories a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tags',
			[
				'label'      => esc_html__('Tags', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => '_skin',
							'value' => 'bdt-table',
						],
						[
							'name'  => 'show_tags',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'tags_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-wc-product-tags'   => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-wc-product-tags a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tags_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-wc-product-tags, {{WRAPPER}} .bdt-wc-product-tags a',
			]
		);

		$this->end_controls_section();

		// FILTER Bar Style
		$this->register_style_controls_filter();
		$this->register_sstyle_controls_ajax_loadmore();
	}


	protected function register_sstyle_controls_ajax_loadmore() {
		$this->start_controls_section(
			'section_style_loadmore',
			[
				'label'     => esc_html__('Load More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'wc_products_show_loadmore' => 'yes',
					'wc_products_enable_ajax_loadmore' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_load_button_style');

		$this->start_controls_tab(
			'tab_load_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'load_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_button_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'load_button_top_spacing',
			[
				'label'         => esc_html__('Top Spacing', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'load_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'load_button_border',
				'selector'    => '{{WRAPPER}} .bdt-loadmore-container .bdt-button',
			]
		);
		$this->add_responsive_control(
			'load_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'load_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-loadmore-container .bdt-button',
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'load_button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-loadmore-container .bdt-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_load_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'load_button_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'load_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'load_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-loadmore-container .bdt-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function render_add_to_cart() {
		global $product;
		$settings = $this->get_settings_for_display();
		if ('yes' == $settings['show_cart']) : ?>
			<?php if ($product) {
				$defaults = [
					'quantity'   => 1,
					'class'      => implode(
						' ',
						array_filter(
							[
								'button',
								'product_type_' . $product->get_type(),
								$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
								$product->supports('ajax_add_to_cart') && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
							]
						)
					),
					'attributes' => [
						'data-product_id'  => $product->get_id(),
						'data-product_sku' => $product->get_sku(),
						'aria-label'       => $product->add_to_cart_description(),
						'rel'              => 'nofollow',
					],
				];
				$args = apply_filters('woocommerce_loop_add_to_cart_args', wp_parse_args($defaults), $product);
				if (isset($args['attributes']['aria-label'])) {
					$args['attributes']['aria-label'] = wp_strip_all_tags($args['attributes']['aria-label']);
				}
				echo apply_filters(
					'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
					sprintf(
						'<a href="%s" data-quantity="%s" class="%s" %s>%s <i class="button-icon eicon-arrow-right"></i></a>',
						esc_url($product->add_to_cart_url()),
						esc_attr(isset($args['quantity']) ? $args['quantity'] : 1),
						esc_attr(isset($args['class']) ? $args['class'] : 'button'),
						isset($args['attributes']) ? wc_implode_html_attributes($args['attributes']) : '',
						esc_html($product->add_to_cart_text())
					),
					$product,
					$args
				);
			}; ?>
		<?php endif;
	}

	public function render_image() {
		$settings = $this->get_settings_for_display(); ?>
		<div class="bdt-wc-product-image bdt-position-relative bdt-background-cover">
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['image_size']); ?>" alt="<?php echo get_the_title(); ?>">
			</a>

			<?php if ('yes' == $settings['show_cart']) : ?>
				<div class="bdt-wc-add-to-cart <?php echo esc_attr($settings['cart_hide_mobile'] ? 'bdt-visible@s' : '') ?>">
					<?php
					// woocommerce_template_loop_add_to_cart();
					$this->render_add_to_cart();
					?>

				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_quick_view($product_id) {
		$settings = $this->get_settings_for_display();
		if ('yes' == $settings['show_quick_view']) : ?>
			<div class="bdt-quick-view <?php echo esc_attr($settings['quick_view_hide_mobile'] ? 'bdt-visible@s' : '') ?>">
				<?php wp_nonce_field('ajax-ep-wc-product-nonce', 'bdt-wc-product-modal-sc'); ?>
				<input type="hidden" class="bdt_modal_spinner_message" value="<?php echo __('Please wait...', 'bdthemes-element-pack'); ?>" />
				<a href="javascript:void(0)" data-id="<?php echo absint($product_id); ?>" data-bdt-tooltip="title: <?php echo __('Quick View', 'bdthemes-element-pack'); ?>; pos: left;">
					<i class="ep-icon-eye"></i>
				</a>
			</div>
		<?php endif;
	}

	public function render_header() {

		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['match_height']) {
			$this->add_render_attribute('wc-products', 'data-bdt-height-match', 'target: > div > div > .bdt-wc-product-inner');
		}

		$this->add_render_attribute('wc-products', 'class', ['bdt-wc-products', 'bdt-wc-products-skin-default']);

		if ($settings['show_filter_bar']) {
			$this->add_render_attribute('wc-products', 'data-bdt-filter', 'animation: false; target: #bdt-wc-product-' . $this->get_id());
		}

		$this->add_render_attribute(
			[
				'wc-products' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'posts_source' => isset($settings['posts_source']) ? $settings['posts_source'] : 'product',
							'posts_per_page' => isset($settings['posts_per_page']) ? $settings['posts_per_page'] : 6,
							'ajax_item_load' => isset($settings['wc_products_enable_ajax_loadmore_items']) ? $settings['wc_products_enable_ajax_loadmore_items'] : 4,
							'posts_selected_ids' => isset($settings['posts_selected_ids']) ? $settings['posts_selected_ids'] : '',
							'posts_include_by' => isset($settings['posts_include_by']) ? $settings['posts_include_by'] : '',
							'posts_include_author_ids' => isset($settings['posts_include_author_ids']) ? $settings['posts_include_author_ids'] : '',
							'posts_include_term_ids' => isset($settings['posts_include_term_ids']) ? $settings['posts_include_term_ids'] : '',
							'posts_exclude_by' => isset($settings['posts_exclude_by']) ? $settings['posts_exclude_by'] : '',
							'posts_exclude_ids' => isset($settings['posts_exclude_ids']) ? $settings['posts_exclude_ids'] : '',
							'posts_exclude_author_ids' => isset($settings['posts_exclude_author_ids']) ? $settings['posts_exclude_author_ids'] : '',
							'posts_exclude_term_ids' => isset($settings['posts_exclude_term_ids']) ? $settings['posts_exclude_term_ids'] : '',
							'posts_offset' => $settings['posts_offset'],
							'posts_select_date' => isset($settings['posts_select_date']) ? $settings['posts_select_date'] : '',
							'posts_date_before' => isset($settings['posts_date_before']) ? $settings['posts_date_before'] : '',
							'posts_date_after' => isset($settings['posts_date_after']) ? $settings['posts_date_after'] : '',
							'posts_orderby' => isset($settings['posts_orderby']) ? $settings['posts_orderby'] : 'date',
							'posts_order' => isset($settings['posts_order']) ? $settings['posts_order'] : 'desc',
							'posts_ignore_sticky_posts' => isset($settings['posts_ignore_sticky_posts']) ? $settings['posts_ignore_sticky_posts'] : '',
							'posts_only_with_featured_image' => isset($settings['posts_only_with_featured_image']) ? $settings['posts_only_with_featured_image'] : '',
							// 'totalPages' => $totalPages,
							'nonce' => wp_create_nonce('ajax-ep-wc-product-nonce'),
							// show hide options
							'show_filter_bar' => $settings['show_filter_bar'],
							'hide_out_stock' => $settings['product_hide_out_stock'],
							'show_badge' => $settings['show_badge'],
							'show_title' => $settings['show_title'],
							'title_tags' => $settings['title_tags'],
							'show_rating' => $settings['show_rating'],
							'show_price' => $settings['show_price'],
							'show_cart' => $settings['show_cart'],
							'cart_hide_mobile' => $settings['cart_hide_mobile'],
							'show_quick_view' => $settings['show_quick_view'],
							'quick_view_hide_mobile' => $settings['quick_view_hide_mobile'],
							// 'show_pagination' => $settings['show_pagination'],
							// 'show_title' => $settings['show_title'],
							// 'show_image' => $settings['show_image'],
							// 'show_categories' => $settings['show_categories'],
							// 'show_tags' => $settings['show_tags'],



						]))
					]
				]
			]
		);

		?>
		<div <?php echo $this->get_render_attribute_string('wc-products'); ?>>

			<?php if ($settings['show_filter_bar']) {
				$this->render_filter_menu();
			}
		}

		public function render_footer() {
			?>

		</div>
	<?php
		}

		public function render_query($posts_per_page) {
			$settings = $this->get_settings();
			$args    = [];
			if ($posts_per_page) {
				$args['posts_per_page'] = $posts_per_page;

				if ($settings['show_pagination']) {
					$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
				}
			}
			$args['post_type'] = 'product';
			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			if ('yes' == $settings['product_hide_free']) {
				$args['meta_query'][] = [
					'key'     => '_price',
					'value'   => 0,
					'compare' => '>',
					'type'    => 'DECIMAL',
				];
			}

			if ('yes' == $settings['product_hide_out_stock']) {
				$args['tax_query'][] = [
					[
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['outofstock'],
						'operator' => 'NOT IN',
					],
				];
			}

			switch ($settings['product_show_product_type']) {
				case 'featured':
					$args['tax_query'][] = [
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					];
					break;
				case 'onsale':
					$product_ids_on_sale    = wc_get_product_ids_on_sale();
					$product_ids_on_sale[]  = 0;
					$args['post__in'] = $product_ids_on_sale;
					break;
			}
			switch ($settings['posts_orderby']) {
				case 'price':
					$args['meta_key'] = '_price'; // WPCS: slow query ok.
					$args['orderby']  = 'meta_value_num';
					break;
				case 'sales':
					$args['meta_key'] = 'total_sales'; // WPCS: slow query ok.
					$args['orderby']  = 'meta_value_num';
					break;
				default:
					$args['orderby'] = $settings['posts_orderby'];
			}
			$default = $this->getGroupControlQueryArgs();
			$args              = array_merge($default, $args);
			$this->_query = new \WP_Query($args);
		}



		public function render_filter_menu() {
			$settings           = $this->get_settings_for_display();
			$product_categories = [];
			$this->render_query($settings['posts_per_page']);
			$wp_query = $this->get_query();

			if ('by_name' === $settings['posts_source'] and !empty($settings['product_categories'])) {
				$product_categories = $settings['product_categories'];
			} else {
				while ($wp_query->have_posts()) : $wp_query->the_post();
					$terms = get_the_terms(get_the_ID(), 'product_cat');
					if (!empty($terms)) {
						foreach ($terms as $term) {
							$product_categories[] = esc_attr($term->slug);
						};
					}
				endwhile;

				wp_reset_postdata();

				$product_categories = array_unique($product_categories);
			}
			$this->add_render_attribute(
				[
					'portfolio-gallery-hash-data' => [
						'data-hash-settings' => [
							wp_json_encode(
								array_filter([
									"id"       => 'bdt-products-' . $this->get_id(),
									'activeHash'  		=> $settings['active_hash'],
									'hashTopOffset'  	=> isset($settings['hash_top_offset']['size']) ? $settings['hash_top_offset']['size'] : 70,
									'hashScrollspyTime' => isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1000,
								])
							),
						],
					],
				]
			); ?>

		<div class="bdt-ep-grid-filters-wrapper" id="<?php echo 'bdt-products-' . $this->get_id(); ?>" <?php echo $this->get_render_attribute_string('portfolio-gallery-hash-data'); ?>>

			<button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php esc_html_e('Filter', 'bdthemes-element-pack'); ?></button>
			<div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom bdt-hidden@m">
				<ul class="bdt-nav bdt-dropdown-nav">
					<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control><a href="#"><?php esc_html_e('All Products', 'bdthemes-element-pack'); ?></a></li>
					<?php foreach ($product_categories as $product_category => $value) : ?>
						<?php $filter_name = get_term_by('slug', $value, 'product_cat'); ?>
						<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
							<a href="#"><?php echo esc_html($filter_name->name); ?></a>
						</li>
					<?php endforeach; ?>

				</ul>
			</div>


			<ul class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
				<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control><a href="#"><?php esc_html_e('All Products', 'bdthemes-element-pack'); ?></a></li>
				<?php foreach ($product_categories as $product_category => $value) : ?>
					<?php $filter_name = get_term_by('slug', $value, 'product_cat'); ?>
					<li class="bdt-ep-grid-filter" data-bdt-filter-control="[data-filter*='bdtf-<?php echo esc_attr(trim($value)); ?>']">
						<a href="#"><?php echo esc_html($filter_name->name); ?></a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
		}

		public function render_loop_item() {
			$settings = $this->get_settings_for_display();
			$id       = 'bdt-wc-product-' . $this->get_id();
			// $wp_query = $this->render_query($settings['posts_per_page']);



			$this->render_query($settings['posts_per_page']);

			$wp_query = $this->get_query();

			if ($wp_query->have_posts()) {

				$this->add_render_attribute('wc-products-wrapper', 'data-bdt-grid', '');

				if ($settings['masonry']) {
					$this->add_render_attribute('wc-products-wrapper', 'data-bdt-grid', 'masonry: true');
				}

				$column = 4;
				$column_tablet = 2;
				$column_mobile = 1;

				if (isset($settings['columns'])) {
					$column = $settings['columns'];
				}

				if (isset($settings['columns_tablet'])) {
					$column_tablet = $settings['columns_tablet'];
				}

				if (isset($settings['columns_mobile'])) {
					$column_mobile = $settings['columns_mobile'];
				}

				$this->add_render_attribute(
					[
						'wc-products-wrapper' => [
							'class' => [
								'bdt-wc-products-wrapper',
								'bdt-grid',
								'bdt-grid-medium',
								'bdt-child-width-1-' . $column . '@m',
								'bdt-child-width-1-' . $column_tablet . '@s',
								'bdt-child-width-1-' . $column_mobile,
							],
							'id' => esc_attr($id),
						],
					]
				);


				if ($settings['grid_animation_type'] !== '') {
					$this->add_render_attribute('wc-products-wrapper', 'data-bdt-scrollspy', 'cls: bdt-animation-' . esc_attr($settings['grid_animation_type']) . ';');
					$this->add_render_attribute('wc-products-wrapper', 'data-bdt-scrollspy', 'delay: ' . esc_attr($settings['grid_anim_delay']['size']) . ';');
					$this->add_render_attribute('wc-products-wrapper', 'data-bdt-scrollspy', 'target: > div > .bdt-wc-product-inner' . ';');
				}

		?>
			<div <?php echo $this->get_render_attribute_string('wc-products-wrapper'); ?>>
				<?php

				$this->add_render_attribute('wc-product', 'class', 'bdt-wc-product');

				$this->add_render_attribute('bdt-wc-product-title', 'class', 'bdt-wc-product-title');

				while ($wp_query->have_posts()) : $wp_query->the_post();
					global $post, $product;

				?>

					<?php if ($settings['show_filter_bar']) {
						$terms = get_the_terms(get_the_ID(), 'product_cat');
						$product_filter_cat = [];
						if (!empty($terms)) {
							foreach ($terms as $term) {
								$product_filter_cat[] = 'bdtf-' . esc_attr($term->slug);
							};
						}
						$this->add_render_attribute('wc-product', 'data-filter', implode(' ', $product_filter_cat), true);
					} ?>

					<div <?php echo $this->get_render_attribute_string('wc-product'); ?>>
						<div class="bdt-wc-product-inner">

							<?php if ($settings['show_badge'] and !$product->is_in_stock()) : ?>
								<div class="bdt-badge bdt-position-top-left bdt-position-small">
									<?php echo apply_filters('woocommerce_product_is_in_stock', '<span class="bdt-onsale">' . esc_html__('Out of Stock!', 'woocommerce') . '</span>', $post, $product); ?>
								</div>
							<?php elseif ($settings['show_badge'] and $product->is_on_sale()) : ?>
								<div class="bdt-badge bdt-position-top-left bdt-position-small">
									<?php echo apply_filters('woocommerce_sale_flash', '<span class="bdt-onsale">' . esc_html__('Sale!', 'woocommerce') . '</span>', $post, $product); ?>
								</div>
							<?php endif; ?>

							<?php $this->render_image(); ?>

							<?php $this->render_quick_view($product->get_id()) ?>

							<div class="bdt-wc-product-desc">
								<?php if ('yes' == $settings['show_title']) : ?>
									<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-wc-product-title'); ?>>
										<a href="<?php the_permalink(); ?>" class="bdt-link-reset">
											<?php the_title(); ?>
										</a>
									</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
								<?php endif; ?>

								<?php if (('yes' == $settings['show_price']) or ('yes' == $settings['show_rating'])) : ?>
									<?php if ('yes' == $settings['show_price']) : ?>
										<div class="bdt-wc-product-price">
											<?php woocommerce_template_single_price(); ?>
										</div>
									<?php endif; ?>

									<?php if ('yes' == $settings['show_rating']) : ?>
										<div class="bdt-wc-rating">
											<?php woocommerce_template_loop_rating(); ?>
										</div>
									<?php endif; ?>
								<?php endif; ?>
							</div>

						</div>
					</div>
				<?php endwhile;	?>
			</div>
			<?php

				if ($settings['show_pagination']) {
			?>
				<div class="ep-pagination">
					<?php element_pack_post_pagination($wp_query); ?>
				</div>
		<?php
				}

				wp_reset_postdata();
			} else {
				echo '<div class="bdt-alert-warning" data-bdt-alert>' . esc_html__('Ops! There no product to display.', 'bdthemes-element-pack') . '<div>';
			}
		}

		public function render_load_more() {
			$settings = $this->get_settings_for_display(); ?>
		<?php if ($settings['wc_products_show_loadmore'] == 'yes' || $settings['wc_products_show_infinite_scroll'] == 'yes') { ?>
			<div class="bdt-loadmore-container bdt-text-center">
				<?php if ($settings['wc_products_show_infinite_scroll'] == 'yes') : ?>
					<span class="bdt-loadmore" bdt-spinner></span>
					<!-- <span class="bdt-loadmore" bdt-spinner style="display:none;"></span> -->
				<?php else : ?>
					<span class="bdt-loadmore bdt-button bdt-button-primary"><?php esc_html_e('Load More', 'bdthemes-element-pack'); ?></span>
				<?php endif; ?>
			</div>
<?php }
		}

		public function render() {
			$this->render_header();
			$this->render_loop_item();
			$this->render_footer();
			$this->render_load_more();
		}
	}
