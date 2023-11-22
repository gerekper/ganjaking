<?php
/**
 * Class: Premium_Multi_Scroll
 * Name: Multi Scroll Widget
 * Slug: premium-multi-scroll
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

use Elementor\Core\Responsive\Responsive;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Multi_Scroll
 */
class Premium_Multi_Scroll extends Widget_Base {

	/**
	 * Template Instance
	 *
	 * @var template_instance
	 */
	protected $template_instance;

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		return $this->template_instance = Premium_Template_Tags::getInstance();
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-multi-scroll';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Multi Scroll', 'premium-addons-pro' );
	}

	/**
	 * Retrieve Widget Icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string widget icon.
	 */
	public function get_icon() {
		return 'pa-pro-multi-scroll';
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.5.1
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'premium-elements' );
	}

	/**
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'pa', 'premium', 'animation', 'split', 'half', 'slider' );
	}

	/**
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS style handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'multi-scroll',
			'premium-pro',
		);
	}

	/**
	 * Widget preview refresh button.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function is_reload_preview_required() {
		return true;
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://www.youtube.com/watch?v=IzYnD6oDYXw&list=PLLpZVOYpMtTArB4hrlpSnDJB36D2sdoTv';
	}

	/**
	 * Get Repeater Controls
	 *
	 * @since 0.0.1
	 * @access protected
	 *
	 * @param object $repeater repeater object.
	 * @param array  $condition controls condition.
	 */
	protected function get_repeater_controls( $repeater, $condition = array() ) {

		$has_custom_breakpoints =   \Elementor\Plugin::$instance->breakpoints->has_custom_breakpoints();

		$extra_devices = ! $has_custom_breakpoints ? array() : array(
			'widescreen'   => __( 'Widescreen', 'premium-addons-pro' ),
			'laptop'       => __( 'laptop', 'premium-addons-pro' ),
			'tablet_extra' => __( 'Tablet Extra', 'premium-addons-pro' ),
			'mobile_extra' => __( 'Mobile Extra', 'premium-addons-pro' ),
		);

		$repeater->add_control(
			'notice',
			array(
				'label' => __( 'Names are reversed in RTL mode', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$repeater->add_control(
			'left_content',
			array(
				'label'   => __( 'Left Content', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'text' => __( 'Text Editor', 'premium-addons-pro' ),
					'temp' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default' => 'temp',
			)
		);

		$repeater->add_control(
			'left_side_text',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => 'Donec id elit non mi porta gravida at eget metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cras mattis consectetur purus sit amet fermentum. Nullam id dolor id nibh ultricies vehicula ut id elit. Donec id elit non mi porta gravida at eget metus.',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'left_content' => 'text',
				),
			)
		);

		$repeater->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'left_content' => 'temp',
				),
			)
		);

		$repeater->add_control(
			'left_side_template_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'left_content' => 'temp',
				),
			)
		);

		$repeater->add_control(
			'left_side_template',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => false,
				'condition'   => array(
					'left_content' => 'temp',
				),
				'label_block' => true,
			)
		);

		$repeater->add_control(
			'hide_left_section',
			array(
				'label'       => __( 'Hide On', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => array_merge(
					array(
						'tablet' => __( 'Tablet', 'premium-addons-pro' ),
						'mobile' => __( 'Mobile', 'premium-addons-pro' ),
					),
					$extra_devices
				),
				'frontend_available' => true,
			)
		);

		$repeater->add_control(
			'right_content',
			array(
				'label'     => __( 'Right Content', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'text' => __( 'Text Editor', 'premium-addons-pro' ),
					'temp' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'   => 'temp',
				'separator' => 'before',
			)
		);

		$repeater->add_control(
			'right_side_text',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => 'Donec id elit non mi porta gravida at eget metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cras mattis consectetur purus sit amet fermentum. Nullam id dolor id nibh ultricies vehicula ut id elit. Donec id elit non mi porta gravida at eget metus.',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'right_content' => 'text',
				),
			)
		);

		$repeater->add_control(
			'live_temp_content_extra',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'right_content' => 'temp',
				),
			)
		);

		$repeater->add_control(
			'right_side_template_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'right_content' => 'temp',
				),
			)
		);

		$repeater->add_control(
			'right_side_template',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'right_content' => 'temp',
				),
			)
		);

		$repeater->add_control(
			'hide_right_section',
			array(
				'label'              => __( 'Hide On', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT2,
				'multiple'           => true,
				'label_block'        => true,
				'options'            => array_merge(
					array(
						'tablet' => __( 'Tablet', 'premium-addons-pro' ),
						'mobile' => __( 'Mobile', 'premium-addons-pro' ),
					),
					$extra_devices
				),
				'frontend_available' => true,
			)
		);

		$repeater->add_control(
			'custom_navigation',
			array(
				'label'       => __( 'Custom Navigation Element Selector', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'label_block' => true,
				'description' => __( 'Use this to add an element selector to be used to navigate to this slide. For example #slide-1', 'premium-addons-for-elementor' ),
			)
		);

	}

	/**
	 * Register Multi Scroll controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'content_templates',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'scroll_notice',
			array(
				'raw'             => __( 'Please note that Multi Scroll works on mouse and keyboard scrolling only, not when scrolling using the scrollbar.', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$repeater = new REPEATER();

		$this->get_repeater_controls( $repeater, array( 'scroll_responsive_tabs' => 'yes' ) );

		$this->add_control(
			'left_side_repeater',
			array(
				'label'  => __( 'Sections', 'premium-addons-pro' ),
				'type'   => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'nav_menu',
			array(
				'label' => __( 'Navigation', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'nav_menu_switch',
			array(
				'label'       => __( 'Navigation Menu', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option works only on the frontend', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'navigation_menu_pos',
			array(
				'label'        => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'      => 'left',
				'prefix_class' => 'premium-mscroll-nav-',
				'condition'    => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_menu_vpos',
			array(
				'label'        => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'      => 'top',
				'prefix_class' => 'premium-mscroll-nav-',
				'condition'    => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$nav_repeater = new REPEATER();

		$nav_repeater->add_control(
			'nav_menu_item',
			array(
				'label' => __( 'List Item', 'premium-addons-pro' ),
				'type'  => Controls_Manager::TEXT,
			)
		);

		$this->add_control(
			'nav_menu_repeater',
			array(
				'label'       => __( 'List Items', 'premium-addons-pro' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $nav_repeater->get_controls(),
				'title_field' => '{{{ nav_menu_item }}}',
				'condition'   => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_dots',
			array(
				'label'     => __( 'Navigation Dots', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',

			)
		);

		$this->add_control(
			'dots_tooltips',
			array(
				'label'       => __( 'Dots Tooltips Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'Add text for each navigation dot separated by \',\'', 'premium-addons-pro' ),
				'condition'   => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_dots_pos',
			array(
				'label'     => __( 'Dots Horizontal Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'left'  => __( 'Left', 'premium-addons-pro' ),
					'right' => __( 'Right', 'premium-addons-pro' ),
				),
				'default'   => 'right',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->add_control(
			'navigation_dots_v_pos',
			array(
				'label'     => __( 'Dots Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'top'    => __( 'Top', 'premium-addons-pro' ),
					'middle' => __( 'Middle', 'premium-addons-pro' ),
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
				),
				'default'   => 'middle',
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_options',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'left_width',
			array(
				'label'      => esc_html__( 'Left Section Width (%)', 'premium-multi-scroll' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '%',
				'default'    => array(
					'size' => 50,
				),
			)
		);

		$this->add_control(
			'right_width',
			array(
				'label'      => esc_html__( 'Right Section Width (%)', 'premium-multi-scroll' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => '%',
				'default'    => array(
					'size' => 50,
				),
			)
		);

		$this->add_control(
			'scroll_container_height',
			array(
				'label'   => __( 'Height', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'fit' => __( 'Fit to Screen', 'premium-addons-pro' ),
					'min' => __( 'Min Height', 'premium-addons-pro' ),
				),
				'default' => 'min',
			)
		);

		$this->add_responsive_control(
			'container_min_height',
			array(
				'label'     => __( 'Min Height (px)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 500,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 600,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-inner'    => 'min-height: {{SIZE}}px',
				),
				'condition' => array(
					'scroll_container_height' => 'min',
				),
			)
		);

		$this->add_control(
			'keyboard_scrolling',
			array(
				'label'     => __( 'Keyboard Scrolling', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => array(
					'scroll_container_height' => 'min',
				),
			)
		);

		$this->add_control(
			'loop_top',
			array(
				'label'       => __( 'Loop Top', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Defines whether scrolling up in the first section should scroll to the last one or not.', 'premium-addons-pro' ),

			)
		);

		$this->add_control(
			'loop_bottom',
			array(
				'label'       => __( 'Loop Bottom', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Defines whether scrolling down in the last section should scroll to the first one or not.', 'premium-addons-pro' ),

			)
		);

		$this->add_control(
			'scroll_speed',
			array(
				'label'     => __( 'Scroll Speed', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'title'     => __( 'Set scolling speed in seconds, default: 0.7', 'premium-addons-pro' ),
				'default'   => 0.7,
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-inner .premium-scroll-easing'    => '-webkit-transition:all {{VALUE}}s cubic-bezier(0.895, 0.03, 0.685, 0.22); -moz-transition:all {{VALUE}}s cubic-bezier(0.895, 0.03, 0.685, 0.22); -o-transition:all {{VALUE}}s cubic-bezier(0.895, 0.03, 0.685, 0.22); transition:all {{VALUE}}s cubic-bezier(0.895, 0.03, 0.685, 0.22)',
				),
			)
		);

		$this->add_control(
			'scroll_responsive_tabs',
			array(
				'label'       => __( 'Disable on Tablets', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Disable multiscroll on tablets', 'premium-addons-pro' ),
				'default'     => 'yes',
			)
		);

		$this->add_control(
			'scroll_responsive_mobs',
			array(
				'label'       => __( 'Disable on Mobiles', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Disable multiscroll on mobile phones', 'premium-addons-pro' ),
				'default'     => 'yes',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'left_side_text',
			array(
				'label' => __( 'Left Side', 'premium-addons-pro' ),
				'tab'   => CONTROLS_MANAGER::TAB_STYLE,
			)
		);

		$this->add_control(
			'left_side_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ms-left .ms-tableCell' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'left_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-left-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'left_text_background',
			array(
				'label'     => __( 'Text Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-left-text' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'left_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-multiscroll-left-text',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'left_text_border',
				'selector' => '{{WRAPPER}} .premium-multiscroll-left-text',
			)
		);

		$this->add_control(
			'left_text_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-left-text' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_text_vertical',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'middle',
				'selectors' => array(
					'{{WRAPPER}} .ms-left .ms-tableCell' => 'vertical-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-left-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'left_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-left-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'right_side_text',
			array(
				'label' => __( 'Right Side', 'premium-addons-pro' ),
				'tab'   => CONTROLS_MANAGER::TAB_STYLE,
			)
		);

		$this->add_control(
			'right_side_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .ms-right .ms-tableCell' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'right_text_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-right-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'right_text_background',
			array(
				'label'     => __( 'Text Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-multiscroll-right-text' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'right_text_typography',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-multiscroll-right-text',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'right_text_border',
				'selector' => '{{WRAPPER}} .premium-multiscroll-right-text',
			)
		);

		$this->add_control(
			'right_text_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-right-text' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_text_vertical',
			array(
				'label'     => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-up',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'eicon-arrow-down',
					),
				),
				'default'   => 'middle',
				'selectors' => array(
					'{{WRAPPER}} .ms-right .ms-tableCell' => 'vertical-align: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-right-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'right_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-multiscroll-right-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_style',
			array(
				'label'     => __( 'Navigation Dots', 'premium-addons-pro' ),
				'tab'       => CONTROLS_MANAGER::TAB_STYLE,
				'condition' => array(
					'navigation_dots' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'navigation_style_tabs' );

		$this->start_controls_tab(
			'dots_style_tab',
			array(
				'label' => __( 'Dots', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'tooltips_color',
			array(
				'label'     => __( 'Tooltips Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-tooltip' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'navigation_dots' => 'yes',
					'dots_tooltips!'  => '',
				),
			)
		);

		$this->add_control(
			'tooltips_font',
			array(
				'label'     => __( 'Tooltips Text Font', 'premium-addons-pro' ),
				'type'      => Controls_Manager::FONT,
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-tooltip' => 'font-family: {{VALUE}};',
				),
				'condition' => array(
					'navigation_dots' => 'yes',
					'dots_tooltips!'  => '',
				),
			)
		);

		$this->add_control(
			'dots_color',
			array(
				'label'     => __( 'Dots Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-nav span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'active_dot_color',
			array(
				'label'     => __( 'Active Dot Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-nav li .active span'  => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dots_border_color',
			array(
				'label'     => __( 'Dots Border Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-nav span' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'container_style_tab',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'navigation_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .multiscroll-nav' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'navigation_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .multiscroll-nav' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'navigation_box_shadow',
				'selector' => '{{WRAPPER}} .multiscroll-nav',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation_menu_style',
			array(
				'label'     => __( 'Navigation Menu', 'premium-addons-pro' ),
				'tab'       => CONTROLS_MANAGER::TAB_STYLE,
				'condition' => array(
					'nav_menu_switch' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'navigation_items_typography',
				'selector' => '{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item .premium-scroll-nav-link',
			)
		);

		$this->start_controls_tabs( 'navigation_menu_style_tabs' );

		$this->start_controls_tab(
			'normal_style_tab',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'normal_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item .premium-scroll-nav-link'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'normal_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item .premium-scroll-nav-link:hover'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'normal_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'normal_shadow',
				'selector' => '{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'active_style_tab',
			array(
				'label' => __( 'Active', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item.active .premium-scroll-nav-link'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_hover_color',
			array(
				'label'     => __( 'Text Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item.active .premium-scroll-nav-link:hover'  => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item.active'  => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'active_shadow',
				'selector' => '{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item.active',
			)
		);

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'navigation_items_border',
				'selector'  => '{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'navigation_items_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item'  => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_items_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'navigation_items_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-scroll-nav-menu .premium-scroll-nav-item .premium-scroll-nav-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Mutli Scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$navigation_dots = ( 'yes' === $settings['navigation_dots'] ) ? true : false;

		$top_loop = ( 'yes' === $settings['loop_top'] ) ? true : false;

		$bottom_loop = ( 'yes' === $settings['loop_bottom'] ) ? true : false;

		$dots_text = ! empty( $settings['dots_tooltips'] ) ? explode( ',', $settings['dots_tooltips'] ) : array();

		$nav_items = $settings['nav_menu_repeater'];

		$anchors_arr = array();

		$custom_navigation = array();

		if ( 'yes' === $settings['nav_menu_switch'] ) {
			foreach ( $nav_items as $index => $item ) {
				array_push( $anchors_arr, 'section_' . $index );
			}
		}

		$scoll_settings = array(
			'dots'       => $navigation_dots,
			'leftWidth'  => ! empty( $settings['left_width']['size'] ) ? $settings['left_width']['size'] : 50,
			'rightWidth' => ! empty( $settings['right_width']['size'] ) ? $settings['right_width']['size'] : 50,
			'dotsText'   => $dots_text,
			'dotsPos'    => $settings['navigation_dots_pos'],
			'dotsVPos'   => $settings['navigation_dots_v_pos'],
			'topLoop'    => $top_loop,
			'btmLoop'    => $bottom_loop,
			'anchors'    => $anchors_arr,
			'hideTabs'   => ( 'yes' === $settings['scroll_responsive_tabs'] ) ? true : false,
			'tabSize'    => ( 'yes' === $settings['scroll_responsive_tabs'] ) ? Responsive::get_breakpoints()['lg'] : Responsive::get_breakpoints()['lg'],
			'hideMobs'   => ( 'yes' === $settings['scroll_responsive_mobs'] ) ? true : false,
			'mobSize'    => ( 'yes' === $settings['scroll_responsive_mobs'] ) ? Responsive::get_breakpoints()['md'] : Responsive::get_breakpoints()['md'],
			'cellHeight' => ! empty( $settings['container_min_height']['size'] ) ? $settings['container_min_height']['size'] : 500,
			'fit'        => $settings['scroll_container_height'],
			'keyboard'   => ( 'yes' === $settings['keyboard_scrolling'] ) ? true : false,
			'rtl'        => is_rtl(),
			'id'         => esc_attr( $id ),
			'navigation' => $custom_navigation,
		);

		$this->add_render_attribute( 'multiscroll_wrapper', 'class', 'premium-multiscroll-wrap' );

		$this->add_render_attribute(
			'multiscroll_inner',
			array(
				'id'    => 'premium-multiscroll-' . $id,
				'class' => array(
					'premium-multiscroll-inner',
					'premium-scroll-' . $settings['scroll_container_height'],
				),
			)
		);

		$this->add_render_attribute(
			'multiscroll_menu',
			array(
				'id'    => 'premium-scroll-nav-menu-' . $id,
				'class' => array(
					'premium-scroll-nav-menu',
					'premium-scroll-responsive',
				),
			)
		);

		$this->add_render_attribute( 'right_template', 'class', array( 'premium-multiscroll-temp', 'premium-multiscroll-right-temp', 'premium-multiscroll-temp-' . $id ) );

		$this->add_render_attribute( 'left_template', 'class', array( 'premium-multiscroll-temp', 'premium-multiscroll-left-temp', 'premium-multiscroll-temp-' . $id ) );

		$this->add_render_attribute( 'left_side', 'class', 'premium-multiscroll-left-' . $id );

		$this->add_render_attribute( 'right_side', 'class', 'premium-multiscroll-right-' . $id );

		$this->add_inline_editing_attributes( 'left_side_text', 'advanced' );

		$this->add_inline_editing_attributes( 'right_side_text', 'advanced' );

		$this->add_render_attribute( 'left_side_text', 'class', 'premium-multiscroll-left-text' );

		$this->add_render_attribute( 'right_side_text', 'class', 'premium-multiscroll-right-text' );

		$templates = $settings['left_side_repeater'];

		foreach ( $templates as $index => $section ) {

			array_push( $custom_navigation, $section['custom_navigation'] );

			$this->add_render_attribute( 'left_section' . $index, 'data-hide', $section['hide_left_section'] );

			$this->add_render_attribute( 'right_section' . $index, 'data-hide', $section['hide_right_section'] );

		}

		?>

		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'multiscroll_wrapper' ) ); ?> data-settings='<?php echo wp_json_encode( $scoll_settings ); ?>'>
			<?php if ( 'yes' === $settings['nav_menu_switch'] ) : ?>
				<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'multiscroll_menu' ) ); ?>>
					<?php foreach ( $nav_items as $index => $item ) : ?>
						<li data-menuanchor="<?php echo esc_attr( 'section_' . $index ); ?>" class="premium-scroll-nav-item">
							<a class="premium-scroll-nav-link" href="<?php echo esc_attr( '#section_' . $index ); ?>"><?php echo wp_kses_post( $item['nav_menu_item'] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'multiscroll_inner' ) ); ?>>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'left_side' ) ); ?>>
					<?php
					foreach ( $templates as $index => $section ) :
						?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'left_template' ) . $this->get_render_attribute_string( 'left_section' . $index ) ); ?> data-navigation='<?php echo wp_json_encode( $custom_navigation ); ?>'>
						<?php
						if ( 'temp' === $section['left_content'] ) :
							$template = empty( $section['left_side_template'] ) ? $section['live_temp_content'] : $section['left_side_template'];
							echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							else :
								?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'left_side_text' ) ); ?>>
								<?php echo $this->parse_text_editor( $section['left_side_text'] ); ?>
							</div>
								<?php
							endif;
							?>
					</div>
					<?php endforeach; ?>
				</div>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'right_side' ) ); ?>>
					<?php
					foreach ( $templates as $index => $section ) :
						?>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'right_template' ) . $this->get_render_attribute_string( 'right_section' . $index ) ); ?>>
						<?php
						if ( 'temp' === $section['right_content'] ) :
							$template = empty( $section['right_side_template'] ) ? $section['live_temp_content_extra'] : $section['right_side_template'];
							echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							else :
								?>
							<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'right_side_text' ) ); ?>>
								<?php echo $this->parse_text_editor( $section['right_side_text'] ); ?>
							</div>
								<?php
							endif;
							?>
					</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php

	}

}
