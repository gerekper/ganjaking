<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Builder Navigation widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_HB_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_hb_menu';
	}

	public function get_title() {
		return __( 'Navigation Menu', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-hb' );
	}

	public function get_keywords() {
		return array( 'menu', 'navigation', 'main menu', 'primary menu' );
	}

	public function get_icon() {
		return 'Simple-Line-Icons-link';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/porto-menu-element/';
	}

	protected function register_controls() {

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		$this->start_controls_section(
			'section_hb_menu',
			array(
				'label' => __( 'Menu', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'description_menu',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Options -> Menu%2$s.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'location',
				array(
					'type'    => Controls_Manager::SELECT,
					'label'   => __( 'Location', 'porto-functionality' ),
					'options' => array(
						'main-menu'        => __( 'Main Menu', 'porto-functionality' ),
						'secondary-menu'   => __( 'Secondary Menu', 'porto-functionality' ),
						'main-toggle-menu' => __( 'Main Toggle Menu', 'porto-functionality' ),
						'nav-top'          => __( 'Top Navigation', 'porto-functionality' ),
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'top_nav_font',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Top Level Typography', 'porto-functionality' ),
					'selector'  => '#header .elementor-element-{{ID}} .top-links > li.menu-item > a',
					'condition' => array(
						'location' => 'nav-top',
					),
				)
			);

			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'top_nav_font2',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Typography', 'porto-functionality' ),
					'selector'  => '.elementor-element-{{ID}} #main-toggle-menu .menu-title',
					'condition' => array(
						'location' => 'main-toggle-menu',
					),
				)
			);

			$this->add_control(
				'tg_icon_sz',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Size', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} #main-toggle-menu .toggle' => 'font-size: {{SIZE}}{{UNIT}};vertical-align: middle;',
					),
					'condition'  => array(
						'location' => 'main-toggle-menu',
					),
				)
			);

			$this->add_control(
				'between_spacing',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Between Spacing', 'porto-functionality' ),
					'range'       => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} #main-toggle-menu .menu-title .toggle' => "margin-{$right}: {{SIZE}}{{UNIT}};",
					),
					'condition'   => array(
						'location' => 'main-toggle-menu',
					),
					'qa_selector' => '#main-toggle-menu .menu-title .toggle',
				)
			);

			$this->add_control(
				'padding',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top Level Left/Right Padding', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} .top-links > li.menu-item > a' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'location' => 'nav-top',
					),
				)
			);

			$this->add_control(
				'padding2',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Left / Right Padding', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'location' => 'main-toggle-menu',
					),
				)
			);

			$this->add_control(
				'padding3',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Top / Bottom Padding', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'padding-top: {{SIZE}}{{UNIT}};padding-bottom: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'location' => 'main-toggle-menu',
					),
				)
			);

			$this->start_controls_tabs(
				'tabs_hb_menu_color',
				array(
					'condition' => array(
						'location' => array( 'main-toggle-menu', 'nav-top' ),
					),
				)
			);
				$this->start_controls_tab(
					'tab_hb_menu_color',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'toggle_menu_top_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'main-toggle-menu',
							),
						)
					);

					$this->add_control(
						'toggle_menu_top_color1',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Top Level Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links > li.menu-item > a' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'nav-top',
							),
						)
					);

					$this->add_control(
						'toggle_menu_top_bgcolor',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'main-toggle-menu',
							),
						)
					);
				$this->end_controls_tab();
				$this->start_controls_tab(
					'tab_hb_menu_hover_color',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'toggle_menu_top_hover_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} #main-toggle-menu .menu-title:hover' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'main-toggle-menu',
							),
						)
					);

					$this->add_control(
						'toggle_menu_top_hover_color1',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Top Level Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links > li.menu-item:hover > a' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'nav-top',
							),
						)
					);

					$this->add_control(
						'toggle_menu_top_hover_bgcolor',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors' => array(
								'.elementor-element-{{ID}} #main-toggle-menu .menu-title:hover' => 'background-color: {{VALUE}};',
							),
							'condition' => array(
								'location' => 'main-toggle-menu',
							),
						)
					);
				$this->end_controls_tab();
			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hb_menu_toggle_narrow',
			array(
				'label'     => __( 'Toggle Narrow', 'porto-functionality' ),
				'condition' => array(
					'location' => 'main-toggle-menu',
				),
			)
		);
			$this->add_control(
				'show_narrow',
				array(
					'type'      => Controls_Manager::SWITCHER,
					'label'     => __( 'Show Narrow', 'porto-functionality' ),
					'selectors' => array(
						'.elementor-element-{{ID}} .menu-title:after' => "content:'\\e81c';position:absolute;font-family:'porto';{$right}: 1.4rem;",
					),
				)
			);
			$this->add_control(
				'narrow_pos',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Position', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 60,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .menu-title:after' => "{$right}: {{SIZE}}{{UNIT}};",
					),
					'condition'  => array(
						'show_narrow' => 'yes',
					),
				)
			);
			$this->add_control(
				'narrow_sz',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Size', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 60,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} .menu-title:after' => 'font-size: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'show_narrow' => 'yes',
					),
				)
			);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_hb_menu_style_top',
			array(
				'label' => __( 'Top Level Menu', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'      => 'top_level_font',
					'scheme'    => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'     => __( 'Top Level Typography', 'porto-functionality' ),
					'selector'  => '#header .elementor-element-{{ID}} .main-menu > li.menu-item > a, #header .elementor-element-{{ID}} .menu-custom-block span, #header .elementor-element-{{ID}} .menu-custom-block a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item > a',
					'condition' => array(
						'location!' => 'nav-top',
					),
				)
			);

			$this->start_controls_tabs( 'tabs_top_level_color' );
				$this->start_controls_tab(
					'tab_top_level_color',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'top_level_link_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Link Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .main-menu > li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item > a' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location!' => 'nav-top',
							),
						)
					);
					$this->add_control(
						'top_level_link_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links > li.menu-item > a, #header .elementor-element-{{ID}} .main-menu > li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_top_level_hover_color',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'top_level_link_hover_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .main-menu > li.menu-item.active:hover > a, #header .elementor-element-{{ID}} .main-menu > li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active > a' => 'color: {{VALUE}};',
							),
							'condition' => array(
								'location!' => 'nav-top',
							),
						)
					);
					$this->add_control(
						'top_level_link_hover_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links > li.menu-item:hover > a, #header .elementor-element-{{ID}} .top-links > li.menu-item.has-sub:hover > a, #header .elementor-element-{{ID}} .main-menu > li.menu-item.active:hover > a, #header .elementor-element-{{ID}} .main-menu > li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'top_level_padding',
				array(
					'label'       => esc_html__( 'Menu Item Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'#header .elementor-element-{{ID}} .top-links > li.menu-item > a, #header .elementor-element-{{ID}} .main-menu > li.menu-item > a, #header .elementor-element-{{ID}} .menu-custom-block a, #header .elementor-element-{{ID}} .menu-custom-block span, .elementor-element-{{ID}} .sidebar-menu>li.menu-item>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'   => 'before',
					'qa_selector' => '.top-links > li:nth-child(2) > a, .main-menu > li:nth-child(2) > a',
				)
			);

			$this->add_responsive_control(
				'top_level_margin',
				array(
					'label'      => esc_html__( 'Menu Item Spacing', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} .top-links > li.menu-item, #header .elementor-element-{{ID}} .main-menu > li.menu-item, #header .elementor-element-{{ID}} .menu-custom-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array(
						'location!' => 'main-toggle-menu',
					),
				)
			);

			$this->add_control(
				'top_level_icon_sz',
				array(
					'type'        => Controls_Manager::SLIDER,
					'label'       => __( 'Icon Size', 'porto-functionality' ),
					'range'       => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'.elementor-element-{{ID}} li.menu-item>a>[class*=" fa-"]' => 'width: {{SIZE}}{{UNIT}};',
						'.elementor-element-{{ID}} li.menu-item>a>i' => 'font-size: {{SIZE}}{{UNIT}};vertical-align: middle;',
					),
					'qa_selector' => 'li.menu-item>a>i',
				)
			);

			$this->add_control(
				'top_level_icon_spacing',
				array(
					'type'       => Controls_Manager::SLIDER,
					'label'      => __( 'Icon Spacing', 'porto-functionality' ),
					'range'      => array(
						'px' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 72,
						),
						'em' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 5,
						),
					),
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'.elementor-element-{{ID}} li.menu-item>a>.avatar, .elementor-element-{{ID}} li.menu-item>a>i' => "margin-{$right}: {{SIZE}}{{UNIT}};",
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_hb_menu_style_submenu',
			array(
				'label' => __( 'Menu Popup', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'submenu_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Popup Typography', 'porto-functionality' ),
					'selector' => '#header .elementor-element-{{ID}} .main-menu .popup a, .elementor-element-{{ID}} .sidebar-menu .popup, .elementor-element-{{ID}} .porto-popup-menu .sub-menu, #header .elementor-element-{{ID}} .top-links .narrow li.menu-item>a',
				)
			);

			$this->start_controls_tabs( 'tabs_submenu_color' );
				$this->start_controls_tab(
					'tab_submenu_color',
					array(
						'label' => esc_html__( 'Normal', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'submenu_link_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Link Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links .narrow li.menu-item > a, #header .elementor-element-{{ID}} .main-menu .wide li.sub li.menu-item > a, #header .elementor-element-{{ID}} .main-menu .narrow li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu .wide li.menu-item li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu .wide li.sub li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu .narrow li.menu-item > a, .elementor-element-{{ID}} .porto-popup-menu .sub-menu a' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'submenu_link_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Background Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links .narrow ul.sub-menu, #header .elementor-element-{{ID}} .main-menu .wide .popup > .inner, #header .elementor-element-{{ID}} .main-menu .narrow ul.sub-menu, .elementor-element-{{ID}} .sidebar-menu .wide .popup > .inner, .elementor-element-{{ID}} .sidebar-menu .narrow ul.sub-menu, .elementor-element-{{ID}} .porto-popup-menu .sub-menu a' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

				$this->start_controls_tab(
					'tab_submenu_hover_color',
					array(
						'label' => esc_html__( 'Hover', 'porto-functionality' ),
					)
				);
					$this->add_control(
						'submenu_link_hover_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links .narrow li.menu-item:hover > a, #header .elementor-element-{{ID}} .main-menu .wide li.menu-item li.menu-item>a:hover, #header .elementor-element-{{ID}} .main-menu .narrow li.menu-item:hover > a, .elementor-element-{{ID}} .porto-popup-menu .sub-menu a:hover, .elementor-element-{{ID}} .sidebar-menu .narrow li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu .wide li.menu-item li.menu-item > a:hover' => 'color: {{VALUE}};',
							),
						)
					);
					$this->add_control(
						'submenu_link_hover_bg_color',
						array(
							'type'      => Controls_Manager::COLOR,
							'label'     => __( 'Hover Background Color', 'porto-functionality' ),
							'selectors' => array(
								'#header .elementor-element-{{ID}} .top-links .narrow li.menu-item:hover > a, #header .elementor-element-{{ID}} .sidebar-menu .narrow .menu-item:hover > a, #header .elementor-element-{{ID}} .main-menu .narrow li.menu-item:hover > a, #header .elementor-element-{{ID}} .main-menu .wide li.menu-item li.menu-item>a:hover, .elementor-element-{{ID}} .sidebar-menu .wide li.menu-item li.menu-item > a:hover, .elementor-element-{{ID}} .porto-popup-menu .sub-menu a:hover' => 'background-color: {{VALUE}};',
							),
						)
					);
				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'submenu_item_padding',
				array(
					'label'       => esc_html__( 'Item Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'#header .elementor-element-{{ID}} .narrow li.menu-item>a, .elementor-element-{{ID}} .wide li.sub li.menu-item>a, .elementor-element-{{ID}} .porto-popup-menu .sub-menu li.menu-item>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'separator'   => 'before',
					'qa_selector' => '.narrow li:first-child>a, .wide li.sub li:first-child>a, .porto-popup-menu .sub-menu li:first-child>a',
				)
			);

			$this->add_control(
				'submenu_padding',
				array(
					'label'       => esc_html__( 'SubMenu Padding', 'porto-functionality' ),
					'type'        => Controls_Manager::DIMENSIONS,
					'size_units'  => array(
						'px',
						'em',
					),
					'selectors'   => array(
						'#header .elementor-element-{{ID}} .narrow ul.sub-menu, .elementor-element-{{ID}} .wide .popup>.inner, .elementor-element-{{ID}} .porto-popup-menu .sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'qa_selector' => '.narrow ul.sub-menu, .wide .popup>.inner',
				)
			);

			$this->add_control(
				'submenu_narrow_border_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Item Border Color on Narrow Menu', 'porto-functionality' ),
					'selectors'   => array(
						'#header .elementor-element-{{ID}} .narrow li.menu-item>a' => 'border-bottom-color: {{VALUE}};',
					),
					'qa_selector' => '.narrow li:nth-child(2)>a',
				)
			);

			$this->add_control(
				'heading_wide_subheading',
				array(
					'label'     => esc_html__( 'Sub Heading on Mega Menu', 'porto-functionality' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'mega_title_color',
				array(
					'type'        => Controls_Manager::COLOR,
					'label'       => __( 'Color', 'porto-functionality' ),
					'selectors'   => array(
						'#header .elementor-element-{{ID}} .main-menu .wide li.sub > a, .elementor-element-{{ID}} .sidebar-menu .wide li.sub > a' => 'color: {{VALUE}};',
						'#header .elementor-element-{{ID}} .wide li.side-menu-sub-title > a' => 'color: {{VALUE}} !important;',
					),
					'qa_selector' => '.wide li.sub > a',
				)
			);
			$this->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => 'mega_title_font',
					'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
					'label'    => __( 'Typography', 'porto-functionality' ),
					'selector' => '#header .elementor-element-{{ID}} .wide li.side-menu-sub-title > a, #header .elementor-element-{{ID}} .main-menu .wide li.sub > a, .elementor-element-{{ID}} .sidebar-menu .wide li.sub > a',
				)
			);

			$this->add_control(
				'mega_title_padding',
				array(
					'label'      => esc_html__( 'Padding', 'porto-functionality' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array(
						'px',
						'em',
					),
					'selectors'  => array(
						'#header .elementor-element-{{ID}} .wide li.side-menu-sub-title > a, #header .elementor-element-{{ID}} .main-menu .wide li.sub > a, .elementor-element-{{ID}} .sidebar-menu .wide li.sub > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);
		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( function_exists( 'porto_header_elements' ) ) {
			porto_header_elements( array( (object) array( $settings['location'] => '' ) ) );
		}
	}
}
