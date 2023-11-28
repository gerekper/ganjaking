<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Icons_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Plugin;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Classes\Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Lightbox extends Widget_Base
{

	public function get_name()
	{
		return 'eael-lightbox';
	}

	public function get_title()
	{
		return esc_html__('Lightbox &amp; Modal', 'essential-addons-elementor');
	}

	public function get_icon()
	{
		return 'eaicon-lightbox-modal';
	}

	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'lightbox',
			'ea lightbox',
			'ea modal',
			'ea popup',
			'modal popup',
			'popup',
			'popup builder',
			'fomo popup',
			'ea',
			'essential addons'
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/lightbox-modal/';
	}



	protected function register_controls()
	{

        $this->content_tab();

		/**
		 * Style Tab: Title
		 * -------------------------------------------------
		 */

        $this->style_title();

		/**
		 * Style Tab: Lightbox
		 * -------------------------------------------------
		 */

		$this->style_lightbox();

		 # Lightbox styles

		/**
		 * Style Tab: Overlay
		 * -------------------------------------------------
		 */

		$this->style_overlay();

		# Lightbox styles


		/**
		 * Style Tab: Icon
		 * -------------------------------------------------
		 */

        $this->style_icon();

		/**
		 * Style Tab: Button
		 * -------------------------------------------------
		 */

		$this->style_button();

		/**
		 * Style Tab: Content Styles
		 * -------------------------------------------------
		 */

        $this->content_style();

		/**
		 * Style Tab: Close Button
		 * -------------------------------------------------
		 */
		$this->close_button_style();

	}

	public function content_tab(){
		/*
		/*	CONTENT TAB
		/*-------------------------------------------------*/

		# Lightbox || Modal
		$this->start_controls_section(
			'eael_section_ligthbox_modal',
			[
				'label' => esc_html__('Lightbox || Modal', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'layout_type',
			[
				'label'                 => __('Layout', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'standard'      => __('Standard', 'essential-addons-elementor'),
					'fullscreen'    => __('Fullscreen', 'essential-addons-elementor'),
				],
				'default'               => 'standard',
			]
		);

		$this->add_responsive_control(
			'lightbox_popup_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '550',
					'unit'      => 'px',
				],
				'mobile_default' => [
					'size' => 325,
					'unit' => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 1920,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}}' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
					'.eael-lightbox-modal-window-{{ID}}' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}'
				],
				'condition'             => [
					'layout_type'    => 'standard',
				],
			]
		);

		$this->add_control(
			'auto_height',
			[
				'label'             => __('Auto Height', 'essential-addons-elementor'),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __('Yes', 'essential-addons-elementor'),
				'label_off'         => __('No', 'essential-addons-elementor'),
				'return_value'      => 'yes',
				'condition'             => [
					'layout_type'  => 'standard',
				],
			]
		);

		$this->add_responsive_control(
			'popup_height',
			[
				'label'                 => __('Height', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '450',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 1000,
						'step'  => 1,
					],
					'em'        => [
						'min'   => 0,
						'max'   => 50,
						'step'  => 0.1,
					],
				],
				'size_units'            => ['px', 'em'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .eael-lightbox-container' => 'height: {{SIZE}}{{UNIT}}; overflow: auto;',
					'.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}}.lightbox_type_image .eael-lightbox-container img' => 'max-height: 100%;',
					'.eael-lightbox-modal-window-{{ID}} .modal-popup-window-inner' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'auto_height!' => 'yes',
					'layout_type'  => 'standard',
				],
			]
		);
		$this->end_controls_section(); # End Of Lightbox || Modal

		# Content Section
		$this->start_controls_section(
			'eael_section_ligthbox_content',
			[
				'label' => esc_html__('Content', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'popup_lightbox_title',
			[
				'label'             => __('Enable Title', 'essential-addons-elementor'),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => '',
				'label_on'          => __('Yes', 'essential-addons-elementor'),
				'label_off'         => __('No', 'essential-addons-elementor'),
				'return_value'      => 'yes',
				'condition'             => [
					'eael_lightbox_type'  => ['lightbox_type_content','lightbox_type_custom_html'],
				],
			]
		);

		$this->add_control(
			'title',
			[
				'label'                 => __('Title', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::TEXT,
				'default'               => __('Lightbox Title', 'essential-addons-elementor'),
				'condition'             => [
					'popup_lightbox_title'  => 'yes',
					'eael_lightbox_type'  => ['lightbox_type_content','lightbox_type_custom_html'],
				],
				'dynamic'	=> ['active', true],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_lightbox_type',
			[
				'label'   => esc_html__('Type', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'lightbox_type_image',
				'options' => [
					'lightbox_type_image'		=> esc_html__('Image', 'essential-addons-elementor'),
					'lightbox_type_url'			=> esc_html__('Link (Page/Video/Map)', 'essential-addons-elementor'),
					'lightbox_type_content'		=> esc_html__('Content', 'essential-addons-elementor'),
					'lightbox_type_template'	=> esc_html__('Saved Templates', 'essential-addons-elementor'),
					'lightbox_type_custom_html'	=> esc_html__('Custom HTML', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'eael_lightbox_type_image',
			[
				'label'   => __('Choose Lightbox Image', 'essential-addons-elementor'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eael_lightbox_type' => 'lightbox_type_image',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_primary_templates',
			[
				'label'     => __('Choose Template', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SELECT,
				'options'   => Helper::get_elementor_templates(),
				'condition' => [
					'eael_lightbox_type' => 'lightbox_type_template',
				],
			]
		);

		$this->add_control(
			'eael_lightbox_type_content',
			[
				'label'     => __('Add your content here (HTML/Shortcode)', 'essential-addons-elementor'),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => __('Add your popup content here', 'essential-addons-elementor'),
				'condition' => [
					'eael_lightbox_type'      => 'lightbox_type_content'
				],
				'dynamic' => ['active' => true]
			]
		);

		$this->add_control(
			'eael_lightbox_type_url',
			[
				'label'       => __('Provide Page/Video/Map URL', 'essential-addons-elementor'),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => 'https://www.youtube.com/watch?v=Y2Xt0RE9HDQ',
				],
				'show_external'     => false,
				'title'       => __('Place Page/Video/Map URL', 'essential-addons-elementor'),
				'condition'   => [
					'eael_lightbox_type' => 'lightbox_type_url',
				],
			]
		);

		$this->add_control(
			'custom_html',
			[
				'label'                 => __('Custom HTML', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CODE,
				'language'          => 'html',
				'condition'             => [
					'eael_lightbox_type'    => 'lightbox_type_custom_html',
				],
			]
		);

		$this->end_controls_section(); # End Of Content Section


		# Settings Section
		$this->start_controls_section(
			'eael_section_ligthbox_settings',
			[
				'label' => esc_html__('Settings', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'eael_lightbox_trigger_type',
			[
				'label'   => esc_html__('Trigger', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'eael_lightbox_trigger_button',
				'options' => [
					'eael_lightbox_trigger_button'		=> esc_html__('Button Click', 'essential-addons-elementor'),
					'eael_lightbox_trigger_pageload'	=> esc_html__('Page Load', 'essential-addons-elementor'),
					'eael_lightbox_trigger_exit_intent'	=> esc_html__('Exit Intent', 'essential-addons-elementor'),
					'eael_lightbox_trigger_external'	=> esc_html__('External Element', 'essential-addons-elementor'),
				],
			]
		);

		# Lightbox trigger button
		$this->add_control(
			'page_load_heading',
			[
				'label'                 => __('Button Click Settings', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'eael_lightbox_trigger_type'    => 'eael_lightbox_trigger_button',
				],
			]
		);

		$this->add_control(
			'trigger_type',
			[
				'label'                 => __('Type', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'button',
				'options'               => [
					'button'       => __('Button', 'essential-addons-elementor'),
					'icon'         => __('Icon', 'essential-addons-elementor'),
					'image'        => __('Image', 'essential-addons-elementor'),
				],
				'condition'             => [
					'eael_lightbox_trigger_type'    => 'eael_lightbox_trigger_button',
				],
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn',
			[
				'label'       => esc_html__('Button Text', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
				'default'     => esc_html__( 'Open Popup', 'essential-addons-elementor' ),
				'condition'   => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'	=> 'button'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn_icon_new',
			[
				'label'     => esc_html__('Button Icon', 'essential-addons-elementor'),
				'type'      => Controls_Manager::ICONS,
				'fa4compatibility' => 'eael_lightbox_open_btn_icon',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'	=> 'button'
				],
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn_icon_align',
			[
				'label'   => esc_html__('Icon Position', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__('Before', 'essential-addons-elementor'),
					'right' => esc_html__('After', 'essential-addons-elementor'),
				],
				'condition' => [
					'eael_lightbox_open_btn_icon_new!' => '',
					'eael_lightbox_trigger_type'   => 'eael_lightbox_trigger_button',
					'trigger_type'                 => 'button'
				],
			]
		);

		$this->add_control(
			'trigger_only_icon_new',
			[
				'label'     => esc_html__('Trigger Icon', 'essential-addons-elementor'),
				'type'      => Controls_Manager::ICONS,
				'fa4compatibility' => 'trigger_only_icon',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'               => 'icon'
				],
			]
		);

		$this->add_control(
			'trigger_only_image',
			[
				'label'   => __('Trigger Image', 'essential-addons-elementor'),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'               => 'image'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_open_btn_alignment',
			[
				'label'       => esc_html__('Alignment', 'essential-addons-elementor'),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-wrapper' => 'justify-content: {{VALUE}}',
				],
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
				],
			]
		);
		# End of lightbox trigger button

		# Lightbox trigger Page load
		$this->add_control(
			'delay_heading',
			[
				'label'			=> __('Page Load Settings', 'essential-addons-elementor'),
				'type'			=> Controls_Manager::HEADING,
				'separator'		=> 'before',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_pageload',
				],
			]
		);

		$this->add_control(
			'delay',
			[
				'label'                 => __( 'Delay', 'essential-addons-elementor' ),
				'title'                 => __( 'seconds', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default'               => '1',
				'condition'             => [
					'eael_lightbox_trigger_type'	=> 'eael_lightbox_trigger_pageload',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'display_after_page_load',
			[
				'label'                 => __('Display After', 'essential-addons-elementor'),
				'title'                 => __('day(s)', 'essential-addons-elementor'),
				'description'           => __('If a user closes the modal box, it will be displayed only after the defined day(s)', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::TEXT,
				'default'               => '1',
				'condition'             => [
					'eael_lightbox_trigger_type'	=> 'eael_lightbox_trigger_pageload',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		# End of lightbox trigger Page load

		# Exit intent
		$this->add_control(
			'exit_intent_heading',
			[
				'label'                 => __('Exit Intent Settings', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'eael_lightbox_trigger_type'	=> 'eael_lightbox_trigger_exit_intent',
				],
			]
		);

		$this->add_control(
			'display_after_exit_intent',
			[
				'label'                 => __('Display After', 'essential-addons-elementor'),
				'title'                 => __('day(s)', 'essential-addons-elementor'),
				'description'           => __('If a user closes the modal box, it will be displayed only after the defined day(s)', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::TEXT,
				'default'               => '1',
				'condition'             => [
					'eael_lightbox_trigger_type'	=> 'eael_lightbox_trigger_exit_intent',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		# End of exit intent

		# Lightbox trigger external
		$this->add_control(
			'eael_lightbox_trigger_external',
			[
				'label'       => __('Element Identifier', 'essential-addons-elementor'),
				'type'        => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'label_block' => true,
				'default'     => '#open-popup',
				'placeholder' => __('#open-popup', 'essential-addons-elementor'),
				'title'       => __('#open-popup', 'essential-addons-elementor'),
				'description' => __('You can also use class identifier such as <strong>.open-popup</strong>', 'essential-addons-elementor'),
				'condition'   => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_external',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		# End of lightbox trigger external

		# Exit settings
		$this->add_control(
			'exit_heading',
			[
				'label'			=> __('Exit Settings', 'essential-addons-elementor'),
				'type'			=> Controls_Manager::HEADING,
				'separator'		=> 'before',
			]
		);

		$this->add_control(
			'close_button',
			[
				'label'             => __('Show Close Button', 'essential-addons-elementor'),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __('Yes', 'essential-addons-elementor'),
				'label_off'         => __('No', 'essential-addons-elementor'),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'esc_exit',
			[
				'label'             => __('Esc to Exit', 'essential-addons-elementor'),
				'description'       => __('Close the modal box by pressing the Esc key', 'essential-addons-elementor'),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __('Yes', 'essential-addons-elementor'),
				'label_off'         => __('No', 'essential-addons-elementor'),
				'return_value'      => 'yes',
			]
		);

		$this->add_control(
			'click_exit',
			[
				'label'             => __('Click to Exit', 'essential-addons-elementor'),
				'description'       => __('Close the modal box by clicking anywhere outside the modal window', 'essential-addons-elementor'),
				'type'              => Controls_Manager::SWITCHER,
				'default'           => 'yes',
				'label_on'          => __('Yes', 'essential-addons-elementor'),
				'label_off'         => __('No', 'essential-addons-elementor'),
				'return_value'      => 'yes',
			]
		);
		# End of exit settings

		# Lightbox trigger page load
		$this->add_control(
			'eael_lightbox_trigger_pageload',
			[
				'label'   => esc_html__('Delay (Seconds)', 'essential-addons-elementor'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range'	=> [
					'ms'	=> [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_pageload',
				],
			]
		); # End of lightbox trigger page load

		$this->end_controls_section();
		# End of Settings Section


		# Animation Section
		$this->start_controls_section(
			'animation_section',
			[
				'label' => esc_html__('Animation', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'lightbox_modal_animation_in',
			[
				'label'                 => __('Animation', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT2,
				'default'               => '',
				'options'               => [
					'mfp-zoom-in'       => __('Zoom In', 'essential-addons-elementor'),
					'mfp-zoom-out'      => __('Zoom Out', 'essential-addons-elementor'),
					'mfp-3d-unfold'     => __('3D Unfold', 'essential-addons-elementor'),
					'mfp-newspaper'     => __('Newspaper', 'essential-addons-elementor'),
					'mfp-move-from-top' => __('Move From Top', 'essential-addons-elementor'),
					'mfp-move-left'     => __('Move Left', 'essential-addons-elementor'),
					'mfp-move-right'    => __('Move Right', 'essential-addons-elementor'),
				],
			]
		);

		$this->end_controls_section(); # End of Animation Section
	}

	public function style_title(){
		$this->start_controls_section(
			'section_title_style',
			[
				'label'                 => __('Title', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'popup_lightbox_title'   => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'title_align',
			[
				'label'                 => __('Alignment', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'text-align: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title'	=> 'text-align: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'title_bg',
			[
				'label'                 => __('Background Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'background-color: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'                 => __('Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'color: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'title_border',
				'label'                 => __('Border', 'essential-addons-elementor'),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title, .eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title',
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'                 => __('Padding', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'title_typography',
				'label'                 => __('Typography', 'essential-addons-elementor'),
				'selector'              => '.eael-lightbox-popup-window-{{ID}} .eael-lightbox-header .eael-lightbox-title, .eael-lightbox-modal-window-{{ID}} .eael-lightbox-header .eael-lightbox-title',
			]
		);
		$this->end_controls_section();
	}

	public function style_lightbox(){
		$this->start_controls_section(
			'eael_section_lightbox_styles',
			[
				'label' => esc_html__('Lightbox', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'eael_lightbox_container_bg',
			[
				'label'     => esc_html__('Background', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}} .eael-lightbox-container' => 'background-color: {{VALUE}};',
					'.eael-lightbox-popup-window.eael-lightbox-modal-window-{{ID}} .mfp-iframe' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'eael_lightbox_container_border',
				'selector' => '.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}} .eael-lightbox-container, .eael-lightbox-popup-window.eael-lightbox-modal-window-{{ID}} .mfp-iframe',
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_container_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'.eael-lightbox-popup-window.eael-lightbox-modal-window-{{ID}} .mfp-iframe' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
					'.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}} .eael-lightbox-container'	=> 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_container_padding',
			[
				'label'      => esc_html__('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}} .eael-lightbox-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.eael-lightbox-popup-window.eael-lightbox-modal-window-{{ID}} .mfp-iframe' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'lightbox_box_shadow',
				'selector'              => '.eael-lightbox-popup-window.eael-lightbox-popup-window-{{ID}}, .eael-lightbox-popup-window.eael-lightbox-modal-window-{{ID}}',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	public function style_overlay(){
		$this->start_controls_section(
			'eael_section_lightbox_overlay',
			[
				'label' => esc_html__('Overlay', 'essential-addons-elementor'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);
		$this->add_control(
			'eael_lightbox_container_overlay',
			[
				'label'     => esc_html__('Enable dark overlay?', 'essential-addons-elementor'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => __('yes', 'essential-addons-elementor'),
				'label_off' => __('no', 'essential-addons-elementor'),
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'eael_lightbox_container_overlay_color',
			[
				'label'     => esc_html__('Overlay Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => "rgba(0,0,0,.8)",
				'selectors' => [
					'.mfp-bg.eael-lightbox-modal-popup-{{ID}}' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_lightbox_container_overlay' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	public function style_icon(){
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'                 => __('Icon', 'essential-addons-elementor'),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'               => ['icon', 'image']
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'                 => __('Size', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '28',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'{{WRAPPER}} .eael-trigger-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eael-trigger-svg-icon svg'	=> 'width: {{SIZE}}{{UNIT}};'
				],
				'condition'             => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_responsive_control(
			'icon_bg_size',
			[
				'label' => __('Icon Background Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 90,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
				'condition' => [
					'lightbox_icon_bg_shape!' => 'none',
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->start_controls_tabs('eael_lightbox_icon_style_controls');

		$this->start_controls_tab(
			'lightbox_icon_normal',
			[
				'label' => esc_html__('Normal', 'essential-addons-elementor'),
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_color',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#4d4d4d',
				'selectors' => [
					'{{WRAPPER}} .eael-trigger-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-trigger-icon svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_bg_shape',
			[
				'label' => esc_html__('Background Shape', 'essential-addons-elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'label_block' => false,
				'options' => [
					'none' => esc_html__('None', 'essential-addons-elementor'),
					'circle' => esc_html__('Circle', 'essential-addons-elementor'),
					'radius' => esc_html__('Radius', 'essential-addons-elementor'),
					'square' => esc_html__('Square', 'essential-addons-elementor'),
				],
				'prefix_class' => 'eael-lightbox-icon-bg-shape-',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn' => 'background: {{VALUE}};',
				],
				'condition' => [
					'lightbox_icon_bg_shape!' => 'none',
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'lightbox_icon_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-lightbox-btn',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'lightbox_icon_shadow',
				'selector' => '{{WRAPPER}} .eael-lightbox-btn',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'lightbox_icon_hover',
			[
				'label' => esc_html__('Hover', 'essential-addons-elementor'),
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_hover_animation',
			[
				'label' => esc_html__('Animation', 'essential-addons-elementor'),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_hover_color',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-trigger-icon:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .eael-trigger-icon:hover svg' => 'fill: {{VALUE}}',
				],
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'lightbox_icon_hover_bg_shape!' => 'none',
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_control(
			'lightbox_icon_hover_bg_shape',
			[
				'label' => esc_html__('Background Shape', 'essential-addons-elementor'),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'label_block' => false,
				'options' => [
					'none' => esc_html__('None', 'essential-addons-elementor'),
					'circle' => esc_html__('Circle', 'essential-addons-elementor'),
					'radius' => esc_html__('Radius', 'essential-addons-elementor'),
					'square' => esc_html__('Square', 'essential-addons-elementor'),
				],
				'prefix_class' => 'eael-lightbox-icon-hover-bg-shape-',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'lightbox_hover_icon_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .eael-lightbox-btn:hover',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'lightbox_icon_hover_shadow',
				'selector' => '{{WRAPPER}} .eael-lightbox-btn:hover',
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'icon',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();





		$this->add_responsive_control(
			'icon_image_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'default'	=> [
					'unit'	=> 'px',
					'size'	=> 150
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'{{WRAPPER}} .eael-trigger-image' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'image',
				],
			]
		);

		$this->add_control(
			'icon_image_border_radius',
			[
				'label'                 => __('Border Radius', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'{{WRAPPER}} .eael-trigger-image' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'  => 'image',
				],
			]
		);

		$this->end_controls_section();
	}

	public function style_button(){
		$this->start_controls_section(
			'eael_section_lightbox_trigger_styles',
			[
				'label'     => esc_html__('Button', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
					'trigger_type'               => 'button',
					'eael_lightbox_open_btn!'    => '',
				]
			]
		);

		$this->add_control(
			'button_size',
			[
				'label'                 => __('Size', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'md',
				'options'               => [
					'xs' => __('Extra Small', 'essential-addons-elementor'),
					'sm' => __('Small', 'essential-addons-elementor'),
					'md' => __('Medium', 'essential-addons-elementor'),
					'lg' => __('Large', 'essential-addons-elementor'),
					'xl' => __('Extra Large', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn_icon_indent',
			[
				'label' => esc_html__('Icon Spacing', 'essential-addons-elementor'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'	=> [
					'{{WRAPPER}} .eael-lightbox-btn .open-pop-up-button-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-lightbox-btn .open-pop-up-button-icon-left'  => 'margin-right: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_open_btn_padding',
			[
				'label'      => esc_html__('Padding', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .eael-lightbox-btn > span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_open_btn_margin',
			[
				'label'      => esc_html__('Margin', 'essential-addons-elementor'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .eael-lightbox-btn > span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'eael_lightbox_open_btn_border_radius',
            [
                'label' => esc_html__('Button Border Radius', 'essential-addons-elementor'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eael-lightbox-btn > span' => 'border-radius: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

        $this->add_control(
            'eael_lightbox_icon_size',
            [
                'label' => esc_html__('Icon Size', 'essential-addons-elementor'),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'default' => ['size'=>'16','unit'=>'px'],
                'selectors' => [
                    '{{WRAPPER}} .eael-lightbox-btn > span i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-lightbox-btn > span svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ]
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'eael_lightbox_open_btn_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector'  => '{{WRAPPER}} .eael-lightbox-btn > span'
			]
		);

		$this->start_controls_tabs('eael_lightbox_open_btn_content_tabs');

		$this->start_controls_tab(
			'normal_default_content',
			[
				'label'     => esc_html__('Normal', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn_text_color',
			[
				'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn > span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-lightbox-btn > span .eael-lightbox-button-svg-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_lightbox_open_btn_background_color',
			[
				'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn > span' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'eael_lightbox_open_btn_border',
				'selector'  => '{{WRAPPER}} .eael-lightbox-btn > span',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_lightbox_open_btn_shadow',
				'selector' => '{{WRAPPER}} .eael-lightbox-btn > span'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'eael_lightbox-open_btn_hover',
			[
				'label'     => esc_html__('Hover', 'essential-addons-elementor'),
				'condition' => [
					'eael_lightbox_trigger_type' => 'eael_lightbox_trigger_button',
				],
			]
		);

		$this->add_control(
			'eael_lightbox-open_btn_hover_text_color',
			[
				'label'     => esc_html__('Text Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn > span:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-lightbox-btn > span:hover .eael-lightbox-button-svg-icon svg' => 'fill: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'eael_lightbox-open_btn_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#272727',
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn > span:hover' => 'background-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'eael_lightbox-open_btn_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eael-lightbox-btn > span:hover' => 'border-color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'button_animation',
			[
				'label'     => __('Animation', 'essential-addons-elementor'),
				'type'      => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					'trigger_type'               => 'button',
					'eael_lightbox_open_btn!'    => ''
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'eael_lightbox_open_btn_hover_shadow',
				'selector' => '{{WRAPPER}} .eael-lightbox-btn > span:hover'
			]
		);
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	public function content_style(){
		$this->start_controls_section(
			'eael_section_lightbox_content_styles',
			[
				'label'     => esc_html__('Content Styles', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_lightbox_type' => 'lightbox_type_content',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'eael_lightbox_content_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY
				],
				'selector' => '.eael-lightbox-container .eael-lightbox-content'
			]
		);

		$this->add_control(
			'eael_lightbox_content_color',
			[
				'label'     => esc_html__('Color', 'essential-addons-elementor'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.eael-lightbox-container .eael-lightbox-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function close_button_style(){
		$this->start_controls_section(
			'eael_section_lightbox_closebtn_styles',
			[
				'label'     => esc_html__('Close Button', 'essential-addons-elementor'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'close_button'	=> 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'close_button_size',
			[
				'label'                 => __('Size', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '28',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 10,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close' => 'font-size: {{SIZE}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_weight',
			[
				'label'                 => __('Weight', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'normal',
				'options'               => [
					'normal'   => __('Normal', 'essential-addons-elementor'),
					'bold'     => __('Bold', 'essential-addons-elementor'),
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close' => 'font-weight: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close' => 'font-weight: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_width',
			[
				'label'                 => __('Width', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 150,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close, .eael-lightbox-popup-window-{{ID}} .mfp-close:hover' => 'width: {{SIZE}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close, .eael-lightbox-modal-window-{{ID}} .mfp-close:hover' => 'width: {{SIZE}}{{UNIT}}; text-align: center;',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_height',
			[
				'label'                 => __('Height', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 150,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close' => 'height: {{SIZE}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_position_heading',
			[
				'label'                 => __('Close Button Position', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::HEADING,
				'separator'				=> 'before',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_close_button_left_position',
			[
				'label'      => esc_html__('Position Right', 'essential-addons-elementor'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'			=> [
					'px'	=> [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'.eael-lightbox-popup-window-{{ID}} button.mfp-close' => 'right: -{{SIZE}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} button.mfp-close' => 'right: -{{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'eael_lightbox_close_button_top_position',
			[
				'label'			=> esc_html__('Position Top', 'essential-addons-elementor'),
				'type'			=> Controls_Manager::SLIDER,
				'size_units'	=> ['px', '%'],
				'separator'		=> 'after',
				'range'			=> [
					'px'	=> [
						'min'	=> 0,
						'max'	=> 500,
					],
				],
				'selectors' => [
					'.eael-lightbox-popup-window-{{ID}} button.mfp-close' => 'top: -{{SIZE}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} button.mfp-close' => 'top: -{{SIZE}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_margin',
			[
				'label'       => __('Margin', 'essential-addons-elementor'),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => ['px', '%'],
				'separator'   => 'before',
				'placeholder' => [
					'top'    => '',
					'right'  => '',
					'bottom' => '',
					'left'   => '',
				],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} button.mfp-close' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} button.mfp-close' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'close_button' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'close_button_padding',
			[
				'label'                 => __('Padding', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%'],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} button.mfp-close' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} button.mfp-close' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_close_button_style');

		$this->start_controls_tab(
			'tab_close_button_normal',
			[
				'label'                 => __('Normal', 'essential-addons-elementor'),
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'eael_lightbox_closebtn_color',
			[
				'label'                 => __('Icon Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ffffff',
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close' => 'color: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'eael_lightbox_closebtn_bg',
				'label'                 => __('Background', 'essential-addons-elementor'),
				'types'                 => ['classic', 'gradient'],
				'selector'              => '.eael-lightbox-popup-window-{{ID}} button.mfp-close, .eael-lightbox-modal-window-{{ID}} button.mfp-close',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'close_button_border_normal',
				'label'                 => __('Border', 'essential-addons-elementor'),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.eael-lightbox-popup-window-{{ID}} .mfp-close, .eael-lightbox-modal-window-{{ID}} .mfp-close',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_border_radius',
			[
				'label'                 => __('Border Radius', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_close_button_hover',
			[
				'label'                 => __('Hover', 'essential-addons-elementor'),
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_color_hover',
			[
				'label'                 => __('Icon Color', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close:hover' => 'color: {{VALUE}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close:hover' => 'color: {{VALUE}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'close_button_bg_hover',
				'label'                 => __('Background', 'essential-addons-elementor'),
				'types'                 => ['classic', 'gradient'],
				'selector'              => '.eael-lightbox-popup-window-{{ID}} .mfp-close:hover, .eael-lightbox-modal-window-{{ID}} .mfp-close:hover',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'close_button_border_hover',
				'label'                 => __('Border', 'essential-addons-elementor'),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '.eael-lightbox-popup-window-{{ID}} .mfp-close:hover, .eael-lightbox-modal-window-{{ID}} .mfp-close:hover',
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->add_control(
			'close_button_border_radius_hover',
			[
				'label'                 => __('Border Radius', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', '%'],
				'selectors'             => [
					'.eael-lightbox-popup-window-{{ID}} .mfp-close:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.eael-lightbox-modal-window-{{ID}} .mfp-close:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'close_button'   => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
        $uniq_id = uniqid() ;
        $widget_id = $this->get_id();
		$settings    = $this->get_settings_for_display();
		$popup_image = $settings['eael_lightbox_type_image'];
        $button_icon_migrated = isset($settings['__fa4_migrated']['eael_lightbox_open_btn_icon_new']);
        $button_icon_is_new = empty($settings['eael_lightbox_open_btn_icon']);
        $trigger_icon_migrated = isset($settings['__fa4_migrated']['trigger_only_icon_new']);
        $trigger_icon_is_new = empty($settings['trigger_only_icon']);

		$this->add_render_attribute(
			'eael-lightbox-wrapper',
			[
				'data-lightbox-type'              => $settings['eael_lightbox_type'],
				'data-lightbox-type-url'          => $settings['eael_lightbox_type_url'],
				'data-lightbox-trigger-pageload'  => $this->get_settings('eael_lightbox_trigger_pageload')['size'],
				'data-lightbox-closebtn-color'    => $settings['eael_lightbox_closebtn_color']
			]
		);

		$this->add_render_attribute('eael-lightbox-wrapper', 'class', 'eael-lightbox-wrapper');
		$this->add_render_attribute('eael-lightbox-wrapper', 'data-trigger', $settings['eael_lightbox_trigger_type']);
		$this->add_render_attribute('eael-lightbox-wrapper', 'data-lightbox-id', 'lightbox_' . esc_attr($uniq_id));

		// Popup Type
		if ('lightbox_type_image' == $settings['eael_lightbox_type'] || 'lightbox_type_content' == $settings['eael_lightbox_type'] || 'lightbox_type_template' == $settings['eael_lightbox_type'] || 'lightbox_type_custom_html' == $settings['eael_lightbox_type']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-type', 'inline');
		} else if ('lightbox_type_url' === $settings['eael_lightbox_type']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-type', 'iframe');
		} else {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-type', $settings['eael_lightbox_type']);
		}

		if ('lightbox_type_image' === $settings['eael_lightbox_type'] || 'lightbox_type_content' === $settings['eael_lightbox_type'] || 'lightbox_type_template' === $settings['eael_lightbox_type'] || 'lightbox_type_custom_html' === $settings['eael_lightbox_type']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-src', '#eael-lightbox-window-' . esc_attr($uniq_id));
		}
		if ('lightbox_type_url' === $settings['eael_lightbox_type']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-src', esc_url($settings['eael_lightbox_type_url']['url']));
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-iframe-class', 'eael-lightbox-popup-window eael-lightbox-modal-window-' . esc_attr($widget_id));
		}

		if ($settings['layout_type'] == 'fullscreen') {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-popup-layout', 'eael-lightbox-popup-fullscreen');
		} else {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-popup-layout', 'eael-lightbox-popup-standard');
		}

		if ('yes' == $settings['eael_lightbox_container_overlay']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-main-class', 'eael-lightbox-modal-popup-' . esc_attr($widget_id));
		} else {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-main-class', 'eael-lightbox-no-overlay eael-lightbox-modal-popup-' . esc_attr($uniq_id));
		}

		if ('yes' === $settings['close_button']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-close_button', 'yes');
		}

		if ('yes' === $settings['esc_exit']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-esc_exit', 'yes');
		}

		if ('yes' === $settings['click_exit']) {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-click_exit', 'yes');
		}

		$this->add_render_attribute('eael-lightbox-wrapper', 'data-effect', 'animated ' . $settings['lightbox_modal_animation_in']);

		// Trigger
		if ($settings['eael_lightbox_trigger_type'] != 'eael_lightbox_trigger_external') {
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-trigger-element', '.eael-modal-popup-link-' . esc_attr($uniq_id));
		}

		if (($settings['eael_lightbox_trigger_type']) == 'eael_lightbox_trigger_button' && $settings['trigger_type'] == 'button') {
			$trigger_html_tag = 'span';

			$this->add_render_attribute(
				'trigger_button',
				[
					'id'       => 'btn-eael-lightbox-' . esc_attr( $uniq_id ),
					'class'    => [
						'eael-modal-popup-button',
						'eael-modal-popup-link',
						'eael-modal-popup-link-' . esc_attr( $uniq_id ),
						'elementor-button',
						'elementor-size-' . $settings['button_size'],
					],
					'tabindex' => '0'
				]
			);

			if ($settings['button_animation']) {
				$this->add_render_attribute('trigger_button', 'class', 'elementor-animation-' . $settings['button_animation']);
			}
		} else if ($settings['eael_lightbox_trigger_type'] == 'eael_lightbox_trigger_pageload') {
			$eael_delay = 1000;
			if ($settings['delay'] != '') {
				$eael_delay = $settings['delay'] * 1000;
			}
			$this->add_render_attribute('eael-lightbox-wrapper', 'data-delay', $eael_delay);

			if ($settings['display_after_page_load'] != '') {
				$this->add_render_attribute('eael-lightbox-wrapper', 'data-display-after', $settings['display_after_page_load']);
			}
		} else if ($settings['eael_lightbox_trigger_type'] == 'eael_lightbox_trigger_exit_intent') {
			if ($settings['display_after_exit_intent'] != '') {
				$this->add_render_attribute('eael-lightbox-wrapper', 'data-display-after', $settings['display_after_exit_intent']);
			}
		} else if ($settings['eael_lightbox_trigger_type'] == 'eael_lightbox_trigger_external') {
			if ($settings['eael_lightbox_trigger_external'] != '') {
				$this->add_render_attribute('eael-lightbox-wrapper', 'data-trigger-element', $settings['eael_lightbox_trigger_external']);
			}
		}

		// Popup Window
		$this->add_render_attribute('lightbox-popup-window', 'class', 'eael-lightbox-popup-window eael-lightbox-popup-window-' . esc_attr($widget_id));

		$this->add_render_attribute('lightbox-popup-window', 'id', 'eael-lightbox-window-' . esc_attr($uniq_id));

		// Popup window container
		$this->add_render_attribute('popup-window-container', 'class', 'eael-lightbox-container');

		// Content based wrapper class
		$this->add_render_attribute('lightbox-popup-window', 'class', $settings['eael_lightbox_type']);
?>


		<div <?php echo $this->get_render_attribute_string('eael-lightbox-wrapper'); ?>>
			<div class="eael-lightbox-btn">
				<?php
				if (($settings['eael_lightbox_trigger_type']) == 'eael_lightbox_trigger_button') {

					if ('button' == $settings['trigger_type']) {
						printf('<%1$s %2$s>', $trigger_html_tag, $this->get_render_attribute_string('trigger_button'));
						if ( ($button_icon_migrated || $button_icon_is_new) && $settings['eael_lightbox_open_btn_icon_align'] == 'left') {
                            echo '<span class="open-pop-up-button-icon-left eael-lightbox-button-svg-icon">';
                            Icons_Manager::render_icon( $settings['eael_lightbox_open_btn_icon_new'] );
                            echo '</span>';
                        } else if (!empty($settings['eael_lightbox_open_btn_icon'])) {
                            printf('<i class="open-pop-up-button-icon-left %1$s" aria-hidden="true"></i>', $settings['eael_lightbox_open_btn_icon']);
                        }

						echo Helper::eael_wp_kses($settings['eael_lightbox_open_btn']);

                        if ( ($button_icon_migrated || $button_icon_is_new) && $settings['eael_lightbox_open_btn_icon_align'] == 'right') {
                            echo '<span class="open-pop-up-button-icon-right eael-lightbox-button-svg-icon">';
                            Icons_Manager::render_icon( $settings['eael_lightbox_open_btn_icon_new'] );
                            echo '</span>';
                        } else if (!empty($settings['eael_lightbox_open_btn_icon'])) {
                            printf('<i class="open-pop-up-button-icon-right %1$s" aria-hidden="true"></i>', $settings['eael_lightbox_open_btn_icon']);
                        }

						printf('</%1$s>', $trigger_html_tag);

					} else if ('icon' ==  $settings['trigger_type']) {

                        if ( $trigger_icon_migrated || $trigger_icon_is_new ) {
                            echo '<span class="eael-trigger-icon eael-trigger-svg-icon eael-modal-popup-link eael-modal-popup-link-' . esc_attr($uniq_id) .'">';
                            Icons_Manager::render_icon( $settings['trigger_only_icon_new'] );
                            echo '</span>';
                        } else if (!empty($settings['trigger_only_icon'])) {
                            printf('<i class="eael-trigger-icon eael-modal-popup-link %1$s %2$s" aria-hidden="true"></i>', $settings['trigger_only_icon'], 'eael-modal-popup-link-' . esc_attr($uniq_id));
                        }

					} else if ('image' == $settings['trigger_type']) {
						$trigger_image = $settings['trigger_only_image'];
						if (!empty($trigger_image['url'])) {
							printf('<img class="eael-trigger-image eael-modal-popup-link %1$s" src="%2$s" alt="%3$s">', 'eael-modal-popup-link-' . esc_attr($uniq_id), esc_url($trigger_image['url']), esc_attr(get_post_meta($trigger_image['id'], '_wp_attachment_image_alt', true)));
						}
					}
				}
				?>
			</div><!-- close .eael-lightbox-btn -->
		</div>

		<div <?php echo $this->get_render_attribute_string('lightbox-popup-window'); ?>>
			<div <?php echo $this->get_render_attribute_string('popup-window-container'); ?>>
				<?php
				if ($settings['popup_lightbox_title'] == 'yes' && $settings['title'] != '') : ?>
					<div class="eael-lightbox-header">
						<h2 class="eael-lightbox-title"><?php echo $settings['title']; ?></h2>
					</div>
				<?php
				endif; // if ( $settings['popup_title'] == 'yes' ...)

				if ('lightbox_type_image' == ($settings['eael_lightbox_type'])) {
					printf('<img src="%1$s" alt="%2$s">', esc_url($popup_image['url']), esc_attr(get_post_meta($popup_image['id'], '_wp_attachment_image_alt', true)));
				} elseif ('lightbox_type_content' == ($settings['eael_lightbox_type'])) {
                    echo '<div class="eael-lightbox-content">';
                      echo do_shortcode($settings['eael_lightbox_type_content']);
                    echo '</div>';
				} elseif ('lightbox_type_template' == $settings['eael_lightbox_type']) {

					if ( ! empty( $settings['eael_primary_templates'] ) ) {
						// WPML Compatibility
						if ( ! is_array( $settings['eael_primary_templates'] ) ) {
							$settings['eael_primary_templates'] = apply_filters( 'wpml_object_id', $settings['eael_primary_templates'], 'wp_template', true );
						}
						echo Plugin::$instance->frontend->get_builder_content( $settings['eael_primary_templates'], true );
					}
				} else if ('lightbox_type_custom_html' == $settings['eael_lightbox_type']) {
					echo $settings['custom_html'];
				}
				?>


			</div>
		</div>
<?php

	}

	protected function content_template()
	{
	}
}
