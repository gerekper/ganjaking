<?php

namespace ElementPack\Modules\FancyCard\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use ElementPack\Utils;

use ElementPack\Modules\FancyCard\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Fancy_Card extends Module_Base {

	public function get_name() {
		return 'bdt-fancy-card';
	}

	public function get_title() {
		return BDTEP . esc_html__('Fancy Card', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-fancy-card';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['fancy', 'advanced', 'icon', 'features', 'card'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-fancy-card'];
		}
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Stack($this));
		$this->add_skin(new Skins\Skin_Batty($this));
		$this->add_skin(new Skins\Skin_Climax($this));
		$this->add_skin(new Skins\Skin_Flux($this));
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/BXdVB1pLfXE';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_icon_box',
			[
				'label' => __('Icon Box', 'bdthemes-element-pack'),
			]
		);


		$this->add_control(
			'icon_type',
			[
				'label'        => esc_html__('Icon Type', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::CHOOSE,
				'toggle'       => false,
				'default'      => 'icon',
				'prefix_class' => 'bdt-icon-type-',
				'render_type'  => 'template',
				'options'      => [
					'icon' => [
						'title' => esc_html__('Icon', 'bdthemes-element-pack'),
						'icon'  => 'far fa-laugh-wink'
					],
					'image' => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'far fa-image'
					]
				]
			]
		);

		$this->add_control(
			'selected_icon',
			[
				'label'            => __('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-laugh',
					'library' => 'fa-regular',
				],
				'condition'        => [
					'icon_type' => 'icon',
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'image',
			[
				'label'       => __('Image Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::MEDIA,
				'render_type' => 'template',
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'label'     => __('Thumbnail Size', 'bdthemes-element-pack') . BDTEP_NC,
				'exclude'   => ['custom'],
				'default'   => 'full',
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => __('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('Laugh', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_link',
			[
				'label'        => __('Title Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-fancy-card-title-link-'
			]
		);


		$this->add_control(
			'title_link_url',
			[
				'label'       => __('Title Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'condition'   => [
					'title_link' => 'yes'
				]
			]
		);

		$this->add_control(
			'description_text',
			[
				'label'   => __('Description', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('Click edit button to change this text. If you are going to use you need to be sure there text.', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your description', 'bdthemes-element-pack'),
				'rows'        => 10,
				'separator'   => 'before',
			]
		);


		$this->add_responsive_control(
			'text_align',
			[
				'label'   => __('Text Alignment', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_align',
			[
				'label'   => __('Icon Alignment', 'bdthemes-element-pack') . BDTEP_NC,
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
					'flex-end' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon' => 'justify-content: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'flux',
				],
			]
		);

		$this->add_control(
			'fancy_card_icon_position',
			[
				'label'   => __('Fancy Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''   => __('Default', 'bdthemes-element-pack'),
					'left'   => __('Left', 'bdthemes-element-pack'),
					'right'  => __('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'fancy_card_icon_style',
			[
				'label'   => __('Select Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style1',
				'options' => [
					'style1'   => __('Style 01', 'bdthemes-element-pack'),
					'style2'   => __('Style 02', 'bdthemes-element-pack'),
					'style3'   => __('Style 03', 'bdthemes-element-pack'),
					'style4'   => __('Style 04', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin!' => ['', 'batty', 'climax', 'flux'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label'     => __('Read More', 'bdthemes-element-pack'),
				'condition' => [
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => __('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => __('Read More', 'bdthemes-element-pack'),
				'placeholder' => __('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_link',
			[
				'label'     => __('Link to', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::URL,
				'dynamic'   => [
					'active' => true,
				],
				'placeholder' => __('https://your-link.com', 'bdthemes-element-pack'),
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'readmore'       => 'yes',
					//'readmore_text!' => '',
				]
			]
		);

		$this->add_control(
			'onclick',
			[
				'label'     => esc_html__('OnClick', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'readmore'       => 'yes',
					//'readmore_text!' => '',
				]
			]
		);

		$this->add_control(
			'onclick_event',
			[
				'label'       => esc_html__('OnClick Event', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf(esc_html__('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp'),
				'condition' => [
					'readmore'       => 'yes',
					//'readmore_text!' => '',
					'onclick'        => 'yes'
				]
			]
		);

		$this->add_control(
			'advanced_readmore_icon',
			[
				'label'       => __('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'label_block' => false,
				'condition'   => [
					'readmore'       => 'yes'
				],
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'readmore_icon_align',
			[
				'label'   => __('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __('Left', 'bdthemes-element-pack'),
					'right'  => __('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'advanced_readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'readmore_icon_indent',
			[
				'label' => __('Icon Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 8,
				],
				'condition' => [
					'advanced_readmore_icon[value]!' => '',
					'readmore_text!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-fancy-card-readmore .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_indicator',
			[
				'label'     => __('Indicator', 'bdthemes-element-pack'),
				'condition' => [
					'indicator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_width',
			[
				'label' => __('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-indicator-svg' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'indicator_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'indicator_horizontal_offset',
			[
				'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'indicator_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-indicator-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_vertical_offset',
			[
				'label' => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'indicator_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-indicator-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'indicator_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-indicator-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_badge',
			[
				'label'     => __('Badge', 'bdthemes-element-pack'),
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label'       => __('Badge Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'POPULAR',
				'placeholder' => 'Type Badge Title',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'badge_position',
			[
				'label'   => esc_html__('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top-right',
				'options' => element_pack_position(),
			]
		);

		$this->add_control(
			'badge_offset_toggle',
			[
				'label' => __('Offset', 'bdthemes-element-pack'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __('None', 'bdthemes-element-pack'),
				'label_on' => __('Custom', 'bdthemes-element-pack'),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[
				'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-badge-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[
				'label' => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -300,
						'step' => 1,
						'max'  => 300,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-badge-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min'  => -360,
						'max'  => 360,
						'step' => 5,
					],
				],
				'condition' => [
					'badge_offset_toggle' => 'yes'
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}}' => '--ep-badge-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => __('Additional Options', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'top_icon_vertical_offset',
			[
				'label' => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'position' => 'top',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-icon' => 'margin-top: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'top_icon_horizontal_offset',
			[
				'label' => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => 'top',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-icon' => 'transform: translateX({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'left_icon_horizontal_offset',
			[
				'label' => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => 'left',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-icon' => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'right_icon_horizontal_offset',
			[
				'label' => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => 'right',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-icon' => 'margin-right: -{{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'left_right_icon_vertical_offset',
			[
				'label' => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition' => [
					'position' => ['left', 'right'],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-icon' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'title_size',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->add_control(
			'readmore',
			[
				'label'     => __('Read More Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'toggle_icon',
			[
				'label'            => __('Toggle Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'fa-solid',
				],
				'condition' => [
					'_skin' => 'climax'
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'toggle_position',
			[
				'label'   => __('Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom-right',
				'options' => [
					'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
					'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
				],
				'separator' => 'after',
				'condition' => [
					'_skin' => 'climax'
				]
			]
		);

		$this->add_control(
			'show_data_label',
			[
				'label' => __('Show Data Label', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes'
			]
		);

		$this->add_control(
			'indicator',
			[
				'label' => __('Indicator', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'badge',
			[
				'label' => __('Badge', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'global_link',
			[
				'label'        => __('Global Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => __('Be aware! When Global Link activated then title link and read more link will not work', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'global_link_url',
			[
				'label'       => __('Global Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'condition'   => [
					'global_link' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_icon_box',
			[
				'label'      => __('Icon/Image', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'selected_icon[value]',
							'operator' => '!=',
							'value'    => ''
						],
						[
							'name'     => 'image[url]',
							'operator' => '!=',
							'value'    => ''
						],
					]
				]
			]
		);

		$this->start_controls_tabs('icon_colors');

		$this->start_controls_tab(
			'icon_colors_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'glassmorphism_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'condition' => [
					'_skin' => '',
					'fancy_card_icon_position' => ['left', 'right']
				],

			]
		);

		$this->add_control(
			'glassmorphism_blur_level',
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
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'_skin' => '',
					'glassmorphism_effect' => 'yes',
					'fancy_card_icon_position' => ['left', 'right']
				]
			]
		);

		$this->add_control(
			'icon_primary_color',
			[
				'label'     => __('Primary Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_primary_background',
				'label'     => __('Primary Background', 'bdthemes-element-pack'),
				'types'     => ['gradient'],
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-batty .bdt-batty-face.bdt-batty-face2',
				'condition' => [
					'_skin' => 'batty',
				],
			]
		);

		$this->add_responsive_control(
			'icon_primary_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				],
			]
		);

		$this->add_control(
			'icon_heading',
			[
				'label'     => __('Icon/Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner',

			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner'
			]
		);

		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'icon_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_radius_advanced_show',
			[
				'label' => __('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_responsive_control(
			'icon_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner'     => 'border-radius: {{VALUE}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner img' => 'border-radius: {{VALUE}}; overflow: hidden;'
				],
				'condition' => [
					'icon_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner',
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_control(
			'image_fullwidth',
			[
				'label' => __('Image Fullwidth', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner' => 'width: 100%; box-sizing: border-box;',
				],
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);

		$this->add_responsive_control(
			'card_image_size',
			[
				'label'   => __('Image Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'image_fullwidth' => '',
					'icon_type' => 'image'
				]
			]
		);

		$this->add_control(
			'rotate',
			[
				'label'   => __('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner i, {{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner svg, {{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner img' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_background_rotate',
			[
				'label'   => __('Background Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon-inner' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_icon_heading',
			[
				'label'     => __('Image Effect', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card img',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.3,
				],
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card img' => 'transition-duration: {{SIZE}}s',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'label_heading',
			[
				'label'     => __('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'_skin!' => ['batty']
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => ['batty']
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'label_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-icon:before',
				'condition' => [
					'_skin!' => ['batty']
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_primary_hover_color',
			[
				'label'     => __('Primary Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon' => 'background: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_primary_background',
				'label'     => __('Primary Background', 'bdthemes-element-pack'),
				'types'     => ['gradient'],
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-batty:hover .bdt-batty-face.bdt-batty-face2',
				'condition' => [
					'_skin' => 'batty',
				],
			]
		);

		$this->add_responsive_control(
			'icon_primary_hover_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				],
			]
		);

		$this->add_control(
			'icon_hover_heading',
			[
				'label'     => __('Icon/Image', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator'  => 'before',
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => __('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner',
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'icon_hover_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner, {{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_hover_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner',
				'condition' => [
					'icon_type!' => 'image',
					'_skin'		 => 'batty',
				],
			]
		);

		$this->add_control(
			'icon_hover_rotate',
			[
				'label'   => __('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner i, {{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner svg, {{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner img'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_hover_background_rotate',
			[
				'label'   => __('Background Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'deg',
				],
				'range' => [
					'deg' => [
						'max'  => 360,
						'min'  => -360,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_icon_hover_heading',
			[
				'label'     => __('Image Effect', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'      => 'css_filters_hover',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner img',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon-inner img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'label_hover_heading',
			[
				'label'     => __('Label', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'_skin!' => 'batty'
				],
			]
		);

		$this->add_control(
			'label_hover_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'batty'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'label_hover_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-icon:before',
				'condition' => [
					'_skin!' => 'batty'
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => __('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_title_style');

		$this->start_controls_tab(
			'tab_title_style_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'batty',
				]
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-content .bdt-ep-fancy-card-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card-content .bdt-ep-fancy-card-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_style_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'batty',
				]


			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-content .bdt-ep-fancy-card-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-content .bdt-ep-fancy-card-title',
				'condition' => [
					'_skin!' => 'batty',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => __('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_description_style');

		$this->start_controls_tab(
			'tab_description_style_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'batty'
				]

			]
		);

		$this->add_responsive_control(
			'description_bottom_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-content .bdt-ep-fancy-card-text' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-content .bdt-ep-fancy-card-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card-content .bdt-ep-fancy-card-text',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_description_style_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'_skin!' => 'batty'
				]
			]
		);

		$this->add_control(
			'description_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-content .bdt-ep-fancy-card-text' => 'color: {{VALUE}};',
				],
				'condition' => [
					'_skin!' => 'batty',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card:hover .bdt-ep-fancy-card-content .bdt-ep-fancy-card-text',
				'condition' => [
					'_skin!' => 'batty',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_readmore',
			[
				'label'     => __('Read More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'readmore'       => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_attention',
			[
				'label' => __('Attention', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->start_controls_tabs('tabs_readmore_style');

		$this->start_controls_tab(
			'tab_readmore_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_text_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'separator'   => 'before',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_readmore_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_hover_text_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore:hover',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'readmore_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content .bdt-ep-fancy-card-readmore:hover',
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => __('Hover Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_toggle',
			[
				'label'     => __('Toggle Icon', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin'       => 'climax',
				],
			]
		);

		$this->add_control(
			'icon_active_background_color',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole' => 'box-shadow: 0 0 0 1920px {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole' => 'box-shadow: 0 0 0 0 {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_toggle_style');

		$this->start_controls_tab(
			'tab_toggle_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'toggle_icon_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'toggle_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'toggle_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole'
			]
		);

		$this->add_responsive_control(
			'toggle_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_padding',
			[
				'label' => __('Padding', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 20,
						'step' => 1,
						'max'  => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input[type="checkbox"], {{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'toggle_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_active',
			[
				'label' => __('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'toggle_active_text_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_active_shadow_color',
			[
				'label'     => __('Toggle Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax .bdt-ep-fancy-card-toggole' => 'box-shadow: 0 0 0 0 {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole' => 'box-shadow: 0 0 0 1920px {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'toggle_active_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole',
			]
		);

		$this->add_control(
			'toggle_active_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-climax input:checked~.bdt-ep-fancy-card-toggole' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'toggle_border_border!' => ''
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_indicator',
			[
				'label'     => __('Indicator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'indicator' => 'yes',
				],
			]
		);

		$this->add_control(
			'indicator_style',
			[
				'label'   => __('Indicator Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'   => __('Style 1', 'bdthemes-element-pack'),
					'2'   => __('Style 2', 'bdthemes-element-pack'),
					'3'   => __('Style 3', 'bdthemes-element-pack'),
					'4'   => __('Style 4', 'bdthemes-element-pack'),
					'5'   => __('Style 5', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'indicator_fill_color',
			[
				'label'     => __('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-indicator-svg svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_stroke_color',
			[
				'label'     => __('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-indicator-svg svg' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => __('Badge', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card-badge span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'badge_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card-badge span',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'badge_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-card-badge span'
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card-badge span',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card-badge span',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional',
			[
				'label' => __('Additional', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => __('Inner Item', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->start_controls_tabs('tabs_content_style');

		$this->start_controls_tab(
			'tab_content_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card',
				'condition' => [
					'_skin' => ['', 'flux'],
				],
			]
		);

		$this->add_control(
			'skin_content_background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-stack .bdt-ep-fancy-card-content-overlay:before'  => 'background: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-fancy-card.bdt-ep-fancy-card-batty'  => 'background: {{VALUE}} !important;',
				],
				'condition' => [
					'_skin!' => ['', 'climax', 'flux'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'selector'    => '{{WRAPPER}} .bdt-ep-fancy-card'
			]
		);

		$this->add_responsive_control(
			'content_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card, {{WRAPPER}} .bdt-ep-fancy-card-batty',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_content_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-fancy-card:hover',
				'condition' => [
					'_skin' => ['', 'flux'],
				],
			]
		);

		$this->add_control(
			'content_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-fancy-card:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'content_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-fancy-card:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Content Inner Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-fancy-card .bdt-ep-fancy-card-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	public function render_icon() {
		$settings  = $this->get_settings_for_display();

		$has_icon  = !empty($settings['icon']);
		$has_image = !empty($settings['image']['url']);

		if ($has_icon and 'icon' == $settings['icon_type']) {
			$this->add_render_attribute('font-icon', 'class', $settings['selected_icon']);
			$this->add_render_attribute('font-icon', 'aria-hidden', 'true');
		}

		if (!$has_icon && !empty($settings['selected_icon']['value'])) {
			$has_icon = true;
		}

		if ($settings['show_data_label']) {
			$data_label = $settings['title_text'];
		} else {
			$data_label = '';
		}

?>

		<?php if ($has_icon or $has_image) : ?>
			<div class="bdt-ep-fancy-card-icon" data-label="<?php echo esc_attr($data_label); ?>">
				<span class="bdt-ep-fancy-card-icon-inner">
					<?php if ($has_icon and 'icon' == $settings['icon_type']) { ?>

						<?php Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']); ?>

					<?php } elseif ($has_image and 'image' == $settings['icon_type']) { ?>
						<!-- <img <?php //echo $this->get_render_attribute_string( 'image-icon' ); 
									?>> -->
						<?php
						if ($has_image and 'image' == $settings['icon_type']) {
							// $this->add_render_attribute( 'image-icon', 'src', $settings['image']['url'] );
							// $this->add_render_attribute( 'image-icon', 'alt', $settings['title_text'] );
							$thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail', $settings);
							if (!$thumb_url) {
								printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['title_text']));
							} else {
								print(wp_get_attachment_image(
									$settings['image']['id'],
									'full',
									$settings['thumbnail_size'],
									[
										'alt' => esc_html($settings['title_text'])
									]
								));
							}
						}
						?>
					<?php } ?>
				</span>
			</div>
		<?php endif;
	}

	public function render_title() {
		$settings  = $this->get_settings_for_display();

		$this->add_inline_editing_attributes('title_text', 'none');

		$this->add_render_attribute('fancy-card-title', 'class', 'bdt-ep-fancy-card-title');

		if ('yes' == $settings['title_link'] and $settings['title_link_url']['url']) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('fancy-card-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
		}

		?>

		<?php if ($settings['title_text']) : ?>
			<<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> <?php echo $this->get_render_attribute_string('fancy-card-title'); ?>>
				<span <?php echo $this->get_render_attribute_string('title_text'); ?>>
					<?php echo wp_kses($settings['title_text'], element_pack_allow_tags('title')); ?>
				</span>
			</<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
		<?php endif; ?>

	<?php
	}

	public function render_text() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('description_text', 'class', 'bdt-ep-fancy-card-text');
		$this->add_inline_editing_attributes('description_text');

	?>
		<?php if ($settings['description_text']) : ?>
			<div <?php echo $this->get_render_attribute_string('description_text'); ?>>
				<?php echo wp_kses($settings['description_text'], element_pack_allow_tags('text')); ?>
			</div>
		<?php endif; ?>

	<?php
	}

	public function render_readmore() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('readmore', 'class', ['bdt-ep-fancy-card-readmore', 'bdt-display-inline-block']);

		if (!empty($settings['readmore_link']['url'])) {
			$this->add_render_attribute('readmore', 'href', $settings['readmore_link']['url']);

			if ($settings['readmore_link']['is_external']) {
				$this->add_render_attribute('readmore', 'target', '_blank');
			}

			if ($settings['readmore_link']['nofollow']) {
				$this->add_render_attribute('readmore', 'rel', 'nofollow');
			}
		}

		if ($settings['readmore_attention']) {
			$this->add_render_attribute('readmore', 'class', 'bdt-ep-attention-button');
		}

		if ($settings['readmore_hover_animation']) {
			$this->add_render_attribute('readmore', 'class', 'elementor-animation-' . $settings['readmore_hover_animation']);
		}

		if ($settings['onclick']) {
			$this->add_render_attribute('readmore', 'onclick', $settings['onclick_event']);
		}

	?>
		<?php if ($settings['readmore']) : ?>
			<?php if ($settings['_skin'] == 'flux') : ?>
				<div>
				<?php endif; ?>
				<a <?php echo $this->get_render_attribute_string('readmore'); ?>>
					<?php echo esc_html($settings['readmore_text']); ?>
					<?php if ($settings['advanced_readmore_icon']['value']) : ?>
						<span class="bdt-button-icon-align-<?php echo esc_attr($settings['readmore_icon_align']); ?>">
							<?php Icons_Manager::render_icon($settings['advanced_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>
						</span>
					<?php endif; ?>
				</a>
				<?php if ($settings['_skin'] == 'flux') : ?>
				</div>
			<?php endif; ?>
		<?php endif;
	}

	public function render_indicator() {
		$settings  = $this->get_settings_for_display();

		if (!$this->get_settings('indicator')) {
			return;
		}

		?>
		<?php if ($settings['indicator']) : ?>
			<div class="bdt-indicator-svg bdt-svg-style-<?php echo esc_attr($settings['indicator_style']); ?>">
				<?php echo element_pack_svg_icon('arrow-' . $settings['indicator_style']); ?>
			</div>
		<?php endif;
	}

	public function render_badge() {
		$settings  = $this->get_settings_for_display();

		if (!$this->get_settings('badge')) {
			return;
		}

		?>
		<?php if ($settings['badge'] and '' != $settings['badge_text']) : ?>
			<div class="bdt-ep-fancy-card-badge bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
				<span class="bdt-badge bdt-padding-small"><?php echo esc_html($settings['badge_text']); ?></span>
			</div>
		<?php endif;
	}

	public function render() {
		$settings  = $this->get_settings_for_display();

		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('fancy-card', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')");
		}

		if ('left' == $settings['fancy_card_icon_position']) {
			$this->add_render_attribute('fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-default bdt-ep-fancy-card-icon-left');
		} elseif ('right' == $settings['fancy_card_icon_position']) {
			$this->add_render_attribute('fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-default bdt-ep-fancy-card-icon-right');
		} elseif ('' == $settings['fancy_card_icon_position']) {
			$this->add_render_attribute('fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-default bdt-ep-fancy-card-icon-default');
		} else {
			$this->add_render_attribute('fancy-card', 'class', 'bdt-ep-fancy-card bdt-ep-fancy-card-default');
		}

		?>
		<div <?php echo $this->get_render_attribute_string('fancy-card'); ?>>

			<?php $this->render_icon(); ?>

			<div class="bdt-ep-fancy-card-content">
				<?php $this->render_title(); ?>
				<?php $this->render_text(); ?>
				<?php $this->render_readmore(); ?>
			</div>
		</div>

		<?php $this->render_indicator(); ?>
		<?php $this->render_badge(); ?>

<?php
	}
}
