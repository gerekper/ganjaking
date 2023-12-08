<?php

namespace ElementPack\Modules\PortfolioGallery\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Utils;

use ElementPack\Base\Module_Base;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Modules\PortfolioGallery\Skins;
use WP_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Portfolio_Gallery extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'bdt-portfolio-gallery';
	}

	public function get_title() {
		return BDTEP . esc_html__('Portfolio Gallery', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-portfolio-gallery';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['portfolio', 'gallery', 'blog', 'recent', 'news', 'works'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-portfolio-gallery'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'tilt', 'ep-scripts'];
		} else {
			return ['imagesloaded', 'tilt', 'ep-portfolio-gallery'];
		}
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Abetis($this));
		$this->add_skin(new Skins\Skin_Fedara($this));
		$this->add_skin(new Skins\Skin_Trosia($this));
		$this->add_skin(new Skins\Skin_Janes($this));
	}

	public function get_query() {
		return $this->_query;
	}

	public function register_controls() {
		$this->register_section_controls();
	}

	private function register_section_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
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
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'         => 'thumbnail_size',
				'label'        => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude'      => ['custom'],
				'default'      => 'medium',
				'prefix_class' => 'bdt-portfolio--thumbnail-size-',
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
				],
			]
		);

		$this->add_control(
			'item_ratio',
			[
				'label'   => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 250,
				],
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-gallery-thumbnail img' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'masonry!' => 'yes',

				],
			]
		);

		$this->end_controls_section();

		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_source',
			[
				'type' => Controls_Manager::SELECT,
				'default' => 'portfolio'
			]
		);

		$this->update_control(
			'posts_per_page',
			[
				'default' => 9,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'filter_bar',
			[
				'label' => esc_html__('Filter Bar', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_filter_bar',
			[
				'label' => esc_html__('Show', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
		$post_types = $this->getGroupControlQueryPostTypes();

		foreach ($post_types as $key => $post_type) {
			$taxonomies = $this->get_taxonomies($key);
			if (!$taxonomies[$key]) {
				continue;
			}
			$this->add_control(
				'taxonomy_' . $key,
				[
					'label'     => __('Taxonomies', 'bdthemes-element-pack'),
					'type'      => Controls_Manager::SELECT,
					'options'   => $taxonomies[$key],
					'default'   => key($taxonomies[$key]),
					'condition' => [
						'posts_source' => $key,
						'show_filter_bar' => 'yes'
					],
				]
			);
		}

		$this->add_control(
			'show_filter_item_count',
			[
				'label'         => esc_html__('Filter Item Count', 'bdthemes-element-pack') . BDTEP_NC,
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => esc_html__('Show', 'portfolio-gallery'),
				'label_off'     => esc_html__('Hide', 'portfolio-gallery'),
				'return_value'  => 'yes',
				'default'       => 'no',
				'condition' => [
					'show_filter_bar' => 'yes'
				]
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

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
			'title_tag',
			[
				'label'     => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h4',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'excerpt_limit',
			[
				'label'     => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'   => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_category',
			[
				'label' => esc_html__('Show Category', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_link',
			[
				'label'   => esc_html__('Show Link', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'post'     => esc_html__('Details Link', 'bdthemes-element-pack'),
					'lightbox' => esc_html__('Lightbox Link', 'bdthemes-element-pack'),
					'both'     => esc_html__('Both', 'bdthemes-element-pack'),
					'none'     => esc_html__('None', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'external_link',
			[
				'label'   => esc_html__('Show in new Tab (Details Link/Title)', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'show_title',
							'value'    => 'yes'
						],
						[
							'name'     => 'show_link',
							'value'    => 'post'
						],
						[
							'name'     => 'show_link',
							'value'    => 'both'
						],
					]
				],
			]
		);

		$this->add_control(
			'link_type',
			[
				'label'   => esc_html__('Link Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'icon',
				'options' => [
					'icon' => esc_html__('Icon', 'bdthemes-element-pack'),
					'text' => esc_html__('Text', 'bdthemes-element-pack'),
				],
				'condition' => [
					'show_link!' => 'none',
				]
			]
		);

		$this->add_control(
			'tilt_show',
			[
				'label' => esc_html__('Tilt Effect', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'tilt_scale',
			[
				'label'     => esc_html__('Zoom on Hover', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'tilt_show' => 'yes',
				]
			]
		);

		$this->add_control(
			'lightbox_animation',
			[
				'label'   => esc_html__('Lightbox Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
				],
				'condition' => [
					'show_link' => ['both', 'lightbox'],
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lightbox_autoplay',
			[
				'label'   => __('Lightbox Autoplay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_link' => ['both', 'lightbox'],
				]
			]
		);

		$this->add_control(
			'lightbox_pause',
			[
				'label'   => __('Lightbox Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_link' => ['both', 'lightbox'],
					'lightbox_autoplay' => 'yes'
				],

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
			'section_design_layout',
			[
				'label' => esc_html__('Items', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay_style_headline',
			[
				'label'     => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'_skin!' => ['bdt-janes', 'bdt-trosia'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_skin_background',
				'label' => __('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery.skin-abetis .bdt-portfolio-inner:before, {{WRAPPER}} .bdt-portfolio-gallery.skin-fedara .bdt-portfolio-inner:before',
				'condition' => [
					'_skin' => ['bdt-abetis', 'bdt-fedara']
				],
			]
		);

		$this->add_control(
			'overlay_primary_background',
			[
				'label'     => esc_html__('Primary Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery.skin-default .bdt-portfolio-content-inner:before' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'overlay_secondary_background',
			[
				'label'     => esc_html__('Secondary Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery.skin-default .bdt-portfolio-content-inner:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'portfolio_content_style_headline',
			[
				'label'     => esc_html__('Content', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label'     => esc_html__('Content Width(%)', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery.skin-janes .bdt-gallery-item .bdt-portfolio-inner .bdt-portfolio-desc' => 'right: calc(100% - {{SIZE}}%);',
				],
				'condition' => [
					'_skin' => 'bdt-janes',
				],
			]
		);

		$this->add_responsive_control(
			'portfolio_content_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'      => 'center',
				'prefix_class' => 'bdt-custom-gallery-skin-fedara-style-',
				'selectors'    => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-skin-fedara-desc' => 'text-align: {{VALUE}}',
				],
				// 'condition' => [
				// 	'_skin!' => 'bdt-trosia',
				// ],
			]
		);

		// $this->add_control(
		// 	'desc_background_color',
		// 	[
		// 		'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-skin-fedara-desc' => 'background: {{VALUE}};',
		// 		],
		// 		'condition' => [
		// 			'_skin!' => 'bdt-abetis',
		// 		],
		// 	]
		// );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'desc_background_color',
				'selector'  => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-skin-fedara-desc',
				'condition' => [
					'_skin!' => 'bdt-abetis',
				],
			]
		);

		$this->add_responsive_control(
			'desc__padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-skin-fedara-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-desc, {{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-skin-fedara-desc' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery.skin-janes .bdt-gallery-item .bdt-gallery-item-tags' => 'padding-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_category' => 'yes',
					'_skin' => 'bdt-janes',
				],
			]
		);

		$this->add_control(
			'portfolio_item_headline',
			[
				'label'     => esc_html__('Item', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
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
					'{{WRAPPER}} .bdt-portfolio-gallery.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-portfolio-gallery.bdt-grid > *' => 'padding-left: {{SIZE}}px',
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
					'{{WRAPPER}} .bdt-portfolio-gallery.bdt-grid'     => 'margin-top: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-portfolio-gallery.bdt-grid > *' => 'margin-top: {{SIZE}}px',
				],
			]
		);

		$this->start_controls_tabs('tabs_item_style');

		$this->start_controls_tab(
			'tab_item_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-inner',
			]
		);

		$this->add_responsive_control(
			'item_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => 'bdt-janes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_shadow',
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-inner',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => __('hover', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);

		$this->add_control(
			'item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item:hover .bdt-portfolio-inner' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery:hover .bdt-gallery-item .bdt-portfolio-inner',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item .bdt-gallery-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item .bdt-gallery-item-title:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-gallery-item .bdt-gallery-item-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_excerpt',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_margin',
			[
				'label'     => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-excerpt' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-portfolio-excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_link!' => 'none',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link, {{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link i, {{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'border_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'border_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'border_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '30% 70% 82% 18% / 46% 62% 38% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '30% 70% 82% 18% / 46% 62% 38% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link'     => 'border-radius: {{VALUE}}; overflow: hidden;',
				],
				'condition' => [
					'border_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link span, {{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link i',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link:hover i'  => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link:hover span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link:hover, {{WRAPPER}} .bdt-portfolio-gallery.skin-abetis .bdt-gallery-item-link:before' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-link.bdt-link-icon:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// FILTER Bar Style
		$this->register_style_controls_filter();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'      => esc_html__('Category', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes'
				]
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Category Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_separator_color',
			[
				'label'     => esc_html__('Separator Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags .bdt-gallery-item-tag-separator' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tags',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .bdt-portfolio-gallery .bdt-gallery-item-tag',
			]
		);

		$this->end_controls_section();

		//Pagination
		$this->start_controls_section(
			'section_style_pagination',
			[
				'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pagination' => 'yes',
				],
			]
		);

		$this->register_pagination_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_filter_count',
			[
				'label' => esc_html__('Filter Item Count', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_filter_item_count' => 'yes'
				]
			]
		);
		$this->add_control(
			'filter_badge_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'filter_badge_bg_color',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'filter_badge_border',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-ep-grid-filter span',
			]
		);
		$this->add_responsive_control(
			'filter_badge_radius',
			[
				'label'                 => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%', 'em'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-ep-grid-filter span'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'filter_badge_postion_x',
			[
				'label'         => esc_html__('Position (X axis)', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'range'         => [
					'px'        => [
						'min'   => -50,
						'max'   => 50,
						'step'  => 1,
					]
				],
				'default'       => [
					'unit'      => 'px',
					'size'      => -22,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'filter_badge_postion_y',
			[
				'label'         => esc_html__('Position (Y axis)', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				// 'size_units'    => [ 'px', '%' ],
				'range'         => [
					'px'        => [
						'min'   => -50,
						'max'   => 50,
						'step'  => 1,
					]
				],
				'default'       => [
					'unit'      => 'px',
					'size'      => -16,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'filter_badge_width',
			[
				'label'         => esc_html__('Size', 'bdthemes-element-pack'),
				'type'          => Controls_Manager::SLIDER,
				'size_units'    => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-grid-filter span' => 'width: {{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}}'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'filter_badge_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}}  .bdt-ep-grid-filter span',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get post query builder arguments
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings();

		$args = [];
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			if ($settings['show_pagination']) { // fix query offset
				$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
			}
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}
	public function get_taxonomies($post_type = '') {
		$_taxonomies = [];
		if ($post_type) {
			$taxonomies = get_taxonomies(['public' => true, 'object_type' => [$post_type]], 'object');
			$tax = array_diff_key(wp_list_pluck($taxonomies, 'label', 'name'), []);
			$_taxonomies[$post_type] = count($tax) !== 0 ? $tax : '';
		}
		return $_taxonomies;
	}


	public function filter_menu_terms() {
		$settings = $this->get_settings_for_display();
		$taxonomy = $settings['taxonomy_' . $settings['posts_source']];
		$categories = get_the_terms(get_the_ID(), $taxonomy);
		$_categories = [];
		if ($categories) {
			foreach ($categories as $category) {
				$_categories[$category->slug] = $category->slug;
			}
		}
		return implode(' ', $_categories);
	}
	protected function filter_menu_categories() {
		$settings = $this->get_settings_for_display();
		$include_Categories = $settings['posts_include_term_ids'];
		$exclude_Categories = $settings['posts_exclude_term_ids'];
		$post_options = [];
		if (isset($settings['taxonomy_' . $settings['posts_source']])) {
			$taxonomy = $settings['taxonomy_' . $settings['posts_source']];
			$params = [
				'taxonomy' => $taxonomy,
				'hide_empty' => true,
				'include' => $include_Categories,
				'exclude' => $exclude_Categories,
			];
			$post_categories = get_terms($params);
			if (is_wp_error($post_categories)) {
				return $post_options;
			}
			if (false !== $post_categories and is_array($post_categories)) {
				foreach ($post_categories as $category) {
					$post_options[$category->slug] = $category->name;
				}
			}
		}

		return $post_options;
	}


	public function render() {
		$settings = $this->get_settings_for_display();

		// TODO need to delete after v6.5
		if (isset($settings['limit']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->render_header();

		while ($wp_query->have_posts()) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_footer();

		if ($settings['show_pagination']) { ?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination($wp_query); ?>
			</div>
		<?php
		}

		wp_reset_postdata();
	}

	public function render_thumbnail() {
		$settings = $this->get_settings_for_display();

		$settings['thumbnail_size'] = [
			'id' => get_post_thumbnail_id(),
		];

		$thumbnail_html      = Group_Control_Image_Size::get_attachment_image_html($settings, 'thumbnail_size');
		$placeholder_img_src = Utils::get_placeholder_image_src();

		if (!$thumbnail_html) {
			printf('<div class="bdt-gallery-thumbnail"><img src="%1$s" alt="%2$s"></div>', $placeholder_img_src, esc_html(get_the_title()));
		} else {
			echo '<div class="bdt-gallery-thumbnail">';
			print(wp_get_attachment_image(
				get_post_thumbnail_id(),
				$settings['thumbnail_size_size'],
				false,
				[
					'alt' => esc_html(get_the_title())
				]
			));
			echo '</div>';
		}
	}

	public function render_filter_menu() {
		$settings = $this->get_settings_for_display();
		$portfolio_categories = $this->filter_menu_categories();

		$this->add_render_attribute(
			[
				'portfolio-gallery-hash-data' => [
					'data-hash-settings' => [
						wp_json_encode(array_filter([
							"id"       => 'bdt-portfolio-gallery-' . $this->get_id(),
							'activeHash'  		=> $settings['active_hash'],
							'hashTopOffset'  	=> $settings['hash_top_offset']['size'] ?? 70,
							'hashScrollspyTime' => $settings['hash_scrollspy_time']['size'] ?? 1000,
						])),
					],
				],
			]
		);


		?>
		<div class="bdt-ep-grid-filters-wrapper" id="<?php echo 'bdt-portfolio-gallery-' . $this->get_id(); ?>" <?php echo $this->get_render_attribute_string('portfolio-gallery-hash-data'); ?>>

			<button class="bdt-button bdt-button-default bdt-hidden@m" type="button"><?php esc_html_e('Filter', 'bdthemes-element-pack'); ?></button>

			<div data-bdt-dropdown="mode: click;" class="bdt-dropdown bdt-margin-remove-top bdt-margin-remove-bottom">
				<ul class="bdt-nav bdt-dropdown-nav">

					<li class="bdt-active" data-bdt-filter-control>
						<a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
						<?php if ($settings['show_filter_item_count'] === 'yes') : ?>
							<span class="bdt-all-count"></span>
						<?php endif; ?>
					</li>

					<?php foreach ($portfolio_categories as $slug => $category) : ?>
						<li class="bdt-ep-grid-filter" data-bdt-target="<?php echo esc_attr(trim($slug)); ?>" data-bdt-filter-control="[data-filter*='<?php echo esc_attr(trim($slug)); ?>']">
							<a href="#"><?php echo esc_html($category); ?></a>
							<?php if ($settings['show_filter_item_count'] === 'yes') : ?>
								<span class="bdt-count"></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>

				</ul>
			</div>

			<ul id="bdt-ep-grid-filters<?php echo $this->get_id(); ?>" class="bdt-ep-grid-filters bdt-visible@m" data-bdt-margin>
				<li class="bdt-ep-grid-filter bdt-active" data-bdt-filter-control>
					<a href="#"><?php esc_html_e('All', 'bdthemes-element-pack'); ?></a>
					<?php if ($settings['show_filter_item_count'] === 'yes') : ?>
						<span class="bdt-all-count"></span>
					<?php endif; ?>
				</li>
				<?php foreach ($portfolio_categories as $slug => $category) : ?>
					<li class="bdt-ep-grid-filter" data-bdt-target="<?php echo esc_attr(trim($slug)); ?>" data-bdt-filter-control="[data-filter*='<?php echo esc_attr(trim($slug)); ?>']">
						<a href="#"><?php echo esc_html($category); ?></a>
						<?php if ($settings['show_filter_item_count'] === 'yes') : ?>
							<span class="bdt-count"></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_title']) {
			return;
		}

		$tag = $settings['title_tag'];
		$target = ($settings['external_link']) ? 'target="_blank"' : '';

	?>
		<a href="<?php echo get_the_permalink(); ?>" <?php echo $target; ?>>
			<<?php echo Utils::get_valid_html_tag($tag) ?> class="bdt-gallery-item-title bdt-margin-remove">
				<?php the_title() ?>
			</<?php echo Utils::get_valid_html_tag($tag) ?>>
		</a>
	<?php
	}


	public function render_excerpt() {
		if (!$this->get_settings('show_excerpt')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

	?>
		<div class="bdt-portfolio-excerpt">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_limit'), $strip_shortcode);
			}
			?>
		</div>
	<?php
	}

	public function render_categories_names() {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_category')) {
			return;
		}

		$this->add_render_attribute('portfolio-category', 'class', 'bdt-gallery-item-tags', true);

		global $post;

		$separator  = '<span class="bdt-gallery-item-tag-separator"></span>';
		$tags_array = [];

		$item_filters = get_the_terms($post->ID, 'portfolio_filter');

		foreach ($item_filters as $item_filter) {
			$tags_array[] = '<span class="bdt-gallery-item-tag">' . $item_filter->slug . '</span>';
		}

	?>
		<div <?php echo $this->get_render_attribute_string('portfolio-category'); ?>>
			<?php echo implode($separator, $tags_array); ?>
		</div>
	<?php
	}

	public function render_overlay() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute(
			[
				'content-position' => [
					'class' => [
						'bdt-position-center',
					]
				]
			],
			'',
			'',
			true
		);

	?>
		<div <?php echo $this->get_render_attribute_string('content-position'); ?>>
			<div class="bdt-portfolio-content">
				<div class="bdt-gallery-content-inner">
					<?php

					$placeholder_img_src = Utils::get_placeholder_image_src();

					$img_url = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');

					if (!$img_url) {
						$img_url = $placeholder_img_src;
					} else {
						$img_url = $img_url[0];
					}

					$this->add_render_attribute(
						[
							'lightbox-settings' => [
								'class' => [
									'bdt-gallery-item-link',
									'bdt-gallery-lightbox-item',
									('icon' == $settings['link_type']) ? 'bdt-link-icon' : 'bdt-link-text'
								],
								'data-elementor-open-lightbox' => 'no',
								'data-caption'                 => get_the_title(),
								'href'                         => esc_url($img_url)
							]
						],
						'',
						'',
						true
					);

					if ('none' !== $settings['show_link']) : ?>
						<div class="bdt-flex-inline bdt-gallery-item-link-wrapper">
							<?php if (('lightbox' == $settings['show_link']) || ('both' == $settings['show_link'])) : ?>
								<a <?php echo $this->get_render_attribute_string('lightbox-settings'); ?>>
									<?php if ('icon' == $settings['link_type']) : ?>
										<i class="ep-icon-search" aria-hidden="true"></i>
									<?php elseif ('text' == $settings['link_type']) : ?>
										<span><?php esc_html_e('ZOOM', 'bdthemes-element-pack'); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>

							<?php if (('post' == $settings['show_link']) || ('both' == $settings['show_link'])) : ?>
								<?php
								$link_type_class =  ('icon' == $settings['link_type']) ? ' bdt-link-icon' : ' bdt-link-text';
								$target =  ($settings['external_link']) ? 'target="_blank"' : '';

								?>
								<a class="bdt-gallery-item-link<?php echo esc_attr($link_type_class); ?>" href="<?php echo esc_attr(get_permalink()); ?>" <?php echo $target; ?>>
									<?php if ('icon' == $settings['link_type']) : ?>
										<i class="ep-icon-link" aria-hidden="true"></i>
									<?php elseif ('text' == $settings['link_type']) : ?>
										<span><?php esc_html_e('VIEW', 'bdthemes-element-pack'); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php
	}

	public function render_header($skin = 'default') {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-portfolio-gallery' . $this->get_id();

		$this->add_render_attribute('portfolio-wrapper', 'class', 'bdt-portfolio-gallery-wrapper');

		$this->add_render_attribute('portfolio', 'id', esc_attr($id));

		$this->add_render_attribute('portfolio', 'class', ['bdt-portfolio-gallery', 'bdt-ep-grid-filter-container', 'skin-' . $skin]);

		$this->add_render_attribute('portfolio', 'data-bdt-grid', '');
		$this->add_render_attribute('portfolio', 'class', ['bdt-grid', 'bdt-grid-medium']);

		$columns_mobile = isset($settings['columns_mobile']) ? $settings['columns_mobile'] : 1;
		$columns_tablet = isset($settings['columns_tablet']) ? $settings['columns_tablet'] : 2;
		$columns 		 = isset($settings['columns']) ? $settings['columns'] : 3;

		$this->add_render_attribute('portfolio', 'class', 'bdt-child-width-1-' . $columns_mobile);
		$this->add_render_attribute('portfolio', 'class', 'bdt-child-width-1-' . $columns_tablet . '@s');
		$this->add_render_attribute('portfolio', 'class', 'bdt-child-width-1-' . $columns . '@m');

		if ($settings['masonry']) {
			$this->add_render_attribute('portfolio', 'data-bdt-grid', 'masonry: true');
		}

		if ($settings['show_filter_bar']) {
			$this->add_render_attribute('portfolio-wrapper', 'data-bdt-filter', 'target: #' . $id);
		}

		if ('lightbox' === $settings['show_link'] or 'both' === $settings['show_link']) {
			$this->add_render_attribute('portfolio', 'data-bdt-lightbox', 'toggle: .bdt-gallery-lightbox-item; animation:' . $settings['lightbox_animation'] . ';');
			if ($settings['lightbox_autoplay']) {
				$this->add_render_attribute('portfolio', 'data-bdt-lightbox', 'autoplay: 500;');

				if ($settings['lightbox_pause']) {
					$this->add_render_attribute('portfolio', 'data-bdt-lightbox', 'pause-on-hover: true;');
				}
			}
		}

		$this->add_render_attribute(
			[
				'portfolio-wrapper' => [
					'data-settings' => [
						wp_json_encode([
							'id'		=> '#' . $id,
							'tiltShow'  => $settings['tilt_show'] == 'yes' ? true : false
						]),
					],
				],
			]
		);


	?>
		<div <?php echo $this->get_render_attribute_string('portfolio-wrapper'); ?>>

			<?php
			if ($settings['show_filter_bar']) {
				$this->render_filter_menu();
			}

			if ($settings['grid_animation_type'] !== '') {
				$this->add_render_attribute('portfolio', 'bdt-scrollspy', 'cls: bdt-animation-' . esc_attr($settings['grid_animation_type']) . ';');
				$this->add_render_attribute('portfolio', 'bdt-scrollspy', 'delay: ' . esc_attr($settings['grid_anim_delay']['size']) . ';');
				$this->add_render_attribute('portfolio', 'bdt-scrollspy', 'target: > div > div > .bdt-portfolio-inner' . ';');
			}

			?>
			<div <?php echo $this->get_render_attribute_string('portfolio'); ?>>

			<?php
		}

		public function render_footer() {
			?>

			</div>
		</div>
	<?php
		}

		public function render_desc() {
	?>
		<div class="bdt-portfolio-desc">
			<?php
			$this->render_title();
			$this->render_excerpt();
			?>
		</div>
	<?php
		}

		public function render_post() {
			$settings = $this->get_settings_for_display();
			global $post;
			$element_key = 'portfolio-item-' . $post->ID;
			if ($settings['tilt_show']) {
				$this->add_render_attribute('portfolio-item-inner', 'data-tilt', '', true);
				if ($settings['tilt_scale']) {
					$this->add_render_attribute('portfolio-item-inner', 'data-tilt-scale', '1.2', true);
				}
			}
			$this->add_render_attribute('portfolio-item-inner', 'class', 'bdt-portfolio-inner', true);
			$this->add_render_attribute('portfolio-item', 'class', 'bdt-gallery-item bdt-transition-toggle', true);
			if ('yes' === $settings['show_filter_bar']) {
				$this->add_render_attribute($element_key, 'data-filter', $this->filter_menu_terms(), true);
			} ?>
		<div <?php echo $this->get_render_attribute_string($element_key); ?>>
			<div <?php echo $this->get_render_attribute_string('portfolio-item'); ?>>
				<div <?php echo $this->get_render_attribute_string('portfolio-item-inner'); ?>>
					<div class="bdt-portfolio-content-inner">
						<?php
						$this->render_thumbnail();
						$this->render_overlay();
						?>
					</div>
					<?php $this->render_desc(); ?>
					<?php $this->render_categories_names(); ?>
				</div>
			</div>
		</div>
<?php
		}
	}
