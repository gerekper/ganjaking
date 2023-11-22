<?php
namespace ElementPack\Modules\Iconnav\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Repeater;

use ElementPack\Modules\Iconnav\ep_offcanvas_walker;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Iconnav extends Module_Base {
	public function get_name() {
		return 'bdt-iconnav';
	} 

	public function get_title() {
		return BDTEP . esc_html__( 'Icon Nav', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-iconnav';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'iconnav', 'navigation', 'menu' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-font', 'ep-iconnav', 'tippy' ];
        }
    }

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return [ 'popper', 'tippyjs', 'ep-scripts' ];
        } else {
			return [ 'popper', 'tippyjs', 'ep-iconnav' ];
        }
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/Q4YY8pf--ig';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_iconnav',
			[
				'label' => esc_html__( 'Iconnav', 'bdthemes-element-pack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'iconnav_icon', 
			[
				'label'   => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default' => [
					'value' => 'fas fa-home',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'iconnav_title', 
			[
				'label'   => esc_html__( 'Iconnav Title', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Iconnav Title' , 'bdthemes-element-pack' ),
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'iconnav_link', 
			[
				'label'       => esc_html__( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'default'     => [ 'url' => '#' ],
				'description' => 'Add your section id WITH the # key. e.g: #my-id also you can add internal/external URL',
				'dynamic'     => [ 'active' => true ],
			]
		);

		$this->add_control(
			'iconnavs',
			[
				'label'   => esc_html__( 'Iconnav Items', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'iconnav_title' => esc_html__( 'Homepage', 'bdthemes-element-pack' ),
						'iconnav_icon'  => ['value' => 'fas fa-home', 'library' => 'fa-solid'],
						'iconnav_link'  => [
							'url' => '#',
						] 
					],
					[
						'iconnav_title' => esc_html__( 'Product', 'bdthemes-element-pack' ),
						'iconnav_icon'  => ['value' => 'fas fa-shopping-bag', 'library' => 'fa-solid'],
						'iconnav_link'  => [
							'url' => '#',
						]
					],
					[
						'iconnav_title' => esc_html__( 'Support', 'bdthemes-element-pack' ),
						'iconnav_icon'  => ['value' => 'fas fa-wrench', 'library' => 'fa-solid'],
						'iconnav_link'  => [
							'url' => '#',
						]
					],
					[
						'iconnav_title' => esc_html__( 'Blog', 'bdthemes-element-pack' ),
						'iconnav_icon'  => ['value' => 'fas fa-book', 'library' => 'fa-solid'],
						'iconnav_link'  => [
							'url' => '#',
						]
					],
					[
						'iconnav_title' => esc_html__( 'About Us', 'bdthemes-element-pack' ),
						'iconnav_icon'  => ['value' => 'fas fa-envelope', 'library' => 'fa-solid'],
						'iconnav_link'  => [
							'url' => '#',
						]
					],
				],
				'title_field' => '{{{ iconnav_title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_offcanvas_layout',
			[
				'label' => esc_html__( 'Offcanvas Menu', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navbar',
			[
				'label'       => esc_html__( 'Select Menu', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'Child menu not visible in off-canvas for some design issue.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 0,
				'options'     => element_pack_get_menu(),
			]
		);

		$this->add_control(
			'navbar_level',
			[
				'label'       => esc_html__( 'Max Menu Level', 'bdthemes-element-pack' ),
				'description' => esc_html__( 'You can set max 3 level menu because of design issue.', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 1,
				'options'     => [
					1  => esc_html__( 'Level 1', 'bdthemes-element-pack' ),
					2  => esc_html__( 'Level 2', 'bdthemes-element-pack' ),
					3  => esc_html__( 'Level 3', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'offcanvas_overlay',
			[
				'label'        => esc_html__( 'Overlay', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'condition'    => [
					'navbar!' => '0',
				],
			]
		);

		$this->add_control(
			'offcanvas_animations',
			[
				'label'     => esc_html__( 'Animations', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => [
					'slide'  => esc_html__( 'Slide', 'bdthemes-element-pack' ),
					'push'   => esc_html__( 'Push', 'bdthemes-element-pack' ),
					'reveal' => esc_html__( 'Reveal', 'bdthemes-element-pack' ),
					'none'   => esc_html__( 'None', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'navbar!' => '0',
				],
			]
		);

		$this->add_control(
			'offcanvas_flip',
			[
				'label'        => esc_html__( 'Flip', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'condition'    => [
					'navbar!' => '0',
				],
			]
		);

		$this->add_control(
			'offcanvas_close_button',
			[
				'label'     => esc_html__( 'Close Button', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navbar!' => '0',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_branding',
			[
				'label' => esc_html__( 'Branding', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'show_branding',
			[
				'label'   => __( 'Show Branding', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'branding_image',
			[
				'label'     => __( 'Choose Branding Image', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::MEDIA,
				'condition' => [
					'show_branding' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'brading_space',
			[
				'label'   => __( 'Space', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding'     => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additional_settings',
			[
				'label' => esc_html__( 'Additional Settings', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'menu_text',
			[
				'label'   => esc_html__('Menu Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'show_as_tooltip',
				'options' => [
					'show_as_tooltip' => esc_html__('Show as Tooltip', 'bdthemes-element-pack'),
					'show_under_icon' => esc_html__('Show Under Icon', 'bdthemes-element-pack'),
				]
			]
		);

		$this->add_responsive_control(
			'iconnav_width',
			[
				'label' => esc_html__( 'Iconnav Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 48,
						'max'  => 120,
						'step' => 2,
					],
				],
				'default' => [
					'size' => 48,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container'     => 'width: {{SIZE}}{{UNIT}};',
					'body:not(.bdt-offcanvas-flip) #bdt-offcanvas{{ID}}.bdt-offcanvas.bdt-icon-nav-left' => is_rtl() ? 'right: {{SIZE}}{{UNIT}};' : 'left: {{SIZE}}{{UNIT}};',
					'body.bdt-offcanvas-flip #bdt-offcanvas{{ID}}.bdt-offcanvas.bdt-icon-nav-right' => is_rtl() ? 'left: {{SIZE}}{{UNIT}};' : 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'iconnav_position',
			[
				'label'   => esc_html__( 'Iconnav Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left'  => esc_html__( 'Left', 'bdthemes-element-pack' ),
					'right' => esc_html__( 'Right', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_responsive_control(
			'iconnav_top_offset',
			[
				'label'   => __( 'Top Offset', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 80,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container'     => 'padding-top: {{SIZE}}{{UNIT}};',
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'padding-top: calc({{SIZE}}{{UNIT}} + {{brading_space.SIZE}}px + 50px);',
				],
			]
		);

		$this->add_responsive_control(
			'iconnav_gap',
			[
				'label' => __( 'Icon Gap', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav-container ul.bdt-icon-nav.bdt-icon-nav-vertical li + li'     => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'iconnav_tooltip_spacing',
			[
				'label'   => __( 'Tooltip Spacing', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'separator' => 'before',
				'condition' => [
					'menu_text' => 'show_as_tooltip',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_iconnav',
			[
				'label' => esc_html__( 'Iconnav', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'iconnav_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'iconnav_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container',
			]
		);

		$this->add_responsive_control(
			'iconnav_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'iconnav_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-container',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_iconnav_icon',
			[
				'label' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
	
		$this->start_controls_tabs( 'tabs_iconnav_icon_style' );

		$this->start_controls_tab(
			'tab_iconnav_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'iconnav_icon_size',
			[
				'label' => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'max'  => 48,
					],
				],
				'default' => [
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper .bdt-icon-nav-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'iconnav_icon_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper .bdt-icon-nav-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper .bdt-icon-nav-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'iconnav_icon_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'iconnav_icon_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper',
			]
		);

		$this->add_responsive_control(
			'iconnav_icon_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'iconnav_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper',
			]
		);

		$this->add_responsive_control(
			'iconnav_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'iconnav_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'iconnav_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'iconnav_icon_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:hover .bdt-icon-nav-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:hover .bdt-icon-nav-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'iconnav_icon_hover_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'iconnav_icon_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'iconnav_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'iconnav_icon_active',
			[
				'label' => esc_html__( 'Active', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'iconnav_icon_active_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:focus' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'iconnav_icon_active_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'iconnav_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-icon-wrapper:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label'     => esc_html__( 'Tooltip', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_text' => 'show_as_tooltip',
				],
			]
		);

		$this->add_control(
			'tooltip_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tooltip_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'tooltip_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_responsive_control(
			'tooltip_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
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
				'name'     => 'tooltip_shadow',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tooltip_typography',
				'selector' => '.tippy-box[data-theme="bdt-tippy-{{ID}}"]',
			]
		);

		$this->add_control(
			'tooltip_arrow_color',
			[
				'label'     => esc_html__( 'Arrow Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'  => 'border-left-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow' => 'border-right-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'   => 'border-top-color: {{VALUE}}',
					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'border-bottom-color: {{VALUE}}',

					'.tippy-box[data-theme="bdt-tippy-{{ID}}"] .tippy-arrow'=> 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tooltip_animation',
			[
				'label'   => esc_html__( 'Tooltip Animation', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''             => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'shift-toward' => esc_html__( 'Shift Toward', 'bdthemes-element-pack' ),
					'fade'         => esc_html__( 'Fade', 'bdthemes-element-pack' ),
					'scale'        => esc_html__( 'Scale', 'bdthemes-element-pack' ),
					'perspective'  => esc_html__( 'Perspective', 'bdthemes-element-pack' ),
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tooltip_size',
			[
				'label'   => esc_html__( 'Tooltip Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					''      => esc_html__( 'Default', 'bdthemes-element-pack' ),
					'large' => esc_html__( 'Large', 'bdthemes-element-pack' ),
					'small' => esc_html__( 'small', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_menu_text',
			[
				'label'     => esc_html__( 'Menu Text', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'menu_text!' => 'show_as_tooltip',
				],
			]
		);

		$this->add_control(
			'menu_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-menu-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_text_spacing',
			[
				'label'     => esc_html__( 'Space', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-menu-text' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_text_typography',
				'selector' => '{{WRAPPER}} .bdt-icon-nav .bdt-menu-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_offcanvas_content',
			[
				'label'     => esc_html__( 'Offcanvas', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navbar!' => '0',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_color',
			[
				'label'     => esc_html__( 'Link Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar ul > li a'   => 'color: {{VALUE}};',
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar a *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_hover_color',
			[
				'label'     => esc_html__( 'Link Hover Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar ul > li a:hover' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_link_active_color',
			[
				'label'     => esc_html__( 'Link Active Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar ul > li.bdt-active a:before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'offcanvas_content_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'background-color: {{VALUE}} !important;',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'offcanvas_content_box_shadow',
				'selector'  => '#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar',
			]
		);

		$this->add_responsive_control(
			'offcanvas_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'#bdt-offcanvas{{ID}}.bdt-offcanvas .bdt-offcanvas-bar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_branding',
			[
				'label'     => esc_html__( 'Branding', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_branding' => 'yes',
				],
			]
		);

		$this->add_control(
			'branding_background',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'branding_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'branding_border',
				'label'       => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding',
			]
		);

		$this->add_responsive_control(
			'branding_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'branding_shadow',
				'selector' => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'branding_typography',
				'selector' => '{{WRAPPER}} .bdt-icon-nav .bdt-icon-nav-branding',
			]
		);

		$this->end_controls_section();

	}

	public function render_loop_iconnav_list($list) {
		$settings      = $this->get_settings_for_display();
		
		$scroll_active = (preg_match("/(#\s*([a-z]+)\s*)/", $list['iconnav_link']['url'])) ? 'bdt-scroll' : '';

		$this->add_render_attribute( 'iconnav-item-link', 'class', 'bdt-icon-nav-icon-wrapper bdt-flex-middle bdt-flex-center', true );
		$this->add_render_attribute( 'iconnav-item-link', 'href', $list['iconnav_link']['url'], true );
		
		if ( $list['iconnav_link']['is_external'] ) {
			$this->add_render_attribute( 'iconnav-item-link', 'target', '_blank', true );
		}

		if ( $list['iconnav_link']['nofollow'] ) {
			$this->add_render_attribute( 'iconnav-item-link', 'rel', 'nofollow', true );
		}

		$this->add_render_attribute( 'iconnav-item', 'class', 'bdt-icon-nav-item' );

		// Tooltip settings
		if ( 'show_as_tooltip' == $settings['menu_text'] ) {
			$this->add_render_attribute( 'iconnav-item', 'class', 'bdt-tippy-tooltip', true );
			$this->add_render_attribute( 'iconnav-item', 'data-tippy', '', true );
			$this->add_render_attribute( 'iconnav-item', 'data-tippy-content', $list["iconnav_title"], true );
			if ($settings['tooltip_animation']) {
				$this->add_render_attribute( 'iconnav-item', 'data-tippy-animation', $settings['tooltip_animation'], true );
			}
			if ($settings['tooltip_size']) {
				$this->add_render_attribute( 'iconnav-item', 'data-tippy-size', $settings['tooltip_size'], true );
			}
			if ($settings['iconnav_tooltip_spacing']['size']) {
				$this->add_render_attribute( 'iconnav-item', 'data-tippy-distance', $settings['iconnav_tooltip_spacing']['size'], true );
			}
			$this->add_render_attribute( 'iconnav-item', 'data-tippy-placement', 'left', true );
		} else {
			$this->add_render_attribute( 'iconnav-item-link', 'title', $list["iconnav_title"], true );
		}

		if ( ! isset( $settings['icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['icon'] = 'fas fa-home';
		}		

		$migrated  = isset( $list['__fa4_migrated']['iconnav_icon'] );
		$is_new    = empty( $list['icon'] ) && Icons_Manager::is_migration_allowed();

		?>
	    <li <?php echo $this->get_render_attribute_string( 'iconnav-item' ); ?>>
			<a <?php echo $this->get_render_attribute_string( 'iconnav-item-link' ); ?> <?php echo esc_url($scroll_active); ?>>

				<?php if ($list['iconnav_icon']['value']) : ?>
					<span class="bdt-icon-nav-icon">
						
						<?php if ( $is_new || $migrated ) :
							Icons_Manager::render_icon( $list['iconnav_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
						else : ?>
							<i class="<?php echo esc_attr( $list['icon'] ); ?>" aria-hidden="true"></i>
						<?php endif; ?>

					</span>
				<?php endif; ?>
				
				<?php if ('show_under_icon' == $settings['menu_text']) : ?>
					<span class="bdt-menu-text bdt-display-block bdt-text-small"><?php echo esc_attr($list["iconnav_title"]); ?></span>
				<?php endif; ?>
			</a>
		</li>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$id       = $this->get_id();

		$this->add_render_attribute( 'icon-nav', 'class', 'bdt-icon-nav' );
		$this->add_render_attribute( 'icon-nav', 'id', 'bdt-icon-nav-' . $id );

		$this->add_render_attribute( 'nav-container', 'class', ['bdt-icon-nav-container', 'bdt-icon-nav-' . $settings['iconnav_position']] );
		
		?>
		<div <?php echo $this->get_render_attribute_string( 'icon-nav' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'nav-container' ); ?>>
				<div class="bdt-icon-nav-branding">
					<?php if ( $settings['show_branding']) : ?>
						<?php if ( ! empty( $settings['branding_image']['url'] ) ) : ?>
							<div class="bdt-logo-image"><img src="<?php echo esc_url( $settings['branding_image']['url'] ); ?>" alt="<?php echo get_bloginfo( 'name' ); ?>"></div>
						<?php else : ?>
							<?php
								$string          = get_bloginfo( 'name' );
								$words           = explode(" ", $string);
								$letters         = "";
								foreach ($words as $value) {
									$letters .= substr($value, 0, 1);
								}
							?>
							<div><div class="bdt-logo-txt">
								<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"><?php echo esc_attr( $letters ); ?></a></div></div>
						<?php endif; ?>
					<?php endif; ?>

				</div>
				<ul class="bdt-icon-nav bdt-icon-nav-vertical">
					<?php if ( $settings['navbar'] ) : ?>
						<li>
							<a class="bdt-icon-nav-icon-wrapper" href="#" data-bdt-toggle="target: #bdt-offcanvas<?php echo esc_attr($id); ?>">
								<span class="bdt-icon-nav-icon">
									<i class="ep-icon-menu"></i>
								</span>
							</a>
						</li>
					<?php endif; ?>

					<?php
					foreach ($settings['iconnavs'] as $key => $nav) : 
						$this->render_loop_iconnav_list($nav);
					endforeach;
					?>
				</ul>
			</div>
		</div>

	   <?php if ( $settings['navbar'] ) : ?>
		    <?php $this->offcanvas($settings); ?>
		<?php endif;
	}

	private function offcanvas($settings) {
		$id = $this->get_id();

		$this->add_render_attribute(
			[
				'offcanvas-settings' => [
					'id'            => 'bdt-offcanvas' . $id,
					'class'         => [
						'bdt-offcanvas',
						'bdt-icon-nav-offcanvas',
						'bdt-icon-nav-' . $settings['iconnav_position']
					],
				]
			]
		);

		$this->add_render_attribute( 'offcanvas-settings', 'data-bdt-offcanvas', 'mode: ' . $settings['offcanvas_animations'] . ';' );

		if ($settings['offcanvas_overlay']) {
			$this->add_render_attribute( 'offcanvas-settings', 'data-bdt-offcanvas', 'overlay: true;' );
		}

		if ($settings['offcanvas_flip']) {
			$this->add_render_attribute( 'offcanvas-settings', 'data-bdt-offcanvas', 'flip: true;' );
		}

		$nav_menu    = ! empty( $settings['navbar'] ) ? wp_get_nav_menu_object( $settings['navbar'] ) : false;
		$navbar_attr = [];
	    if ( ! $nav_menu ) {
	    	return;
	    }

	    if (1 < $settings['navbar_level']) {
	    	$nav_class = 'bdt-nav bdt-nav-default bdt-nav-parent-icon';
	    } else {
	    	$nav_class = 'bdt-nav bdt-nav-default';
	    }

	    $nav_menu_args = array(
	    	'fallback_cb'    => false,
	    	'container'      => false,
	    	'items_wrap'     => '<ul id="%1$s" class="%2$s" data-bdt-nav>%3$s</ul>',
	    	'menu_id'        => 'bdt-navmenu',
	    	'menu_class'     => $nav_class,
	    	'theme_location' => 'default_navmenu', // creating a fake location for better functional control
	    	'menu'           => $nav_menu,
	    	'echo'           => true,
	    	'depth'          => $settings['navbar_level'],
	    	'walker'         => new ep_offcanvas_walker
	    );

		?>		
	    <div <?php echo $this->get_render_attribute_string( 'offcanvas-settings' ); ?>>
	        <div class="bdt-offcanvas-bar">
				
				<?php if ($settings['offcanvas_close_button']) : ?>
	        		<button class="bdt-offcanvas-close" type="button" data-bdt-close></button>
	        	<?php endif; ?>
				
				<div id="bdt-navbar-<?php echo esc_attr($id); ?>" class="bdt-navbar-wrapper">
					<?php wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings ) ); ?>
				</div>
	        </div>
	    </div>
		<?php
	}

	protected function content_template_delete() {
		$id = $this->get_id();
		?>

		<#
		view.addRenderAttribute( 'icon-nav', 'class', 'bdt-icon-nav' );
		view.addRenderAttribute( 'nav-container', 'class', ['bdt-icon-nav-container', 'bdt-icon-nav-' + settings.iconnav_position] );

		var iconHTML = {},
			migrated = {};
		
		#>
		<div <# print(view.getRenderAttributeString( 'icon-nav')); #>>
			<div <# print(view.getRenderAttributeString( 'nav-container')); #>>
				<div class="bdt-icon-nav-branding">
					<# if ( settings.show_branding) { #>
						<# if ( settings.branding_image.url ) { #>
							<div class="bdt-logo-image"><img src="{{{settings.branding_image.url}}}"></div>
						<# } else { #>
							<#
								var letters = 'EP';
							#>
							<div><div class="bdt-logo-txt">
								<a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">{{{letters}}}</a></div></div>
						<# } #>
					<# } #>

				</div>
				<ul class="bdt-icon-nav bdt-icon-nav-vertical">
					<# if ( 0 != settings.navbar ) { #>
						<li>
							<a class="bdt-icon-nav-icon-wrapper" href="#" data-bdt-toggle="target: #bdt-offcanvas<?php echo esc_attr($id); ?>">
								<span class="bdt-icon-nav-icon">
									<i class="ep-icon-menu"></i>
								</span>
							</a>
						</li>
					<# } #>

					<# _.each( settings.iconnavs, function( item, index ) { 

					view.addRenderAttribute( 'iconnav-item-link', 'class', 'bdt-icon-nav-icon-wrapper bdt-flex-middle bdt-flex-center', true );
					view.addRenderAttribute( 'iconnav-item-link', 'href', item.iconnav_link.url, true );
					
					if ( item.iconnav_link.is_external ) {
						view.addRenderAttribute( 'iconnav-item-link', 'target', '_blank', true );
					}

					if ( item.iconnav_link.nofollow ) {
						view.addRenderAttribute( 'iconnav-item-link', 'rel', 'nofollow', true );
					}

					view.addRenderAttribute( 'iconnav-item', 'class', 'bdt-icon-nav-item' );

					if ( 'show_as_tooltip' == settings.menu_text ) {
						view.addRenderAttribute( 'iconnav-item', 'class', 'bdt-tippy-tooltip', true );
						view.addRenderAttribute( 'iconnav-item', 'data-tippy', '', true );
						view.addRenderAttribute( 'iconnav-item', 'title', item.iconnav_title, true );

						if (settings.tooltip_animation) {
							view.addRenderAttribute( 'iconnav-item', 'data-tippy-animation', settings.tooltip_animation, true );
						}
						if (settings.tooltip_size) {
							view.addRenderAttribute( 'iconnav-item', 'data-tippy-size', settings.tooltip_size, true );
						}
						if (settings.iconnav_tooltip_spacing.size) {
							view.addRenderAttribute( 'iconnav-item', 'data-tippy-distance', settings.iconnav_tooltip_spacing.size, true );
						}
						view.addRenderAttribute( 'iconnav-item', 'data-tippy-placement', 'left', true );
					} else {
						view.addRenderAttribute( 'iconnav-item-link', 'title', item.iconnav_title, true );
					}		

					iconHTML[ index ] = elementor.helpers.renderIcon( view, item.iconnav_icon, { 'aria-hidden': true }, 'i' , 'object' );

					migrated[ index ] = elementor.helpers.isIconMigrated( item, 'iconnav_icon' );

					#>
				    <li <# print(view.getRenderAttributeString( 'iconnav-item' )); #>>
						<a <# print(view.getRenderAttributeString( 'iconnav-item-link' )); #>>
							<# if (item.iconnav_icon.value) { #>
								<span class="bdt-icon-nav-icon">
									
									<# if ( iconHTML[ index ] && iconHTML[ index ].rendered && ( ! item.icon || migrated[ index ] ) ) { #>
										{{{ iconHTML[ index ].value }}}
									<# } else { #>
										<i class="{{ item.icon }}" aria-hidden="true"></i>
									<# } #>

								</span>
							<# } #>
							
							<# if ('show_under_icon' == settings.menu_text) { #>
								<span class="bdt-menu-text bdt-display-block bdt-text-small">{{{item.iconnav_title}}}</span>
							<# } #>
						</a>
					</li>
				<# }); #>

				</ul>
			</div>
		</div>

	   <?php
	}
}
