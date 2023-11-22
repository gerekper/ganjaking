<?php

namespace ElementPack\Modules\AdvancedIconBox\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use ElementPack\Utils;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Advanced_Icon_Box extends Module_Base {

	public function get_name() {
		return 'bdt-advanced-icon-box';
	}

	public function get_title() {
		return BDTEP . esc_html__('Advanced Icon Box', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-advanced-icon-box';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['advanced', 'icon', 'features', 'info', 'box'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-advanced-icon-box'];
		}
	}
	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-scripts'];
		} else {
			return ['ep-advanced-icon-box'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/IU4s5Cc6CUA';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_icon_box',
			[
				'label' => esc_html__('Icon Box', 'bdthemes-element-pack'),
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
						'icon'  => 'fas fa-star'
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
				'label'            => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'fa-solid',
				],
				'render_type'      => 'template',
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
				'label'       => esc_html__('Image Icon', 'bdthemes-element-pack'),
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
				'name'         => 'thumbnail_size',
				'default'      => 'full',
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);

		$this->add_control(
			'title_text',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Icon Box Heading', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your title', 'bdthemes-element-pack'),
				'label_block' => true,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'title_link',
			[
				'label'        => esc_html__('Title Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-title-link-'
			]
		);


		$this->add_control(
			'title_link_url',
			[
				'label'       => esc_html__('Title Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'condition'   => [
					'title_link' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_sub_title',
			[
				'label'        => esc_html__('Show Sub Title', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'sub_title_text',
			[
				'label'   => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Icon Box Sub Heading', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your sub title', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition'	  => [
					'show_sub_title'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label'        => esc_html__('Title Separator', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'description_text',
			[
				'label'   => esc_html__('Description', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Enter your description', 'bdthemes-element-pack'),
				'rows'        => 10,
			]
		);

		$this->add_control(
			'position',
			[
				'label'     => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'default'   => 'top',
				'options'   => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'top' => [
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'prefix_class' => 'elementor-position-',
				'toggle'       => false,
				'render_type' => 'template',
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

		$this->add_control(
			'icon_inline',
			[
				'label'        => esc_html__('Icon Inline', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'condition'    => [
					'position' => ['left', 'right']
				],
			]
		);

		$this->add_control(
			'icon_vertical_alignment',
			[
				'label'   => esc_html__('Icon Vertical Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top'   => [
						'title' => esc_html__('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => esc_html__('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'      => 'top',
				'toggle'       => false,
				'prefix_class' => 'elementor-vertical-align-',
				'condition'    => [
					'position' => ['left', 'right'],
					'icon_inline' => '',
				],
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
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
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_readmore',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'condition' => [
					'readmore' => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_text',
			[
				'label'       => esc_html__('Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'readmore_link',
			[
				'label'     => esc_html__('Link to', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::URL,
				'dynamic'   => [
					'active' => true,
				],
				'placeholder' => esc_html__('https://your-link.com', 'bdthemes-element-pack'),
				'default'     => [
					'url' => '#',
				],
				'condition' => [
					'readmore'       => 'yes',
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
					'onclick'        => 'yes'
				]
			]
		);

		$this->add_control(
			'advanced_readmore_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'readmore_icon',
				'condition'   => [
					'readmore'       => 'yes'
				],
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'readmore_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => esc_html__('Left', 'bdthemes-element-pack'),
					'right'  => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'advanced_readmore_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'readmore_icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'readmore_on_hover',
			[
				'label'        => esc_html__('Show on Hover', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-readmore-on-hover-',
			]
		);

		$this->add_responsive_control(
			'readmore_horizontal_offset',
			[
				'label' => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => -50,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition' => [
					'readmore_on_hover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-readmore-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'readmore_vertical_offset',
			[
				'label' => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
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
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-readmore-v-offset: {{SIZE}}px;'
				],
				'condition' => [
					'readmore_on_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => esc_html__('Button ID', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => esc_html__('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
				'description' => esc_html__('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_indicator',
			[
				'label'     => esc_html__('Indicator', 'bdthemes-element-pack'),
				'condition' => [
					'indicator' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-indicator' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'indicator_horizontal_offset',
			[
				'label' => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-indicator-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_vertical_offset',
			[
				'label' => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-indicator-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'indicator_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'devices' => ['desktop', 'tablet', 'mobile'],
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
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-indicator-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_badge',
			[
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text',
			[
				'label'       => esc_html__('Badge Text', 'bdthemes-element-pack'),
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

		$this->add_responsive_control(
			'badge_horizontal_offset',
			[
				'label' => esc_html__('Horizontal Offset', 'bdthemes-element-pack'),
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-badge-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_vertical_offset',
			[
				'label' => esc_html__('Vertical Offset', 'bdthemes-element-pack'),
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-badge-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'badge_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'devices' => ['desktop', 'tablet', 'mobile'],
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
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-badge-rotate: {{SIZE}}deg;'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'top_icon_vertical_offset',
			[
				'label' => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack'),
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
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'position' => 'top',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-top-v-offset: -{{SIZE}}px;'
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
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => 'top',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-top-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'bottom_icon_vertical_offset',
			[
				'label' => esc_html__('Icon Vertical Offset', 'bdthemes-element-pack') . BDTEP_NC,
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
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'position' => 'bottom',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-bottom-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'bottom_icon_horizontal_offset',
			[
				'label' => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack') . BDTEP_NC,
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
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => 'bottom',
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-bottom-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'left_right_icon_horizontal_offset',
			[
				'label' => esc_html__('Icon Horizontal Offset', 'bdthemes-element-pack'),
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
						'min'  => -200,
						'max'  => 200,
					],
				],
				'condition' => [
					'position' => ['left', 'right'],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-left-h-offset: {{SIZE}}px;'
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
				'default' => [
					'size' => 0,
				],
				'tablet_default' => [
					'size' => 0,
				],
				'mobile_default' => [
					'size' => 0,
				],
				'condition' => [
					'position' => ['left', 'right'],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-advanced-icon-box-icon-left-v-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_control(
			'title_size',
			[
				'label'   => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h3',
				'options' => element_pack_title_tags(),
			]
		);

		$this->add_control(
			'readmore',
			[
				'label'     => esc_html__('Read More Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'indicator',
			[
				'label' => esc_html__('Indicator', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'badge',
			[
				'label' => esc_html__('Badge', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'global_link',
			[
				'label'        => esc_html__('Global Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-global-link-',
				'description'  => esc_html__('Be aware! When Global Link activated then title link and read more link will not work', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'global_link_url',
			[
				'label'       => esc_html__('Global Link URL', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'placeholder' => 'http://your-link.com',
				'condition'   => [
					'global_link' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_icon_box',
			[
				'label'      => esc_html__('Icon/Image', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_control(
			'show_svg_icon_color',
			[
				'label'     => esc_html__('Svg Icon Color ?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_control(
			'svg_icon_fill_color',
			[
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg, {{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
					'show_svg_icon_color' => 'yes',
				],
			]
		);

		$this->add_control(
			'svg_icon_stroke_color',
			[
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg, {{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
					'show_svg_icon_color' => 'yes',
				],
			]
		);

		$this->add_control(
			'glassmorphism_effect',
			[
				'label' => esc_html__('Glassmorphism', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(esc_html__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),

			]
		);

		$this->add_control(
			'glassmorphism_blur_level',
			[
				'label'       => esc_html__('Blur Level', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'glassmorphism_effect' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap'
			]
		);

		$this->add_responsive_control(
			'icon_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'icon_radius_advanced_show!' => 'yes',
				],
			]
		);

		$this->add_control(
			'icon_radius_advanced_show',
			[
				'label' => esc_html__('Advanced Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'icon_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(esc_html__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap'     => 'border-radius: {{VALUE}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap img' => 'border-radius: {{VALUE}}; overflow: hidden;'
				],
				'condition' => [
					'icon_radius_advanced_show' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap'
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'icon_typography',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap',
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'default'   => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-position-right .bdt-ep-advanced-icon-box-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-left .bdt-ep-advanced-icon-box-icon'  => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-top .bdt-ep-advanced-icon-box-icon'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-bottom .bdt-ep-advanced-icon-box-icon'   => 'margin-top: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .bdt-ep-advanced-icon-box-icon'                  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'image_fullwidth',
			[
				'label' => esc_html__('Image Fullwidth', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'width: 100%;box-sizing: border-box;',
				],
				'condition' => [
					'icon_type' => 'image'
				]
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'image_fullwidth',
							'operator' => '==',
							'value'    => ''
						],
						[
							'name'     => 'icon_type',
							'operator' => '==',
							'value'    => 'icon'
						],
					]
				]
			]
		);

		$this->add_control(
			'rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap i, {{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap img, {{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap svg'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_background_rotate',
			[
				'label'   => esc_html__('Background Rotate', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_icon_heading',
			[
				'label'     => esc_html__('Image Effect', 'bdthemes-element-pack'),
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
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box img',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => esc_html__('Transition Duration', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-ep-advanced-icon-box img' => 'transition-duration: {{SIZE}}s',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'icon_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_hover_color',
			[
				'label'     => esc_html__('Icon Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap' => 'color: {{VALUE}};',
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
				],
			]
		);

		$this->add_control(
			'svg_icon_hover_fill_color',
			[
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg, {{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
					'show_svg_icon_color' => 'yes',
				],
			]
		);

		$this->add_control(
			'svg_icon_hover_stroke_color',
			[
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg, {{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
				'condition' => [
					'icon_type!' => 'image',
					'show_svg_icon_color' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'separator' => 'before',
				'selector'  => '{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap:after',
			]
		);

		$this->add_control(
			'icon_effect',
			[
				'label'        => esc_html__('Effect', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'bdt-icon-effect-',
				'default'      => 'none',
				'options'      => [
					'none' => esc_html__('None', 'bdthemes-element-pack'),
					'a'    => esc_html__('Effect A', 'bdthemes-element-pack'),
					'b'    => esc_html__('Effect B', 'bdthemes-element-pack'),
					'c'    => esc_html__('Effect C', 'bdthemes-element-pack'),
					'd'    => esc_html__('Effect D', 'bdthemes-element-pack'),
					'e'    => esc_html__('Effect E', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap'  => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap'
			]
		);

		$this->add_control(
			'icon_hover_rotate',
			[
				'label'   => esc_html__('Rotate', 'bdthemes-element-pack'),
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
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap i, {{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap img, {{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap svg'   => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'icon_hover_background_rotate',
			[
				'label'   => esc_html__('Background Rotate', 'bdthemes-element-pack'),
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
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'image_icon_hover_heading',
			[
				'label'     => esc_html__('Image Effect', 'bdthemes-element-pack'),
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
				'selector'  => '{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap img',
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->add_control(
			'image_opacity_hover',
			[
				'label' => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}}:hover .bdt-ep-advanced-icon-box-icon-wrap img' => 'opacity: {{SIZE}};',
				],
				'condition' => [
					'icon_type' => 'image',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_title_style');

		$this->start_controls_tab(
			'tab_title_style_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'title_bottom_space',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_style_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label' => esc_html__('Sub Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'show_sub_title'	=> 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_sub_title_style');

		$this->start_controls_tab(
			'tab_sub_title_style_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'sub_title_bottom_space',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_title_style_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sub_title_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => esc_html__('Description', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_description_style');

		$this->start_controls_tab(
			'tab_description_style_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'description_bottom_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_description_style_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'description_color_hover',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box:hover .bdt-ep-advanced-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_title_separator',
			[
				'label'     => esc_html__('Title Separator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_separator_type',
			[
				'label'     => esc_html__('Select Separator Type', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'line',
				'options'   => [
					'line'        => esc_html__('Line', 'bdthemes-element-pack'),
					'line-circle' => esc_html__('Line Circle', 'bdthemes-element-pack'),
					'line-cross'  => esc_html__('Line Cross', 'bdthemes-element-pack'),
					'line-star'   => esc_html__('Line Star', 'bdthemes-element-pack'),
					'line-dashed' => esc_html__('Line Dashed', 'bdthemes-element-pack'),
					'heart'       => esc_html__('Heart', 'bdthemes-element-pack'),
					'dashed'      => esc_html__('Dashed', 'bdthemes-element-pack'),
					'floret'      => esc_html__('Floret', 'bdthemes-element-pack'),
					'rectangle'   => esc_html__('Rectangle', 'bdthemes-element-pack'),
					'leaf'        => esc_html__('Leaf', 'bdthemes-element-pack'),
					'slash'       => esc_html__('Slash', 'bdthemes-element-pack'),
					'triangle'    => esc_html__('Triangle', 'bdthemes-element-pack'),
					'wave'        => esc_html__('Wave', 'bdthemes-element-pack'),
					'kiss-curl'   => esc_html__('Kiss Curl', 'bdthemes-element-pack'),
					'zemik'       => esc_html__('Zemik', 'bdthemes-element-pack'),
					'finest'       => esc_html__('Finest', 'bdthemes-element-pack'),
					'furrow'       => esc_html__('Furrow', 'bdthemes-element-pack'),
					'peak'         => esc_html__('Peak', 'bdthemes-element-pack'),
					'melody'       => esc_html__('Melody', 'bdthemes-element-pack'),
					'bloomstar'   => esc_html__('Bloomstar', 'bdthemes-element-pack'),
					'bobbleaf' 	  => esc_html__('Bobbleaf', 'bdthemes-element-pack'),
					'demaxa' 	  => esc_html__('Demaxa', 'bdthemes-element-pack'),
					'fill-circle' => esc_html__('Fill Circle', 'bdthemes-element-pack'),
					'finalio' 	  => esc_html__('Finalio', 'bdthemes-element-pack'),
					'jemik' 	  => esc_html__('Jemik', 'bdthemes-element-pack'),
					'separk' 	  => esc_html__('Separk', 'bdthemes-element-pack'),
					'zigzag-dot'  => esc_html__('Zigzag Dot', 'bdthemes-element-pack'),
					'zozobe' 	  => esc_html__('Zozobe', 'bdthemes-element-pack'),
				],
				'render_type' => 'template'
			]
		);

		$this->add_control(
			'divider_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'center',
				'options'     => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
				],
				'condition'   => [
					'title_separator_type!' => ['line', 'dashed', 'line-circle', 'line-cross', 'line-dashed', 'line-star', 'slash', 'rectangle', 'triangle', 'wave', 'kiss-curl', 'zemik', 'finest', 'furrow']
				],
				'render_type' => 'template'
			]
		);

		$this->add_responsive_control(
			'divider_line_align',
			[
				'label'       => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::CHOOSE,
				'toggle'      => false,
				'default'     => 'center',
				'options'     => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap' => 'text-align: {{VALUE}}; margin: 0 auto; margin-{{VALUE}}: 0;',
				],
				'condition'   => [
					'title_separator_type' => ['line', 'dashed', 'line-circle', 'line-cross', 'line-dashed', 'line-star', 'slash', 'rectangle', 'triangle', 'wave', 'kiss-curl', 'zemik', 'finest', 'furrow']
				],
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'title_separator_border_style',
			[
				'label'   => esc_html__('Separator Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__('Solid', 'bdthemes-element-pack'),
					'dotted' => esc_html__('Dotted', 'bdthemes-element-pack'),
					'dashed' => esc_html__('Dashed', 'bdthemes-element-pack'),
					'groove' => esc_html__('Groove', 'bdthemes-element-pack'),
				],
				'condition' => [
					'title_separator_type' => 'line'
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator' => 'border-top-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_separator_line_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'title_separator_type' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_separator_height',
			[
				'label' => esc_html__('Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 15,
					]
				],
				'condition' => [
					'title_separator_type' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'title_separator_width',
			[
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 300,
					]
				],
				'condition' => [
					'title_separator_type' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_separator_svg_fill_color',
			[
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'title_separator_type!' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_separator_svg_stroke_color',
			[
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'title_separator_type!' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'max_width',
			[
				'label'     => esc_html__('Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1200,
						'min' => 100,
					],
				],
				'condition' => [
					'title_separator_type!' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'line_cap',
			[
				'label'   => esc_html__('Line Cap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ep_square',
				'options' => [
					'ep_square' => esc_html__('Square', 'bdthemes-element-pack'),
					'ep_round'  => esc_html__('Rounded', 'bdthemes-element-pack'),
					'ep_butt'   => esc_html__('Butt', 'bdthemes-element-pack'),
				],
				'condition' => [
					'title_separator_type!' => 'line'
				],
			]
		);

		$this->add_responsive_control(
			'divider_svg_stroke_width',
			[
				'label'     => esc_html__('Stroke Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 10,
						'min' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap svg *' => 'stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'title_separator_type!' => 'line'
				],
			]
		);

		$this->add_responsive_control(
			'divider_crop',
			[
				'label' => esc_html__('Divider Crop', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1000,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap svg' => 'transform: scale({{SIZE}}) scale(0.01)',
				],
				'condition' => [
					'title_separator_type!' => 'line'
				],
			]
		);

		$this->add_responsive_control(
			'max_height',
			[
				'label'     => esc_html__('Match Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap svg' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'title_separator_type!' => 'line'
				],
			]
		);

		$this->add_control(
			'title_separator_spacing',
			[
				'label' => esc_html__('Separator Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-separator-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
					'readmore'       => 'yes',
				],
			]
		);

		$this->add_control(
			'readmore_attention',
			[
				'label' => esc_html__('Attention', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
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
			'readmore_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'readmore_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore'
			]
		);

		$this->add_responsive_control(
			'readmore_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore',
			]
		);

		//full width button
		$this->add_control(
			'readmore_fullwidth',
			[
				'label' => esc_html__('Full Width Button', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-ep-advanced-icon-box-readmore-fullwidth-',
				'render_type' => 'template',
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
			'readmore_hover_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore:hover',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore:hover' => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-readmore:hover',
			]
		);

		$this->add_control(
			'readmore_hover_animation',
			[
				'label' => esc_html__('Hover Animation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_indicator',
			[
				'label'     => esc_html__('Indicator', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'indicator' => 'yes',
				],
			]
		);

		$this->add_control(
			'indicator_style',
			[
				'label'   => esc_html__('Indicator Style', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'   => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2'   => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3'   => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4'   => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5'   => esc_html__('Style 5', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'indicator_fill_color',
			[
				'label'     => esc_html__('Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-indicator svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'indicator_stroke_color',
			[
				'label'     => esc_html__('Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-indicator svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_badge',
			[
				'label'     => esc_html__('Badge', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'badge_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'badge_background',
				'selector'  => '{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span',
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
				'selector'    => '{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span'
			]
		);

		$this->add_responsive_control(
			'badge_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-ep-advanced-icon-box-badge span',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional',
			[
				'label' => esc_html__('Additional', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Content Inner Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'icon_inline_spacing',
			[
				'label'     => esc_html__('Icon Inline Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'position'    => ['left', 'right'],
					'icon_inline' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-advanced-icon-box-icon-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_icon() {
		$settings  = $this->get_settings_for_display();

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-star';
		}

		$has_icon  = !empty($settings['icon']);

		$has_image = !empty($settings['image']['url']);

		if ($has_icon and 'icon' == $settings['icon_type']) {
			$this->add_render_attribute('font-icon', 'class', $settings['selected_icon']);
			$this->add_render_attribute('font-icon', 'aria-hidden', 'true');
		}
		

		if (!$has_icon && !empty($settings['selected_icon']['value'])) {
			$has_icon = true;
		}

		$migrated  = isset($settings['__fa4_migrated']['selected_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();


		?>

		<?php if ($has_icon or $has_image) : ?>
			<div class="bdt-ep-advanced-icon-box-icon">
				<span class="bdt-ep-advanced-icon-box-icon-wrap">


					<?php if ($has_icon and 'icon' == $settings['icon_type']) { ?>

						<?php if ($is_new || $migrated) :
							Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
						else : ?>
							<i <?php echo $this->get_render_attribute_string('font-icon'); ?>></i>
						<?php endif; ?>


					<?php } elseif ($has_image and 'image' == $settings['icon_type']) { 

						$thumb_url = Group_Control_Image_Size::get_attachment_image_src($settings['image']['id'], 'thumbnail_size', $settings);
						if (!$thumb_url) {
						printf('<img src="%1$s" alt="%2$s">', $settings['image']['url'], esc_html($settings['title_text']));
						} else {
							print(wp_get_attachment_image(
								$settings['image']['id'],
								$settings['thumbnail_size_size'],
								false,
								[
									'alt' => esc_html($settings['title_text'])
								]
							));
						}

					} ?>
				</span>
			</div>
		<?php endif; ?>

	<?php
	}

	protected function render_icon_heading() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('advanced-icon-box-title', 'class', 'bdt-ep-advanced-icon-box-title');

		if ('yes' == $settings['icon_inline']) {
			$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-ep-advanced-icon-box-icon-heading bdt-flex bdt-flex-middle');
		}
		if ('right' == $settings['position']) {
			$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-flex-row-reverse');
		}

		$this->add_render_attribute('advanced-icon-box-sub-title', 'class', 'bdt-ep-advanced-icon-box-sub-title');

		if ('yes' == $settings['title_link'] and $settings['title_link_url']['url']) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
		}


		?>
		<div <?php echo $this->get_render_attribute_string('advanced-icon-box-icon-heading'); ?>>

			<?php $this->render_icon(); ?>


			<div class="bdt-icon-box-title-wrapper">

				<?php if ($settings['title_text']) : ?>
					<<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> <?php echo $this->get_render_attribute_string('advanced-icon-box-title'); ?>>
						<span <?php echo $this->get_render_attribute_string('title_text'); ?>>
							<?php echo wp_kses($settings['title_text'], element_pack_allow_tags('title')); ?>
						</span>
					</<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
				<?php endif; ?>


				<?php if ('yes' == $settings['show_sub_title']) : ?>
					<div <?php echo $this->get_render_attribute_string('advanced-icon-box-sub-title'); ?>>
						<?php echo wp_kses($settings['sub_title_text'], element_pack_allow_tags('title')); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php

	}

	protected function render_heading() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('advanced-icon-box-title', 'class', 'bdt-ep-advanced-icon-box-title');

		$this->add_render_attribute('advanced-icon-box-sub-title', 'class', 'bdt-ep-advanced-icon-box-sub-title');

		if ('yes' == $settings['title_link'] and $settings['title_link_url']['url']) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
		}
	?>

		<?php if ($settings['title_text']) : ?>
			<<?php echo Utils::get_valid_html_tag($settings['title_size']); ?> <?php echo $this->get_render_attribute_string('advanced-icon-box-title'); ?>>
				<span <?php echo $this->get_render_attribute_string('title_text'); ?>>
					<?php echo wp_kses($settings['title_text'], element_pack_allow_tags('title')); ?>
				</span>
			</<?php echo Utils::get_valid_html_tag($settings['title_size']); ?>>
		<?php endif; ?>


		<?php if ('yes' == $settings['show_sub_title']) : ?>
			<div <?php echo $this->get_render_attribute_string('advanced-icon-box-sub-title'); ?>>
				<?php echo wp_kses($settings['sub_title_text'], element_pack_allow_tags('title')); ?>
			</div>
		<?php endif; ?>

	<?php

	}

	public function render_svg_image() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('svg-image', 'class', 'bdt-animation-stroke');
		$this->add_render_attribute('svg-image', 'bdt-svg', 'stroke-animation: true;');

		$align     = ('left' == $settings['divider_align'] or 'right' == $settings['divider_align']) ? '-' . $settings['divider_align'] : '';
		$svg_image = BDTEP_ASSETS_URL . 'images/divider/' . $settings['title_separator_type'] . $align . '.svg';

		$line_cap = $settings['line_cap'];

	?>

		<img class="bdt-animation-stroke <?php echo esc_attr($line_cap); ?>" src="<?php echo $svg_image; ?>" alt="advanced divider">

	<?php
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('description_text', 'class', 'bdt-ep-advanced-icon-box-description');

		$this->add_inline_editing_attributes('title_text', 'none');
		$this->add_inline_editing_attributes('description_text');


		$this->add_render_attribute('readmore', 'class', ['bdt-ep-advanced-icon-box-readmore', 'bdt-display-inline-block']);

		if (!empty($settings['readmore_link']['url'])) {
			$this->add_link_attributes( 'readmore', $settings['readmore_link'] );
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

		if (!empty($settings['button_css_id'])) {
			$this->add_render_attribute('readmore', 'id', $settings['button_css_id']);
		}

		$this->add_render_attribute('advanced-icon-box', 'class', 'bdt-ep-advanced-icon-box');

		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')");
		}


		if (!isset($settings['readmore_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['readmore_icon'] = 'fas fa-arrow-right';
		}

		$readmore_migrated  = isset($settings['__fa4_migrated']['advanced_readmore_icon']);
		$readmore_is_new    = empty($settings['readmore_icon']) && Icons_Manager::is_migration_allowed();

	?>
		<div <?php echo $this->get_render_attribute_string('advanced-icon-box'); ?>>

			<?php if ('' == $settings['icon_inline']) : ?>
				<?php $this->render_icon(); ?>
			<?php endif; ?>

			<div class="bdt-ep-advanced-icon-box-content">

				<?php if ('yes' == $settings['icon_inline']) : ?>
					<?php $this->render_icon_heading(); ?>
				<?php else : ?>
					<?php $this->render_heading(); ?>
				<?php endif; ?>

				<?php if ($settings['show_separator']) : ?>

					<?php if ('line' == $settings['title_separator_type']) : ?>
						<div class="bdt-ep-advanced-icon-box-separator-wrap">
							<div class="bdt-ep-advanced-icon-box-separator"></div>
						</div>
					<?php elseif ('line' != $settings['title_separator_type']) : ?>
						<div class="bdt-ep-advanced-icon-box-separator-wrap">
							<?php $this->render_svg_image(); ?>
						</div>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ($settings['description_text']) : ?>
					<div <?php echo $this->get_render_attribute_string('description_text'); ?>>
						<?php echo $this->parse_text_editor($settings['description_text']); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['readmore']) : ?>
					<a <?php echo $this->get_render_attribute_string('readmore'); ?>>
						<?php echo esc_html($settings['readmore_text']); ?>

						<?php if ($settings['advanced_readmore_icon']['value']) : ?>

							<span class="bdt-button-icon-align-<?php echo $settings['readmore_icon_align'] ?>">

								<?php if ($readmore_is_new || $readmore_migrated) :
									Icons_Manager::render_icon($settings['advanced_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
								else : ?>
									<i <?php echo $this->get_render_attribute_string('font-icon'); ?>></i>
								<?php endif; ?>

							</span>

						<?php endif; ?>
					</a>
				<?php endif ?>
			</div>
		</div>

		<?php if ($settings['indicator']) : ?>
			<div class="bdt-ep-advanced-icon-box-indicator bdt-svg-style-<?php echo esc_attr($settings['indicator_style']); ?>">
				<?php echo element_pack_svg_icon('arrow-' . $settings['indicator_style']); ?>
			</div>
		<?php endif; ?>

		<?php if ($settings['badge'] and '' != $settings['badge_text']) : ?>
			<div class="bdt-ep-advanced-icon-box-badge bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
				<span class="bdt-badge bdt-padding-small"><?php echo esc_html($settings['badge_text']); ?></span>
			</div>
		<?php endif; ?>

<?php
	}
}
