<?php
/**
 * Class: Premium_Content_Toggle
 * Name: Content Switcher
 * Slug: premium-addon-content-toggle
 */

namespace PremiumAddonsPro\Widgets;

// PremiumAddonsPro Classes.
use PremiumAddonsPro\Includes\PAPRO_Helper;

// Elementor Classes.
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

// PremiumAddons Classes.
use PremiumAddons\Includes\Helper_Functions;
use PremiumAddons\Includes\Premium_Template_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Premium_Content_Toggle
 */
class Premium_Content_Toggle extends Widget_Base {

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
		return 'premium-addon-content-toggle';
	}

	/**
	 * Retrieve Widget Title.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return sprintf( '%1$s %2$s', Helper_Functions::get_prefix(), __( 'Content Switcher', 'premium-addons-pro' ) );
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
		return 'pa-pro-content-switcher';
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
		return array( 'toggle' );
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
		return array( 'premium-pro' );
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
		return 'https://premiumaddons.com/support/';
	}

	/**
	 * Register Content Toggle controls.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

		$this->start_controls_section(
			'premium_content_toggle_headings_section',
			array(
				'label' => __( 'Switcher', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_labels_switcher',
			array(
				'label'     => __( 'Show Labels', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => 'Show',
				'label_off' => 'Hide',
				'default'   => 'yes',
			)
		);

		$this->add_control(
			'premium_content_toggle_heading_one',
			array(
				'label'     => __( 'First Label', 'premium-addons-pro' ),
				'default'   => __( 'Content #1', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'premium_content_toggle_labels_switcher'    => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_heading_two',
			array(
				'label'     => __( 'Second Label', 'premium-addons-pro' ),
				'default'   => __( 'Content #2', 'premium-addons-pro' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => array(
					'active' => true,
				),
				'condition' => array(
					'premium_content_toggle_labels_switcher'    => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_headings_size',
			array(
				'label'     => __( 'HTML Tag', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				),
				'default'   => 'h3',
				'condition' => array(
					'premium_content_toggle_labels_switcher'    => 'yes',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_heading_layout',
			array(
				'label'        => __( 'Display', 'premium-addons-pro' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'no'  => __( 'Inline', 'premium-addons-pro' ),
					'yes' => __( 'Block', 'premium-addons-pro' ),
				),
				'default'      => 'no',
				'prefix_class' => 'premium-toggle-stack-',
				'condition'    => array(
					'premium_content_toggle_labels_switcher'    => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_headings_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-switcher' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}}.premium-toggle-stack-yes .premium-content-toggle-switcher' => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_first_content_section',
			array(
				'label' => __( 'Content 1', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_tools',
			array(
				'label'   => __( 'Content to Show', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'text_editor'         => __( 'Text Editor', 'premium-addons-pro' ),
					'elementor_templates' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default' => 'text_editor',
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_text',
			array(
				'type'        => Controls_Manager::WYSIWYG,
				'default'     => 'Donec id elit non mi porta gravida at eget metus. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Cras mattis consectetur purus sit amet fermentum. Nullam id dolor id nibh ultricies vehicula ut id elit. Donec id elit non mi porta gravida at eget metus.',
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'premium_content_toggle_first_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_first_content_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'condition' => array(
					'premium_content_toggle_first_content_tools'  => 'text_editor',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-monthly-text' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_templates',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-pro' ),
				'description' => __( 'Elementor Template is a template which you can choose from Elementor library. Each template will be shown in content', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'label_block' => true,
				'condition'   => array(
					'premium_content_toggle_first_content_tools'  => 'elementor_templates',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_second_content_section',
			array(
				'label' => __( 'Content 2', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_tools',
			array(
				'label'   => __( 'Content', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'text_editor'         => __( 'Text Editor', 'premium-addons-pro' ),
					'elementor_templates' => __( 'Elementor Template', 'premium-addons-pro' ),
				),
				'default' => 'text_editor',
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_text',
			array(
				'label'       => __( 'Text Editor', 'premium-addons-pro' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => array( 'active' => true ),
				'default'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
				'label_block' => true,
				'condition'   => array(
					'premium_content_toggle_second_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_second_content_alignment',
			array(
				'label'     => __( 'Alignment', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'    => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'  => array(
						'title' => __( 'Center', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'   => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-right',
					),
					'justify' => array(
						'title' => __( 'Justify', 'premium-addons-pro' ),
						'icon'  => 'fa fa-align-justify',
					),
				),
				'condition' => array(
					'premium_content_toggle_second_content_tools'  => 'text_editor',
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-yearly-text' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_templates',
			array(
				'label'       => __( 'Elementor Template', 'premium-addons-pro' ),
				'description' => __( 'Elementor Template is a template which you can choose from Elementor library. Each template will be shown in content', 'premium-addons-pro' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->getTemplateInstance()->get_elementor_page_list(),
				'label_block' => true,
				'condition'   => array(
					'premium_content_toggle_second_content_tools'  => 'elementor_templates',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_content_display',
			array(
				'label' => __( 'Display Options', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_animation',
			array(
				'label'   => __( 'Animation', 'premium-addons-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'opacity' => __( 'Fade', 'premium-addons-pro' ),
					'fade'    => __( 'Slide', 'premium-addons-pro' ),
				),
				'default' => 'opacity',
			)
		);

		$this->add_control(
			'premium_content_toggle_fade_dir',
			array(
				'label'     => __( 'Direction', 'premium-addons-pro' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'top'    => array(
						'title' => __( 'Top', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-down',
					),
					'right'  => array(
						'title' => __( 'Right', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'bottom' => array(
						'title' => __( 'Bottom', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'left'   => array(
						'title' => __( 'Left', 'premium-addons-pro' ),
						'icon'  => 'fa fa-arrow-right',
					),
				),
				'default'   => 'top',
				'condition' => array(
					'premium_content_toggle_animation' => 'fade',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_switcher_headings_container_style_section',
			array(
				'label' => __( 'Switcher', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'premium_content_toggle_swithcer_headings_container_tabs' );

		$this->start_controls_tab(
			'premium_content_toggle_switcher_style_tab',
			array(
				'label' => __( 'Switcher', 'premium-addons-pro' ),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_switch_size',
			array(
				'label'     => __( 'Size', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 15,
				),
				'range'     => array(
					'px' => array(
						'min' => 1,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-button'   => 'font-size: {{SIZE}}px',
				),

			)
		);

		$this->add_control(
			'controller_border_radius',
			array(
				'label'      => __( 'Controller Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-switch-control:before' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'switcher_border_radius',
			array(
				'label'      => __( 'Switcher Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-switch-control' => 'border-radius: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_switcher_colors_popver',
			array(
				'label' => __( 'Colors', 'premium-addons-pro' ),
				'type'  => Controls_Manager::POPOVER_TOGGLE,
			)
		);

		$this->start_popover();

		$this->add_control(
			'premium_content_toggle_popover_switch_first_content_color',
			array(
				'label' => __( 'Controller State 1 Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_content_toggle_switch_normal_background_color',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-content-toggle-switch-control:before',
			)
		);

		$this->add_control(
			'premium_content_toggle_popover_switch_second_content_color',
			array(
				'label' => __( 'Controller State 2 Color', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_content_toggle_switch_active_background_color',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-content-toggle-switch:checked + .premium-content-toggle-switch-control:before',
			)
		);

		$this->add_control(
			'premium_content_toggle_popover_switch_background',
			array(
				'label' => __( 'Switcher Background', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_content_toggle_fieldset_active_background_color',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-content-toggle-switch-control',
			)
		);

		$this->end_popover();

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Controller Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_content_toggle_switch_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-switch-control:before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'label'    => __( 'Switcher Shadow', 'premium-addons-pro' ),
				'name'     => 'premium_content_toggle_fieldset_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-switch-control',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_content_toggle_headings_style_tab',
			array(
				'label'     => __( 'Labels', 'premium-addons-pro' ),
				'condition' => array(
					'premium_content_toggle_labels_switcher'    => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_switcher_headings_spacing',
			array(
				'label'     => __( 'Spacing', 'premium-addons-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 150,
						'step' => 1,
					),
				),
				'selectors' => array(
					'{{WRAPPER}}.premium-toggle-stack-no .premium-content-toggle-heading-one' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}}.premium-toggle-stack-no .premium-content-toggle-heading-two' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}}.premium-toggle-stack-yes .premium-content-toggle-heading-one' => 'margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}}.premium-toggle-stack-yes .premium-content-toggle-heading-two' => 'margin-top: {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_left_heading_head',
			array(
				'label' => __( 'First Label', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_content_toggle_left_heading_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-heading-one *' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_content_toggle_left_heading_typhography',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-one *',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_left_heading_text_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-one *',
			)
		);

		$this->add_control(
			'premium_content_toggle_left_heading_background_color',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-heading-one *' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_toggle_left_heading_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-one *',
			)
		);

		$this->add_control(
			'premium_content_toggle_left_heading_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-heading-one *' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_left_heading_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-one *',
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_left_headings_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-heading-one *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_right_heading_head',
			array(
				'label' => __( 'Second Label', 'premium-addons-pro' ),
				'type'  => Controls_Manager::HEADING,
			)
		);

		$this->add_control(
			'premium_content_toggle_right_heading_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-heading-two *' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'premium_content_toggle_right_heading_typhography',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-two *',
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_right_heading_text_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-two *',
			)
		);

		$this->add_control(
			'premium_content_toggle_right_heading_background_color',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-heading-two *' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_right_heading_content_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-two *',
			)
		);

		$this->add_control(
			'premium_content_toggle_right_heading_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-heading-two *' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_right_heading_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-heading-two *',
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_right_heading_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-heading-two *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_content_toggle_container_tab',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_switcher_container_background_color',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-switcher' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_toggle_switcher_container_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-switcher',
			)
		);

		$this->add_control(
			'premium_content_toggle_switcher_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-switcher' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_switcher_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-switcher',
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_switcher_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-switcher' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_switcher_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-switcher' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_content_style_section',
			array(
				'label' => __( 'Content', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'premium_content_toggle_two_content_height',
			array(
				'label'      => __( 'Height', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'step' => 1,
						'max'  => 1000,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-container .premium-content-toggle-two-content > li' => 'min-height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'premium_content_toggle_content_style_tabs' );

		$this->start_controls_tab(
			'premium_content_toggle_first_content_style_tab',
			array(
				'label' => __( 'First Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-monthly-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-content-toggle-monthly-text *' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_content_toggle_first_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_content_toggle_first_content_typhography',
				'selector'  => '{{WRAPPER}} .premium-content-toggle-monthly-text',
				'condition' => array(
					'premium_content_toggle_first_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_content_toggle_first_content_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-content-toggle-monthly-text',
				'condition' => array(
					'premium_content_toggle_first_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_background_color',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-monthly' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_toggle_first_content_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-monthly',
			)
		);

		$this->add_control(
			'premium_content_toggle_first_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-monthly' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_first_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-monthly',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'premium_content_toggle_second_content_style_tab',
			array(
				'label' => __( 'Second Content', 'premium-addons-pro' ),
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_color',
			array(
				'label'     => __( 'Text Color', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => array(
					'type'  => Color::get_type(),
					'value' => Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-yearly-text' => 'color: {{VALUE}};',
					'{{WRAPPER}} .premium-content-toggle-yearly-text *' => 'color: {{VALUE}};',
				),
				'condition' => array(
					'premium_content_toggle_second_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'premium_content_toggle_second_content_typhography',
				'selector'  => '{{WRAPPER}} .premium-content-toggle-yearly-text',
				'condition' => array(
					'premium_content_toggle_second_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			array(
				'name'      => 'premium_content_toggle_second_content_text_shadow',
				'selector'  => '{{WRAPPER}} .premium-content-toggle-yearly-text',
				'condition' => array(
					'premium_content_toggle_second_content_tools'  => 'text_editor',
				),
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_background_color',
			array(
				'label'     => __( 'Background', 'premium-addons-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .premium-content-toggle-yearly' => 'background: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_toggle_second_content_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-yearly',
			)
		);

		$this->add_control(
			'premium_content_toggle_second_content_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-yearly' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_second_content_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-yearly',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'premium_content_toggle_contents_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_contents_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-monthly, {{WRAPPER}} .premium-content-toggle-yearly' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'premium_content_toggle_container_style',
			array(
				'label' => __( 'Container', 'premium-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'premium_content_toggle_container_background',
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .premium-content-toggle-container',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'premium_content_toggle_container_border',
				'selector' => '{{WRAPPER}} .premium-content-toggle-container',
			)
		);

		$this->add_control(
			'premium_content_toggle_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'premium-addons-pro' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-container' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'premium_content_toggle_container_box_shadow',
				'selector' => '{{WRAPPER}} .premium-content-toggle-container',
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_container_margin',
			array(
				'label'      => __( 'Margin', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'premium_content_toggle_container_padding',
			array(
				'label'      => __( 'Padding', 'premium-addons-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .premium-content-toggle-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render Content Toggle widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		if ( 'opacity' === $settings['premium_content_toggle_animation'] ) {

			$animation = 'opacity';

		} elseif ( 'fade' === $settings['premium_content_toggle_animation'] ) {

			$animation = 'fade-' . $settings['premium_content_toggle_fade_dir'];

		}

		$this->add_inline_editing_attributes( 'premium_content_toggle_heading_one', 'basic' );

		$this->add_inline_editing_attributes( 'premium_content_toggle_heading_two', 'basic' );

		$this->add_inline_editing_attributes( 'premium_content_toggle_first_content_text', 'advanced' );

		$this->add_inline_editing_attributes( 'premium_content_toggle_second_content_text', 'advanced' );

		$this->add_render_attribute( 'premium_content_toggle_first_content_text', 'class', 'premium-content-toggle-monthly-text' );

		$this->add_render_attribute( 'premium_content_toggle_second_content_text', 'class', 'premium-content-toggle-yearly-text' );

		$heading_size = PAPRO_Helper::validate_html_tag( $settings['premium_content_toggle_headings_size'] );

		?>

		<div class='premium-content-toggle-container'>
			<div class="premium-content-toggle-switcher">
			<?php if ( 'yes' === $settings['premium_content_toggle_labels_switcher'] ) : ?>

				<div class="premium-content-toggle-heading-one">
					<<?php echo wp_kses_post( $heading_size . ' ' . $this->get_render_attribute_string( 'premium_content_toggle_heading_one' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_content_toggle_heading_one'] ); ?>
					</<?php echo wp_kses_post( $heading_size ); ?>>
				</div>

			<?php endif; ?>

			<div class="premium-content-toggle-button">
				<label class="premium-content-toggle-switch-label">
					<input class="premium-content-toggle-switch premium-content-toggle-switch-normal elementor-clickable" type="checkbox">
					<span class="premium-content-toggle-switch-control elementor-clickable"></span>
				</label>
			</div>

			<?php if ( 'yes' === $settings['premium_content_toggle_labels_switcher'] ) : ?>

				<div class="premium-content-toggle-heading-two">
					<<?php echo wp_kses_post( $heading_size . ' ' . $this->get_render_attribute_string( 'premium_content_toggle_heading_two' ) ); ?>>
						<?php echo wp_kses_post( $settings['premium_content_toggle_heading_two'] ); ?>
					</<?php echo wp_kses_post( $heading_size ); ?>>
				</div>

			<?php endif; ?>

		</div>

		<div class="premium-content-toggle-list <?php echo esc_attr( $animation ); ?>">
			<ul class="premium-content-toggle-two-content">
				<li data-type="premium-content-toggle-monthly" class="premium-content-toggle-is-visible premium-content-toggle-monthly">
					<?php if ( 'text_editor' === $settings['premium_content_toggle_first_content_tools'] ) : ?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_content_toggle_first_content_text' ) ); ?>>
						<?php echo $this->parse_text_editor( $settings['premium_content_toggle_first_content_text'] ); ?>
						</div>

						<?php
					elseif ( 'elementor_templates' === $settings['premium_content_toggle_first_content_tools'] ) :
						$first_template = $settings['premium_content_toggle_first_content_templates'];
						?>

					<div class="premium-content-toggle-first-content-item-wrapper">
						<?php echo $this->getTemplateInstance()->get_template_content( $first_template ); ?>
					</div>

					<?php endif; ?>
				</li>

				<li data-type="premium-content-toggle-yearly" class="premium-content-toggle-is-hidden premium-content-toggle-yearly">
					<?php if ( 'text_editor' === $settings['premium_content_toggle_second_content_tools'] ) : ?>

						<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'premium_content_toggle_second_content_text' ) ); ?>>
							<?php echo $this->parse_text_editor( $settings['premium_content_toggle_second_content_text'] ); ?>
						</div>

						<?php
					elseif ( 'elementor_templates' === $settings['premium_content_toggle_second_content_tools'] ) :
						$second_template = $settings['premium_content_toggle_second_content_templates'];
						?>

					<div class="premium-content-toggle-second-content-item-wrapper">
						<?php echo $this->getTemplateInstance()->get_template_content( $second_template ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<?php endif; ?>
				</li>
			</ul>
		</div>
	</div>

		<?php
	}
}
