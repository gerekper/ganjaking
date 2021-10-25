<?php
/**
 * Class: Premium_Hscroll
 * Name: Horizontal Scroll
 * Slug: premium-hscroll
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // If this file is called directly, abort.
}

/**
 * Class Premium_Hscroll
 */
class Premium_Hscroll extends Widget_Base {

	/**
	 * Get Elementor Helper Instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function getTemplateInstance() {
		$this->template_instance = Premium_Template_Tags::getInstance();
		return $this->template_instance;
	}

	/**
	 * Retrieve Widget Name.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_name() {
		return 'premium-hscroll';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Horizontal Scroll', 'premium-addons-pro' ) );
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
		return 'pa-pro-horizontal-scroll';
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
	 * Retrieve Widget Dependent CSS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array CSS script handles.
	 */
	public function get_style_depends() {
		return array(
			'premium-addons',
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
			'pa-tweenmax',
			'pa-gsap',
			'elementor-waypoints',
			'papro-hscroll',
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
	 * Retrieve Widget Keywords.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget keywords.
	 */
	public function get_keywords() {
		return array( 'slider', 'full', 'scene' );
	}

	/**
	 * Retrieve Widget Support URL.
	 *
	 * @access public
	 *
	 * @return string support URL.
	 */
	public function get_custom_help_url() {
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Premium Horizontal controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'content_templates',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'notices',
			array(
				'raw'             => __( '<p>Important:</p><ul><li>Please make sure that "Stretch Section" option is disabled for sections below.</li></ul>', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
			)
		);

		$temp_repeater = new REPEATER();

		$temp_repeater->add_control(
			'template_type',
			array(
				'label'   => __( 'Content Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
					'id'       => __( 'Section ID', 'premium-addons-pro' ),
				),
				'default' => 'id',
			)
		);

		$temp_repeater->add_control(
			'section_template',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'multiple'    => false,
				'label_block' => true,
				'condition'   => array(
					'template_type' => 'template',
				),
			)
		);

		$temp_repeater->add_control(
			'anchor_id',
			array(
				'label'       => __( 'Anchor ID', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'description' => __( 'This ID will be used to anchor your links to this slide', 'premium-addons-pro' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'template_type' => 'template',
				),
			)
		);

		$temp_repeater->add_control(
			'section_id',
			array(
				'label'     => __( 'Section ID', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'template_type' => 'id',
				),
			)
		);

		$temp_repeater->add_control(
			'scroll_bg_transition',
			array(
				'label' => __( 'Scroll Background Transition', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$temp_repeater->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'      => 'scroll_bg',
				'types'     => array( 'classic' ),
				'selector'  => '{{WRAPPER}} {{CURRENT_ITEM}}',
				'condition' => array(
					'scroll_bg_transition' => 'yes',
				),
			)
		);

		$temp_repeater->add_control(
			'hide_section',
			array(
				'label'              => __( 'Hide Section On', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT2,
				'multiple'           => true,
				'label_block'        => true,
				'options'            => array(
					'desktop' => __( 'Desktop', 'premium-addons-pro' ),
					'tablet'  => __( 'Tablet', 'premium-addons-pro' ),
					'mobile'  => __( 'Mobile', 'premium-addons-pro' ),
				),
				'render_type'        => 'template',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'section_repeater',
			array(
				'label'         => __( 'Sections', 'premium-addons-pro' ),
				'type'          => Controls_Manager::REPEATER,
				'fields'        => $temp_repeater->get_controls(),
				'title_field'   => '{{{ "template" === template_type ? section_template : section_id }}}',
				'prevent_empty' => false,
			)
		);

		$this->add_control(
			'scroll_bg_speed',
			array(
				'label'     => __( 'Background Transition Speed (sec)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 3,
						'step' => 0.1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-bg-layer' => 'transition-duration: {{SIZE}}s;',
				),
			)
		);

		$this->add_control(
			'fixed_template',
			array(
				'label'       => __( 'Fixed Content Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'separator'   => 'before',
				'label_block' => true,
				'multiple'    => false,
			)
		);

		$this->add_responsive_control(
			'fixed_content_voffset',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-fixed-content' => 'top: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'fixed_template!' => '',
				),
			)
		);

		$this->add_responsive_control(
			'fixed_content_hoffset',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
					'em' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-fixed-content' => 'left: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'fixed_template!' => '',
				),
			)
		);

		$this->add_control(
			'fixed_content_zindex',
			array(
				'label'     => __( 'z-index', 'premium-addons-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-fixed-content' => 'z-index: {{VALUE}}',
				),
				'condition' => array(
					'fixed_template!' => '',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'advanced_settings',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'slides',
			array(
				'label'          => __( 'Number of Slides in Viewport', 'premium-addons-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'description'    => __( 'Select the number of slides to appear in your browser viewport. For example, 1.5 means half of the next slide will appear on viewport', 'premium-addons-pro' ),
				'range'          => array(
					'px' => array(
						'min'  => 1,
						'step' => 0.1,
					),
				),
				'default'        => array(
					'size' => 1,
				),
				'tablet_default' => array(
					'size' => 0.5,
				),
				'mobile_default' => array(
					'size' => 0.5,
				),
			)
		);

		$this->add_responsive_control(
			'distance',
			array(
				'label'       => __( 'Scroll Distance Beyond Last Slide', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Set value in pixels for the scroll distance after last slide before scroll down to next section', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
				),
				'default'     => array(
					'size' => 0,
				),
			)
		);

		$this->add_responsive_control(
			'trigger_offset',
			array(
				'label'       => __( 'Offset (PX)', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Offset at which the horizontal scroll is triggered', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 600,
					),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-hscroll-sections-wrap' => 'padding-top: {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'scroll_effect',
			array(
				'label'   => __( 'Scroll Type', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'normal' => __( 'Normal', 'premium-addons-pro' ),
					'snap'   => __( 'Snappy', 'premium-addons-pro' ),
				),
				'default' => 'normal',
			)
		);

		$this->add_control(
			'disable_snap',
			array(
				'label'        => __( 'Disable Snappy Effect on Touch Devices', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'scroll_effect' => 'snap',
				),
			)
		);

		$this->add_responsive_control(
			'scroll_speed',
			array(
				'label'       => __( 'Decrease Scroll Speed by', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'For example, 2 means that scene scroll speed will be decreased to half', 'premium-addons-pro' ),
				'min'         => 1,
				'default'     => 1,
				'conditions'  => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'name'  => 'scroll_effect',
							'value' => 'normal',
						),
						array(
							'name'  => 'disable_snap',
							'value' => 'true',
						),
					),
				),
			)
		);

		$this->add_control(
			'progress_bar',
			array(
				'label'        => __( 'Progress Bar', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
			)
		);

		$this->add_responsive_control(
			'progress_offset_left',
			array(
				'label'     => __( 'Progress Bar Left Posiion (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-progress' => 'left: {{SIZE}}px',
				),
				'condition' => array(
					'progress_bar' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'progress_offset_bottom',
			array(
				'label'     => __( 'Progress Bar Bottom Posiion (PX)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-progress' => 'bottom: {{SIZE}}px',
				),
				'condition' => array(
					'progress_bar' => 'true',
				),
			)
		);

		$this->add_control(
			'opacity_transition',
			array(
				'label'        => __( 'Opacity Scroll Effect', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'separator'    => 'before',
				'default'      => 'true',
				'condition'    => array(
					'entrance_animation!' => 'true',
					'rtl_mode!'           => 'true',
				),
			)
		);

		$this->add_control(
			'entrance_animation',
			array(
				'label'        => __( 'Trigger Entrance Animations on Scroll', 'premium-addons-pro' ),
				'description'  => __( 'This option will trigger entrance animations for inner widgets each time you scroll to a slide', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'scroll_effect'       => 'snap',
					'opacity_transition!' => 'true',
					'rtl_mode!'           => 'true',
				),

			)
		);

		$this->add_control(
			'keyboard_scroll',
			array(
				'label'        => __( 'Keyboard Scrolling', 'premium-addons-pro' ),
				'description'  => __( 'Enable or disable scrolling slides using Keyboard', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'rtl_mode',
			array(
				'label'        => __( 'RTL Mode', 'premium-addons-pro' ),
				'description'  => __( 'Enable this option to change scroll direction to RTL', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'prefix_class' => 'premium-hscroll-rtl-',
				'render_type'  => 'template',
			)
		);

		$this->add_control(
			'disable_on',
			array(
				'label'              => __( 'Disable Horizonal Scroll On', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT2,
				'options'            => array(
					'tablet' => __( 'Tablet', 'premium-addons-pro' ),
					'mobile' => __( 'Mobile', 'premium-addons-pro' ),
				),
				'multiple'           => true,
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navigation',
			array(
				'label' => __( 'Navigation', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'nav_dots',
			array(
				'label'        => __( 'Navigation Dots', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'nav_dots_position',
			array(
				'label'        => __( 'Navigation Dots Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'bottom' => __( 'Bottom', 'premium-addons-pro' ),
					'left'   => __( 'Left', 'premium-addons-pro' ),
					'right'  => __( 'Right', 'premium-addons-pro' ),
				),
				'default'      => 'bottom',
				'prefix_class' => 'premium-hscroll-dots-',
				'condition'    => array(
					'nav_dots' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'nav_dots_offset',
			array(
				'label'      => __( 'Dots Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 5,
						'max' => 100,
					),
					'em' => array(
						'min' => 1,
						'max' => 10,
					),
				),
				'condition'  => array(
					'nav_dots' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}}.premium-hscroll-dots-bottom .premium-hscroll-nav' => 'bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-hscroll-dots-left .premium-hscroll-nav' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-hscroll-dots-right .premium-hscroll-nav' => 'right: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'tooltips',
			array(
				'label'        => __( 'Tooltips', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'nav_dots' => 'true',
				),
			)
		);

		$this->add_control(
			'dots_tooltips',
			array(
				'label'       => __( 'Dots Tooltips Text', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => __( 'Add text for each navigation dot separated by \',\'', 'premium-addons-pro' ),
				'label_block' => 'true',
				'condition'   => array(
					'nav_dots' => 'true',
					'tooltips' => 'true',
				),
			)
		);

		$this->add_control(
			'nav_arrows',
			array(
				'label'        => __( 'Navigation Arrows', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'nav_arrow_left',
			array(
				'label'     => __( 'Left Arrow Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-angle-left',
				),
				'condition' => array(
					'nav_arrows' => 'true',
				),
			)
		);

		$this->add_control(
			'nav_arrow_right',
			array(
				'label'     => __( 'Right Arrow Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::ICONS,
				'default'   => array(
					'library' => 'fa-solid',
					'value'   => 'fas fa-angle-right',
				),
				'condition' => array(
					'nav_arrows' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'carousel_arrows_pos',
			array(
				'label'      => __( 'Arrows Position', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -10,
						'max' => 10,
					),
				),
				'condition'  => array(
					'nav_arrows' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-arrow-right' => 'right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-hscroll-arrow-left' => 'left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'scroll_effect' => 'normal',
					'nav_arrows'    => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination',
			array(
				'label' => __( 'Pagination Numbers', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'pagination_number',
			array(
				'label'        => __( 'Enable Pagination Number', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_responsive_control(
			'pagination_hor',
			array(
				'label'      => __( 'Horizontal Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'condition'  => array(
					'pagination_number' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-pagination' => 'left: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_ver',
			array(
				'label'      => __( 'Vertical Offset', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 300,
					),
					'em' => array(
						'min' => 0,
						'max' => 30,
					),
				),
				'condition'  => array(
					'pagination_number' => 'true',
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-pagination' => 'bottom: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'responsive',
			array(
				'label' => __( 'Responsive Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'override_columns',
			array(
				'label'        => __( 'Put Columns Next to Each Other', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'This option will force the columns to be positioned next to each other on small screens' ),
				'prefix_class' => 'premium-hscroll-force-',
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_pa_docs',
			array(
				'label' => __( 'Helpful Documentations', 'premium-addons-pro' ),
			)
		);

		$docs = array(
			'https://premiumaddons.com/docs/horizontal-scroll-widget-tutorial' => __( 'Getting started »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-create-elementor-template-to-be-used-with-premium-addons/' => __( 'How to create Elementor templates to be used in Horizontal Scroll widget »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-anchor-elementor-horizontal-scroll-slides/' => __( 'How to anchor Horizontal Scroll slides »', 'premium-addons-pro' ),
			'https://premiumaddons.com/docs/how-to-play-pause-a-soundtrack-using-premium-button-widget/' => __( 'How to Play/Pause a Soundtrack Using Premium Button Widget »', 'premium-addons-pro' ),
			'https://www.youtube.com/watch?v=4HqT_3s-ZXg' => __( 'Check the video tutorial »', 'premium-addons-pro' ),
		);

		$doc_index = 1;
		foreach ( $docs as $url => $title ) {

			$doc_url = Helper_Functions::get_campaign_link( $url, 'editor-page', 'wp-editor', 'get-support' );

			$this->add_control(
				'doc_' . $doc_index,
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( '<a href="%s" target="_blank">%s</a>', $doc_url, $title ),
					'content_classes' => 'editor-pa-doc',
				)
			);

			$doc_index++;

		}

		$this->end_controls_section();

		$this->start_controls_section(
			'nav_dots_style',
			array(
				'label'     => __( 'Navigation Dots', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nav_dots' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'dots_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-nav-dot' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-dot' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .premium-hscroll-carousel-icon' => 'background-color: {{VALUE}}; color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_color',
			array(
				'label'     => __( 'Active Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-item.active .premium-hscroll-nav-dot' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'dot_border_color',
			array(
				'label'     => __( 'Border Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-item .premium-hscroll-nav-dot' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'active_border_color',
			array(
				'label'     => __( 'Active Border Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-item.active .premium-hscroll-nav-dot' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'dot_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-nav-item .premium-hscroll-nav-dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'tooltips_heading',
			array(
				'label'     => __( 'Tooltips', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_spacing',
			array(
				'label'      => __( 'Spacing', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}}.premium-hscroll-dots-bottom .premium-hscroll-nav-tooltip' => 'bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-hscroll-dots-left .premium-hscroll-nav-tooltip' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.premium-hscroll-dots-right .premium-hscroll-nav-tooltip' => 'right: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_control(
			'tooltip_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-tooltip' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_control(
			'tooltip_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-nav-tooltip' => 'background-color: {{VALUE}}',
					'{{WRAPPER}}.premium-hscroll-dots-left .premium-hscroll-nav-tooltip::after' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}}.premium-hscroll-dots-right .premium-hscroll-nav-tooltip::after' => 'border-left-color: {{VALUE}}',
				),
				'condition' => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'tooltip_typography',
				'selector'  => '{{WRAPPER}} .premium-hscroll-nav-tooltip',
				'condition' => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-nav-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'tooltip_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-nav-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
				'condition'  => array(
					'tooltips' => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'nav_arrows_style',
			array(
				'label'     => __( 'Navigation Arrows', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'nav_arrows' => 'true',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-wrap-icon' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .premium-hscroll-wrap-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-arrow i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-hscroll-arrow svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'arrow_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-arrow:hover i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .premium-hscroll-arrow:hover svg' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-wrap-icon' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'arrow_hover_background',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-wrap-icon:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'arrow_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-wrap-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'arrow_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-wrap-icon' => 'padding: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'progress_style',
			array(
				'label'     => __( 'Progress Bar', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'progress_bar' => 'true',
				),
			)
		);

		$this->add_control(
			'progress_color',
			array(
				'label'     => __( 'Progress Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-progress-line' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'progress_background_color',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-progress' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'pagination_style',
			array(
				'label'     => __( 'Pagination Numbers', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'pagination_number' => 'true',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'pagination_typography',
				'selector' => '{{WRAPPER}} .premium-hscroll-pagination span',
			)
		);

		$this->add_control(
			'pagination_spacing',
			array(
				'label'     => __( 'Spacing Between', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-total-slides:before'  => 'margin: 0 {{SIZE}}px',
				),
			)
		);

		$this->add_control(
			'pagination_numbers_current_color',
			array(
				'label'     => __( 'Current Slide Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-current-slide' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_numbers_sep_color',
			array(
				'label'     => __( 'Separator Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-total-slides:before' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_numbers_total_color',
			array(
				'label'     => __( 'Total Slides Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'after',
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-total-slides' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'pagination_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-hscroll-pagination',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'pagination_border',
				'selector' => '{{WRAPPER}} .premium-hscroll-pagination',
			)
		);

		$this->add_control(
			'pagination_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-pagination' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			)
		);

		$this->add_control(
			'container_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-hscroll-outer-wrap' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-hscroll-outer-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Render Horizontal Scroll widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$widget_id = $this->get_id();

		$this->add_render_attribute(
			'wrap',
			array(
				'id'    => 'premium-hscroll-wrap-' . $widget_id,
				'class' => 'premium-hscroll-wrap',
			)
		);

		if ( 'true' !== $settings['nav_arrows'] ) {
			$this->add_render_attribute( 'wrap', 'class', 'premium-hscroll-arrows-hidden' );
		}

		if ( 'true' !== $settings['nav_dots'] ) {
			$this->add_render_attribute( 'wrap', 'class', 'premium-hscroll-dots-hidden' );
		}

		$this->add_render_attribute(
			'scroller_wrap',
			array(
				'id'            => 'premium-hscroll-scroller-wrap-' . $widget_id,
				'class'         => 'premium-hscroll-scroller-wrap',
				'data-progress' => 'bottom',
			)
		);

		$this->add_render_attribute( 'progress_wrap', 'class', 'premium-hscroll-progress' );
		if ( 'true' !== $settings['progress_bar'] ) {
			$this->add_render_attribute( 'progress_wrap', 'class', 'premium-hscroll-progress-hidden' );
		}

		$this->add_render_attribute(
			'progress',
			array(
				'id'    => 'premium-hscroll-progress-line-' . $widget_id,
				'class' => 'premium-hscroll-progress-line',
			)
		);

		$templates = $settings['section_repeater'];

		$count = count( $templates );

		$disable_snap = false;

		if ( 'snap' === $settings['scroll_effect'] && 'true' === $settings['disable_snap'] ) {
				$disable_snap = true;
		}

		$opacity = 'true' === $settings['opacity_transition'] ? true : false;

		$pagination = 'true' === $settings['pagination_number'] ? true : false;

		if ( 'true' === $settings['tooltips'] ) {
			$tooltips = explode( ',', $settings['dots_tooltips'] );
		}

		$slides = ! empty( $settings['slides']['size'] ) ? floatval( $settings['slides']['size'] ) : 1;

		$distance = ! empty( $settings['distance']['size'] ) ? floatval( $settings['distance']['size'] ) : 0;

		$speed = ! empty( $settings['scroll_speed'] ) ? intval( $settings['scroll_speed'] ) : 1;

		$hscroll_settings = array(
			'id'              => $widget_id,
			'templates'       => $templates,
			'slides'          => $slides,
			'slides_tablet'   => empty( $settings['slides_tablet']['size'] ) ? $slides : floatval( $settings['slides_tablet']['size'] ),
			'slides_mobile'   => empty( $settings['slides_mobile']['size'] ) ? $slides : floatval( $settings['slides_mobile']['size'] ),
			'distance'        => $distance,
			'distance_tablet' => empty( $settings['distance_tablet']['size'] ) ? $slides : floatval( $settings['distance_tablet']['size'] ),
			'distance_mobile' => empty( $settings['distance_mobile']['size'] ) ? $slides : floatval( $settings['distance_mobile']['size'] ),
			'snap'            => $settings['scroll_effect'],
			'disableSnap'     => intval( $disable_snap ),
			'speed'           => $speed,
			'speed_tablet'    => empty( $settings['scroll_speed_tablet'] ) ? $speed : intval( $settings['scroll_speed_tablet'] ),
			'speed_mobile'    => empty( $settings['scroll_speed_mobile'] ) ? $speed : intval( $settings['scroll_speed_mobile'] ),
			'opacity'         => intval( $opacity ),
			'loop'            => $settings['loop'],
			'enternace'       => $settings['entrance_animation'],
			'keyboard'        => $settings['keyboard_scroll'],
			'pagination'      => intval( $pagination ),
			'rtl'             => $settings['rtl_mode'],
			'arrows'          => 'true' === esc_html( $settings['nav_arrows'] ) ? true : false,
			'dots'            => 'true' === esc_html( $settings['nav_dots'] ) ? true : false,
			'disableOn'       => $settings['disable_on'],
		);

		// Fix warning trying to access array offset with value null.
		if ( 'true' === $settings['nav_arrows'] ) {
			$hscroll_settings['leftArrow']  = esc_html( $settings['nav_arrow_left']['value'] );
			$hscroll_settings['rightArrow'] = esc_html( $settings['nav_arrow_right']['value'] );
		}

		$this->add_render_attribute(
			'spacer',
			array(
				'id'    => 'premium-hscroll-spacer-' . $widget_id,
				'class' => 'premium-hscroll-spacer',
			)
		);

		$this->add_render_attribute( 'nav', 'class', 'premium-hscroll-nav' );

		$this->add_render_attribute( 'wrap', 'data-settings', wp_json_encode( $hscroll_settings ) );

		?>
	<div class="premium-hscroll-outer-wrap">
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'spacer' ) ); ?>></div>
			<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrap' ) ); ?>>
				<?php
				foreach ( $templates as $index => $section ) :

					if ( 'yes' === $section['scroll_bg_transition'] ) {
						$list_item_key = 'premium_hscroll_bg_layer_' . $index;

						$this->add_render_attribute(
							$list_item_key,
							array(
								'class'      => array(
									'premium-hscroll-bg-layer',
									'elementor-repeater-item-' . $section['_id'],
								),
								'data-layer' => $index,
							)
						);
						if ( 0 === $index ) {
							$this->add_render_attribute( $list_item_key, 'class', 'premium-hscroll-layer-active' );
						}

						?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( $list_item_key ) ); ?>></div>
						<?php
					}
				endforeach;

				?>
			<?php if ( ! empty( $settings['fixed_template'] ) ) : ?>
				<div class="premium-hscroll-fixed-content">
					<?php
						$template_title = $settings['fixed_template'];
						echo $this->getTemplateInstance()->get_template_content( $template_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
				<?php
			endif;
			if ( 0 !== $count ) :
				?>
				<div class="premium-hscroll-arrow premium-hscroll-arrow-left">
					<div class="premium-hscroll-wrap-icon">
						<?php
						Icons_Manager::render_icon(
							$settings['nav_arrow_left'],
							array(
								'class'       => 'premium-hscroll-prev',
								'aria-hidden' => 'true',
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>
			<div class="premium-hscroll-slider">
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'scroller_wrap' ) ); ?>>
					<div class="premium-hscroll-sections-wrap" data-scroll-opacity="<?php echo esc_attr( $opacity ); ?>">
						<?php
						foreach ( $templates as $index => $section ) :

							$this->add_render_attribute(
								'section_' . $index,
								array(
									'id'        => 'section_' . $widget_id . $index,
									'class'     => 'premium-hscroll-temp',
									'data-hide' => $section['hide_section'],
								)
							);

							if ( 'id' === $section['template_type'] ) {
								$this->add_render_attribute(
									'section_' . $index,
									array(
										'data-section' => $section['section_id'],
									)
								);
							} else {
								if ( ! empty( $section['anchor_id'] ) ) {
									$this->add_render_attribute(
										'section_' . $index,
										array(
											'data-section' => $section['anchor_id'],
										)
									);
								}
							}
							if ( $opacity ) {
								if ( 0 !== $index && ! $settings['rtl_mode'] ) {
									$this->add_render_attribute( 'section_' . $index, 'class', 'premium-hscroll-hide' );
								} elseif ( $count - 1 !== $index && $settings['rtl_mode'] ) {
									$this->add_render_attribute( 'section_' . $index, 'class', 'premium-hscroll-hide' );
								}
							}
							?>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'section_' . $index ) ); ?>>
							<?php
							if ( 'template' === $section['template_type'] ) {
								$template_title = $section['section_template'];
								echo $this->getTemplateInstance()->get_template_content( $template_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
						<?php endforeach; ?>
					</div>
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'progress_wrap' ) ); ?>>
						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'progress' ) ); ?>></div>
					</div>
				</div>
			</div>
			<?php if ( 0 !== $count ) : ?>
				<div class="premium-hscroll-arrow premium-hscroll-arrow-right">
					<div class="premium-hscroll-wrap-icon">
						<?php
						Icons_Manager::render_icon(
							$settings['nav_arrow_right'],
							array(
								'class'       => 'premium-hscroll-next',
								'aria-hidden' => 'true',
							)
						);
						?>
					</div>
				</div>

				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'nav' ) ); ?>>
					<ul class="premium-hscroll-nav-list dots">
						<?php
						foreach ( $templates as $index => $section ) :
							$this->add_render_attribute(
								'item_' . $index,
								array(
									'class'      => 'premium-hscroll-nav-item',
									'data-slide' => 'section_' . $widget_id . $index,
								)
							);
							?>
							<li <?php echo wp_kses_post( $this->get_render_attribute_string( 'item_' . $index ) ); ?>>
								<span class="premium-hscroll-nav-dot"></span>
								<?php if ( 'true' === $settings['tooltips'] && ! empty( $tooltips[ $index ] ) ) : ?>
									<span class="premium-hscroll-nav-tooltip"><?php echo esc_html( $tooltips[ $index ] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
				<?php
			endif;
			if ( 0 !== $count && $settings['pagination_number'] ) :
				?>
				<div class="premium-hscroll-pagination">
					<span class="premium-hscroll-page-item premium-hscroll-current-slide">01</span>
					<span class="premium-hscroll-page-item premium-hscroll-total-slides"><?php echo wp_kses_post( $count > 9 ? $count : sprintf( '0%s', $count ) ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>
		<?php
	}
}
