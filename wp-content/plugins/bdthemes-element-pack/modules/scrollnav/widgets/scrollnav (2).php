<?php
namespace ElementPack\Modules\Scrollnav\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Scrollnav extends Module_Base {
	public function get_name() {
		return 'bdt-scrollnav';
	}

	public function get_title() {
		return BDTEP . __( 'Scroll Navigation', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-scrollnav';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'scrollnav', 'menu' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-scrollnav', 'tippy' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['popper', 'tippyjs', 'ep-scripts'];
        } else {
			return [ 'popper', 'tippyjs', 'ep-scrollnav' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/X2B23zD7gRg';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_scrollnav',
			[
				'label' => __( 'Scrollnav', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'nav_title',
			[
				'label'   => __( 'Nav Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'dynamic' => [ 'active' => true ],
				'default' => __( 'Nav Title' , 'bdthemes-element-pack' ),
			]
		);

		$repeater->add_control(
			'nav_link',
			[
				'label'       => __( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'default'     => [ 'url' => '#' ],
				'description' => 'Add your section id WITH the # key. e.g: #my-id also you can add internal/external URL',
			]
		);

		$repeater->add_control(
			'scroll_nav_icon',
			[
				'label' => __( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'nav_icon',
				'skin' => 'inline',
			]
		);

		$this->add_control(
			'navs',
			[
				'label' => __( 'Nav Items', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'nav_title' => __( 'Nav #1', 'bdthemes-element-pack' ),
						'nav_link'  => [
							'url' => '#section-1',
						]
					],
					[
						'nav_title'   => __( 'Nav #2', 'bdthemes-element-pack' ),
						'nav_link'  => [
							'url' => '#section-2',
						]
					],
					[
						'nav_title'   => __( 'Nav #3', 'bdthemes-element-pack' ),
						'nav_link'  => [
							'url' => '#section-3',
						]
					],
					[
						'nav_title'   => __( 'Nav #4', 'bdthemes-element-pack' ),
						'nav_link'  => [
							'url' => '#section-4',
						]
					],
					[
						'nav_title'   => __( 'Nav #5', 'bdthemes-element-pack' ),
						'nav_link'  => [
							'url' => '#section-5',
						]
					],
				],
				'title_field' => '{{{ nav_title }}}',
			]
		);

		$this->add_control(
			'nav_style',
			[
				'label'     => __( 'Nav View', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'default',
				'options'   => [
					'default' => __( 'Text', 'bdthemes-element-pack' ),
					'dot'     => __( 'Dots', 'bdthemes-element-pack' ),
				]
			]
		);

		$this->add_control(
			'vertical_nav',
			[
				'label' => __( 'Vertical Nav', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'fixed_nav',
			[
				'label'        => __( 'Fixed Nav', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-scrollnav-fixed-',
				'render_type'  => 'template',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional',
			[
				'label' => __( 'Additional', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'alignment',
			[
				'label'       => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'left',
				'condition' => [
					'fixed_nav!' => 'yes',
				]
			]
		);

		$this->add_control(
			'nav_position',
			[
				'label'     => __( 'Nav Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center-left',
				'options'   => element_pack_position(),
				'condition' => [
					'fixed_nav' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'nav_offset',
			[
				'label' => __( 'Nav Offset', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 250,
						'step' => 5,
					],
				],
				'condition' => [
					'fixed_nav' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav div[class*="bdt-navbar"]' => 'margin: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'nav_spacing',
			[
				'label'      => __( 'Nav Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_height',
			[
				'label' => __( 'Menu Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 150,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'nav_style' => 'default'
				]
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'     => __( 'Icon Position', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'right',
				'options'   => [
					'left'  => __( 'Before', 'bdthemes-element-pack' ),
					'right' => __( 'After', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'nav_style' => 'default',
				],
			]
		);

		$this->add_responsive_control(
			'icon_indent',
			[
				'label'   => __( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-button-icon-align-right' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-scrollnav .bdt-button-icon-align-left'  => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'nav_style' => 'default',
				],
			]
		);

		$this->add_control(
			'content_offset',
			[
				'label'       => __( 'Target Offset', 'bdthemes-element-pack' ),
				'description' => __( 'This offset work when you click and go to targeted location', 'bdthemes-element-pack' ),
				'separator'   => 'before',
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => -250,
						'max'  => 250,
						'step' => 5
					]
				],
				'default' => [
					'size' => 0
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_tooltip',
			[
				'label'     => __( 'Tooltip', 'bdthemes-element-pack' ),
				'condition' => [
					'nav_style' => 'dot',
				]
			]
		);



		$this->add_control(
			'dotnav_tooltip_animation',
			[
				'label'   => __( 'Animation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shift-toward',
				'options' => [
					'shift-away'   => __( 'Shift-Away', 'bdthemes-element-pack' ),
					'shift-toward' => __( 'Shift-Toward', 'bdthemes-element-pack' ),
					'fade'         => __( 'Fade', 'bdthemes-element-pack' ),
					'scale'        => __( 'Scale', 'bdthemes-element-pack' ),
					'perspective'  => __( 'Perspective', 'bdthemes-element-pack' ),
				],
				'render_type'  => 'template',
			]
		);



		$this->add_control(
			'dotnav_tooltip_placement',
			[
				'name'    => 'marker_tooltip_placement',
				'label'   => __( 'Placement', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'top'    => __( 'Top', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
					'left'   => __( 'Left', 'bdthemes-element-pack' ),
					'right'  => __( 'Right', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'dotnav_tooltip_x_offset',
			[
				'label'   => __( 'Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'dotnav_tooltip_y_offset',
			[
				'label'   => __( 'Distance', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
			]
		);

		$this->add_control(
			'dotnav_tooltip_arrow',
			[
				'label'        => __( 'Arrow', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'dotnav_tooltip_trigger',
			[
				'label'       => __( 'Trigger on Click', 'bdthemes-element-pack' ),
				'description' => __( 'Don\'t set yes when you set lightbox image with marker.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_nav',
			[
				'label'     => __( 'Default Nav', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'nav_style' => 'default',
				],
			]
		);

		$this->add_control(
			'navbar_style',
			[
				'label'   => __( 'Navbar Style', 'bdthemes-element-pack' ) . BDTEP_UC,
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => __( 'Default', 'bdthemes-element-pack' ),
					'1' => __( 'Style 1', 'bdthemes-element-pack' ),
					'2' => __( 'Style 2', 'bdthemes-element-pack' ),
					'3' => __( 'Style 3', 'bdthemes-element-pack' ),
					'4' => __( 'Style 4', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-navbar-style-',
			]
		);

		$this->start_controls_tabs( 'tabs_nav_style' );

		$this->start_controls_tab(
			'tab_nav_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'nav_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'nav_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-scrollnav ul li > a',
			]
		);

		$this->add_responsive_control(
			'nav_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'nav_shadow',
				'selector' => '{{WRAPPER}} .bdt-scrollnav ul li > a',
			]
		);

		$this->add_responsive_control(
			'nav_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'nav_margin',
			[
				'label'      => __( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'nav_typography',
				'label'    => __( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-scrollnav ul li > a',
			]
		);

		$this->add_control(
			'nav_icon_heading',
			[
				'label'     => __( 'Icon', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'nav_icon_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a span' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-scrollnav ul li > a svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'nav_icon_size',
			[
				'label'      => esc_html__( 'Size', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'       => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a span' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_nav_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navbar_hover_style_color',
			[
				'label'     => __( 'Style Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li:hover > a:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-navbar-nav > li:hover > a:after'  => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navbar_style!' => '',
				],
			]
		);

		$this->add_control(
			'nav_hover_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_hover_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'nav_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_icon_hover_heading',
			[
				'label'     => __( 'Icon', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'nav_icon_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li > a:hover span' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-scrollnav ul li > a:hover svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_nav_active',
			[
				'label' => __( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navbar_active_style_color',
			[
				'label'     => __( 'Style Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a:after'  => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navbar_style!' => '',
				],
			]
		);

		$this->add_control(
			'nav_active_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li.bdt-active > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_active_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li.bdt-active > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_active_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'nav_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li.bdt-active > a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'nav_icon_active_heading',
			[
				'label'     => __( 'Icon', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'nav_icon_active_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav ul li.bdt-active > a span' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-scrollnav ul li.bdt-active > a svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dot_nav',
			[
				'label'     => __( 'Dot Nav', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'nav_style' => 'dot',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_nav_style_dot' );

		$this->start_controls_tab(
			'tab_dot_nav_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'dot_nav_size',
			[
				'label' => __( 'Dots Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav>*>*' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dot_nav_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dot_nav_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a',
			]
		);

		$this->add_responsive_control(
			'dot_nav_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dot_nav_shadow',
				'selector' => '{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a',
			]
		);

		$this->add_responsive_control(
			'dot_nav_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dot_nav_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'dot_nav_hover_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dot_nav_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'dot_nav_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li > a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dot_nav_active',
			[
				'label' => __( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'dot_nav_active_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li.bdt-active > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dot_nav_active_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'dot_nav_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-scrollnav .bdt-dotnav > li.bdt-active > a' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'dotnav_tooltip_styles_tab',
			[
				'label'     => __( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'nav_style' => 'dot',
				],
			]
		);

		$this->add_responsive_control(
			'dotnav_tooltip_width',
			[
				'label'      => __( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [
					'px', 'em',
				],
				'range'      => [
					'px' => [
						'min' => 50,
						'max' => 500,
					],
				],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dotnav_tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'dotnav_tooltip_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'dotnav_tooltip_text_align',
			[
				'label'   => __( 'Text Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'dotnav_tooltip_background',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"], .tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-backdrop',
			]
		);

		$this->add_control(
			'dotnav_tooltip_arrow_color',
			[
				'label'  => __( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',

					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'color: {{VALUE}}',
				],
				'condition' => [
					'dotnav_tooltip_arrow' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dotnav_tooltip_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'dotnav_tooltip_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'dotnav_tooltip_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dotnav_tooltip_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->end_controls_section();
	}

	public function render_dotnav_tooltip($settings) {

		// Tooltip settings
		$this->add_render_attribute( 'nav-link', 'class', 'bdt-tippy-tooltip', true );
		$this->add_render_attribute( 'nav-link', 'data-tippy', '', true );

		if ($settings['dotnav_tooltip_placement']) {
			$this->add_render_attribute( 'nav-link', 'data-tippy-placement', $settings['dotnav_tooltip_placement'], true );
		}

		if ($settings['dotnav_tooltip_animation']) {
			$this->add_render_attribute( 'nav-link', 'data-tippy-animation', $settings['dotnav_tooltip_animation'], true );
		}

		if ($settings['dotnav_tooltip_x_offset']['size'] or $settings['dotnav_tooltip_y_offset']['size']) {
			$this->add_render_attribute( 'nav-link', 'data-tippy-offset', '[' . $settings['dotnav_tooltip_x_offset']['size'] .','. $settings['dotnav_tooltip_y_offset']['size'] . ']', true );
		}

		if ('yes' == $settings['dotnav_tooltip_arrow']) {
			$this->add_render_attribute( 'nav-link', 'data-tippy-arrow', 'true', true );
		}else{
			$this->add_render_attribute( 'nav-link', 'data-tippy-arrow', 'false', true );
		}

		if ('yes' == $settings['dotnav_tooltip_trigger']) {
			$this->add_render_attribute( 'nav-link', 'data-tippy-trigger', 'click', true );
		}
	}

	public function render_loop_nav_list($list) {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute(
			[
				'nav-link' => [
					//'id'     => 'bdt-tooltip-' . $this->get_id(),
					'href'   => esc_attr($list['nav_link']['url']),
					'target' => $list['nav_link']['is_external'] ? '_blank' : '_self',
					'rel'    => $list['nav_link']['nofollow'] ? 'rel="nofollow"' : '',
					'data-tippy-content'  => ( 'dot' == $settings['nav_style'] ) ? esc_html($list["nav_title"]) : '',
				]
			], '', '', true
		);
		if ( 'dot' == $settings['nav_style'] ) {
			$this->render_dotnav_tooltip( $settings );
		}

		if ( ! isset( $list['nav_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$list['nav_icon'] = 'fas fa-home';
		}

		$migrated  = isset( $list['__fa4_migrated']['scroll_nav_icon'] );
		$is_new    = empty( $list['nav_icon'] ) && Icons_Manager::is_migration_allowed();

		?>
	    <li>
			<a <?php echo ( $this->get_render_attribute_string( 'nav-link' ) ); ?>>
				<?php echo esc_attr($list['nav_title']); ?>
				<?php if ($list['scroll_nav_icon']['value']) : ?>
					<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

						<?php if ( $is_new || $migrated ) :
							Icons_Manager::render_icon( $list['scroll_nav_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
						else : ?>
							<i class="<?php echo esc_attr( $list['nav_icon'] ); ?>" aria-hidden="true"></i>
						<?php endif; ?>

					</span>
				<?php endif; ?>
			</a>
		</li>
		<?php
	}

	public function render() {
		$settings          = $this->get_settings_for_display();

		if ( 'dot' !== $settings['nav_style'] ) :
			$this->add_render_attribute(
				[
					'nav-style' => [
						'class' => $settings['vertical_nav'] ? 'bdt-nav bdt-nav-default' : 'bdt-navbar-nav',
					]
				]
			);
		else :
			$this->add_render_attribute(
				[
					'nav-style' => [
						'class' => $settings['vertical_nav'] ? 'bdt-dotnav bdt-dotnav-vertical' : 'bdt-dotnav',
					]
				]
			);
		endif;

		$this->add_render_attribute( 'nav-style', 'data-bdt-scrollspy-nav', 'closest: li; scroll: true; offset: ' . $settings["content_offset"]["size"] . ';' );

		$this->add_render_attribute(
			[
				'scrollnav' => [
					'class' => [
						'bdt-scrollnav',
						'bdt-navbar-container',
						'bdt-navbar-transparent',
						'bdt-navbar',
						$settings['fixed_nav'] ? 'bdt-position-' . esc_attr($settings['nav_position']) . ' bdt-position-z-index' : '',
					],
					'data-bdt-navbar' => ''
				]
			]
		);

		?>
		<div <?php echo $this->get_render_attribute_string( 'scrollnav' ); ?>>
			<div class="bdt-navbar-<?php echo esc_attr($settings['alignment']); ?>">
				<ul <?php echo $this->get_render_attribute_string( 'nav-style' ); ?>>
					<?php
					foreach ($settings['navs'] as $key => $nav) :
						$this->render_loop_nav_list($nav);
					endforeach;
					?>
				</ul>
			</div>
		</div>
	    <?php
	}
}
