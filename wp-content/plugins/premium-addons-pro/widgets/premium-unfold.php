<?php
/**
 * Class: Premium_Unfold
 * Name: Unfold
 * Slug: premium-unfold-addon
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

/**
 * Class Premium_Unfold
 */
class Premium_Unfold extends Widget_Base {

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
		return 'premium-unfold-addon';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Unfold', 'premium-addons-pro' );
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
		return 'pa-pro-unfold';
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
			'jquery-ui',
			'premium-pro',
		);
	}

	/**
	 * Retrieve Widget Categories.
	 *
	 * @since 1.0.0
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
		return array( 'pa', 'premium', 'read', 'section', 'more', 'cta', 'content' );
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
	 * Register Unfold controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_unfold_general_settings',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_title_switcher',
			array(
				'label'   => __( 'Title', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_control(
			'premium_unfold_title',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array( 'active' => true ),
				'default'   => 'Premium Unfold',
				'condition' => array(
					'premium_unfold_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_unfold_title_heading',
			array(
				'label'     => __( 'Title Heading', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'h3',
				'options'   => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'condition' => array(
					'premium_unfold_title_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'content_type',
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
			'premium_unfold_content',
			array(
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum',
				'dynamic'   => array( 'active' => true ),
				'condition' => array(
					'content_type' => 'editor',
				),
			)
		);

		$this->add_control(
			'live_temp_content',
			array(
				'label'       => __( 'Template Title', 'premium-addons-pro' ),
				'type'        => Controls_Manager::TEXT,
				'classes'     => 'premium-live-temp-title control-hidden',
				'label_block' => true,
				'condition'   => array(
					'content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'content_temp_live',
			array(
				'type'        => Controls_Manager::BUTTON,
				'label_block' => true,
				'button_type' => 'default papro-btn-block',
				'text'        => __( 'Create / Edit Template', 'premium-addons-pro' ),
				'event'       => 'createLiveTemp',
				'condition'   => array(
					'content_type' => 'template',
				),
			)
		);

		$this->add_control(
			'content_temp',
			array(
				'label'       => __( 'OR Select Existing Template', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'classes'     => 'premium-live-temp-label',
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'condition'   => array(
					'content_type' => 'template',
				),
				'label_block' => true,
			)
		);

		$this->add_responsive_control(
			'premium_unfold_content_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-justify',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-unfold-content,{{WRAPPER}} .premium-unfold-heading' => 'text-align: {{VALUE}}',
				),
				'default'   => 'left',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_button_settings',
			array(
				'label' => __( 'Button', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_button_fold_text',
			array(
				'label'              => __( 'Unfold Text', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => array( 'active' => true ),
				'default'            => __( 'Show more', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_button_unfold_text',
			array(
				'label'              => __( 'Fold Text', 'premium-addons-pro' ),
				'type'               => Controls_Manager::TEXT,
				'dynamic'            => array( 'active' => true ),
				'default'            => __( 'Show Less', 'premium-addons-pro' ),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_switcher',
			array(
				'label'       => __( 'Icon', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Enable or disable button icon', 'premium-addons-pro' ),
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_updated',
			array(
				'label'            => __( 'Fold Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_unfold_button_icon',
				'default'          => array(
					'value'   => 'fas fa-arrow-up',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
				'label_block'      => true,
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_unfolded_updated',
			array(
				'label'            => __( 'Unfold Icon', 'premium-addons-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'premium_unfold_button_icon_unfolded',
				'default'          => array(
					'value'   => 'fas fa-arrow-down',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
				'label_block'      => true,
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_position',
			array(
				'label'       => __( 'Icon Position', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'before',
				'options'     => array(
					'before' => __( 'Before', 'premium-addons-pro' ),
					'after'  => __( 'After', 'premium-addons-pro' ),
				),
				'condition'   => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
				'label_block' => true,
			)
		);

		$this->add_control(
			'premium_unfold_button_size',
			array(
				'label'       => __( 'Button Size', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'sm',
				'options'     => array(
					'sm'    => __( 'Small', 'premium-addons-pro' ),
					'md'    => __( 'Medium', 'premium-addons-pro' ),
					'lg'    => __( 'Large', 'premium-addons-pro' ),
					'block' => __( 'Block', 'premium-addons-pro' ),
				),
				'label_block' => true,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'premium_unfold_button_position',
			array(
				'label'        => __( 'Button Position', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'inside',
				'options'      => array(
					'inside'  => __( 'Inside', 'premium-addons-pro' ),
					'outside' => __( 'Outside', 'premium-addons-pro' ),
				),
				'prefix_class' => 'premium-unfold-btn-',
				'label_block'  => true,
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_unfold_button_align',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'toggle'    => false,
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'eicon-text-align-right',

					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button' => 'align-self: {{VALUE}}',
				),
				'condition' => array(
					'premium_unfold_button_size!' => 'block',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_sep_settings',
			array(
				'label' => __( 'Fade Effect', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_sep_switcher',
			array(
				'label'   => __( 'Faded Content', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			)
		);

		$this->add_responsive_control(
			'premium_unfold_sep_height',
			array(
				'label'       => __( 'Fade Height', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'description' => __( 'Increase or decrease fade height. The default value is 30px', 'premium-addons-pro' ),
				'range'       => array(
					'px' => array(
						'min' => 1,
						'max' => 400,
					),
				),
				'default'     => array(
					'size' => 30,
					'unit' => 'px',
				),
				'condition'   => array(
					'premium_unfold_sep_switcher' => 'yes',

				),
				'selectors'   => array(
					'{{WRAPPER}} #premium-unfold-gradient-{{ID}}' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_adv_settings',
			array(
				'label' => __( 'Advanced Settings', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_fold_height_select',
			array(
				'label'              => __( 'Fold Height', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'percent',
				'options'            => array(
					'percent' => __( 'Percentage', 'premium-addons-pro' ),
					'pixel'   => __( 'Pixels', 'premium-addons-pro' ),
				),
				'label_block'        => true,
				'separator'          => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'premium_unfold_fold_height',
			array(
				'label'              => __( 'Fold Height', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'How much of the folded content should be shown, default is 60%', 'premium-addons-pro' ),
				'min'                => 0,
				'default'            => 60,
				'condition'          => array(
					'premium_unfold_fold_height_select' => 'percent',
				),
				'frontend_available' => true,
			)
		);

		$this->add_responsive_control(
			'premium_unfold_fold_height_pix',
			array(
				'label'              => __( 'Fold Height', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'How much of the folded content should be shown, default is 100px', 'premium-addons-pro' ),
				'min'                => 0,
				'desktop_default'    => 100,
				'tablet_default'     => 100,
				'mobile_default'     => 100,
				'condition'          => array(
					'premium_unfold_fold_height_select' => 'pixel',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_fold_dur_select',
			array(
				'label'              => __( 'Fold Duration', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'fast',
				'options'            => array(
					'slow'   => __( 'Slow', 'premium-addons-pro' ),
					'fast'   => __( 'Fast', 'premium-addons-pro' ),
					'custom' => __( 'Custom', 'premium-addons-pro' ),
				),
				'label_block'        => true,
				'separator'          => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_fold_dur',
			array(
				'label'              => __( 'Number of Seconds', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'How much time does it take for the fold, default is 0.5s', 'premium-addons-pro' ),
				'min'                => 0.1,
				'default'            => 0.5,
				'condition'          => array(
					'premium_unfold_fold_dur_select' => 'custom',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_fold_easing',
			array(
				'label'              => __( 'Fold Easing', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'swing',
				'options'            => array(
					'swing'  => 'Swing',
					'linear' => 'Linear',
				),
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_unfold_dur_select',
			array(
				'label'              => __( 'Unfold Duration', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'fast',
				'options'            => array(
					'slow'   => 'Slow',
					'fast'   => 'Fast',
					'custom' => 'Custom',
				),
				'label_block'        => true,
				'separator'          => 'before',
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_unfold_dur',
			array(
				'label'              => __( 'Number of Seconds', 'premium-addons-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'description'        => __( 'How much time does it take for the unfold, default is 0.5s', 'premium-addons-pro' ),
				'min'                => 0.1,
				'default'            => 0.5,
				'condition'          => array(
					'premium_unfold_unfold_dur_select' => 'custom',
				),
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'premium_unfold_unfold_easing',
			array(
				'label'              => __( 'Unfold Easing', 'premium-addons-pro' ),
				'type'               => Controls_Manager::SELECT,
				'description'        => __( 'Choose the animation style', 'premium-addons-pro' ),
				'default'            => 'swing',
				'options'            => array(
					'swing'  => 'Swing',
					'linear' => 'Linear',
				),
				'label_block'        => true,
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();

		/*Start Box Style Settings*/
		$this->start_controls_section(
			'premium_unfold_style_settings',
			array(
				'label' => __( 'Box Settings', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_unfold_box_style_tabs' );

		$this->start_controls_tab(
			'premium_unfold_box_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		/*Box Background*/
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_unfold_box_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-unfold-container',
			)
		);

		/*Box Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_box_border',
				'selector' => '{{WRAPPER}} .premium-unfold-container',
			)
		);

		/*Box Border Radius*/
		$this->add_control(
			'premium_unfold_box_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Button Shadow*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_unfold_box_shadow',
				'selector' => '{{WRAPPER}} .premium-unfold-container',
			)
		);

		/*Box Margin*/
		$this->add_responsive_control(
			'premium_unfold_box_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Box Padding*/
		$this->add_responsive_control(
			'premium_unfold_box_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_unfold_box_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_unfold_box_background_hover',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-unfold-container:hover',
			)
		);

		/*Box Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_box_border_hover',
				'selector' => '{{WRAPPER}} .premium-unfold-container:hover',
			)
		);

		/*Box Border Radius*/
		$this->add_control(
			'premium_unfold_box_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Box Shadow*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_unfold_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-unfold-container:hover',
			)
		);

		/*Box Margin*/
		$this->add_responsive_control(
			'premium_unfold_box_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Box Padding*/
		$this->add_responsive_control(
			'premium_unfold_box_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-container:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/*End Box Style Settings*/
		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_title_style',
			array(
				'label'     => __( 'Title', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_unfold_title_switcher' => 'yes',
				),
			)
		);

		/*Title Color*/
		$this->add_control(
			'premium_unfold_heading_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-unfold-heading' => 'color: {{VALUE}};',
				),
			)
		);

		/*Title Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_unfold_heading_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-unfold-heading',
			)
		);

		/*Title Background*/
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_unfold_heading_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-unfold-heading',
			)
		);

		/*Title Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_title_border',
				'selector' => '{{WRAPPER}} .premium-unfold-heading',
			)
		);

		/*Title Border Radius*/
		$this->add_control(
			'premium_unfold_title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-heading' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_unfold_title_shadow',
				'selector' => '{{WRAPPER}} .premium-unfold-heading',
			)
		);

		/*TItle Margin*/
		$this->add_responsive_control(
			'premium_unfold_title_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Title Padding*/
		$this->add_responsive_control(
			'premium_unfold_title_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*End Content Style Settings*/
		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_content_style',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_pricing_desc_color',
			array(
				'label'     => __( 'Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-unfold-content' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'content_type' => 'editor',
				),
			)
		);

		/*Description Typography*/
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'unfold_content_typo',
				'global'    => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector'  => '{{WRAPPER}} .premium-unfold-content',
				'condition' => array(
					'content_type' => 'editor',
				),
			)
		);

		/*Description Background*/
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_unfold_content_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-unfold-content',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_unfold_content_shadow',
				'selector'  => '{{WRAPPER}} .premium-unfold-content',
				'condition' => array(
					'content_type' => 'editor',
				),
			)
		);

		/*Description Margin*/
		$this->add_responsive_control(
			'premium_unfold_content_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Description Padding*/
		$this->add_responsive_control(
			'premium_unfold_content_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'content_type' => 'editor',
				),
			)
		);

		/*End Content Style Settings*/
		$this->end_controls_section();

		/*Start Styling Section*/
		$this->start_controls_section(
			'premium_unfold_button_style_section',
			array(
				'label' => __( 'Button', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'premium_unfold_button_icon_size',
			array(
				'label'      => __( 'Icon Size', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button .premium-unfold-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .premium-button .premium-unfold-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		$icon_spacing = is_rtl() ? 'left' : 'right';

		$icon_spacing_after = is_rtl() ? 'right' : 'left';

		$this->add_responsive_control(
			'icon_spacing',
			array(
				'label'     => __( 'Icon Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => array(
					'{{WRAPPER}} .premium-unfold-after'  => 'margin-' . $icon_spacing_after . ': {{SIZE}}px',
					'{{WRAPPER}} .premium-unfold-before' => 'margin-' . $icon_spacing . ': {{SIZE}}px',
				),
				'condition' => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_unfold_button_typo',
				'global'   => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .premium-button',
			)
		);

		$this->start_controls_tabs( 'premium_unfold_button_style_tabs' );

		$this->start_controls_tab(
			'premium_unfold_button_style_normal',
			array(
				'label' => __( 'Normal', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button span' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_color_normal',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-button svg, {{WRAPPER}} .premium-button svg g path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_unfold_button_background_normal',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button' => 'background-color: {{VALUE}};',
				),
			)
		);

		/*Button Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_button_border_normal',
				'selector' => '{{WRAPPER}} .premium-button',
			)
		);

		/*Button Border Radius*/
		$this->add_control(
			'premium_unfold_button_border_radius_normal',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Icon Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_unfold_button_icon_shadow_normal',
				'selector'  => '{{WRAPPER}} .premium-button i',
				'condition' => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		/*Text Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Text Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_unfold_button_text_shadow_normal',
				'selector' => '{{WRAPPER}} .premium-button span',
			)
		);

		/*Button Shadow*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_unfold_button_box_shadow_normal',
				'selector' => '{{WRAPPER}} .premium-button',
			)
		);

		/*Button Margin*/
		$this->add_responsive_control(
			'premium_unfold_button_margin_normal',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Button Padding*/
		$this->add_responsive_control(
			'premium_unfold_button_padding_normal',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_unfold_button_style_hover',
			array(
				'label' => __( 'Hover', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_unfold_button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button:hover span'   => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_unfold_button_icon_color_hover',
			array(
				'label'     => __( 'Icon Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button:hover i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-button:hover svg, {{WRAPPER}} .premium-button:hover svg g path' => 'fill: {{VALUE}};',
				),
				'condition' => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_unfold_button_background_hover',
			array(
				'label'     => __( 'Background Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-button:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		/*Button Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_button_border_hover',
				'selector' => '{{WRAPPER}} .premium-button:hover',
			)
		);

		/*Button Border Radius*/
		$this->add_control(
			'premium_unfold_button_border_radius_hover',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button:hover' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Icon Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'     => __( 'Icon Shadow', 'premium-addons-pro' ),
				'name'      => 'premium_unfold_button_icon_shadow_hover',
				'selector'  => '{{WRAPPER}} .premium-button:hover i',
				'condition' => array(
					'premium_unfold_button_icon_switcher' => 'yes',
				),
			)
		);

		/*Text Shadow*/
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'label'    => __( 'Text Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_unfold_button_text_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-button:hover span',
			)
		);

		/*Button Shadow*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Button Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_unfold_button_box_shadow_hover',
				'selector' => '{{WRAPPER}} .premium-button:hover',
			)
		);

		/*Button Margin*/
		$this->add_responsive_control(
			'premium_unfold_button_margin_hover',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button:hover' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		/*Button Padding*/
		$this->add_responsive_control(
			'premium_unfold_button_padding_hover',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-button:hover' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/*End Button Style Section*/
		$this->end_controls_section();

		$this->start_controls_section(
			'premium_unfold_grad_style',
			array(
				'label'     => __( 'Fade Color', 'premium-addons-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'premium_unfold_sep_switcher' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_unfold_sep_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-unfold-gradient',
			)
		);

		/*Separator Border*/
		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_unfold_sep_border',
				'selector' => '{{WRAPPER}} .premium-unfold-gradient',
			)
		);

		/*Separator Border Radius*/
		$this->add_control(
			'premium_unfold_sep_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-gradient' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		/*Separator Padding*/
		$this->add_responsive_control(
			'premium_unfold_sep_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-unfold-gradient' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}


	/**
	 * Render Unfold Button
	 *
	 * Renders the HTML content of the unfold button.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render_unfold_button() {

		$settings    = $this->get_settings_for_display();
		$button_size = 'premium-btn-' . $settings['premium_unfold_button_size'];

		?>
		<a class="premium-button <?php echo esc_attr( $button_size ); ?>" href="javascript:;">
		<?php if ( $settings['premium_unfold_button_icon_switcher'] && 'before' === $settings['premium_unfold_button_icon_position'] ) : ?>
			<span class="premium-unfold-icon premium-unfold-before"></span>
		<?php endif; ?>
			<span class="premium-unfold-button-text"></span>
		<?php if ( $settings['premium_unfold_button_icon_switcher'] && 'after' === $settings['premium_unfold_button_icon_position'] ) : ?>
			<span class="premium-unfold-icon premium-unfold-after"></span>
		<?php endif; ?>
		</a>
		<?php
	}

	/**
	 * Render Unfold widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'premium_unfold_title', 'class', 'premium-unfold-heading' );

		$this->add_inline_editing_attributes( 'premium_unfold_title', 'basic' );

		$this->add_render_attribute( 'premium_unfold_content', 'class', 'premium-unfold-content-wrap' );

		if ( 'editor' === $settings['content_type'] ) {
			$this->add_inline_editing_attributes( 'premium_unfold_content', 'advanced' );
		}

		$fold_migrated = isset( $settings['__fa4_migrated']['premium_unfold_button_icon_updated'] );
		$fold_is_new   = empty( $settings['premium_unfold_button_icon'] ) && Icons_Manager::is_migration_allowed();

		$unfold_migrated = isset( $settings['__fa4_migrated']['premium_unfold_button_icon_unfolded_updated'] );
		$unfold_is_new   = empty( $settings['premium_unfold_button_icon_unfolded'] ) && Icons_Manager::is_migration_allowed();

		$title_tag = PAPRO_Helper::validate_html_tag( $settings['premium_unfold_title_heading'] );

		?>

		<div class='premium-unfold-container'>
			<div class='premium-unfold-folder'>
				<?php if ( 'yes' === $settings['premium_unfold_title_switcher'] && ! empty( $settings['premium_unfold_title'] ) ) : ?>
					<<?php echo wp_kses_post( $title_tag . ' ' . $this->get_render_attribute_string( 'premium_unfold_title' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_unfold_title'] ); ?>
					</<?php echo wp_kses_post( $title_tag ); ?>>
				<?php endif; ?>

				<div id="premium-unfold-content-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-unfold-content toggled">
					<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_unfold_content' ) ); ?>>
						<?php if ( 'editor' === $settings['content_type'] ) : ?>
							<?php echo $this->parse_text_editor( $settings['premium_unfold_content'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php
						else :
							$temp_id = empty( $settings['content_temp'] ) ? $settings['live_temp_content'] : $settings['content_temp'];
							echo $this->getTemplateInstance()->get_template_content( $temp_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( 'yes' === $settings['premium_unfold_sep_switcher'] ) : ?>
				<div id="premium-unfold-gradient-<?php echo esc_attr( $this->get_id() ); ?>" class="premium-unfold-gradient toggled"></div>
				<?php endif; ?>
			</div>
		<?php
		if ( 'inside' === $settings['premium_unfold_button_position'] ) {
			$this->render_unfold_button();
		}
		?>
		</div>
		<?php
		if ( 'outside' === $settings['premium_unfold_button_position'] ) {
			$this->render_unfold_button();
		}
		?>

		<?php if ( $settings['premium_unfold_button_icon_switcher'] ) : ?>
			<span class="premium-icon-holder-fold">
				<?php
				if ( $fold_migrated || $fold_is_new ) :
					Icons_Manager::render_icon( $settings['premium_unfold_button_icon_updated'], array( 'aria-hidden' => 'true' ) );
					else :
						?>
					<i class ="<?php echo esc_attr( $settings['premium_unfold_button_icon'] ); ?>"></i>
				<?php endif; ?>
			</span>
			<span class="premium-icon-holder-unfolded">
				<?php
				if ( $unfold_migrated || $unfold_is_new ) :
					Icons_Manager::render_icon( $settings['premium_unfold_button_icon_unfolded_updated'], array( 'aria-hidden' => 'true' ) );
					else :
						?>
					<i class ="<?php echo esc_attr( $settings['premium_unfold_button_icon_unfolded'] ); ?>"></i>
				<?php endif; ?>
			</span>
		<?php endif; ?>
		<?php
	}
}
