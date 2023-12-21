<?php
/**
 * Fun Factor widget class
 *
 * @package Happy_Addons
 */

namespace Happy_Addons\Elementor\Widget;

use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || die();

class Fun_Factor extends Base {

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_title() {
		return __('Fun Factor', 'happy-elementor-addons');
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 *
	 */
	public function get_icon() {
		return 'hm hm-cross-game';
	}

	public function get_keywords() {
		return ['fun', 'factor', 'animation', 'info', 'box', 'number', 'animated'];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__content_controls();
		$this->__option_content_controls();
	}

	protected function __content_controls() {

		$this->start_controls_section(
			'_section_contents',
			[
				'label' => __('Contents', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'media_type',
			[
				'label'          => __('Media Type', 'happy-elementor-addons'),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => false,
				'options'        => [
					'icon'  => [
						'title' => __('Icon', 'happy-elementor-addons'),
						'icon'  => 'eicon-star',
					],
					'image' => [
						'title' => __('Image', 'happy-elementor-addons'),
						'icon'  => 'eicon-image',
					],
				],
				'default'        => 'icon',
				'toggle'         => false,
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'image',
			[
				'label'     => __('Image', 'happy-elementor-addons'),
				'type'      => Controls_Manager::MEDIA,
				'default'   => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'media_type' => 'image'
				],
				'dynamic'   => [
					'active' => true,
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'medium_large',
				'separator' => 'none',
				'exclude'   => [
					'full',
					'custom',
					'large',
					'shop_catalog',
					'shop_single',
					'shop_thumbnail'
				],
				'condition' => [
					'media_type' => 'image'
				]
			]
		);

		$this->add_control(
			'icons',
			[
				'label'      => __('Icons', 'happy-elementor-addons'),
				'type'       => Controls_Manager::ICONS,
				'show_label' => true,
				'default'    => [
					'value'   => 'far fa-grin-wink',
					'library' => 'solid',
				],
				'condition'  => [
					'media_type' => 'icon',
				],

			]
		);

		$this->add_control(
			'image_icon_position',
			[
				'label'          => __('Position', 'happy-elementor-addons'),
				'type'           => Controls_Manager::CHOOSE,
				'label_block'    => false,
				'options'        => [
					'left'  => [
						'title' => __('Left', 'happy-elementor-addons'),
						'icon'  => 'eicon-h-align-left',
					],
					'top'   => [
						'title' => __('Top', 'happy-elementor-addons'),
						'icon'  => 'eicon-v-align-top',
					],
					'right' => [
						'title' => __('Right', 'happy-elementor-addons'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle'         => false,
				'default'        => 'top',
				'prefix_class'   => 'ha-ff-icon--',
				'style_transfer' => true,
			]
		);

		/*
		 * number section
		 */

		$this->add_control(
			'fun_factor_number',
			[
				'label'     => __('Number', 'happy-elementor-addons'),
				'type'      => Controls_Manager::TEXT,
				'default'   => '507',
				'dynamic'   => [
					'active' => true,
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'fun_factor_number_prefix',
			[
				'label'     => __('Number Prefix', 'happy-elementor-addons'),
				'type'      => Controls_Manager::TEXT,
				'placeholder'   => '1',
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'fun_factor_number_suffix',
			[
				'label'     => __('Number Suffix', 'happy-elementor-addons'),
				'type'      => Controls_Manager::TEXT,
				'placeholder'   => '+',
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'fun_factor_title',
			[
				'label'   => __('Title', 'happy-elementor-addons'),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => __('Happy Clients', 'happy-elementor-addons'),
			]
		);

		$this->add_control(
			'animate_number',
			[
				'label'        => __('Animate', 'happy-elementor-addons'),
				'description'  => __('Only number is animatable'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Yes', 'happy-elementor-addons'),
				'label_off'    => __('No', 'happy-elementor-addons'),
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => 'yes'
			]
		);

		$this->add_control(
			'animate_duration',
			[
				'label'     => __('Duration', 'happy-elementor-addons'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 100,
				'max'       => 10000,
				'step'      => 10,
				'default'   => 500,
				'condition' => [
					'animate_number!' => ''
				],
				'dynamic'   => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __option_content_controls() {

		// options section in contents tab

		$this->start_controls_section(
			'_section_options',
			[
				'label' => __('Options', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_CONTENT
			]
		);

		$this->add_control(
			'divider_show_hide',
			[
				'label'        => __('Show Divider', 'happy-elementor-addons'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __('Show', 'happy-elementor-addons'),
				'label_off'    => __('Hide', 'happy-elementor-addons'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_responsive_control(
			'text_align',
			[
				'label'       => __('Text Alignment', 'happy-elementor-addons'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'   => [
						'title' => __('Left', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __('Right', 'happy-elementor-addons'),
						'icon'  => 'eicon-text-align-right',
					],
					// 'justify'  => [
					// 	'title' => __('Justify', 'happy-elementor-addons'),
					// 	'icon'  => 'eicon-text-align-justify',
					// ],
				],
				'toggle'      => false,
				// 'selectors_dictionary' => [
                //     'left' => 'text-align: left; justify-content: flex-start;',
                //     'center' => 'text-align: center; justify-content: center;',
                //     'right' => 'text-align: right; justify-content: flex-end;',
                // ],
				'selectors_dictionary' => [
                    'left' => '--ha-ff-align: left; --ha-ff-number-align: flex-start;',
                    'center' => '--ha-ff-align: center; --ha-ff-number-align: center;',
                    'right' => '--ha-ff-align: right; --ha-ff-number-align: flex-end;',
                ],
				'selectors'   => [
					'{{WRAPPER}}.ha-fun-factor ' => '{{VALUE}};',
					'{{WRAPPER}} .ha-fun-factor__wrap, {{WRAPPER}} .ha-fun-factor__media--image, {{WRAPPER}} .ha-fun-factor__content, {{WRAPPER}} .ha-fun-factor__content' => 'text-align:var(--ha-ff-align);',
					'{{WRAPPER}} .ha-fun-factor__content-number-wrap' => 'justify-content:var(--ha-ff-number-align);',
				],
				// 'selectors'   => [
				// 	'{{WRAPPER}} .ha-fun-factor__wrap, {{WRAPPER}} .ha-fun-factor__media--image, {{WRAPPER}} .ha-fun-factor__content, {{WRAPPER}} .ha-fun-factor__content-number-wrap' => '{{VALUE}};',
				// ],
				'default'     => 'center',
				// 'render_type' => 'template',
				// 'prefix_class' => 'ha-fun-factor-align-',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'   => __('Title HTML Tag', 'happy-elementor-addons'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'h1' => [
						'title' => __('H1', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h1'
					],
					'h2' => [
						'title' => __('H2', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h2'
					],
					'h3' => [
						'title' => __('H3', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h3'
					],
					'h4' => [
						'title' => __('H4', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h4'
					],
					'h5' => [
						'title' => __('H5', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h5'
					],
					'h6' => [
						'title' => __('H6', 'happy-elementor-addons'),
						'icon'  => 'eicon-editor-h6'
					]
				],
				'default' => 'h2',
				'toggle'  => false,
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__icon_image_style_controls();
		$this->__number_title_style_controls();
		$this->__divider_style_controls();
	}

	protected function __icon_image_style_controls() {

		$this->start_controls_section(
			'_section_style_icon_image',
			[
				'label' => __('Icon / Image', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_width',
			[
				'label'      => __('Width', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 150,
						'max' => 500,
					],
					'%'  => [
						'min' => 30,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}}.ha-ff-icon--top .ha-fun-factor__media--image' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}:not(.ha-ff-icon--top) .ha-fun-factor__media--image' => 'flex: 0 0 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'media_type' => 'image',
				]
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => __('Height', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 150,
						'max' => 1024,
					],
					'%'  => [
						'min' => 30,
						'max' => 100,
					],
				],
				'default'    => [
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__media--image' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'media_type' => 'image',
				]
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'           => __('Size', 'happy-elementor-addons'),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => ['px'],
				'range'           => [
					'px' => [
						'min'  => 6,
						'max'  => 300,
					],
				],
				'selectors'       => [
					'{{WRAPPER}} .ha-fun-factor__media--icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'       => [
					'media_type' => 'icon',
				],
				'render_type'     => 'template',
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __('Icon Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__media--icon' => 'color: {{VALUE}};',
				],
				'condition' => [
					'media_type' => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'media_padding',
			[
				'label'      => __('Padding', 'happy-elementor-addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__media--image img, {{WRAPPER}} .ha-fun-factor__media--icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'media_border',
				'selector'  => '{{WRAPPER}} .ha-fun-factor__media--image img, {{WRAPPER}} .ha-fun-factor__media--icon',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'media_border_radius',
			[
				'label'      => __('Border Radius', 'happy-elementor-addons'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__media--image img, {{WRAPPER}} .ha-fun-factor__media--icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'media_box_shadow',
				'selector' => '{{WRAPPER}} .ha-fun-factor__media--image img, {{WRAPPER}} .ha-fun-factor__media--icon',
			]
		);

		$this->add_responsive_control(
			'icon_image_bottom_spacing',
			[
				'label'     => __('Bottom Spacing', 'happy-elementor-addons'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__media--icon, {{WRAPPER}} .ha-fun-factor__media--image' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'     => __('Background Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__media--icon, {{WRAPPER}} .ha-fun-factor__media--image' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'media_type' => 'icon'
				]
			]
		);

		$this->add_control(
			'offset_toggle',
			[
				'label'        => __('Offset', 'happy-elementor-addons'),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __('No', 'happy-elementor-addons'),
				'label_on'     => __('Yes', 'happy-elementor-addons'),
				'return_value' => 'yes',
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'media_offset_x',
			[
				'label'      => __('Offset Left', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'offset_toggle' => 'yes'
				],
				'range'      => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],
			]
		);

		$this->add_responsive_control(
			'media_offset_y',
			[
				'label'      => __('Offset Top', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'condition'  => [
					'offset_toggle' => 'yes'
				],
				'range'      => [
					'px' => [
						'min' => -1000,
						'max' => 1000,
					],
				],

				'selectors' => [
					// Left image position styles
					'(desktop){{WRAPPER}}.ha-ff-icon--left .ha-fun-factor__content'                               => 'margin-left: {{media_offset_x.SIZE || 0}}{{UNIT}}; max-width: calc((100% - {{image_width.SIZE || 50}}{{image_width.UNIT}}) + (-1 * {{media_offset_x.SIZE || 0}}{{UNIT}}));',
					'(tablet){{WRAPPER}}.ha-ff-icon--left .ha-fun-factor__content'                                => 'margin-left: {{media_offset_x_tablet.SIZE || 0}}{{UNIT}}; max-width: calc((100% - {{image_width_tablet.SIZE || 50}}{{image_width_tablet.UNIT}}) + (-1 * {{media_offset_x_tablet.SIZE || 0}}{{UNIT}}));',
					'(mobile){{WRAPPER}}.ha-ff-icon--left .ha-fun-factor__content'                                => 'margin-left: {{media_offset_x_mobile.SIZE || 0}}{{UNIT}}; max-width: calc((100% - {{image_width_mobile.SIZE || 50}}{{image_width_mobile.UNIT}}) + (-1 * {{media_offset_x_mobile.SIZE || 0}}{{UNIT}}));',
					// Image right position styles
					'(desktop){{WRAPPER}}.ha-ff-icon--right .ha-fun-factor__content'                              => 'margin-right: calc(-1 * {{media_offset_x.SIZE || 0}}{{UNIT}}); max-width: calc((100% - {{image_width.SIZE || 50}}{{image_width.UNIT}}) + {{media_offset_x.SIZE || 0}}{{UNIT}});',
					'(tablet){{WRAPPER}}.ha-ff-icon--right .ha-fun-factor__content'                               => 'margin-right: calc(-1 * {{media_offset_x_tablet.SIZE || 0}}{{UNIT}}); max-width: calc((100% - {{image_width_tablet.SIZE || 50}}{{image_width_tablet.UNIT}}) + {{media_offset_x_tablet.SIZE || 0}}{{UNIT}});',
					'(mobile){{WRAPPER}}.ha-ff-icon--right .ha-fun-factor__content'                               => 'margin-right: calc(-1 * {{media_offset_x_mobile.SIZE || 0}}{{UNIT}}); max-width: calc((100% - {{image_width_mobile.SIZE || 50}}{{image_width_mobile.UNIT}}) + {{media_offset_x_mobile.SIZE || 0}}{{UNIT}});',
					// Image translate styles
					'(desktop){{WRAPPER}} .ha-fun-factor__media--icon, {{WRAPPER}} .ha-fun-factor__media--image' => '-ms-transform: translate({{media_offset_x.SIZE || 0}}{{UNIT}}, {{media_offset_y.SIZE || 0}}{{UNIT}}); -webkit-transform: translate({{media_offset_x.SIZE || 0}}{{UNIT}}, {{media_offset_y.SIZE || 0}}{{UNIT}}); transform: translate({{media_offset_x.SIZE || 0}}{{UNIT}}, {{media_offset_y.SIZE || 0}}{{UNIT}});',
					'(tablet){{WRAPPER}} .ha-fun-factor__media--icon, {WRAPPER}} .ha-fun-factor__media--image'   => '-ms-transform: translate({{media_offset_x_tablet.SIZE || 0}}{{UNIT}}, {{media_offset_y_tablet.SIZE || 0}}{{UNIT}}); -webkit-transform: translate({{media_offset_x_tablet.SIZE || 0}}{{UNIT}}, {{media_offset_y_tablet.SIZE || 0}}{{UNIT}}); transform: translate({{media_offset_x_tablet.SIZE || 0}}{{UNIT}}, {{media_offset_y_tablet.SIZE || 0}}{{UNIT}});',
					'(mobile){{WRAPPER}} .ha-fun-factor__media--icon, {{WRAPPER}} .ha-fun-factor__media--image'  => '-ms-transform: translate({{media_offset_x_mobile.SIZE || 0}}{{UNIT}}, {{media_offset_y_mobile.SIZE || 0}}{{UNIT}}); -webkit-transform: translate({{media_offset_x_mobile.SIZE || 0}}{{UNIT}}, {{media_offset_y_mobile.SIZE || 0}}{{UNIT}}); transform: translate({{media_offset_x_mobile.SIZE || 0}}{{UNIT}}, {{media_offset_y_mobile.SIZE || 0}}{{UNIT}});',
					// Fun Factor body styles
					'{{WRAPPER}}.ha-ff-icon--top .ha-fun-factor__content'                                         => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_popover();

		$this->end_controls_section();
	}

	protected function __number_title_style_controls() {

		$this->start_controls_section(
			'_section_style_number_title',
			[
				'label' => __('Number & Title', 'happy-elementor-addons'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'     => __('Padding', 'happy-elementor-addons'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_control(
			'fun_factor_number_heading',
			[
				'label' => __('Number', 'happy-elementor-addons'),
				'type'  => Controls_Manager::HEADING
			]
		);

		$this->add_control(
			'fun_factor_number_bottom_spacing',
			[
				'label'      => __('Bottom Spacing', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__content-number-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'fun_factor_number_prefix_spacing',
			[
				'label'      => __('Prefix Spacing', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'condition'  => [
					'fun_factor_number_prefix!' => ''
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__content-number-prefix' => 'margin-right: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'fun_factor_number_suffix_spacing',
			[
				'label'      => __('Suffix Spacing', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'condition'  => [
					'fun_factor_number_suffix!' => ''
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__content-number-suffix' => 'margin-left: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'fun_factor_number_color',
			[
				'label'     => __('Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__content-number-prefix' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .ha-fun-factor__content-number' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .ha-fun-factor__content-number-suffix' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'number_typography',
				'label'    => __('Typography', 'happy-elementor-addons'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-fun-factor__content-number-prefix, {{WRAPPER}} .ha-fun-factor__content-number, {{WRAPPER}} .ha-fun-factor__content-number-suffix',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'fun_factor_number_shadow',
				'label'    => __('Text Shadow', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-fun-factor__content-number-prefix, {{WRAPPER}} .ha-fun-factor__content-number, {{WRAPPER}} .ha-fun-factor__content-number-suffix',
			]
		);

		/*
		 * Title section
		 */

		$this->add_control(
			'content_title_heading_style',
			[
				'label'     => __('Title', 'happy-elementor-addons'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'fun_factor_content_bottom_spacing',
			[
				'label'      => __('Bottom Spacing', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__content-text' => 'margin-bottom: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'fun_factor_content_color',
			[
				'label'     => __('Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__content-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typography',
				'label'    => __('Typography', 'happy-elementor-addons'),
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ha-fun-factor__content-text',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'fun_factor_content_shadow',
				'label'    => __('Text Shadow', 'happy-elementor-addons'),
				'selector' => '{{WRAPPER}} .ha-fun-factor__content-text',
			]
		);

		$this->end_controls_section();
	}

	protected function __divider_style_controls() {

		$this->start_controls_section(
			'_section_divider',
			[
				'label'     => __('Divider', 'happy-elementor-addons'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'divider_show_hide' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'divider_width',
			[
				'label'      => __('Width', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range'      => [
					'%' => [
						'min' => 10,
						'max' => 100
					],
				],
				'default'    => [
					'unit' => '%'
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__divider' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'divider_height',
			[
				'label'      => __('Height', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%', 'em'],
				'default'    => [
					'px' => 1
				],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__divider' => 'height: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'divider_border_radius',
			[
				'label'     => __('Border Radius', 'happy-elementor-addons'),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__divider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'     => __('Color', 'happy-elementor-addons'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-fun-factor__divider' => 'background-color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'divider_bottom_spacing',
			[
				'label'      => __('Bottom Spacing', 'happy-elementor-addons'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .ha-fun-factor__divider' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('fun_factor_number', 'class', 'ha-fun-factor__content-number');
		$number           = $settings['fun_factor_number'];
		$fun_factor_title = $settings['fun_factor_title'];

		if ($settings['animate_number']) {
			$data = [
				'toValue'  => intval($settings['fun_factor_number']),
				'duration' => intval($settings['animate_duration']),
			];
			$this->add_render_attribute('fun_factor_number', 'data-animation', wp_json_encode($data));
			$number = 0;
		}
		?>

		<div class="ha-fun-factor__wrap">
            <?php if (!empty($settings['icons']['value'])) : ?>
                <div class="ha-fun-factor__media ha-fun-factor__media--icon">
                    <?php Icons_Manager::render_icon( $settings['icons'], ['aria-hidden' => 'true'] ); ?>
                </div>
            <?php elseif ( isset($settings['image']) && isset($settings['image']['url']) && isset($settings['image']['id']) ) : ?>
                <div class="ha-fun-factor__media ha-fun-factor__media--image">
                    <?php echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'thumbnail', 'image' ); ?>
                </div>
            <?php endif; ?>

            <div class="ha-fun-factor__content">
				<div class="ha-fun-factor__content-number-wrap">
					<?php if ( $settings['fun_factor_number_prefix'] ) : ?>
						<span class="ha-fun-factor__content-number-prefix"><?php esc_html_e( $settings['fun_factor_number_prefix'] ); ?></span>
					<?php endif; ?>
	                <span <?php $this->print_render_attribute_string( 'fun_factor_number' ); ?> ><?php echo esc_html( $number ); ?></span>
					<?php if ( $settings['fun_factor_number_suffix'] ) : ?>
						<span class="ha-fun-factor__content-number-suffix"><?php esc_html_e( $settings['fun_factor_number_suffix'] ); ?></span>
					<?php endif; ?>
				</div>
                <?php if ( 'yes' === $settings['divider_show_hide'] ) : ?>
                    <span class="ha-fun-factor__divider ha-fun-factor__divider-align-<?php echo esc_attr( $settings['text_align'] ); ?>"></span>
                <?php endif; ?>
                <?php printf( '<%1$s class="ha-fun-factor__content-text">%2$s</%1$s>',
                    ha_escape_tags( $settings['title_tag'], 'h2' ),
                    ha_kses_basic( $fun_factor_title )
                ); ?>
            </div>
        </div>
		<?php
	}
}
