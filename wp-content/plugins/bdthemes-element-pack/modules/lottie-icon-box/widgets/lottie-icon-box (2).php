<?php

namespace ElementPack\Modules\LottieIconBox\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Css_Filter;
use Elementor\Icons_Manager;
use ElementPack\Utils;

use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Lottie_Icon_Box extends Module_Base {

	public function get_name() {
		return 'bdt-lottie-icon-box';
	}

	public function get_title() {
		return BDTEP . esc_html__('Lottie Icon Box', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-lottie-icon-box';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['advanced', 'icon', 'features', 'lottie', 'box', 'animation', 'bodymovin', 'transition', 'image', 'svg'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-lottie-icon-box'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['lottie', 'ep-scripts'];
		} else {
			return ['lottie', 'ep-lottie-icon-box'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/1jKFSglW6qE';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'lottie_json_source',
			[
				'label'   => __('Select JSON Source', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'url',
				'options' => [
					'url'    => __('Load From URL', 'bdthemes-element-pack'),
					'local'  => __('Self Hosted', 'bdthemes-element-pack'),
					'custom' => __('Custom JSON Code', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'lottie_json_path',
			[
				'label'         => __('Lottie JSON URL', 'bdthemes-element-pack'),
				'description'   => sprintf(__('Enter your lottie josn file, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
				'type'          => Controls_Manager::TEXT,
				'autocomplete'  => false,
				'show_external' => false,
				'label_block'   => true,
				'show_label'    => false,
				'default'       => BDTEP_ASSETS_URL . 'others/rocket-space.json',
				'placeholder'   => __('Enter your json URL', 'bdthemes-element-pack'),
				'condition'     => [
					'lottie_json_source' => 'url',
				],
				'dynamic'       => [
					'active'     => true,
				],

			]
		);

		$this->add_control(
			'upload_json_file',
			[
				'label'       => __('Select JSON File', 'bdthemes-element-pack'),
				'type'        => 'json-upload',
				'label_block' => true,
				'show_label'  => true,
				//'callback_selector'=>'lottie_json_path',
				'condition'     => [
					'lottie_json_source' => 'local',
				],
				'dynamic'       => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'lottie_json_code',
			[
				'label'         => __('Paste JSON Code', 'bdthemes-element-pack'),
				'description'   => sprintf(__('Enter your lottie josn text, if you don\'t understand lottie json file so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://lottiefiles.com/featured" target="_blank">', '</a>'),
				'type'          => Controls_Manager::TEXTAREA,
				'label_block'   => true,
				'show_label'    => true,
				'dynamic'       => [
					'active'    => true,
				],
				'placeholder'   => __('Enter your json TEXT', 'bdthemes-element-pack'),
				'condition'     => [
					'lottie_json_source' => 'custom',
				],

			]
		);

		$this->add_control(
			'play_action',
			[
				'label' => __('Play Action', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'autoplay',
				'options' => [
					''         => __('None', 'bdthemes-element-pack'),
					'autoplay' => __('Auto Play', 'bdthemes-element-pack'),
					'click'    => __('Play on Click', 'bdthemes-element-pack'),
					'column'   => __('Play on Hover', 'bdthemes-element-pack'),
					'section'  => __('Play on Hover Section', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'view_type',
			[
				'label'   => esc_html__('Start When', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'pageload'   => esc_html__('Page Loaded', 'bdthemes-element-pack'),
					'scroll' => esc_html__('When Scroll', 'bdthemes-element-pack'),
				],
				'default'   => 'pageload',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control( //*
			'lottie_number_of_times',
			[
				'label' => __('Times', 'bdthemes-element-pack'),
				'type' => Controls_Manager::NUMBER,
				'render_type' => 'content',
				// 'conditions' => [
				//  'relation' => 'and',
				//  'terms' => [
				//      [
				//          'name' => 'lottie_trigger',
				//          'operator' => '!==',
				//          'value' => 'bind_to_scroll',
				//      ],
				//      [
				//          'name' => 'loop',
				//          'operator' => '===',
				//          'value' => 'yes',
				//      ],
				//  ],
				// ],
				'min' => 0,
				'step' => 1,
				'frontend_available' => true,
				'condition' => [
					'loop' => ['yes'],
				]
			]
		);

		$this->add_control(
			'speed',
			[
				'label' => esc_html__('Play Speed', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'lottie_start_point',
			[
				'label' => __('Start Point', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type' => 'content',
				'default' => [
					'size' => '0',
					'unit' => '%',
				],
				'size_units' => ['%'],
			]
		);

		$this->add_control(
			'lottie_end_point',
			[
				'label' => __('End Point', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type' => 'content',
				'default' => [
					'size' => '100',
					'unit' => '%',
				],
				'size_units' => ['%'],
			]
		);

		$this->add_control(
			'lottie_renderer',
			[
				'label' => __('Renderer', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SELECT,
				'default' => 'svg',
				'options' => [
					'svg' => __('SVG', 'bdthemes-element-pack'),
					'canvas' => __('Canvas', 'bdthemes-element-pack'),
				],
				'separator' => 'before',
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
				'default'     => __('Icon Box Heading', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your title', 'bdthemes-element-pack'),
				'label_block' => true,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'title_link',
			[
				'label'        => __('Title Link', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-title-link-'
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
			'show_sub_title',
			[
				'label'        => __('Show Sub Title', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'sub_title_text',
			[
				'label'   => __('Sub Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => __('Icon Box Sub Heading', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your sub title', 'bdthemes-element-pack'),
				'label_block' => true,
				'condition'	  => [
					'show_sub_title'	=> 'yes',
				],
			]
		);

		$this->add_control(
			'show_separator',
			[
				'label'        => __('Title Separator', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'separator'    => 'before',
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
				'default'     => __('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'bdthemes-element-pack'),
				'placeholder' => __('Enter your description', 'bdthemes-element-pack'),
				'rows'        => 10,
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'position',
			[
				'label'     => __('Icon Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'separator' => 'before',
				'default'   => 'top',
				'options'   => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'top' => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor-position-',
				'toggle'       => false,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'icon_inline',
			[
				'label'        => __('Icon Inline', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'condition'    => [
					'position' => ['left', 'right']
				],
			]
		);

		$this->add_control(
			'icon_vertical_alignment',
			[
				'label'   => __('Icon Vertical Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'top'   => [
						'title' => __('Top', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __('Middle', 'bdthemes-element-pack'),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __('Bottom', 'bdthemes-element-pack'),
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
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box' => 'text-align: {{VALUE}};',
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
				'type'        => Controls_Manager::ICONS,
				'condition'   => [
					'readmore'   => 'yes'
				],
				'label_block' => false,
				'skin'        => 'inline'
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
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'readmore_on_hover',
			[
				'label'        => __('Show on Hover', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-readmore-on-hover-',
			]
		);

		$this->add_responsive_control(
			'readmore_horizontal_offset',
			[
				'label' => __('Horizontal Offset', 'bdthemes-element-pack'),
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-readmore-h-offset: {{SIZE}}px;'
				],
			]
		);

		$this->add_responsive_control(
			'readmore_vertical_offset',
			[
				'label' => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
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
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-lottie-icon-box-readmore-v-offset: {{SIZE}}px;'
				],
				'condition' => [
					'readmore_on_hover' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => __('Button ID', 'bdthemes-element-pack') . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack'),
				'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack'),
				'separator' => 'before',
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-h-offset: {{SIZE}}px;'
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-v-offset: {{SIZE}}px;'
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-indicator-rotate: {{SIZE}}deg;'
				],
			]
		);

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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-h-offset: {{SIZE}}px;'
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
						'step' => 2,
						'max'  => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-v-offset: {{SIZE}}px;'
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-badge-rotate: {{SIZE}}deg;'
				],
			]
		);

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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-top-v-offset: -{{SIZE}}px;'
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-top-h-offset: {{SIZE}}px;'
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-left-h-offset: {{SIZE}}px;'
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
					'{{WRAPPER}}' => '--ep-lottie-icon-box-icon-left-v-offset: {{SIZE}}px;'
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


		//Style
		$this->start_controls_section(
			'section_style_image',
			[
				'label' => __('Lottie Icon', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('image_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_fill_color',
			[
				'label'     => __('Icon Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_stroke_color',
			[
				'label'     => __('Icon Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap',
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'separator'  => 'before',
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'icon_border',
				'placeholder' => '1px',
				'separator'   => 'before',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'
			]
		);

		$this->add_control(
			'icon_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
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

		$this->add_control(
			'icon_radius_advanced',
			[
				'label'       => esc_html__('Radius', 'bdthemes-element-pack'),
				'description' => sprintf(__('For example: <b>%1s</b> or Go <a href="%2s" target="_blank">this link</a> and copy and paste the radius value.', 'bdthemes-element-pack'), '75% 25% 43% 57% / 46% 29% 71% 54%', 'https://9elements.github.io/fancy-border-radius/'),
				'type'        => Controls_Manager::TEXT,
				'size_units'  => ['px', '%'],
				'separator'   => 'after',
				'default'     => '75% 25% 43% 57% / 46% 29% 71% 54%',
				'selectors'   => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'     => 'border-radius: {{VALUE}}; overflow: hidden;',
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap img' => 'border-radius: {{VALUE}}; overflow: hidden;'
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
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap'
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label'     => __('Spacing', 'bdthemes-element-pack'),
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
					'{{WRAPPER}}.elementor-position-right .bdt-lottie-icon-box-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-left .bdt-lottie-icon-box-icon'  => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.elementor-position-top .bdt-lottie-icon-box-icon'   => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} .bdt-lottie-icon-box-icon'                  => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'vh', 'vw'],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap .bdt-lottie-container'   => 'transform: rotate({{SIZE}}{{UNIT}});',
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'background_hover_transition_image',
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-icon-wrap' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'opacity',
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
					'{{WRAPPER}} .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .bdt-lottie-image svg',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'icon_fill_hover_color',
			[
				'label'     => __('Icon Fill Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap svg *' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_stroke_hover_color',
			[
				'label'     => __('Icon Stroke Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'icon_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap:after',
			]
		);

		$this->add_control(
			'icon_effect',
			[
				'label'        => __('Effect', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SELECT,
				'prefix_class' => 'bdt-icon-effect-',
				'default'      => 'none',
				'options'      => [
					'none' => __('None', 'bdthemes-element-pack'),
					'a'    => __('Effect A', 'bdthemes-element-pack'),
					'b'    => __('Effect B', 'bdthemes-element-pack'),
					'c'    => __('Effect C', 'bdthemes-element-pack'),
					'd'    => __('Effect D', 'bdthemes-element-pack'),
					'e'    => __('Effect E', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'icon_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'icon_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'icon_hover_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap'
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
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap .bdt-lottie-container'   => 'transform: rotate({{SIZE}}{{UNIT}});',
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
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-icon-wrap' => 'transform: rotate({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_control(
			'opacity_hover',
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
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-image svg' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-image svg',
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
					'{{WRAPPER}} .bdt-lottie-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_style_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'title_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_sub_title',
			[
				'label' => __('Sub Title', 'bdthemes-element-pack'),
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
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'sub_title_bottom_space',
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
					'{{WRAPPER}} .bdt-lottie-icon-box-sub-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'sub_title_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_sub_title_style_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'sub_title_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_title_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-sub-title',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_description',
			[
				'label' => __('Description', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_description_style');

		$this->start_controls_tab(
			'tab_description_style_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_responsive_control(
			'description_bottom_space',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_description_style_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'description_color_hover',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'description_typography_hover',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box:hover .bdt-lottie-icon-box-content .bdt-lottie-icon-box-description',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_title_separator',
			[
				'label'     => __('Title Separator', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_separator' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_separator_type',
			[
				'label'   => esc_html__('Separator Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'line',
				'options' => [
					'line'  	  => esc_html__('Line', 'bdthemes-element-pack'),
					'bloomstar'   => esc_html__('Bloomstar', 'bdthemes-element-pack'),
					'bobbleaf' 	  => esc_html__('Bobbleaf', 'bdthemes-element-pack'),
					'demaxa' 	  => esc_html__('Demaxa', 'bdthemes-element-pack'),
					'fill-circle' => esc_html__('Fill Circle', 'bdthemes-element-pack'),
					'finalio' 	  => esc_html__('Finalio', 'bdthemes-element-pack'),
					//'fitical' 	  => esc_html__( 'Fitical', 'bdthemes-element-pack' ),
					'jemik' 	  => esc_html__('Jemik', 'bdthemes-element-pack'),
					//'genizen' 	  => esc_html__( 'Genizen', 'bdthemes-element-pack' ),
					'leaf-line'   => esc_html__('Leaf Line', 'bdthemes-element-pack'),
					//'lendine' 	  => esc_html__( 'Lendine', 'bdthemes-element-pack' ),
					'multinus' 	  => esc_html__('Multinus', 'bdthemes-element-pack'),
					//'oradox' 	  => esc_html__( 'Oradox', 'bdthemes-element-pack' ),
					'rotate-box'  => esc_html__('Rotate Box', 'bdthemes-element-pack'),
					'sarator' 	  => esc_html__('Sarator', 'bdthemes-element-pack'),
					'separk' 	  => esc_html__('Separk', 'bdthemes-element-pack'),
					'slash-line'  => esc_html__('Slash Line', 'bdthemes-element-pack'),
					//'subtrexo' 	  => esc_html__( 'Subtrexo', 'bdthemes-element-pack' ),
					'tripline' 	  => esc_html__('Tripline', 'bdthemes-element-pack'),
					'vague' 	  => esc_html__('Vague', 'bdthemes-element-pack'),
					'zigzag-dot'  => esc_html__('Zigzag Dot', 'bdthemes-element-pack'),
					'zozobe' 	  => esc_html__('Zozobe', 'bdthemes-element-pack'),
				],
				//'render_type' => 'none',		
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-top-style: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_separator_height',
			[
				'label' => __('Height', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'border-top-width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_responsive_control(
			'title_separator_width',
			[
				'label' => __('Width', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator' => 'width: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap svg *' => 'fill: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap svg *' => 'stroke: {{VALUE}};',
				],
			]
		);


		$this->add_responsive_control(
			'title_separator_svg_width',
			[
				'label' => __('Width', 'bdthemes-element-pack'),
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
					'title_separator_type!' => 'line'
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap > *' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'title_separator_spacing',
			[
				'label' => __('Separator Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-separator-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);


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
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
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
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box-readmore'
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
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'readmore_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
			]
		);

		$this->add_responsive_control(
			'readmore_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'readmore_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore',
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
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'readmore_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'readmore_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover' => 'border-color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-readmore:hover',
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
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'badge_background',
				'selector'  => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
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
				'selector'    => '{{WRAPPER}} .bdt-lottie-icon-box-badge span'
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
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'badge_shadow',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
			]
		);

		$this->add_responsive_control(
			'badge_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box-badge span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'badge_typography',
				'selector' => '{{WRAPPER}} .bdt-lottie-icon-box-badge span',
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

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Content Inner Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-lottie-icon-box-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'icon_inline_spacing',
			[
				'label' => __('Icon Inline Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'condition' => [
					'position' => ['left', 'right'],
					'icon_inline' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-lottie-icon-box .bdt-icon-heading' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render_lottie_icon() {
		$settings = $this->get_settings_for_display();
		$json_code = '';
		$json_path = '';
		$is_json_url = true;

		if ($settings['lottie_json_source'] == 'url') {
			$json_path = $settings['lottie_json_path'];
		} elseif ($settings['lottie_json_source'] == 'local') {
			$json_path = $settings['upload_json_file'];
		} elseif ($settings['lottie_json_source'] == 'custom') {
			$json_code = $settings['lottie_json_code'];
			$is_json_url = false;
		}

		$this->add_render_attribute('wrapper', 'class', 'bdt-lottie-image bdt-lottie-icon-box-icon-wrap');


		if (!empty($settings['shape'])) {
			$this->add_render_attribute('wrapper', 'class', 'elementor-image-shape-' . $settings['shape']);
		}

		$lottie_start_point = (!empty($settings['lottie_start_point']['size']) ? $settings['lottie_start_point']['size'] : 0);
		$lottie_end_point   = (isset($settings['lottie_end_point']['size'])) ? $settings['lottie_end_point']['size'] : 0;
		$lottie_end_point = (strlen($lottie_end_point) > 0) ? $lottie_end_point : 100;

		$loopSet = '';
		if (isset($settings['loop'])) {
			$loopSet = ($settings['loop']) ? true : false;
		}

		if (!empty($settings['lottie_number_of_times']) && strlen($settings['lottie_number_of_times']) > 0) {
			$loopSet = ($settings['lottie_number_of_times']) - 1;
		}

		$this->add_render_attribute(
			[
				'lottie' => [
					'id' => 'bdt-lottie-' . $this->get_id(),
					'class' => 'bdt-lottie-container',
					'data-settings' => [
						wp_json_encode([
							'loop'            => $loopSet,
							'is_json_url'     => $is_json_url,
							'json_path'       => $json_path,
							'json_code'       => $json_code,
							'view_type'       => $settings['view_type'],
							'speed'           => ($settings['speed']['size']) ? $settings['speed']['size'] : 1,
							'play_action'     => $settings['play_action'],
							'start_point'     => $lottie_start_point,
							'end_point'       => $lottie_end_point,
							'lottie_renderer' => $settings['lottie_renderer'],
						])
					]
				]
			]
		);

?>
		<div class="bdt-lottie-icon-box-icon">
			<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
				<div <?php echo $this->get_render_attribute_string('lottie'); ?>></div>
			</div>
		</div>

	<?php
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();

		$this->add_render_attribute('advanced-icon-box-title', 'class', 'bdt-lottie-icon-box-title');

		$this->add_render_attribute('advanced-icon-box-sub-title', 'class', 'bdt-lottie-icon-box-sub-title');

		if ('yes' == $settings['title_link'] and $settings['title_link_url']['url']) {

			$target = $settings['title_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box-title', 'onclick', "window.open('" . $settings['title_link_url']['url'] . "', '$target')");
		}

		$this->add_render_attribute('description_text', 'class', 'bdt-lottie-icon-box-description');

		$this->add_inline_editing_attributes('title_text', 'none');
		$this->add_inline_editing_attributes('description_text');

		$this->add_render_attribute('readmore', 'class', ['bdt-lottie-icon-box-readmore', 'bdt-display-inline-block']);

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

		if (!empty($settings['button_css_id'])) {
			$this->add_render_attribute('readmore', 'id', $settings['button_css_id']);
		}

		$this->add_render_attribute('advanced-icon-box', 'class', 'bdt-lottie-icon-box');

		if ('yes' == $settings['global_link'] and $settings['global_link_url']['url']) {

			$target = $settings['global_link_url']['is_external'] ? '_blank' : '_self';

			$this->add_render_attribute('advanced-icon-box', 'onclick', "window.open('" . $settings['global_link_url']['url'] . "', '$target')");
		}

		if ('yes' == $settings['icon_inline'] && 'top' != $settings['position']) {
			$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-icon-heading bdt-flex bdt-flex-middle');

			if ('right' == $settings['position']) {
				$this->add_render_attribute('advanced-icon-box-icon-heading', 'class', 'bdt-flex-row-reverse');
			}
		}

	?>
		<div <?php echo $this->get_render_attribute_string('advanced-icon-box'); ?>>

			<?php if ('' == $settings['icon_inline']) : ?>
				<?php $this->render_lottie_icon(); ?>
			<?php endif; ?>

			<div class="bdt-lottie-icon-box-content">

				<div <?php echo $this->get_render_attribute_string('advanced-icon-box-icon-heading'); ?>>
					<?php if ('yes' == $settings['icon_inline']) : ?>
						<?php $this->render_lottie_icon(); ?>
					<?php endif; ?>

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

				<?php if ($settings['show_separator']) : ?>

					<?php if ('line' == $settings['title_separator_type']) : ?>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<div class="bdt-lottie-icon-box-separator"></div>
						</div>
					<?php elseif ('line' != $settings['title_separator_type']) : ?>
						<div class="bdt-lottie-icon-box-separator-wrap">
							<?php
							$svg_image = BDTEP_ASSETS_PATH . 'images/separator/' . $settings['title_separator_type'] . '.svg';

							if (file_exists($svg_image)) {

								ob_start();

								include($svg_image);

								$svg_image = ob_get_clean();

								echo wp_kses($svg_image, element_pack_allow_tags('svg'));
							}
							?>
						</div>
					<?php endif; ?>

				<?php endif; ?>

				<?php if ($settings['description_text']) : ?>
					<div <?php echo $this->get_render_attribute_string('description_text'); ?>>
						<?php echo wp_kses($settings['description_text'], element_pack_allow_tags('text')); ?>
					</div>
				<?php endif; ?>

				<?php if ($settings['readmore']) : ?>
					<a <?php echo $this->get_render_attribute_string('readmore'); ?>>
						<?php echo esc_html($settings['readmore_text']); ?>

						<?php if ($settings['advanced_readmore_icon']['value']) : ?>

							<span class="bdt-button-icon-align-<?php echo $settings['readmore_icon_align'] ?>">

								<?php Icons_Manager::render_icon($settings['advanced_readmore_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']); ?>

							</span>

						<?php endif; ?>
					</a>
				<?php endif ?>
			</div>
		</div>

		<?php if ($settings['indicator']) : ?>
			<div class="bdt-indicator-svg bdt-svg-style-<?php echo esc_attr($settings['indicator_style']); ?>">
				<?php echo element_pack_svg_icon('arrow-' . $settings['indicator_style']); ?>
			</div>
		<?php endif; ?>

		<?php if ($settings['badge'] and '' != $settings['badge_text']) : ?>
			<div class="bdt-lottie-icon-box-badge bdt-position-<?php echo esc_attr($settings['badge_position']); ?>">
				<span class="bdt-badge bdt-padding-small"><?php echo esc_html($settings['badge_text']); ?></span>
			</div>
		<?php endif; ?>

<?php
	}
}
