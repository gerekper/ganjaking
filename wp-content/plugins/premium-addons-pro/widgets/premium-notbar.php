<?php
/**
 * Class: Premium_Notbar
 * Name: Alert Box
 * Slug: premium-notbar
 */

namespace PremiumAddonsPro\Widgets;

// Elementor Classes.
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Plugin;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Responsive\Responsive;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Notbar
 */
class Premium_Notbar extends Widget_Base {

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
		return 'premium-notbar';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Alert Box', 'premium-addons-pro' ) );
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
		return 'pa-pro-notification-bar';
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
	 * Retrieve Widget Dependent JS.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array JS script handles.
	 */
	public function get_script_depends() {
		return array(
			'lottie-js',
			'premium-pro',
		);
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
		return array( 'notification', 'bar', 'popup', 'modal', 'event' );
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
	 * Register Alert Box controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_notbar_general_section',
			array(
				'label' => __( 'Bar', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_position',
			array(
				'label'   => __( 'Position', 'premium-addons-pro' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-up',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-down',
					),
					'middle' => array(
						'title' => __( 'Middle', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'float'  => array(
						'title' => __( 'Custom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'default' => 'float',
				'toggle'  => false,
			)
		);

		$this->add_responsive_control(
			'premium_notbar_float_pos',
			array(
				'label'     => __( 'Vertical Offset (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 10,
					'unit' => '%',
				),
				'condition' => array(
					'premium_notbar_position' => 'float',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-notbar' => 'top: {{SIZE}}%;',
				),
			)
		);

		$this->add_control(
			'premium_notbar_top_select',
			array(
				'label'       => __( 'Layout', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'fixed'    => __( 'Fixed', 'premium-addons-pro' ),
					'relative' => __( 'Relative', 'premium-addons-pro' ),
				),
				'default'     => 'relative',
				'condition'   => array(
					'premium_notbar_position' => 'top',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_width',
			array(
				'label'       => __( 'Width', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'wide'  => __( 'Full Width', 'premium-addons-pro' ),
					'boxed' => __( 'Boxed', 'premium-addons-pro' ),
				),
				'default'     => 'boxed',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_direction',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'row'         => array(
						'title' => __( 'LTR', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-right',
					),
					'row-reverse' => array(
						'title' => __( 'RTL', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-circle-left',
					),
				),
				'default'   => 'row',
				'selectors' => array(
					'{{WRAPPER}} .premium-notbar-content-wrapper, {{WRAPPER}} .premium-notbar-icon-text-container'    => '-webkit-flex-direction: {{VALUE}}; flex-direction: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_content_type' => 'editor',
				),
				'toggle'    => false,
			)
		);

		$this->add_control(
			'premium_notbar_close_heading',
			array(
				'label'     => __( 'Button', 'premium-addons-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'premium_notbar_close_hor_position',
			array(
				'label'       => __( 'Horizontal Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'row'         => __( 'After', 'premium-addons-pro' ),
					'row-reverse' => __( 'Before', 'premium-addons-pro' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-notbar-content-wrapper'    => '-webkit-flex-direction: {{VALUE}}; flex-direction: {{VALUE}};',
				),
				'default'     => 'row',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_close_ver_position',
			array(
				'label'       => __( 'Vertical Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'flex-start' => __( 'Top', 'premium-addons-pro' ),
					'center'     => __( 'Middle', 'premium-addons-pro' ),
					'flex-end'   => __( 'Bottom', 'premium-addons-pro' ),
				),
				'selectors'   => array(
					'{{WRAPPER}} .premium-notbar-content-wrapper'    => 'align-items: {{VALUE}};',
				),
				'default'     => 'center',
				'label_block' => true,
				'separator'   => 'after',
			)
		);

		$this->add_control(
			'enable_background_overlay',
			array(
				'label' => __( 'Overlay Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'background_overlay_notice',
			array(
				'raw'             => __( 'Please note that Overlay Background works only on the frontend', 'premium-addons-pro' ),
				'type'            => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				'condition'       => array(
					'enable_background_overlay' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_index',
			array(
				'label'       => __( 'z-index', 'premium-addons-pro' ),
				'description' => __( 'Set a z-index for the notification bar, default is: 9999', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 0,
				'max'         => 9999,
				'selectors'   => array(
					'#premium-notbar-{{ID}}' => 'z-index: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_content',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_content_type',
			array(
				'label'       => __( 'Content to Show', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'editor'   => __( 'Text Editor', 'premium-addons-pro' ),
					'template' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default'     => 'editor',
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_notbar_content_temp',
			array(
				'label'       => __( 'Content', 'premium-addons-pro' ),
				'description' => __( 'Elementor Template is a template which you can choose from Elementor Templates library', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'premium_notbar_content_type' => 'template',
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_notbar_temp_width',
			array(
				'label'     => __( 'Content Width (%)', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'condition' => array(
					'premium_notbar_content_type' => 'template',
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper'   => 'width: {{SIZE}}%;',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_switcher',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => array(
					'premium_notbar_content_type' => 'editor',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_selector',
			array(
				'label'     => __( 'Icon Type', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'font-awesome-icon',
				'options'   => array(
					'font-awesome-icon' => __( 'Font Awesome Icon', 'premium-addons-pro' ),
					'custom-image'      => __( 'Custom Image', 'premium-addons-pro' ),
					'animation'         => __( 'Lottie Animation', 'premium-addons-pro' ),
				),
				'condition' => array(
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_updated',
			array(
				'label'            => __( 'Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_notbar_icon',
				'default'          => array(
					'value'   => 'fas fa-exclamation-circle',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_control(
			'premium_notbar_custom_image',
			array(
				'label'     => __( 'Custom Image', 'premium-addons-pro' ),
				'type'      => Controls_Manager::MEDIA,
				'dynamic'   => array( 'active' => true ),
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'custom-image',
				),
			)
		);

		$this->add_control(
			'lottie_url',
			array(
				'label'       => __( 'Animation JSON URL', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array( 'active' => true ),
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_loop',
			array(
				'label'        => __( 'Loop', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default'      => 'true',
				'condition'    => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'animation',
				),
			)
		);

		$this->add_control(
			'lottie_reverse',
			array(
				'label'        => __( 'Reverse', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'animation',
				),
			)
		);

		$this->add_control(
			'premium_notbar_text',
			array(
				'type'       => Controls_Manager::WYSIWYG,
				'dynamic'    => array( 'active' => true ),
				'default'    => 'Morbi vel neque a est hendrerit laoreet in quis massa.',
				'condition'  => array(
					'premium_notbar_content_type' => 'editor',
				),
				'show_label' => false,
			)
		);

		$this->add_control(
			'premium_notbar_close_text',
			array(
				'label'   => __( 'Button Text', 'premium-addons-pro' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => array( 'active' => true ),
				'default' => 'x',
			)
		);

		$this->add_control(
			'premium_notbar_link_switcher',
			array(
				'label' => __( 'Link', 'premium-addons-pro' ),
				'type'  => Controls_Manager::SWITCHER,
			)
		);

		$this->add_control(
			'premium_notbar_link_selection',
			array(
				'label'       => __( 'Link Type', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'url'  => __( 'URL', 'premium-addons-pro' ),
					'link' => __( 'Existing Page', 'premium-addons-pro' ),
				),
				'default'     => 'url',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_link_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_link',
			array(
				'label'       => __( 'Link', 'premium-addons-pro' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => array( 'active' => true ),
				'default'     => array(
					'url' => '#',
				),
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => array(
					'premium_notbar_link_switcher'  => 'yes',
					'premium_notbar_link_selection' => 'url',
				),
			)
		);

		$this->add_control(
			'premium_notbar_existing_link',
			array(
				'label'       => __( 'Existing Page', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_all_posts(),
				'multiple'    => false,
				'condition'   => array(
					'premium_notbar_link_switcher'  => 'yes',
					'premium_notbar_link_selection' => 'link',
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'condition' => array(
					'premium_notbar_content_type' => 'editor',
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'justify-content: {{VALUE}}; text-align: {{VALUE}};',
				),
				'default'   => 'left',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_advanced',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_notbar_cookies',
			array(
				'label'       => __( 'Use Cookies', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This option will use cookies to remember user action', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'cookies_rule',
			array(
				'label'       => __( 'Cookies For Logged In Users', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable cookies also for logged in users', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_notbar_cookies' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_interval',
			array(
				'label'       => __( 'Expiration Time', 'premium-addons-pro' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'How much time before removing cookie, set the value in hours, default is: 1 hour', 'premium-addons-pro' ),
				'default'     => 1,
				'min'         => 0,
				'condition'   => array(
					'premium_notbar_cookies' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_responsive_switcher',
			array(
				'label'       => __( 'Responsive Controls', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'This options will hide the notification bar below a specific screen size', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', 'vh' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_overflow',
			array(
				'label'       => __( 'Overflow', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'visible' => __( 'Show', 'premium-addons-pro' ),
					'scroll'  => __( 'Scroll', 'premium-addons-pro' ),
					'auto'    => __( 'Auto', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'default'     => 'auto',
				'selectors'   => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon-text-container' => 'overflow-y: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_hide_tabs',
			array(
				'label'       => __( 'Hide on Tablets', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Hide Notification Bar below Elementor\'s Tablet Breakpoint ', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_notbar_responsive_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_notbar_hide_mobs',
			array(
				'label'       => __( 'Hide on Mobiles', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Hide Notification Bar below Elementor\'s Mobile Breakpoint ', 'premium-addons-pro' ),
				'condition'   => array(
					'premium_notbar_responsive_switcher' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_style',
			array(
				'label' => __( 'Bar', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_notbar_background',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}}' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'background_overlay',
			array(
				'label'     => __( 'Overlay Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => array(
					'enable_background_overlay' => 'yes',
				),
				'selectors' => array(
					'#premium-notbar-outer-container-{{ID}} .premium-notbar-background-overlay'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_border',
				'selector' => '#premium-notbar-{{ID}}',
			)
		);

		$this->add_control(
			'premium_notbar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_shadow',
				'selector' => '#premium-notbar-{{ID}}',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_icon_style',
			array(
				'label'     => __( 'Icon', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon'   => 'color: {{VALUE}};',
					'#premium-notbar-{{ID}} .premium-notbar-icon-wrap svg'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}}:hover .premium-notbar-icon'   => 'color: {{VALUE}};',
					'#premium-notbar-{{ID}}:hover .premium-notbar-icon-wrap svg'   => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_size',
			array(
				'label'      => __( 'Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon' => 'font-size: {{SIZE}}px;',
					'#premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_notbar_icon_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg'    => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_icon_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg',
			)
		);

		$this->add_control(
			'premium_notbar_icon_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie, #premium-notbar-{{ID}} .premium-notbar-icon-wrap svg' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_notbar_icon_shadow',
				'selector'  => '#premium-notbar-{{ID}} .premium-notbar-icon',
				'condition' => array(
					'premium_notbar_icon_switcher' => 'yes',
					'premium_notbar_content_type'  => 'editor',
					'premium_notbar_icon_selector' => 'font-awesome-icon',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'premium_notbar_img_shadow',
				'selector'  => '#premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie',
				'condition' => array(
					'premium_notbar_icon_switcher'  => 'yes',
					'premium_notbar_content_type'   => 'editor',
					'premium_notbar_icon_selector!' => 'font-awesome-icon',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon , #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_icon_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-icon, #premium-notbar-{{ID}} .premium-notbar-custom-image, #premium-notbar-{{ID}} .premium-notbar-icon-lottie' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_text_style',
			array(
				'label'     => __( 'Text', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_notbar_content_type' => 'editor',
				),
			)
		);

		$this->add_control(
			'premium_notbar_text_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-text'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_text_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-text',
			)
		);

		$this->add_control(
			'premium_notbar_text_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_text_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text',
			)
		);

		$this->add_control(
			'premium_notbar_text_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_text_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-text',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_notbar_text_box_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_text_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-content-wrapper .premium-notbar-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_notbar_close_style',
			array(
				'label' => __( 'Button', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_notbar_close_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_close_hover_color',
			array(
				'label'     => __( 'Hover Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close:hover'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_notbar_close_typo',
				'scheme'   => Typography::TYPOGRAPHY_1,
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_control(
			'premium_notbar_close_backcolor',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_notbar_close_backcolor_hover',
			array(
				'label'     => __( 'Hover Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				),
				'selectors' => array(
					'#premium-notbar-{{ID}} .premium-notbar-close:hover'   => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_notbar_close_border',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_control(
			'premium_notbar_close_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_notbar_close_shadow',
				'selector' => '#premium-notbar-{{ID}} .premium-notbar-close',
			)
		);

		$this->add_responsive_control(
			'premium_notbar_close_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'premium_notbar_close_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'#premium-notbar-{{ID}} .premium-notbar-close' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get Responsive Style
	 *
	 * Returns the responsive style based on Elementor's Breakpoints.
	 *
	 * @access protected
	 * @return string
	 */
	protected function get_responsive_style() {

		$breakpoints = Responsive::get_breakpoints();
		$style       = '<style>';
		$style      .= '@media ( max-width: ' . $breakpoints['md'] . 'px ) {';
		$style      .= '.premium-notbar-content-wrapper, .premium-notbar-icon-text-container {';
		$style      .= 'flex-direction: column !important; -moz-flex-direction: column !important; -webkit-flex-direction: column !important;';
		$style      .= '}';
		$style      .= '}';
		$style      .= '</style>';

		return $style;

	}

	/**
	 * Render Alert Box widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$id = $this->get_id();

		$icon_type = $settings['premium_notbar_icon_selector'];

		$bar_position = $settings['premium_notbar_position'];

		$bar_layout = ' premium-notbar-' . $settings['premium_notbar_top_select'];

		$bar_width = $settings['premium_notbar_width'];

		$content_type = $settings['premium_notbar_content_type'];

		if ( 'template' === $content_type ) {
			$template = $settings['premium_notbar_content_temp'];
		}

		if ( 'top' !== $bar_position ) {
			$this->add_render_attribute( 'wrap', 'class', 'premium-notbar-' . $bar_position );
			$this->add_render_attribute( 'button', 'class', 'premium-notbar-' . $bar_position );
		} elseif ( 'top' === $bar_position && is_user_logged_in() ) {
			$this->add_render_attribute( 'wrap', 'class', 'premium-notbar-edit-top' . $bar_layout );
			$this->add_render_attribute( 'button', 'class', 'premium-notbar-edit-top' );
		} else {
			$this->add_render_attribute( 'wrap', 'class', array( 'premium-notbar-top', $bar_layout ) );
			$this->add_render_attribute( 'button', 'class', 'premium-notbar-top' );
		}

		$this->add_render_attribute(
			'button',
			array(
				'type'  => 'button',
				'id'    => 'premium-notbar-close-' . $id,
				'class' => 'premium-notbar-close',
			)
		);

		if ( 'yes' === $settings['premium_notbar_link_switcher'] ) {

			if ( 'url' === $settings['premium_notbar_link_selection'] ) {
				$button_url = $settings['premium_notbar_link']['url'];
			} else {
				$button_url = get_permalink( $settings['premium_notbar_existing_link'] );
			}

			if ( ! empty( $button_url ) ) {

				$this->add_render_attribute( 'button', 'href', $button_url );

				if ( ! empty( $settings['premium_notbar_link']['is_external'] ) ) {
					$this->add_render_attribute( 'button', 'target', '_blank' );
				}

				if ( ! empty( $settings['premium_notbar_link']['nofollow'] ) ) {
					$this->add_render_attribute( 'button', 'rel', 'nofollow' );
				}
			}
		}

		if ( 'font-awesome-icon' === $icon_type ) {

			if ( ! empty( $settings['premium_notbar_icon'] ) ) {
				$this->add_render_attribute(
					'icon',
					array(
						'class'       => array(
							'premium-notbar-icon',
							$settings['premium_notbar_icon'],
						),
						'aria-hidden' => 'true',
					)
				);

			}

			$migrated = isset( $settings['__fa4_migrated']['premium_notbar_icon_updated'] );
			$is_new   = empty( $settings['premium_notbar_icon'] ) && Icons_Manager::is_migration_allowed();

		} elseif ( 'custom-image' === $icon_type ) {

			$src = $settings['premium_notbar_custom_image']['url'];

			$alt = Control_Media::get_image_alt( $settings['premium_notbar_custom_image'] );

			$this->add_render_attribute(
				'image',
				array(
					'class' => 'premium-notbar-custom-image',
					'src'   => $src,
					'alt'   => $alt,
				)
			);

		} else {

			$this->add_render_attribute(
				'alert_lottie',
				array(
					'class'               => array(
						'premium-notbar-icon-lottie',
						'premium-lottie-animation',
					),
					'data-lottie-url'     => $settings['lottie_url'],
					'data-lottie-loop'    => $settings['lottie_loop'],
					'data-lottie-reverse' => $settings['lottie_reverse'],
				)
			);

		}

		$bar_settings = array(
			'layout'     => $bar_width,
			'location'   => $bar_position,
			'position'   => $bar_layout,
			'varPos'     => ! empty( $settings['premium_notbar_float_pos'] ) ? $settings['premium_notbar_float_pos'] : '10%',
			'responsive' => ( 'yes' === $settings['premium_notbar_responsive_switcher'] ) ? true : false,
			'hideTabs'   => ( 'yes' === $settings['premium_notbar_hide_tabs'] ) ? true : false,
			'hideMobs'   => ( 'yes' === $settings['premium_notbar_hide_mobs'] ) ? true : false,
			'cookies'    => ( 'yes' === $settings['premium_notbar_cookies'] ) ? true : false,
			'logged'     => ( 'yes' === $settings['cookies_rule'] ) ? true : false,
			'interval'   => ! empty( $settings['premium_notbar_interval'] ) ? $settings['premium_notbar_interval'] : 1,
			'id'         => $id,
			'link'       => $settings['premium_notbar_link_switcher'],
		);

		$this->add_render_attribute( 'premium_notbar_text', 'class', 'premium-notbar-text' );

		$this->add_render_attribute(
			'alert',
			array(
				'id'            => 'premium-notbar-outer-container-' . $id,
				'class'         => array(
					'premium-notbar-outer-container',
					'premium-notbar-' . $settings['premium_notbar_content_type'],
				),
				'data-settings' => wp_json_encode( $bar_settings ),
			)
		);

		$this->add_render_attribute(
			'wrap',
			array(
				'id'    => 'premium-notbar-' . $id,
				'class' => array(
					'premium-notbar',
					'premium-notbar-' . $bar_width,
				),
			)
		);

		?>

	<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'alert' ) ); ?>>

		<?php if ( ! Plugin::instance()->editor->is_edit_mode() && 'yes' === $settings['enable_background_overlay'] ) : ?>
			<div class="premium-notbar-background-overlay"></div>
		<?php endif; ?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'wrap' ) ); ?>>
			<div class="premium-notbar-content-wrapper">
				<div class="premium-notbar-icon-text-container">
					<?php if ( 'yes' === $settings['premium_notbar_icon_switcher'] && 'editor' === $settings['premium_notbar_content_type'] ) : ?>
						<div class="premium-notbar-icon-wrap">
							<?php
							if ( 'font-awesome-icon' === $icon_type ) :
								if ( $is_new || $migrated ) :
									Icons_Manager::render_icon(
										$settings['premium_notbar_icon_updated'],
										array(
											'class'       => 'premium-notbar-icon',
											'aria-hidden' => 'true',
										)
									);
								else :
									?>
									<i <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon' ) ); ?>></i>
									<?php
								endif;
							elseif ( 'custom-image' === $icon_type ) :
								?>
								<img <?php echo wp_kses_post( $this->get_render_attribute_string( 'image' ) ); ?>>
							<?php else : ?>
								<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'alert_lottie' ) ); ?>></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( 'editor' === $content_type ) : ?>
						<span <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_notbar_text' ) ); ?>>
							<?php echo $this->parse_text_editor( $settings['premium_notbar_text'] ); ?>
						</span>
						<?php
					else :
						echo $this->getTemplateInstance()->get_template_content( $template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					endif;
					?>
				</div>
				<div class="premium-notbar-button-wrap">
					<a <?php echo wp_kses_post( $this->get_render_attribute_string( 'button' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_notbar_close_text'] ); ?>
					</a>
				</div>
			</div>
		</div>
	</div>

		<?php
		echo $this->get_responsive_style();
	}
}
