<?php
/**
 * Advanced Tabs
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Widget;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Happy_Addons\Elementor\Controls\Group_Control_Foreground;

defined( 'ABSPATH' ) || die();

class Advanced_Tabs extends Base {

	/**
	 * Get widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Advanced Tabs', 'happy-addons-pro' );
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
		return 'hm hm-tab';
	}

	public function get_keywords() {
		return [ 'tabs', 'section', 'advanced', 'toggle' ];
	}

	/**
     * Register widget content controls
     */
	protected function register_content_controls() {
		$this->__tabs_content_controls();
		$this->__options_content_controls();
	}

	protected function __tabs_content_controls() {

		$this->start_controls_section(
			'_section_tabs',
			[
				'label' => __( 'Tabs', 'happy-addons-pro' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'type' => Controls_Manager::TEXT,
				'label' => __( 'Title', 'happy-addons-pro' ),
				'default' => __( 'Tab Title', 'happy-addons-pro' ),
				'placeholder' => __( 'Type Tab Title', 'happy-addons-pro' ),
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'icon',
			[
				'type' => Controls_Manager::ICONS,
				'label' => __( 'Icon', 'happy-addons-pro' ),
				'show_label' => false,
			]
		);

		$repeater->add_control(
			'source',
			[
				'type' => Controls_Manager::SELECT,
				'label' => __( 'Content Source', 'happy-addons-pro' ),
				'default' => 'editor',
				'separator' => 'before',
				'options' => [
					'editor' => __( 'Editor', 'happy-addons-pro' ),
					'template' => __( 'Template', 'happy-addons-pro' ),
					'link' => __( 'Link', 'happy-addons-pro' ),
				]
			]
		);

		$repeater->add_control(
			'editor',
			[
				'label' => __( 'Content Editor', 'happy-addons-pro' ),
				'show_label' => false,
				'type' => Controls_Manager::WYSIWYG,
				'condition' => [
					'source' => 'editor',
				],
				'dynamic' => [
					'active' => true,
				]
			]
		);

		$repeater->add_control(
			'template',
			[
				'label' => __( 'Section Template', 'happy-addons-pro' ),
				'placeholder' => __( 'Select a section template for as tab content', 'happy-addons-pro' ),
				'description' => sprintf( __( 'Wondering what is section template or need to create one? Please click %1$shere%2$s ', 'happy-addons-pro' ),
					'<a target="_blank" href="' . esc_url( admin_url( '/edit.php?post_type=elementor_library&tabs_group=library&elementor_library_type=section' ) ) . '">',
					'</a>'
				),
				'type' => Controls_Manager::SELECT2,
				'label_block' => true,
				'options' => hapro_get_section_templates(),
				'condition' => [
					'source' => 'template',
				]
			]
		);

		$repeater->add_control(
			'link',
			[
				'label' => __('Link', 'happy-addons-pro'),
				'type' => Controls_Manager::URL,
				'placeholder' => __('https://example.com/', 'happy-addons-pro'),
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'source' => 'link',
				]
			]
		);

		$this->add_control(
			'tabs',
			[
				'show_label' => false,
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'title_field' => '{{title}}',
				'default' => [
					[
						'title' => 'Tab 1',
						'source' => 'editor',
						'editor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
					],
					[
						'title' => 'Tab 2',
						'source' => 'editor',
						'editor' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore <br><br>et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
					]
				]
			]
		);

		$this->end_controls_section();
	}

	protected function __options_content_controls() {

		$this->start_controls_section(
			'_section_options',
			[
				'label' => __( 'Options', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'_heading_tab_title',
			[
				'label' => __( 'Tab Title', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'nav_position',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Position', 'happy-addons-pro' ),
				'description' => __( 'Only applicable for desktop', 'happy-addons-pro' ),
				'default' => 'top',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' =>  __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' =>  __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'right' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'nav_align_x',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'default' => 'x-left',
				'toggle' => false,
				'options' => [
					'x-left' => [
						'title' =>  __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'x-center' => [
						'title' =>  __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-center',
					],
					'x-justify' => [
						'title' =>  __( 'Stretch', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-stretch',
					],
					'x-right' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					// '(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-top > .ha-tabs__nav' => 'justify-content: {{VALUE}};',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-top > .ha-tabs__nav' => 'justify-content: {{VALUE}}; flex-wrap: unset;',
				],
				'selectors_dictionary' => [
					'x-left' => 'flex-start',
					'x-right' => 'flex-end',
					'x-center' => 'center',
					'x-justify' => 'space-evenly'
				],
				'condition' => [
					'nav_position' => ['top', 'bottom'],
				],
				'style_transfer' => true,
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'nav_align_y',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Alignment', 'happy-addons-pro' ),
				'default' => 'y-top',
				'toggle' => false,
				'options' => [
					'y-top' => [
						'title' =>  __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'y-center' => [
						'title' =>  __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-middle',
					],
					'y-bottom' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-left > .ha-tabs__nav' => 'justify-content: {{VALUE}};',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-right > .ha-tabs__nav' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'y-top' => 'flex-start',
					'y-center' => 'center',
					'y-bottom' => 'flex-end',
				],
				'condition' => [
					'nav_position' => ['left', 'right'],
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'nav_text_align',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Text Alignment', 'happy-addons-pro' ),
				'default' => 'center',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' =>  __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' =>  __( 'Center', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title--desktop' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'center' => 'center',
					'right' => 'flex-end',
				],
				'style_transfer' => true,
			]
		);

		$this->add_control(
			'_heading_tab_icon',
			[
				'label' => __( 'Tab Icon', 'happy-addons-pro' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'nav_icon_position',
			[
				'type' => Controls_Manager::CHOOSE,
				'label' => __( 'Position', 'happy-addons-pro' ),
				'default' => 'left',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' =>  __( 'Left', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-left',
					],
					'top' => [
						'title' =>  __( 'Top', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-top',
					],
					'bottom' => [
						'title' =>  __( 'Bottom', 'happy-addons-pro' ),
						'icon' => 'eicon-v-align-bottom',
					],
					'right' => [
						'title' =>  __( 'Right', 'happy-addons-pro' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'style_transfer' => true,
			]
		);

		$this->end_controls_section();
	}

	/**
     * Register widget style controls
     */
	protected function register_style_controls() {
		$this->__tab_title_style_controls();
		$this->__tab_icon_style_controls();
		$this->__tab_content_style_controls();
	}

	protected function __tab_title_style_controls() {

		$this->start_controls_section(
			'_section_tab_nav',
			[
				'label' => __( 'Tab Title', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_margin_x',
			[
				'label' => __( 'Horizontal Margin (px)', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'step' => 'any',
				'selectors' => [
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-top > .ha-tabs__nav > .ha-tab__title:not(:last-child)' => 'margin-right: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-bottom > .ha-tabs__nav > .ha-tab__title:not(:last-child)' => 'margin-right: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-left > .ha-tabs__nav > .ha-tab__title' => 'margin-right: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-right > .ha-tabs__nav > .ha-tab__title' => 'margin-left: {{VALUE}}px;',
				],
			]
		);

		$this->add_control(
			'nav_margin_y',
			[
				'label' => __( 'Vertical Margin (px)', 'happy-addons-pro' ),
				'type' => Controls_Manager::NUMBER,
				'step' => 'any',
				'selectors' => [
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-top > .ha-tabs__nav > .ha-tab__title' => 'margin-bottom: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-bottom > .ha-tabs__nav > .ha-tab__title' => 'margin-top: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-left > .ha-tabs__nav > .ha-tab__title:not(:last-child)' => 'margin-bottom: {{VALUE}}px;',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--nav-right > .ha-tabs__nav > .ha-tab__title:not(:last-child)' => 'margin-bottom: {{VALUE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'nav_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'nav_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'nav_typography',
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'nav_text_shadow',
				'label' => __( 'Text Shadow', 'happy-addons-pro' ),
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title',
			]
		);

		$this->start_controls_tabs( '_tab_nav_stats' );
		$this->start_controls_tab(
			'_tab_nav_normal',
			[
				'label' => __( 'Normal', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'nav_border_style',
			[
				'label' => __( 'Border Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'happy-addons-pro' ),
					'solid' => __( 'Solid', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
				],
				'default' => 'none',
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'nav_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'condition' => [
					'nav_border_style!' => 'none'
				],
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'nav_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'nav_border_style!' => 'none'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title' => 'border-style: {{nav_border_style.VALUE}}; border-color: {{VALUE}};',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title' => 'border-width: {{nav_border_width.TOP}}px {{nav_border_width.RIGHT}}px {{nav_border_width.BOTTOM}}px {{nav_border_width.LEFT}}px;',
				],
				'default' => '#e8e8e8',
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'nav_text_gradient',
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav .ha-tab__title-text, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav .ha-tab__title-icon, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content .ha-tab__title-text, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content .ha-tab__title-icon',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'nav_bg',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav .ha-tab__title, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content .ha-tab__title',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'_tab_nav_active',
			[
				'label' => __( 'Active', 'happy-addons-pro' ),
			]
		);

		$this->add_control(
			'nav_active_border_style',
			[
				'label' => __( 'Border Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'happy-addons-pro' ),
					'solid' => __( 'Solid', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
				],
				'default' => 'none',
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'nav_active_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'condition' => [
					'nav_active_border_style!' => 'none'
				],
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'nav_active_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'nav_active_border_style!' => 'none'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title.ha-tab--active, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title.ha-tab--active' => 'border-style: {{nav_active_border_style.VALUE}}; border-color: {{VALUE}};',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title.ha-tab--active' => 'border-width: {{nav_active_border_width.TOP}}px {{nav_active_border_width.RIGHT}}px {{nav_active_border_width.BOTTOM}}px {{nav_active_border_width.LEFT}}px;',
				],
				'default' => '#e8e8e8',
			]
		);

		$this->add_group_control(
			Group_Control_Foreground::get_type(),
			[
				'name' => 'nav_active_text_gradient',
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab--active .ha-tab__title-text, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab--active .ha-tab__title-icon, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab--active .ha-tab__title-text',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'nav_active_bg',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav > .ha-tab__title.ha-tab--active, {{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__title.ha-tab--active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function __tab_icon_style_controls() {

		$this->start_controls_section(
			'_section_nav_icon',
			[
				'label' => __( 'Tab Icon', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'nav_icon_spacing',
			[
				'label' => __( 'Spacing', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--icon-left > .ha-tabs__nav .ha-tab__title-icon' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--icon-right > .ha-tabs__nav .ha-tab__title-icon' => 'margin-left: {{SIZE}}px;',
					'{{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--icon-top > .ha-tabs__nav .ha-tab__title-icon' => 'margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .ha-tabs-{{ID}}.ha-tabs--icon-bottom > .ha-tabs__nav .ha-tab__title-icon' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'nav_icon_size',
			[
				'label' => __( 'Size', 'happy-addons-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__nav .ha-tab__title-icon' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function __tab_content_style_controls() {

		$this->start_controls_section(
			'_section_tab_content',
			[
				'label' => __( 'Tab Content', 'happy-addons-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => __( 'Padding', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_border_style',
			[
				'label' => __( 'Border Style', 'happy-addons-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => __( 'None', 'happy-addons-pro' ),
					'solid' => __( 'Solid', 'happy-addons-pro' ),
					'double' => __( 'Double', 'happy-addons-pro' ),
					'dotted' => __( 'Dotted', 'happy-addons-pro' ),
					'dashed' => __( 'Dashed', 'happy-addons-pro' ),
				],
				'default' => 'none',
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'content_border_width',
			[
				'label' => __( 'Border Width', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'condition' => [
					'content_border_style!' => 'none'
				],
				'render_type' => 'ui',
			]
		);

		$this->add_control(
			'content_border_color',
			[
				'label' => __( 'Border Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'content_border_style!' => 'none'
				],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content' => 'border-style: {{content_border_style.VALUE}}; border-color: {{VALUE}};',
					'(tablet+){{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content' => 'border-width: {{content_border_width.TOP}}px {{content_border_width.RIGHT}}px {{content_border_width.BOTTOM}}px {{content_border_width.LEFT}}px;',
				],
				'default' => '#e8e8e8',
			]
		);

		$this->add_control(
			'content_border_radius',
			[
				'label' => __( 'Border Radius', 'happy-addons-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'content_typography',
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_control(
			'content_color',
			[
				'label' => __( 'Color', 'happy-addons-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'content_bg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ha-tabs-{{ID}} > .ha-tabs__content > .ha-tab__content',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$tabs = (array) $settings['tabs'];
		$id_int = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'tabs_wrapper', 'class', [
			'ha-tabs-' . $this->get_id(),
			'ha-tabs',
			'ha-tabs--nav-' . $settings['nav_position'],
			in_array( $settings['nav_position'], ['top', 'bottom'] ) ? 'ha-tabs--nav-' . $settings['nav_align_x'] : '',
			in_array( $settings['nav_position'], ['left', 'right'] ) ? 'ha-tabs--nav-' . $settings['nav_align_y'] : '',
			'ha-tabs--icon-' . $settings['nav_icon_position'],
		] );

		$this->add_render_attribute( 'tabs_wrapper', 'role', 'tablist' );
		?>
		<div <?php $this->print_render_attribute_string( 'tabs_wrapper' ); ?>>
			<div class="ha-tabs__nav">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					$tab_title_setting_key = $this->get_repeater_setting_key( 'title', 'tabs', $index );
					$tab_content_id = 'ha-tab__content-' . $id_int . $tab_count;

					$this->add_render_attribute( $tab_title_setting_key, [
						'id' => 'ha-tab-title-' . $id_int . $tab_count,
						'class' => [ 'ha-tab__title', 'ha-tab__title--desktop', 'elementor-repeater-item-' . $item['_id'] ],
						'data-tab' => $tab_count,
						'role' => 'tab',
						'aria-controls' => $tab_content_id,
					] );
					$item_tag = 'div';
					if ( 'link' === $item['source'] && $item['link']['url'] ) {
						$item_tag = 'a';
						$this->add_link_attributes( $tab_title_setting_key, $item['link'] );
					}
					?>
					<<?php echo ha_escape_tags($item_tag,'',['a']) . ' ' . $this->get_render_attribute_string( $tab_title_setting_key ); ?>>
						<?php if ( ! empty( $item['icon'] ) && ! empty( $item['icon']['value'] ) ) : ?>
						<span class="ha-tab__title-icon"><?php ha_render_icon( $item, false, 'icon' ); ?></span>
						<?php endif; ?>
						<span class="ha-tab__title-text"><?php echo ha_kses_basic( $item['title'] ); ?></span></<?php echo ha_escape_tags($item_tag,'',['a']);?>>
				<?php endforeach; ?>
			</div>
			<div class="ha-tabs__content">
				<?php
				foreach ( $tabs as $index => $item ) :
					$tab_count = $index + 1;

					if ( $item['source'] === 'editor' ) {
						$tab_content_setting_key = $this->get_repeater_setting_key( 'editor', 'tabs', $index );
						// $this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
					} else {
						$tab_content_setting_key = $this->get_repeater_setting_key( 'section', 'tabs', $index );
					}

					$tab_title_mobile_setting_key = $this->get_repeater_setting_key( 'tab_title_mobile', 'tabs', $tab_count );

					$this->add_render_attribute( $tab_content_setting_key, [
						'id' => 'ha-tab-content-' . $id_int . $tab_count,
						'class' => [ 'ha-tab__content', 'ha-clearfix', 'elementor-repeater-item-' . $item['_id'] ],
						'data-tab' => $tab_count,
						'role' => 'tabpanel',
						'aria-labelledby' => 'ha-tab-title-' . $id_int . $tab_count,
					] );

					$this->add_render_attribute( $tab_title_mobile_setting_key, [
						'class' => [ 'ha-tab__title', 'ha-tab__title--mobile', 'elementor-repeater-item-' . $item['_id'] ],
						'data-tab' => $tab_count,
						'role' => 'tab',
					] );
					$item_tag = 'div';
					if ( 'link' === $item['source'] && $item['link']['url'] ) {
						$item_tag = 'a';
						$this->add_link_attributes( $tab_title_mobile_setting_key, $item['link'] );
					}
					?>
					<<?php echo ha_escape_tags($item_tag,'',['a']) . ' ' . $this->get_render_attribute_string( $tab_title_mobile_setting_key ); ?>>
						<?php if ( ! empty( $item['icon'] ) && ! empty( $item['icon']['value'] ) ) : ?>
						<span class="ha-tab__title-icon"><?php ha_render_icon( $item, false, 'icon' ); ?></span>
						<?php endif; ?>
						<span class="ha-tab__title-text"><?php echo ha_kses_basic( $item['title'] ); ?></span>
					</<?php echo ha_escape_tags($item_tag,'',['a']);?>>
					<div <?php echo $this->get_render_attribute_string( $tab_content_setting_key ); ?>>
						<?php
						if ( $item['source'] === 'editor' ) :
							echo $this->parse_text_editor( $item['editor'] );
						elseif ( $item['source'] === 'template' && $item['template'] ) :
							echo ha_elementor()->frontend->get_builder_content_for_display( $item['template'] );
						endif;
						?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}
}
