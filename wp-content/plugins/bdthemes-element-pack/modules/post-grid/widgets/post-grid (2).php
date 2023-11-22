<?php

namespace ElementPack\Modules\PostGrid\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;

use ElementPack\Utils;
use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Traits\Global_Widget_Controls;
use ElementPack\Modules\PostGrid\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Grid extends Module_Base {
	use Group_Control_Query;
	use Global_Widget_Controls;

	private $_query = null;

	public function get_name() {
		return 'bdt-post-grid';
	}

	public function get_title() {
		return BDTEP . esc_html__('Post Grid', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-grid';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'grid', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-post-grid'];
		}
	}
	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-post-grid'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/z3gWwPIsCkg';
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Modern($this));
		$this->add_skin(new Skins\Skin_Elanza($this));
		$this->add_skin(new Skins\Skin_Carmie($this));
		$this->add_skin(new Skins\Skin_Trosia($this));
		$this->add_skin(new Skins\Skin_Harold($this));
		$this->add_skin(new Skins\Skin_Reverse($this));
		$this->add_skin(new Skins\Skin_Alter($this));
		$this->add_skin(new Skins\Skin_Paddle($this));
		$this->add_skin(new Skins\Skin_Alite($this));
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
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
				'condition' => [
					'_skin' => ['bdt-carmie', 'bdt-harold', 'bdt-alite', 'bdt-trosia', 'bdt-reverse'],
				],
			]
		);

		$this->add_control(
			'default_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'_skin' => '',
				],
				'default' => [
					'size' => 5,
				],
			]
		);

		$this->add_control(
			'carmie_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'_skin' => ['bdt-carmie'],
				],
				'default' => [
					'size' => 6,
				],
			]
		);

		$this->add_control(
			'harold_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'_skin' => ['bdt-harold', 'bdt-alite'],
				],
				'default' => [
					'size' => 4,
				],
			]
		);

		$this->add_control(
			'trosia_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'condition' => [
					'_skin' => 'bdt-trosia',
				],
				'default' => [
					'size' => 9,
				],
			]
		);

		$this->add_control(
			'reverse_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 21,
					],
				],
				'condition' => [
					'_skin' => 'bdt-reverse',
				],
				'default' => [
					'size' => 6,
				],
			]
		);

		$this->add_control(
			'alter_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 21,
					],
				],
				'condition' => [
					'_skin' => 'bdt-alter',
				],
				'default' => [
					'size' => 6,
				],
			]
		);

		$this->add_control(
			'paddle_item_limit',
			[
				'label' => esc_html__('Item Limit', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 21,
					],
				],
				'condition' => [
					'_skin' => 'bdt-paddle',
				],
				'default' => [
					'size' => 8,
				],
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'x-small'  => esc_html__('X-Small', 'bdthemes-element-pack'),
					'small'    => esc_html__('Small', 'bdthemes-element-pack'),
					'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
					'large'    => esc_html__('Large', 'bdthemes-element-pack'),
					'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin!' => ['bdt-reverse', 'bdt-alter']
				]
			]
		);

		$this->add_control(
			'odd_item_columns',
			[
				'label'          => esc_html__('Odd Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '2',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin' => 'bdt-paddle',
				],
			]
		);

		$this->add_control(
			'even_item_columns',
			[
				'label'          => esc_html__('Even Columns', 'bdthemes-element-pack'),
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'condition' => [
					'_skin' => 'bdt-paddle',
				],
			]
		);

		$this->add_control(
			'primary_item_hide',
			[
				'label' => esc_html__('Primary Item Hide', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-primary' => 'display: none;',
				],
				'condition' => [
					'_skin' => ['bdt-alite', 'bdt-harold'],
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'primary_item_height',
			[
				'label' => esc_html__('Primary Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-primary .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin!' => ['bdt-carmie', 'bdt-trosia', 'bdt-reverse', 'bdt-alter', 'bdt-paddle'],
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'odd_item_height',
			[
				'label' => esc_html__('Odd Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-primary .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => 'bdt-paddle',
				],
			]
		);

		$this->add_control(
			'alter_skin_image_width',
			[
				'label' => esc_html__('Thumbnail Width', 'bdthemes-element-pack'),
				'description' => esc_html__('Thumbnail width only works in desktop and tablet mode', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
					],
				],
				'condition' => [
					'_skin' => ['bdt-alter'],
				],
				'default' => [
					'size' => 50,
				],
				'selectors' => [
					'(desktop){{WRAPPER}} .bdt-post-grid-skin-alter .bdt-post-grid-item .bdt-pg-alter-image' => 'width: {{SIZE}}%',
					'(tablet){{WRAPPER}} .bdt-post-grid-skin-alter .bdt-post-grid-item .bdt-pg-alter-image' => 'width: {{SIZE}}%',
					'(mobile){{WRAPPER}} .bdt-post-grid-skin-alter .bdt-post-grid-item .bdt-pg-alter-image' => 'width: 100%',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'primary_thumbnail',
				'exclude'   => ['custom'],
				'default'   => 'full',
				'condition' => [
					'_skin' => ['', 'bdt-harold', 'bdt-elanza', 'bdt-modern', 'bdt-paddle', 'bdt-alite'],
				],
			]
		);

		$this->add_control(
			'secondary_divider',
			[
				'type'           => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'secondary_columns',
			[
				'label'          => esc_html__('Secondary Columns', 'bdthemes-element-pack') . BDTEP_NC,
				'type'           => Controls_Manager::SELECT,
				'default'        => '3',
				'tablet_default' => '3',
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
				'condition' => [
					'_skin' => ['', 'bdt-elanza'],
				],
			]
		);

		$this->add_responsive_control(
			'secondary_item_height',
			[
				'label' => esc_html__('Secondary Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-secondary .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin!' => ['bdt-carmie', 'bdt-trosia', 'bdt-reverse', 'bdt-alter', 'bdt-paddle'],
				],
			]
		);

		$this->add_responsive_control(
			'even_item_height',
			[
				'label' => esc_html__('Even Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-secondary .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => 'bdt-paddle',
				],
			]
		);

		$this->add_control(
			'secondary_grid_height',
			[
				'label'        => esc_html__('Secondary Grid Height', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'none',
				'options'      => [
					'none'         => esc_html__('None', 'bdthemes-element-pack'),
					'match-height' => esc_html__('Match Height', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => ['bdt-harold', 'bdt-alite']
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'secondary_thumbnail',
				'default'   => 'medium',
				'exclude'   => ['custom'],
				'condition' => [
					'_skin' => ['', 'bdt-harold', 'bdt-elanza', 'bdt-modern', 'bdt-paddle', 'bdt-alite'],
				],
			]
		);

		$this->add_responsive_control(
			'carmie_item_height',
			[
				'label' => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-carmie .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => 'bdt-carmie',
				],
			]
		);

		$this->add_responsive_control(
			'trosia_item_height',
			[
				'label' => esc_html__('Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-trosia .bdt-post-grid-img-wrap a' => 'height: {{SIZE}}px',
				],
				'condition' => [
					'_skin' => 'bdt-trosia',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'full',
				'exclude'   => ['custom'],
				'condition' => [
					'_skin' => ['bdt-carmie', 'bdt-trosia', 'bdt-reverse', 'bdt-alter'],
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
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
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item *' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'secondary_text_align',
			[
				'label'   => __('Secondary Alignment', 'bdthemes-element-pack') . BDTEP_NC,
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
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-secondary .bdt-post-grid-item *' => 'text-align: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => ['bdt-carmie', 'bdt-trosia', 'bdt-reverse', 'bdt-alter'],
				],
			]
		);

		$this->add_control(
			'show_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);
		$this->add_control(
			'post_grid_ajax_loadmore',
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
			'post_grid_ajax_loadmore_items',
			[
				'label' => esc_html__('Load More Items', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::NUMBER,
				'default' => 3,
				'condition' => [
					'show_pagination!' => 'yes',
					'post_grid_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				// 'frontend_available' => true,
			]
		);
		$this->add_control(
			'post_grid_show_loadmore',
			[
				'label' => esc_html__('Load More Button', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_pagination!' => 'yes',
					'post_grid_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				'default' => 'yes',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'post_grid_show_infinite_scroll',
			[
				'label' => esc_html__('Infinite Scroll', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'condition' => [
					'show_pagination!' => 'yes',
					'post_grid_show_loadmore!' => 'yes',
					'post_grid_ajax_loadmore' => 'yes',
					'_skin' => '',
				],
				'frontend_available' => true,
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

		$this->update_control(
			'posts_per_page',
			[
				'type' => Controls_Manager::HIDDEN,
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
			'show_author',
			[
				'label'   => esc_html__('Author', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
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
				'label'     => esc_html__('Comments', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				// 'condition' => [
				// 	'_skin!' => 'bdt-carmie',
				// ],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_category',
			[
				'label'   => esc_html__('Category', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_tags',
			[
				'label'   => esc_html__('Tags', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				// 'default' => 'yes',
			]
		);

		$this->add_control(
			'tags_string',
			[
				'label'       => esc_html__('Tags String', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Tags: ', 'bdthemes-element-pack'),
				'description' => esc_html__('Just leave blank if you hide this string.', 'bdthemes-element-pack'),
				'condition'   => [
					'show_tags' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label' => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'      => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 15,
				'condition' => [
					'_skin!' => ['bdt-harold', 'bdt-alite'],
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'primary_excerpt_length',
			[
				'label'      => esc_html__('Primary Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 40,
				'condition' => [
					'_skin' => ['bdt-harold', 'bdt-alite'],
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'secondary_excerpt_length',
			[
				'label'      => esc_html__('Secondary Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::NUMBER,
				'default'    => 15,
				'condition' => [
					'_skin' => ['bdt-harold', 'bdt-alite'],
					'show_excerpt' => 'yes'
				],
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
				'description' => __('Remove the shortcodes from the post description text and vice versa.', 'bdthemes-element-pack'),
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
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'_skin!' => 'bdt-carmie',
				],
				'separator' => 'before'
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
			'post_grid_icon',
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
					'post_grid_icon[value]!' => '',
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
					'post_grid_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-grid .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'global_link',
			[
				'label'        => __('Item Wrapper Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => __('Be aware! When Item Wrapper Link activated then title link and read more link will not work', 'bdthemes-element-pack'),
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//style
		$this->start_controls_section(
			'section_style_item',
			[
				'label' => esc_html__('Item', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __('Content Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				// 'condition' => [
				// 	'_skin!' => ['bdt-carmie'],
				// ],
			]
		);

		// $this->add_responsive_control(
		//     'skin_content_padding',
		//     [
		//         'label'      => __('Content Padding', 'bdthemes-element-pack'),
		//         'type'       => Controls_Manager::DIMENSIONS,
		//         'size_units' => ['px', 'em', '%'],
		//         'selectors'  => [
		//             '{{WRAPPER}} .bdt-post-grid-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		//         ],
		// 		'condition' => [
		// 			'_skin' => ['bdt-carmie'],
		// 		],
		//     ]
		// );

		$this->add_responsive_control(
			'content_margin',
			[
				'label'      => __('Content Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-desc' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => ['bdt-carmie', 'bdt-trosia'],
				],
			]
		);

		$this->add_responsive_control(
			'skin_content_margin',
			[
				'label'      => __('Content Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-desc > .bdt-position-medium ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => ['bdt-carmie', 'bdt-trosia'],
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

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-post-grid-item .bdt-custom-overlay',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Overlay Color', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'_skin!' => ['bdt-harold', 'bdt-reverse', 'bdt-alter', 'bdt-alite'],
				],
			]
		);

		$this->add_responsive_control(
			'overlay_opacity',
			[
				'label'     => __('Overlay Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-item .bdt-custom-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'_skin!' => ['bdt-harold', 'bdt-reverse', 'bdt-alter', 'bdt-alite'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'item_bg_color',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-post-grid-item',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Background Type', 'bdthemes-element-pack') . BDTEP_NC,
					],
				],
				'condition' => [
					'_skin' => ['bdt-alite'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'item_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item',
				'condition'   => [
					'_skin!' => ['bdt-reverse', 'bdt-alter'],
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'item_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_item_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'overlay_color_hover',
				'label' => esc_html__('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .bdt-post-grid-item:hover .bdt-custom-overlay',
				'fields_options' => [
					'background' => [
						'label' => esc_html__('Overlay Color', 'bdthemes-element-pack'),
					],
				],
				'condition' => [
					'_skin!' => ['bdt-harold', 'bdt-reverse', 'bdt-alter', 'bdt-alite'],
				],
			]
		);

		$this->add_responsive_control(
			'overlay_hover_opacity',
			[
				'label'     => __('Overlay Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid-item:hover .bdt-custom-overlay' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'_skin!' => ['bdt-harold', 'bdt-reverse', 'bdt-alter', 'bdt-alite'],
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow_hover',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item:hover'
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

		$this->start_controls_tabs('tabs_image_style');

		$this->start_controls_tab(
			'tab_image_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-post-grid-img-wrap a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_image_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'image_animation',
			[
				'label'   => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'scale-up',
				'options' => [
					'scale-up'   => esc_html__('Scale-Up', 'bdthemes-element-pack'),
					'scale-down' => esc_html__('Scale-Down', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-post-grid-item:hover .bdt-post-grid-img-wrap a',
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-title a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-title a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-title'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-grid .bdt-secondary .bdt-post-grid-title' => 'margin-bottom: 0;',
				],
				'condition' => [
					'_skin!' => 'bdt-carmie',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'title_typography',
				'label'     => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-title a',
				'condition' => [
					'_skin' => ['bdt-carmie', 'bdt-reverse', 'bdt-alter', 'bdt-trosia'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'        => 'primary_title_typography',
				'label'       => esc_html__('Primary Typography', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-primary .bdt-post-grid-title a',
				'condition'   => [
					'_skin!' => ['bdt-carmie', 'bdt-reverse', 'bdt-alter', 'bdt-trosia'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'        => 'secondary_title_typography',
				'label'       => esc_html__('Secondary Typography', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-secondary .bdt-post-grid-title a',
				'condition'   => [
					'_skin!' => ['bdt-carmie', 'bdt-reverse', 'bdt-alter', 'bdt-trosia'],
				],
			]
		);

		$this->add_control(
			'title_advanced_style',
			[
				'label' => esc_html__('Advanced Style', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'title_background',
				'label' => __('Background', 'bdthemes-element-pack'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_text_shadow',
				'label' => __('Text Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' 	   => 'title_border',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_border_radius',
			[
				'label'		 => __('Border Radius', 'bdthemes-element-pack'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' 	   => 'title_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a',
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'title_text_padding',
			[
				'label' 	 => __('Padding', 'bdthemes-element-pack'),
				'type' 		 => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-title a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'title_advanced_style' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_author',
			[
				'label'     => esc_html__('Author', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_author' => 'yes',
				],
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-author a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'author_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-author a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_date',
			[
				'label'     => esc_html__('Date', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'date_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'date_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-subnav span:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'date_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-date',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_comments',
			[
				'label'      => esc_html__('Comments', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'show_comments',
							'value' => 'yes',
						],
						// [
						// 	'name'     => '_skin',
						// 	'operator' => '!=',
						// 	'value'    => 'bdt-carmie',
						// ],
					],
				],
			]
		);

		$this->add_control(
			'comments_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-comments *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'comments_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-comments',
			]
		);

		// $this->add_responsive_control(
		// 	'comments_margin',
		// 	[
		// 		'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ) . BDTEP_NC,
		// 		'type'       => Controls_Manager::DIMENSIONS,
		// 		'size_units' => [ 'px', 'em', '%' ],
		// 		'selectors'  => [
		// 			'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-comments' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
		// 		],
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_category',
			[
				'label'     => esc_html__('Category', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_category' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_category_style');

		$this->start_controls_tab(
			'tab_category_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'category_border',
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a',
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-category' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'category_spacing',
			[
				'label'   => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-category' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'category_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'category_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_category_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'category_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'category_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-excerpt' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label'   => esc_html__('Top Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-excerpt' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-excerpt',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label'     => esc_html__('Meta', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => ['bdt-alite', 'bdt-harold', 'bdt-reverse', 'bdt-alter'],
				],
			]
		);

		$this->add_control(
			'meta_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-alite .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-harold .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-reverse .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-alter .bdt-post-grid-meta' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-alite .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-harold .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-reverse .bdt-post-grid-meta, {{WRAPPER}} .bdt-post-grid.bdt-post-grid-skin-alter .bdt-post-grid-meta' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore',
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'readmore_spacing',
			[
				'label'   => esc_html__('Top Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore',
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
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'readmore_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-readmore:hover' => 'border-color: {{VALUE}};',
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
			'section_style_tags',
			[
				'label'     => esc_html__('Tags', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_tags' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_tag_style');

		$this->start_controls_tab(
			'tab_tag_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tag_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'tag_border',
				'selector'    => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a',
			]
		);

		$this->add_responsive_control(
			'tag_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tag_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tag_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-grid-tags' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tag_spacing',
			[
				'label'   => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tag_typography',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tag_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_tag_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'tag_hover_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tag_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tag_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'tag_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-item .bdt-post-grid-tags .bdt-post-grid-tag li a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_skin',
			[
				'label'     => esc_html__('Skin', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => ['bdt-carmie', 'bdt-trosia'],
				],
			]
		);

		$this->add_control(
			'carmie_desc_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-desc' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-carmie',
				],
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label' => esc_html__('Blur Effect', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'condition' => [
					'_skin' => 'bdt-trosia',
				],
			]
		);

		$this->add_control(
			'overlay_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-post-grid .bdt-custom-overlay' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px); opacity: 1;'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes',
					'_skin' => 'bdt-trosia',
				]
			]
		);

		$this->add_control(
			'overlay_background',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-grid .bdt-custom-overlay' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-grid .bdt-post-grid-desc' => 'background: -webkit-linear-gradient(top, rgba(0,0,0,0) 0%,{{VALUE)}} 70%);
					                                                     background: linear-gradient(to bottom, rgba(0,0,0,0) 0%,{{VALUE)}} 70%);',
				],
				'condition' => [
					'_skin' => 'bdt-trosia',
				],
			]
		);

		$this->end_controls_section();

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


		$this->start_controls_tabs('tabs_pagination_style');

		$this->start_controls_tab(
			'tab_pagination_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pagination_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} ul.bdt-pagination li a',
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'pagination_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'    => '{{WRAPPER}} ul.bdt-pagination li a',
			]
		);

		$this->add_responsive_control(
			'pagination_offset',
			[
				'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-pagination' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-pagination' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-pagination > *' => 'padding-left: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label'     => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a' => 'padding: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_radius',
			[
				'label'     => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
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
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} ul.bdt-pagination li a, {{WRAPPER}} ul.bdt-pagination li span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pagination_hover_background',
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} ul.bdt-pagination li a:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.bdt-pagination li.bdt-active a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pagination_active_background',
				'selector' => '{{WRAPPER}} ul.bdt-pagination li.bdt-active a',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label'     => esc_html__('Description', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => ['bdt-reverse', 'bdt-alter'],
				],
			]
		);

		$this->add_control(
			'description_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-reverse .bdt-post-grid-img-wrap:after'      => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-plane .bdt-post-grid-img-wrap:after'        => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-grid-skin-reverse .bdt-post-grid-desc' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-reverse',
				],
			]
		);

		$this->add_control(
			'alter_description_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-alter .bdt-post-grid-img-wrap:after'      => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-plane .bdt-post-grid-img-wrap:after'      => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-grid-skin-alter .bdt-post-grid-desc' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'bdt-alter',
				],
			]
		);

		$this->end_controls_section();
		$this->start_controls_section(
			'section_style_loadmore',
			[
				'label'     => esc_html__('Load More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'post_grid_ajax_loadmore' => 'yes',
					'post_grid_show_loadmore' => 'yes',
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
			if ($settings['show_pagination']) { // fix query offset
				$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
			}
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}

	public function render_image($image_id, $size) {
		$placeholder_image_src = Utils::get_placeholder_image_src();

		$image_src = wp_get_attachment_image_src($image_id, $size);

		if (!$image_src) {
			$image_src = $placeholder_image_src;
		} else {
			$image_src = $image_src[0];
		}

		echo
		'<div class="bdt-post-grid-img-wrap bdt-overflow-hidden">
				<a href="' . esc_url(get_permalink()) . '" class="bdt-transition-' . esc_attr($this->get_settings('image_animation')) . ' bdt-background-cover bdt-transition-opaque bdt-flex" title="' . esc_attr(get_the_title()) . '" style="background-image: url(' . esc_url($image_src) . ')">
  				</a>
			</div>';
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_title')) {
			return;
		}

		$this->add_render_attribute('bdt-post-grid-title', 'class', 'bdt-post-grid-title');
		$titleClass = $this->get_render_attribute_string('bdt-post-grid-title');
		echo
		'<' . Utils::get_valid_html_tag($settings['title_tags']) . ' ' . $titleClass . ' >
				<a href="' . esc_url(get_permalink()) . '" class="bdt-post-grid-link" title="' . esc_attr(get_the_title()) . '">
					' . esc_html(get_the_title())  . '
				</a>
			</' . Utils::get_valid_html_tag($settings['title_tags']) . '>';
	}

	public function render_author() {

		if (!$this->get_settings('show_author')) {
			return;
		}

		echo
		'<span class="bdt-post-grid-author"><a href="' . get_author_posts_url(get_the_author_meta('ID')) . '">' . get_the_author() . '</a></span>';
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_date']) {
			return;
		}

		echo '<span class="bdt-post-grid-date">';

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
		'<div class="bdt-post-grid-comments bdt-inline-block"><span><i class="ep-icon-bubble" aria-hidden="true"></i> ' . get_comments_number() . '</span></div>';
	}

	public function render_category() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_category')) {
			return;
		}
?>
		<div class="bdt-post-grid-category bdt-position-small bdt-position-top-left">

			<?php
			echo element_pack_get_category_list($settings['posts_source']);
			?>
		</div>
	<?php
	}

	public function render_tags() {
		if (!$this->get_settings('show_tags')) {
			return;
		}
	?>

		<?php
		$tags = get_the_tags($id = 0);  ?>

		<?php if (!empty($tags)) { ?>
			<div class="bdt-post-grid-tags">
				<?php if (!empty($this->get_settings('tags_string'))) {
					echo '<div class="bdt-tags-string bdt-margin-small-right">' . esc_html($this->get_settings('tags_string')) . '</div>';
				} ?>
				<ul class="bdt-post-grid-tag">
					<?php foreach ($tags as $tag) :  ?>
						<li>
							<a href="<?php bloginfo('url'); ?>/tag/<?php echo esc_attr($tag->slug); ?>">
								<?php echo esc_html($tag->name); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php } ?>

	<?php
	}

	public function render_excerpt($excerpt_length) {

		if (!$this->get_settings('show_excerpt')) {
			return;
		}
		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');
	?>
		<div class="bdt-post-grid-excerpt">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo element_pack_custom_excerpt($excerpt_length, $strip_shortcode);
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

		$migrated  = isset($settings['__fa4_migrated']['post_grid_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

	?>

		<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-grid-readmore bdt-display-inline-block <?php echo esc_attr($animation); ?>">
			<?php echo esc_html($this->get_settings('readmore_text')); ?>

			<?php if ($settings['post_grid_icon']['value']) : ?>
				<span class="bdt-button-icon-align-<?php echo esc_attr($this->get_settings('icon_align')); ?>">

					<?php if ($is_new || $migrated) :
						Icons_Manager::render_icon($settings['post_grid_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
					else : ?>
						<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
					<?php endif; ?>

				</span>
			<?php endif; ?>
		</a>
	<?php
	}

	public function render_post_grid_item($post_id, $image_size, $excerpt_length) {
		$settings = $this->get_settings_for_display();

		if ('yes' == $settings['global_link']) {

			$this->add_render_attribute('grid-item', 'onclick', "window.open('" . esc_url(get_permalink()) . "', '_self')", true);
		}

		$this->add_render_attribute('grid-item', 'class', 'bdt-post-grid-item bdt-transition-toggle bdt-position-relative', true);

	?>
		<div <?php echo $this->get_render_attribute_string('grid-item'); ?>>

			<?php $this->render_image(get_post_thumbnail_id($post_id), $image_size); ?>

			<div class="bdt-custom-overlay bdt-position-cover"></div>

			<div class="bdt-post-grid-desc bdt-position-medium bdt-position-bottom-left">

				<?php $this->render_title(); ?>

				<?php if ($settings['show_author'] or $settings['show_date']) : ?>
					<div class="bdt-post-grid-meta bdt-subnav bdt-flex-inline bdt-flex-middle">
						<?php $this->render_author(); ?>
						<?php $this->render_date(); ?>
						<?php $this->render_comments(); ?>
						<?php $this->render_tags(); ?>
					</div>
				<?php endif; ?>

				<?php $this->render_excerpt($excerpt_length); ?>
				<?php $this->render_readmore(); ?>

			</div>

			<?php $this->render_category(); ?>

		</div>
	<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->query_posts($settings['default_item_limit']['size']);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->add_render_attribute('bdt-post-grid', 'id', 'bdt-post-grid-' . esc_html($id) . '', true);
		$this->add_render_attribute('bdt-post-grid', 'class', ['bdt-post-grid', 'bdt-post-grid-skin-default'], true);
		// $this->add_render_attribute('bdt-post-grid', 'class', ['bdt-post-grid', 'bdt-post-grid-skin-default'], true);
		// if ($settings['post_grid_show_loadmore'] == 'yes') {
		$this->add_render_attribute(
			[
				'bdt-post-grid' => [
					'data-settings' => [
						wp_json_encode(array_filter([
							'posts_source' => $settings['posts_source'],
							'posts_per_page' => $settings['default_item_limit']['size'],
							'ajax_item_load' => isset($settings['post_grid_ajax_loadmore_items']) ? $settings['post_grid_ajax_loadmore_items'] : 3,
							// 'ajax_item_load' => $settings['ajax_item_load'],
							'posts_selected_ids' => $settings['posts_selected_ids'],
							'posts_include_by' => $settings['posts_include_by'],
							'posts_include_author_ids' => $settings['posts_include_author_ids'],
							'posts_include_term_ids' => $settings['posts_include_term_ids'],
							'posts_exclude_by' => $settings['posts_exclude_by'],
							'posts_exclude_ids' => $settings['posts_exclude_ids'],
							'posts_exclude_author_ids' => $settings['posts_exclude_author_ids'],
							'posts_exclude_term_ids' => $settings['posts_exclude_term_ids'],
							'posts_offset' => $settings['posts_offset'],
							'posts_select_date' => $settings['posts_select_date'],
							'posts_date_before' => $settings['posts_date_before'],
							'posts_date_after' => $settings['posts_date_after'],
							'posts_orderby' => $settings['posts_orderby'],
							'posts_order' => $settings['posts_order'],
							'posts_ignore_sticky_posts' => $settings['posts_ignore_sticky_posts'],
							'posts_only_with_featured_image' => $settings['posts_only_with_featured_image'],
							// 'totalPages' => $totalPages,
							'show_readmore' => $settings['show_readmore'],

						]))

					]
				]
			]
		);
		// }
		if ($settings['show_readmore'] === 'yes') :
			$this->add_render_attribute(
				[
					'bdt-post-grid' => [
						'data-settings-button' => [
							wp_json_encode(array_filter([
								'post_grid_icon' => $settings['post_grid_icon'],
								'readmore_text' => $settings['readmore_text'],
								'readmore_icon_align' => $settings['icon_align'],
								'readmore_hover_animation' => $settings['readmore_hover_animation'],
							]))

						]
					]
				]
			);
		endif;

	?>
		<div <?php $this->print_render_attribute_string('bdt-post-grid'); ?>>
			<div class="bdt-grid bdt-grid-<?php echo esc_attr($settings['column_gap']); ?>" data-bdt-grid>

				<?php $bdt_count = 0;

				$secondary_columns_mobile = isset($settings['secondary_columns_mobile']) ? $settings['secondary_columns_mobile'] : 1;
				$secondary_columns_tablet = isset($settings['secondary_columns_tablet']) ? $settings['secondary_columns_tablet'] : 3;
				$secondary_columns 		 = isset($settings['secondary_columns']) ? $settings['secondary_columns'] : 3;

				while ($wp_query->have_posts()) :
					$wp_query->the_post();

					$bdt_count++;

					if ($bdt_count <= 2) {
						$bdt_grid_raw = ' bdt-width-1-2@s';
						$bdt_post_class = ' bdt-primary';
						$thumbnail_size = $settings['primary_thumbnail_size'];
					} else {
						$bdt_grid_raw   = ' bdt-width-1-' . esc_attr($secondary_columns) . '@m bdt-width-1-' . esc_attr($secondary_columns_tablet) . '@s bdt-width-1-' . esc_attr($secondary_columns_mobile);
						$bdt_post_class = ' bdt-secondary';
						$thumbnail_size = $settings['secondary_thumbnail_size'];
					}
				?>

					<div class="<?php echo esc_attr($bdt_grid_raw . $bdt_post_class); ?>">
						<?php $this->render_post_grid_item(get_the_ID(), $thumbnail_size, $settings['excerpt_length']); ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
		<?php if ($settings['post_grid_show_loadmore'] == 'yes' || $settings['post_grid_show_infinite_scroll'] == 'yes') { ?>
			<div class="bdt-loadmore-container bdt-text-center">
				<?php if ($settings['post_grid_show_infinite_scroll'] == 'yes') : ?>
					<span class="bdt-loadmore" bdt-spinner style="display:none;"></span>
				<?php else : ?>
					<span class="bdt-loadmore bdt-button bdt-button-primary"><?php esc_html_e('Load More', 'bdthemes-element-pack'); ?></span>
				<?php endif; ?>
			</div>
		<?php } ?>


		<?php if ($settings['show_pagination']) { ?>
			<div class="ep-pagination">
				<?php element_pack_post_pagination($wp_query); ?>
			</div>
<?php
		}
		wp_reset_postdata();
	}
}
