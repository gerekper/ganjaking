<?php
/**
 * creative slider widget class
 *
 * @package Happy_Addons
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined( 'ABSPATH' ) || die();

class Creative_Slider extends Base {

    /**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Creative Slider', 'happy-addons-pro' );
	}

	/**
	 * Get widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'hm hm-slider';
	}

	public function get_keywords() {
		return [ 'creative-slider', 'creative-slider', 'creative slider', 'ha slider', 'ha creative slider', 'slider', 'Creative Slider', 'HA Slider' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__creative_slider_general_controls();
		$this->__creative_slider_settings_controls();
	}

	// define shipping bar content controls
	public function __creative_slider_general_controls() {
		$this->start_controls_section(
			'_section_creative_slider',
			[
				'label' => __( 'Creative Slider', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs('tabs_cc_item');
		$repeater->start_controls_tab(
			'tab_cc_content_item',
			[
				'label' => __('Content', 'happy-addons-pro'),
			]
		);

		$repeater->add_control(
			'cc_pre_title',
			[
				'label'   => __( 'Pre Title', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Pre Title', 'happy-addons-pro' ),
				'label_block' => true,
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$repeater->add_control(
			'cc_pre_title_icon',
			[
				'label' => __( 'Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif'],
				'frontend_available' => true,
				'default' => [
					'value' => 'far fa-gem',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'cc_title',
			[
				'label'   => __( 'Title', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __('Title', 'happy-addons-pro'),
				'label_block' => true,
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$repeater->add_control(
			'cc_sub_title',
			[
				'label'   => __( 'Sub Title', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Sub Title', 'happy-addons-pro' ),
				'label_block' => true,
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$repeater->add_control(
			'cc_description',
			[
				'label'   => __( 'Description', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => __( 'Lorem Ipsum is simply dummy text of the printing and typesetting industry...', 'happy-addons-pro' ),
				'label_block' => true,
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$repeater->add_control(
			'cc_btn_text',
			[
				'label'   => __( 'Button Text', 'happy-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'default' => __( 'Explore', 'happy-addons-pro' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'cc_btn_url',
			[
                'label' => __('Button Link', 'happy-addons-pro'),
                'type' => Controls_Manager::URL,
                'placeholder' => __('https://your-link.com', 'happy-addons-pro'),
                'show_external' => true,
                'default' => [
                    'url' => 'https://happyaddons.com/',
                    'is_external' => true,
                    'nofollow' => true,
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
		);

		$repeater->add_control(
			'cc_image',
			[
				'label'   => __( 'Choose Slide Image', 'happy-addons-pro' ),
				'type'    => Controls_Manager::MEDIA,
				'separator' => 'before',
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
                    'active' => true,
                ],
			]
		);
		$repeater->end_controls_tab(); // end slider content tab

		//Timeline Style Tab
		$repeater->start_controls_tab(
			'tab_cc_style_item',
			[
				'label' => __('Style', 'happy-addons-pro'),
			]
		);

		$repeater->add_control(
			'ccr_slider_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		/*
		$repeater->add_control(
			'ccr_icon_size',
			[
				'label' => __('Icon Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_category i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$repeater->add_control(
			'ccr_icon_color',
			[
				'label' => __('Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_category i' => 'color: {{VALUE}}',
				],
			]
		); */

		$repeater->add_control(
			'ccr_pre_title_style_heading',
			[
				'label' => __( 'Pre Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccr_pretitle_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}}  {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_category',
			]
		);
		$repeater->add_control(
			'ccr_pre_title_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_category' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'ccr_title_style_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccr_title_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_title',
			]
		);

		$repeater->add_control(
			'ccr_title_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_title' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'ccr_sub_title_style_heading',
			[
				'label' => __( 'Sub Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccr_subtitle_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_sub_title',
			]
		);

		$repeater->add_control(
			'ccr_sub_title_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_sub_title' => 'color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'ccr_des_style_heading',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);
		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccr_description_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_description',
			]
		);

		$repeater->add_control(
			'ccr_description_color',
			[
				'label' => __('Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_description' => 'color: {{VALUE}}',
				],
			]
		);

		// start custom btn
		$repeater->add_control(
			'ccr_enable_custom_btn',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'prefix_class' => 'ha_cc_custom_btn_'
			]
		);

		$repeater->add_control(
			'ccr_btn_normal_bg_color',
			[
				'label' => __('Backgroung Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'background-color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_hover_bg_color',
			[
				'label' => __('Hover Backgroung Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_normal_text_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_hover_text_color',
			[
				'label' => __('Hover Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ccr_btn_normal_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn',
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_border_normal_style',
			[
				'label' => __( 'Border Type', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
					'none' => __( 'None', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-style: {{VALUE}};',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_normal_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_control(
			'ccr_btn_hover_border_color',
			[
				'label' => __('Hover Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'border-color: {{VALUE}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_responsive_control(
			'ccr_btn_normal_border_width',
			[
				'label' => __('Border Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_responsive_control(
			'ccr_btn_normal_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->add_responsive_control(
			'ccr_btn_normal_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['ccr_enable_custom_btn' => 'yes'],
			]
		);

		$repeater->end_controls_tab();//end slider style tab
		$repeater->end_controls_tabs();

		//slider default list
		$this->add_control(
			'ha_creative_slider_list',
			[
				'label' => __( 'Slider', 'happy-addons-pro' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'cc_pre_title' => __( 'Pre title', 'happy-addons-pro' ),
						'cc_title' => 'Title',
						'cc_sub_title' => 'Sub Title',
						'cc_description' => __('Lorem Ipsum is simply dummy text of the printing and typesettingLorem Ipsum is simply dummy text of the printing and typesetting industry.', 'happy-addons-pro'),
						'cc_btn_text' => __('Explore', 'happy-addons-pro'),
					],
					[
						'cc_pre_title' => __( 'Pre title', 'happy-addons-pro' ),
						'cc_title' => 'Title',
						'cc_sub_title' => 'Sub Title',
						'cc_description' => __('Lorem Ipsum is simply dummy text of the printing and typesettingLorem Ipsum is simply dummy text of the printing and typesetting industry.', 'happy-addons-pro'),
						'cc_btn_text' => __('Explore', 'happy-addons-pro'),
					],
					[
						'cc_pre_title' => __( 'Pre title', 'happy-addons-pro' ),
						'cc_title' => 'Title',
						'cc_sub_title' => 'Sub Title',
						'cc_description' => __('Lorem Ipsum is simply dummy text of the printing and typesettingLorem Ipsum is simply dummy text of the printing and typesetting industry.', 'happy-addons-pro'),
						'cc_btn_text' => __('Explore', 'happy-addons-pro'),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	// define creative slider settings controls
	public function __creative_slider_settings_controls() {

		$this->start_controls_section(
			'_section_creative_slider_settings',
			[
				'label' => __( 'Settings', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'cs_image_width',
			[
				'label' => __('Slide Image Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 750,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_image_item img' => 'max-width: {{SIZE}}{{UNIT}} !important',
				],
			]
		);

		$this->add_responsive_control(
			'disable_thumb_img',
			[
				'label' => __( 'Disable Thumbnail Image', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'prefix_class' => 'ha_cc_disable_thumb_img_'
			]
		);

		$this->add_control(
			'show_flip_view',
			[
				'label' => __( 'Flip Slider', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'prefix_class' => 'ha_cc_flip_'
			]
		);

		$this->add_control(
			'cs_desc_word_limit',
			[
				'label' => __( 'Description Word Limit', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'label_block' => false,
				'min' => 10,
				'max' => 1000,
				'step' => 1,
				'default' => 45,
			]
		);

		$this->add_control(
			'cc_next_prev_icon_heading',
			[
				'label' => __( 'Next/Prev Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'cs_next_icon',
			[
				'label' => __( 'Next Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif','png'],
				'frontend_available' => true,
				'default' => [
					'value' => 'hm hm-angle-right',
					'library' => 'happy-icons',
				],
			]
		);

		$this->add_control(
			'cs_prev_icon',
			[
				'label' => __( 'Prev Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg','gif','png'],
				'frontend_available' => true,
				'default' => [
					'value' => 'hm hm-angle-left',
					'library' => 'happy-icons',
				],
			]
		);

		$this->end_controls_section();
	}


	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__creative_slider_style_controls();
		$this->__creative_slider_btn_style_controls();
		$this->__creative_slider_next_prev_style_controls();
	}

	//define rcc style controll
	protected function __creative_slider_style_controls() {
		$this->start_controls_section(
			'_section_creative_slider_style',
			[
				'label' => __( 'Creative Slider', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'cs_height',
			[
				'label' => __('Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2500,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 2500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 450,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container' => '--ha-main-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'cs_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'render_type' => 'template',
				'default' => [
					'top' => 35,
					'right' => 50,
					'bottom' => 35,
					'left' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_text_items ' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container' => '--ha-cs-infotext-padding-top: {{TOP}}{{UNIT}}; --ha-cs-infotext-padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cs_slider_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f7f7f7',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_text_items' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha_cc_info_text_inner' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cc_pre_title_style_heading',
			[
				'label' => __( 'Pre Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		/*
		$this->add_control(
			'cs_icon_size',
			[
				'label' => __('Icon Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_category i' => 'font-size: {{SIZE}}{{UNIT}} !important',
				],
			]
		);

		$this->add_control(
			'cs_icon_color',
			[
				'label' => __('Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_info_text_item_category i' => 'color: {{VALUE}}',
				],
			]
		); */

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}}  .ha-creative-slider-container .ha_cc_info_text_item_category',
			]
		);

		$this->add_control(
			'cs_text_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_category' => 'color: {{VALUE}}',
				],
			]
		);

		// start title style here
		$this->add_control(
			'cc_title_style_heading',
			[
				'label' => __( 'Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_title_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_title',
			]
		);

		$this->add_control(
			'cs_title_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_title_specing',
			[
				'label' => __('Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
					'em' => [
						'min' => -250,
						'max' => 250,
					],
					'%' => [
						'min' => -250,
						'max' => 250,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		// start sub title style here
		$this->add_control(
			'cc_sub_title_style_heading',
			[
				'label' => __( 'Sub Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_sub_title_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_sub_title',
			]
		);

		$this->add_control(
			'cs_sub_title_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_sub_title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_sub_title_specing',
			[
				'label' => __('Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
					'em' => [
						'min' => -250,
						'max' => 250,
					],
					'%' => [
						'min' => -250,
						'max' => 250,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_sub_title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		// start description style here
		$this->add_control(
			'cc_desctiption_style_heading',
			[
				'label' => __( 'Description', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_description_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_description',
			]
		);

		$this->add_control(
			'cs_description_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_description_specing',
			[
				'label' => __('Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
					'em' => [
						'min' => -250,
						'max' => 250,
					],
					'%' => [
						'min' => -250,
						'max' => 250,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_description' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	//define btn styale
	protected function __creative_slider_btn_style_controls() {
		$this->start_controls_section(
			'_section_creative_slider_btn_style',
			[
				'label' => __( 'Button', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_btn_normal_typography',
				'label' => __('Typography', 'happy-addons-pro'),
				// 'global' => [
				// 	'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				// ],
				'selector' => '{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn',
				'fields_options' => [
					'text_decoration' => [
						'default' => 'none'
					]
				],
			]
		);

		$this->add_control(
			'cs_btn_border_style',
			[
				'label' => __( 'Border Type', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => __( 'Solid', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
					'none' => __( 'None', 'happy-addons-pro' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'cs_btn_border_width',
			[
				'label' => __('Border Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'cs_btn_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cs_btn_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top' => 20,
					'right' => 50,
					'bottom' => 20,
					'left' => 50,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'cs_btn_specing',
			[
				'label' => __('Spacing', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => -250,
						'max' => 250,
						'step' => 1,
					],
					'em' => [
						'min' => -250,
						'max' => 250,
					],
					'%' => [
						'min' => -250,
						'max' => 250,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_cs_btn');
		$this->start_controls_tab(
			'tab_cs_btn_style_normal',
			[
				'label' => __('Normal', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'cs_btn_normal_bg_color',
			[
				'label' => __('Backgroung Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => 'transparent',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_btn_normal_text_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#b48b3c',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_btn_normal_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#b48b3c',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab(); //end normal style

		$this->start_controls_tab(
			'tab_cs_btn_style_hover',
			[
				'label' => __('Hover', 'happy-addons-pro'),
			]
		);

		$this->add_control(
			'cs_btn_hover_bg_color',
			[
				'label' => __('Backgroung Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#b48b3c',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_btn_hover_text_color',
			[
				'label' => __('Text Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_btn_hover_border_color',
			[
				'label' => __('Border Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#b48b3c',
				'selectors' => [
					'{{WRAPPER}} .ha_cc_info_text_inner .ha_cc_info_text_item_btn:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();



		$this->end_controls_section();
	}

	//define next/prev thumb style
	protected function __creative_slider_next_prev_style_controls() {
		$this->start_controls_section(
			'_section_creative_slider_next_pre_thumb_style',
			[
				'label' => __( 'Next/Prev Thumb', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'cs_next_prev_icon_size',
			[
				'label' => __('Icon Size', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 22,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_next i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_prev i' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'cs_next_prev_icon_color',
			[
				'label' => __('Icon Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFF',
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_next i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_prev i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cc_next_prev_style_heading',
			[
				'label' => __( 'Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
			]
		);

		$this->add_control(
			'cs_next_prev_bg_color',
			[
				'label' => __('Background Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#0000000F',
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_wrapper' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_wrapper' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_next' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cs_mobile_prev' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'cs_next_prev_overlay',
			[
				'label' => __( 'Overlay', 'happy-addons-pro' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'happy-addons-pro' ),
				'label_off' => __( 'Hide', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'prefix_class' => 'next_prev_overlay_'
			]
		);

		$this->add_control(
			'cs_overlay_color',
			[
				'label' => __('Overlay Color', 'happy-addons-pro'),
				'type' => Controls_Manager::COLOR,
				'default' => '#00000029',
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon:before' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon:before' => 'background-color: {{VALUE}}',
				],
				'condition' => [ 'cs_next_prev_overlay' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'cs_next_prev_overlay_opacity',
			[
				'label' => __('Opacity', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0.8,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon:before' => 'opacity: {{SIZE}}',
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon:before' => 'opacity: {{SIZE}}',
				],
				'condition' => [ 'cs_next_prev_overlay' => 'yes' ],
			]
		);

		/*
		$this->add_control(
			'cs_next_icon_offset',
			[
				'label' => __( 'Next Icon Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'prefix_class' => 'ha-cs-next-icon-offset-'
			]
		);

		$this->start_popover();
		$this->add_responsive_control(
			'cs_next_icon_align_x',
			[
				'label' => __( 'Horizontal Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon i' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cs_next_icon_offset' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'cs_next_icon_align_y',
			[
				'label' => __( 'Vertical Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon i' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cs_next_icon_offset' => 'yes',
				]
			]
		);
		$this->end_popover(); */
		//end next icon offset

		//prev icon offset here..
		/*
		$this->add_control(
			'cs_prev_icon_offset',
			[
				'label' => __( 'Prev Icon Offset', 'happy-addons-pro' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'prefix_class' => 'ha-cs-next-icon-offset-'
			]
		);

		$this->start_popover();
		$this->add_responsive_control(
			'cs_prev_icon_align_x',
			[
				'label' => __( 'Horizontal Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon i' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cs_prev_icon_offset' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'cs_prev_icon_align_y',
			[
				'label' => __( 'Vertical Align', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1
					],
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon i' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'cs_prev_icon_offset' => 'yes',
				]
			]
		);
		$this->end_popover(); */
		//end next icon offset

		$this->add_control(
			'cs_advance_thumb_style',
			[
				'label' => __( '<b>Advance Style</b>', 'happy-addons-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'happy-addons-pro' ),
				'label_off' => __( 'No', 'happy-addons-pro' ),
				'return_value' => 'yes',
				'default' => 'no',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'cs_next_prev_width',
			[
				'label' => __('Width', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'render_type' => 'template',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 12,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container' => '--ha-cs-arrow-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => ['cs_advance_thumb_style' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'cs_next_prev_height',
			[
				'label' => __('Height', 'happy-addons-pro'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'em' => [
						'min' => 0,
						'max' => 12,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container ' => '--ha-cs-arrow-height: {{SIZE}}{{UNIT}}',
				],
				'condition' => ['cs_advance_thumb_style' => 'yes'],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'cs_next_prev_border',
                'selector' => '{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_item, {{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_item',
				'condition' => ['cs_advance_thumb_style' => 'yes'],
            ]
        );

		$this->add_responsive_control(
			'cs_next_prev_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .cc_prev_icon:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-creative-slider-container .cc_next_icon:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['cs_advance_thumb_style' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'cs_next_margin',
			[
				'label' => __( 'Next Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_next_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['cs_advance_thumb_style' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'cs_prev_margin',
			[
				'label' => __( 'Prev Margin', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ha-creative-slider-container .ha_cc_prev_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['cs_advance_thumb_style' => 'yes'],
			]
		);

		$this->end_controls_section();
	}

	//load/render preview
	protected function render() {

		$settings = $this->get_settings_for_display();
		extract( $settings );
		$sliders = !empty($settings['ha_creative_slider_list']) ? $settings['ha_creative_slider_list'] : [];

	?>

<div id="ha_creative_slider" class="ha-creative-slider-container">

		<div class="ha_cs_mobile_prev">
			<?php Icons_Manager::render_icon( $settings['cs_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
		</div>
		<div class="ha_cs_mobile_next">
			<?php Icons_Manager::render_icon( $settings['cs_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
		</div>

    <div class="ha_cc_prev_wrapper">
        <div class="ha_cc_prev_items owl-carousel owl-theme">
            <?php
				foreach ( $sliders as $key => $prevThumb ) { ?>
					<div class="ha_cc_prev_item elementor-repeater-item-<?php echo $prevThumb['_id']; ?>">
						<img src="<?php echo $prevThumb['cc_image']['url']; ?>" alt="Prev Thumb" />
					</div>
				<?php }
			?>
        </div>
		<span class="cc_prev_icon">
			<?php Icons_Manager::render_icon( $settings['cs_prev_icon'], [ 'aria-hidden' => 'true' ] ); ?>
		</span>
    </div>

    <div class="ha_cc_inner_wrapper">

        <div class="ha_cc_text_items owl-carousel">

		<?php
			foreach ( $sliders as $key => $textSlide ) { ?>

				<div class="ha_cc_info_text_inner elementor-repeater-item-<?php echo $textSlide['_id']; ?>">
					<div class="ha_cc_infotext_item">

						<?php
							if( !empty( $textSlide['cc_pre_title'] ) ) { ?>
								<span class="ha_cc_info_text_item_category">
									<?php Icons_Manager::render_icon( $textSlide['cc_pre_title_icon'], [ 'aria-hidden' => 'true' ] ); ?>
									<?php echo $textSlide['cc_pre_title']; ?>
								</span>
						<?php } if( !empty( $textSlide['cc_title'] ) ) { ?>
							<h3 class="ha_cc_info_text_title"><?php echo $textSlide['cc_title']; ?></h3>
						<?php } if( !empty( $textSlide['cc_sub_title'] )  ) { ?>
							<h4 class="ha_cc_info_text_sub_title"><?php echo $textSlide['cc_sub_title']; ?></h4>
						<?php } if( !empty( $textSlide['cc_description'] ) ) { ?>
							<p class="ha_cc_info_text_item_description"><?php echo $this->ha_get_word_trim($textSlide['cc_description'], $settings['cs_desc_word_limit']); ?></p>
						<?php } if( !empty( $textSlide['cc_btn_text'] ) ) { ?>
							<a class="ha_cc_info_text_item_btn" href="<?php echo esc_url(isset($textSlide['cc_btn_url']['url']) ? $textSlide['cc_btn_url']['url'] : ''); ?>" <?php echo esc_attr(($textSlide['cc_btn_url']['is_external']) ? 'target="_blank"' : ''); ?>>
								<?php echo $textSlide['cc_btn_text']; ?>
							</a>
						<?php } ?>

					</div>
				</div>

		<?php } ?>

        </div>

        <div class="ha_cc_inner_image_items owl-carousel">
			<?php
				foreach ( $sliders as $key => $slide ) { ?>
					<div class="ha_cc_image_item elementor-repeater-item-<?php echo $slide['_id']; ?>">
						<img src="<?php echo $slide['cc_image']['url']; ?>" alt="Creative Slider" />
					</div>
			<?php }
			?>
        </div>

        <div class="ha_cc_next_wrapper">
            <div class="ha_cc_next_items owl-carousel">
				<?php
					foreach ( $sliders as $key => $nextThumb ) { ?>
						<div class="ha_cc_next_item elementor-repeater-item-<?php echo $nextThumb['_id']; ?>">
							<img src="<?php echo $nextThumb['cc_image']['url'] ?>" alt="Next Thumb" />
						</div>
				<?php }
				?>
            </div>
			<span class="cc_next_icon">
				<?php Icons_Manager::render_icon( $settings['cs_next_icon'], [ 'aria-hidden' => 'true' ] ); ?>
			</span>
        </div>

    </div>

</div>

<?php
	}


	/**
	 * ha_get_word_trim
	 * @param (str) $description
	 * @param (int) $wordsLimit
	 *
	 * @return formated string
	 */
	protected function ha_get_word_trim( $description, $wordsLimit=30 ) {
		$formated_string = implode(
			'',
			array_slice(
				preg_split(
					'/([\s,\.;\?\!]+)/',
					$description,
					$wordsLimit*2+1,
					PREG_SPLIT_DELIM_CAPTURE
				),
				0,
				$wordsLimit*2-1
			)
		);

		//count word & concatenate spread
		$orginString 	= preg_replace('/\s+/', ' ', trim($description));
        $getWords 		= explode(" ", $orginString);
        $countWords 	= count($getWords);
		$spred = !empty( $formated_string ) && $countWords > $wordsLimit ? '...' : '';

		return $formated_string . $spred;

	}

}
