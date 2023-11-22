<?php

namespace ElementPack\Modules\PostGridTab\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Utils;
use Elementor\Icons_Manager;

use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Grid_Tab extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'bdt-post-grid-tab';
	}

	public function get_title() {
		return BDTEP . esc_html__('Post Grid Tab', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-grid-tab';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'tab', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-post-grid-tab', 'ep-font'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['gridtab', 'recliner', 'ep-scripts'];
		} else {
			return ['gridtab', 'recliner', 'ep-post-grid-tab'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/kFEL4AGnIv4';
	}

	public function get_query() {
		return $this->_query;
	}

	public function register_controls() {
		$this->register_query_section_controls();
	}

	private function register_query_section_controls() {
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
				'description'    => esc_html__('Note:- The changes will reflect on Preview Page.', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => 4,
				'tablet_default' => 3,
				'mobile_default' => 2,
				'options'        => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
				],
			]
		);

		$this->add_responsive_control(
			'item_ratio',
			[
				'label' => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
						'step' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab-thumbnail img' => 'height: {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'grid_tab_item',
			[
				'label'   => esc_html__('Grid Tab Item', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'image',
				'options' => [
					'image' => esc_html__('Image', 'bdthemes-element-pack'),
					'title' => esc_html__('Title', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'exclude'   => ['custom'],
				'condition' => ['grid_tab_item' => 'image'],
				'default'   => 'medium',
			]
		);

		$this->add_control(
			'content_reverse',
			[
				'label'   => esc_html__('Content Reverse', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
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
			'posts_per_page',
			[
				'default' => 8,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
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
				'default'   => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
				'separator' => 'after'
			]
		);

		$this->add_control(
			'show_author',
			[
				'label'   => esc_html__('Author', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_date',
			[
				'label'   => esc_html__('Date', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'human_diff_time' => 'yes',
					'show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_comments',
			[
				'label'   => esc_html__('Comments', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'     => esc_html__('Category', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'after'
			]
		);

		$this->add_control(
			'content_image',
			[
				'label'   => esc_html__('Post Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'content_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
				'condition' => [
					'grid_tab_item' => 'image',
					'content_image' => 'yes'
				],
				'separator' => 'after'
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'     => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 45,
				'condition' => [
					'show_excerpt' => 'yes',
				],
				'separator' => 'after'
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
			'show_readmore',
			[
				'label'   => esc_html__('Read More', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__('Read More Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
				'condition'   => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'post_grid_tab_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition'   => [
					'show_readmore' => 'yes',
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__('Left', 'bdthemes-element-pack'),
					'right' => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'post_grid_tab_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'post_grid_tab_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after'
			]
		);

		$this->add_control(
			'show_close',
			[
				'label'   => esc_html__('Close Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		// $this->add_control(
		// 	'show_arrows',
		// 	[
		// 		'label' => esc_html__( 'Arrows', 'bdthemes-element-pack' ),
		// 		'type'  => Controls_Manager::SWITCHER,
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tab_padding',
			[
				'label'   => esc_html__('Item Padding', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '0',
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 2,
					],
				],
			]
		);

		$this->add_responsive_control(
			'tab_text_align',
			[
				'label'   => __('Item Text Align', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
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
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab > dt' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'grid_tab_item' => 'title',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tab_text_typography',
				//'scheme'    => Schemes\Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .bdt-post-grid-tab .gridtab > dt',
				'condition' => [
					'grid_tab_item' => 'title',
				],
			]
		);

		$this->add_control(
			'item_border_width',
			[
				'label'   => esc_html__('Item Border Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 2,
					],
				],
			]
		);

		$this->add_control(
			'tab_border_color',
			[
				'label'     => esc_html__('Item Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ddd',
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab > dt, {{WRAPPER}} .bdt-post-grid-tab .gridtab > dd' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_tab_no',
			[
				'label'     => esc_html__('Active Tab Number', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
					],
				],
			]
		);

		$this->add_control(
			'active_tab_background',
			[
				'label'     => esc_html__('Active Item Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab > dt.is-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'active_tab_text_color',
			[
				'label'     => esc_html__('Active Item Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab > dt.is-active' => 'color: {{VALUE}};',
				],
				'condition' => [
					'grid_tab_item' => 'title',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __('Content Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-desc-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label'     => esc_html__('Content Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab > dd' => 'background-color: {{VALUE}};',
				],
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
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-item-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-item-title'   => 'padding-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-item-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => esc_html__('Meta', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#adb5bd',
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-meta *',
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#adb5bd',
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-subnav span:after' => 'background-color: {{VALUE}};',
				],
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
				'label'     => esc_html__('Excerpt Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_readmore' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_readmore_style');

		$this->start_controls_tab(
			'tab_readmore_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'readmore_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'readmore_spacing',
			[
				'label'   => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .bdt-post-grid-tab-readmore:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_close_button',
			[
				'label'     => esc_html__('Close Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_close' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_close_button_style');

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close:before, {{WRAPPER}} .bdt-post-grid-tab .gridtab__close:after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'close_button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-grid-tab .gridtab__close',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'close_button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'close_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid-tab .gridtab__close',
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'close_button_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close:hover::before, {{WRAPPER}} .bdt-post-grid-tab .gridtab__close:hover::after' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'close_button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'close_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-tab .gridtab__close:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// $this->start_controls_section(
		// 	'section_style_arrows',
		// 	[
		// 		'label'     => esc_html__( 'Arrows', 'bdthemes-element-pack' ),
		// 		'tab'       => Controls_Manager::TAB_STYLE,
		// 		'condition' => [
		// 			'show_arrows' => 'yes',
		// 		],
		// 	]
		// );

		// $this->start_controls_tabs( 'tabs_arrows_style' );

		// $this->start_controls_tab(
		// 	'tab_arrows_normal',
		// 	[
		// 		'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
		// 	]
		// );

		// $this->add_control(
		// 	'arrows_color',
		// 	[
		// 		'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:before, {{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:after' => 'background: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'arrows_background',
		// 	[
		// 		'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow' => 'background-color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Border::get_type(),
		// 	[
		// 		'name'        => 'arrows_border',
		// 		'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
		// 		'placeholder' => '1px',
		// 		'default'     => '1px',
		// 		'selector'    => '{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow',
		// 		'separator'   => 'before',
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'arrows_border_radius',
		// 	[
		// 		'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => [ 'px', '%' ],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		// $this->add_group_control(
		// 	Group_Control_Box_Shadow::get_type(),
		// 	[
		// 		'name'     => 'arrows_shadow',
		// 		'selector' => '{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow',
		// 	]
		// );

		// $this->add_responsive_control(
		// 	'arrows_padding',
		// 	[
		// 		'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => [ 'px', 'em', '%' ],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 		'separator' => 'before',
		// 	]
		// );

		// $this->end_controls_tab();

		// $this->start_controls_tab(
		// 	'tab_arrows_hover',
		// 	[
		// 		'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
		// 	]
		// );

		// $this->add_control(
		// 	'arrows_hover_color',
		// 	[
		// 		'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:hover::before, {{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:hover::after' => 'background: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'arrows_hover_background',
		// 	[
		// 		'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:hover' => 'background-color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->add_control(
		// 	'arrows_hover_border_color',
		// 	[
		// 		'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'condition' => [
		// 			'arrows_border_border!' => '',
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-post-grid-tab .gridtab__arrow:hover' => 'border-color: {{VALUE}};',
		// 		],
		// 	]
		// );

		// $this->end_controls_tab();

		// $this->end_controls_tabs();

		// $this->end_controls_section();
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	public function get_posts_tags() {
		$taxonomy = $this->get_settings('taxonomy');

		foreach ($this->_query->posts as $post) {
			if (!$taxonomy) {
				$post->tags = [];

				continue;
			}

			$tags = wp_get_post_terms($post->ID, $taxonomy);

			$tags_slugs = [];

			foreach ($tags as $tag) {
				$tags_slugs[$tag->term_id] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	/**
	 * Get post query builder arguments
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings();

		$args = [];
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-post-grid-tab-' . $this->get_id();

		$this->query_posts($settings['posts_per_page']);
		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->get_posts_tags();

		$this->render_header();

?>
		<dl id="<?php echo esc_attr($id); ?>" class="gridtab">
			<?php

			while ($wp_query->have_posts()) {
				$wp_query->the_post();
				$this->render_post();
			} ?>

		</dl>
		</div>
	<?php
		wp_reset_postdata();
	}

	public function render_content_image($image_id, $size) {

		$loading_img = BDTEP_ASSETS_URL . 'images/loading.svg';

		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = wp_get_attachment_image_src($image_id, $size);
	?>
		<div class="bdt-post-grid-tab-image">
			<div class="bdt-post-grid-tab-image-inner bdt-gt-mh bdt-cover-container">
				<?php
				if (!$image_src) {
					printf('<img src="%1$s" alt="%2$s" class="%2$s">', $placeholder_image_src, esc_html(get_the_title()), esc_attr($size));
				} else {
					print(wp_get_attachment_image(
						get_post_thumbnail_id(),
						$size,
						false,
						[
							'class' => esc_attr($size),
							'alt' => esc_html(get_the_title())
						]
					));
				}
				?>
			</div>
		</div>
	<?php
	}

	public function render_tab_image($image_id) {
		$settings              = $this->get_settings_for_display();

		$placeholder_image_src = Utils::get_placeholder_image_src();
		$image_src             = Group_Control_Image_Size::get_attachment_image_src($image_id, 'thumbnail', $settings);

	?>
		<div class="bdt-post-grid-tab-thumbnail" title="<?php echo get_the_title(); ?>">
			<?php
			if (!$image_src) {
				printf('<img src="%1$s" alt="%2$s">', $placeholder_image_src, esc_html(get_the_title()));
			} else {
				print(wp_get_attachment_image(
					get_post_thumbnail_id(),
					isset($settings['thumbnail_size']) ? $settings['thumbnail_size'] : 'thumbnail',
					false,
					[
						'alt' => esc_html(get_the_title())
					]
				));
			}
			?>
		</div>
	<?php
	}

	public function render_title() {
		if (!$this->get_settings('show_title')) {
			return;
		}

		$tag = $this->get_settings('title_tag');

	?>
		<a href="<?php echo get_the_permalink(); ?>">
			<<?php echo Utils::get_valid_html_tag($tag) ?> class="bdt-post-grid-tab-item-title bdt-margin-remove">
				<?php the_title() ?>
			</<?php echo Utils::get_valid_html_tag($tag) ?>>
		</a>
	<?php
	}

	public function render_tab_title() {
		echo '<div class="bdt-post-grid-tab-title">';
		the_title();
		echo '</div>';
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

		echo
		'<span class="bdt-post-grid-tab-author bdt-text-capitalize">' . get_the_author() . '</span>';
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_date']) {
			return;
		}

		echo '<span class="bdt-post-grid-tab-date">';

		if ($settings['human_diff_time'] == 'yes') {
			echo element_pack_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}

		echo '</span>';
	}

	public function render_comments() {

		if (!$this->get_settings('show_comments')) {
			return;
		}

		echo
		'<span><i class="ep-icon-bubble bdt-display-inline-block" aria-hidden="true"></i> ' . get_comments_number() . '</span>';
	}

	public function render_category() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_category')) {
			return;
		}
	?>
		<span class="bdt-post-grid-tab-category">

			<?php
			echo element_pack_get_category_list($settings['posts_source']);
			?>
		</span>
	<?php
	}


	public function render_excerpt() {
		if (!$this->get_settings('show_excerpt')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

	?>
		<div class="bdt-post-grid-tab-excerpt">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_length'), $strip_shortcode);
			}
			?>
		</div>
	<?php

	}

	public function render_readmore() {
		$settings        = $this->get_settings_for_display();

		if (!$this->get_settings('show_readmore')) {
			return;
		}

		$animation = ($this->get_settings('readmore_hover_animation')) ? ' elementor-animation-' . $this->get_settings('readmore_hover_animation') : '';

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['post_grid_tab_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

	?>

		<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-grid-tab-readmore bdt-display-inline-block <?php echo esc_attr($animation); ?>">
			<?php echo esc_html($this->get_settings('readmore_text')); ?>

			<span class="bdt-button-icon-align-<?php echo esc_attr($this->get_settings('icon_align')); ?>">

				<?php if ($is_new || $migrated) :
					Icons_Manager::render_icon($settings['post_grid_tab_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
				else : ?>
					<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
				<?php endif; ?>

			</span>
		</a>
	<?php
	}

	public function render_header($skin = 'default') {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('post-grid-tab', 'class', ['bdt-post-grid-tab', 'bdt-post-grid-tab-skin-' . $skin]);

		$columns_tablet = isset($settings["columns_tablet"]) ? esc_attr($settings["columns_tablet"]) : 3;
		$columns_mobile = isset($settings["columns_mobile"]) ? esc_attr($settings["columns_mobile"]) : 2;

		if ($this->ep_is_edit_mode()) {
			$this->add_render_attribute(
				[
					'post-grid-tab' => [
						'data-settings' => [
							wp_json_encode(array_filter([
								"grid"           => isset($settings["columns"]) ? $settings["columns"] : 4,
								"tabPadding"     => $settings["tab_padding"]["size"],
								"borderWidth"    => $settings["item_border_width"]["size"],
								"config"         => [
									"layout"     => ("title" === $settings["grid_tab_item"]) ? "tab" : "grid",
									"activeTab"  => $settings["active_tab_no"]["size"],
									"showClose"  => ($settings["show_close"]) ? true : false,
									// "showArrows" => ( $settings["show_arrows"] ) ? true : false,
								],
							]))
						]
					]
				]
			);
		} else {
			$this->add_render_attribute(
				[
					'post-grid-tab' => [
						'data-settings' => [
							wp_json_encode(array_filter([
								"grid"           => isset($settings["columns"]) ? $settings["columns"] : 4,
								"tabPadding"     => $settings["tab_padding"]["size"],
								"borderWidth"    => $settings["item_border_width"]["size"],
								"config"         => [
									"layout"     => ("title" === $settings["grid_tab_item"]) ? "tab" : "grid",
									"activeTab"  => $settings["active_tab_no"]["size"],
									"showClose"  => ($settings["show_close"]) ? true : false,
									// "showArrows" => ( $settings["show_arrows"] ) ? true : false,
								],
								"responsive" => [
									[
										"breakpoint" => 1023,
										"settings"   => [
											"grid"   => $columns_tablet,
										]
									],
									[
										"breakpoint" => 767,
										"settings"   => [
											"grid"   => $columns_mobile,
										]
									]
								]
							]))
						]
					]
				]
			);
		}

	?>
		<div <?php echo $this->get_render_attribute_string('post-grid-tab'); ?>>
		<?php
	}

	public function render_post_grid_tab_item($post_id, $image_size = 'full') {
		$settings = $this->get_settings_for_display();
		global $post;

		$this->add_render_attribute('post-grid-tab-item', 'data-bdt-grid');


		if ($settings['content_image'] and ($settings['show_title'] or $settings['show_author'] or $settings['show_date'] or $settings['show_comments'] or $settings['show_category'] or $settings['show_excerpt'] or $settings['show_readmore'])) {
			$this->add_render_attribute('post-grid-tab-item', 'class', ['bdt-post-grid-tab-item', 'bdt-grid', 'bdt-grid-collapse', 'bdt-child-width-1-2@m']);
			if ('yes' == $settings['content_reverse']) {
				$this->add_render_attribute('post-grid-tab-item', 'class', 'bdt-flex-row-reverse');
			}
		} else {
			$this->add_render_attribute('post-grid-tab-item', 'class', ['bdt-post-grid-tab-item', 'bdt-grid', 'bdt-child-width-1-1']);
		}


		?>
			<div <?php echo $this->get_render_attribute_string('post-grid-tab-item') ?>>
				<?php if ($settings['content_image']) : ?>
					<?php $this->render_content_image(get_post_thumbnail_id($post_id), $image_size); ?>
				<?php endif; ?>
				<div class="bdt-post-grid-tab-desc">
					<div class="bdt-post-grid-desc-inner bdt-gt-mh">
						<?php $this->render_title(); ?>

						<?php if ($settings['show_author'] or $settings['show_date'] or $settings['show_category'] or $settings['show_comments']) : ?>
							<div class="bdt-post-grid-tab-meta bdt-subnav bdt-flex-middle bdt-margin-small-top">
								<?php $this->render_author(); ?>
								<?php $this->render_date(); ?>
								<?php $this->render_category(); ?>
								<?php $this->render_comments(); ?>
							</div>
						<?php endif; ?>

						<?php $this->render_excerpt(); ?>
						<?php $this->render_readmore(); ?>
					</div>
				</div>
			</div>
		<?php
	}

	public function render_post() {
		global $post;
		$settings = $this->get_settings_for_display();

		?>
			<dt>
				<?php
				if ('title' === $settings['grid_tab_item']) {
					$this->render_tab_title();
				} else {
					$this->render_tab_image(get_post_thumbnail_id($post->ID), $settings['thumbnail_size']);
				}
				?>
			</dt>
			<dd><?php $this->render_post_grid_tab_item($post->ID, $settings['content_thumbnail_size']); ?></dd>
	<?php
	}
}
