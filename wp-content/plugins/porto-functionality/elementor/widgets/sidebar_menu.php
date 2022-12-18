<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Side Menu Widget
 *
 * Porto Elementor widget to display a side menu.
 *
 * @since 1.7.1
 */

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

class Porto_Elementor_Sidebar_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sidebar_menu';
	}

	public function get_title() {
		return __( 'Porto Side Menu', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'sidebar', 'menu', 'navigation', 'vertical' );
	}

	public function get_icon() {
		return 'eicon-navigation-vertical';
	}

	protected function register_controls() {

		$left  = is_rtl() ? 'right' : 'left';
		$right = is_rtl() ? 'left' : 'right';

		$custom_menus = array();
		$menus        = get_terms(
			array(
				'taxonomy'   => 'nav_menu',
				'hide_empty' => false,
			)
		);
		if ( is_array( $menus ) && ! empty( $menus ) ) {
			foreach ( $menus as $single_menu ) {
				if ( is_object( $single_menu ) && isset( $single_menu->name, $single_menu->term_id ) ) {
					$custom_menus[ $single_menu->term_id ] = $single_menu->name;
				}
			}
		}

		$this->start_controls_section(
			'section_sidebar_menu',
			array(
				'label' => __( 'Side Menu', 'porto-functionality' ),
			)
		);

			$this->add_control(
				'description_sidebar_menu',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Option > Menu%2$s.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'description_sidebar_menu_skin',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => sprintf( esc_html__( 'Please see %1$sTheme Option > Skin > Main Menu%2$s panel.', 'porto-functionality' ), '<b>', '</b>' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'title',
				array(
					'label' => __( 'Title', 'porto-functionality' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

			$this->add_control(
				'nav_menu',
				array(
					'label'       => __( 'Menu', 'porto-functionality' ),
					'type'        => Controls_Manager::SELECT,
					'options'     => $custom_menus,
					/* translators: opening and closing bold tags */
					'description' => empty( $custom_menus ) ? sprintf( esc_html__( 'Custom menus not found. Please visit %1$sAppearance > Menus%2$s page to create new menu.', 'porto-functionality' ), '<b>', '</b>' ) : esc_html__( 'Select menu to display.', 'porto-functionality' ),
				)
			);

			$this->add_control(
				'el_class',
				array(
					'label' => __( 'Custom CSS Class', 'porto-functionality' ),
					'type'  => Controls_Manager::TEXT,
				)
			);

		$this->end_controls_section();

		// style options
		$this->start_controls_section(
			'sidebar_menu_style',
			array(
				'label' => __( 'Menu', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'menu_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .widget_sidebar_menu' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'menu_bd',
				'selector' => '.elementor-element-{{ID}} .widget_sidebar_menu',
			)
		);

		$this->add_control(
			'icon_fs',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Icon Font Size', 'porto-functionality' ),
				'range'       => array(
					'px' => array(
						'step' => 1,
						'min'  => 1,
						'max'  => 50,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'rem',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sidebar-menu li.menu-item > a > i' => 'font-size: {{SIZE}}{{UNIT}};',
					'.elementor-element-{{ID}} .sidebar-menu li.menu-item > a' => 'display: flex; align-items: center;',
				),
				'qa_selector' => '.sidebar-menu li.menu-item > a > i',
			)
		);

		$this->add_control(
			'icon_space',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Icon Space', 'porto-functionality' ),
				'range'      => array(
					'px' => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} .sidebar-menu li.menu-item > a > i' => "margin-{$right}: {{SIZE}}{{UNIT}};",
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sidebar_menu_item_style',
			array(
				'label' => __( 'Top Level Menu Item', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'item_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .sidebar-menu > li.menu-item > a',
			)
		);

		$this->add_control(
			'item_mg',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Left & Right Spacing', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'em',
					'rem',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > a' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.sidebar-menu > li.menu-item:first-child > a',
			)
		);

		$this->add_responsive_control(
			'item_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'.elementor-element-{{ID}} .sidebar-menu .popup:before' => 'top: calc( {{TOP}}{{UNIT}} / 2 + {{BOTTOM}}{{UNIT}} / 2 - 0.5px );',
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > .arrow' => 'margin: 0; top: calc( {{TOP}}{{UNIT}} / 2 + {{BOTTOM}}{{UNIT}} / 2 - 6px );',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_control(
			'arrow_rp',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Arrow right position', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'em',
					'rem',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > .arrow' => ( is_rtl() ? 'left' : 'right' ) . ': {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.sidebar-menu > li.menu-item > .arrow',
			)
		);

		$this->add_control(
			'item_clr',
			array(
				'label'     => __( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_bc',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover + li.menu-item > a' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_clr',
			array(
				'label'     => __( 'Arrow Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item > .arrow:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_bgc',
			array(
				'label'     => __( 'Hover Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.open, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_clr',
			array(
				'label'     => __( 'Hover Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.open > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'item_hover_bc',
			array(
				'label'     => __( 'Hover Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.open > a, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active > a' => 'border-top-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_hover_clr',
			array(
				'label'     => __( 'Hover Arrow Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu > li.menu-item:hover > .arrow:before, .elementor-element-{{ID}} .sidebar-menu > li.menu-item.active > .arrow:before' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sidebar_sub_style',
			array(
				'label' => __( 'Sub Menu', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .sidebar-menu .popup',
			)
		);

		$this->add_control(
			'sub_clr',
			array(
				'label'     => __( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu .popup' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_bgc',
			array(
				'label'       => __( 'Background Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .wide .popup > .inner, .elementor-element-{{ID}} .narrow ul.sub-menu' => 'background-color: {{VALUE}};',
					'.elementor-element-{{ID}} .popup:before' => 'border-' . ( is_rtl() ? 'left' : 'right' ) . '-color: {{VALUE}};',
				),
				'qa_selector' => '.sidebar-menu>li.has-sub>.popup',
			)
		);

		$this->add_responsive_control(
			'sub_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .wide .popup > .inner, .elementor-element-{{ID}} .narrow ul.sub-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_responsive_control(
			'sub_sub_lpd',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Left padding in third level menu', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'em',
					'rem',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} .narrow ul.sub-menu ul.sub-menu' => 'padding-' . ( is_rtl() ? 'right' : 'left' ) . ': {{SIZE}}{{UNIT}};',
				),
				'qa_selector' => '.narrow .inner>.sub-menu>li.menu-item-has-children>.sub-menu',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'sub_label_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Label Typography in wide sub menu', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .wide li.sub > a',
			)
		);

		$this->add_control(
			'sub_label_clr',
			array(
				'label'       => __( 'Label Color in wide sub menu', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .wide li.sub > a' => 'color: {{VALUE}};',
				),
				'qa_selector' => '.wide li.sub > a',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'sidebar_subitem_style',
			array(
				'label' => __( 'Sub Menu Item', 'porto-functionality' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'subitem_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .wide li.sub li.menu-item > a, .elementor-element-{{ID}} .narrow li.menu-item > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_control(
			'subitem_clr',
			array(
				'label'       => __( 'Color', 'porto-functionality' ),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => array(
					'.elementor-element-{{ID}} .sidebar-menu .narrow li.menu-item > a, .elementor-element-{{ID}} .wide li.sub li.menu-item > a' => 'color: {{VALUE}};',
				),
				'qa_selector' => '.wide .inner>.sub-menu .sub-menu li.menu-item:first-child > a, .narrow .inner>.sub-menu>li:first-child > a',
			)
		);

		$this->start_controls_tabs( 'subitem_style' );
		$this->start_controls_tab(
			'subitem_narrow',
			array(
				'label' => __( 'Narrow Menu Item', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'subitem_narrow_bc',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .narrow li.menu-item > a' => 'border-bottom-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subitem_hover_clr',
			array(
				'label'     => __( 'Color on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu .narrow li.menu-item:hover > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subitem_hover_bgc',
			array(
				'label'     => __( 'Background on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .sidebar-menu .narrow li.menu-item:hover > a' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'subitem_hover_td',
			array(
				'label'     => __( 'Text decoration on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''             => __( 'Default', 'porto-functionality' ),
					'none'         => 'none',
					'underline'    => 'underline',
					'overline'     => 'overline',
					'line-through' => 'line-through',
					'blink'        => 'blink',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .narrow li.menu-item > a:hover' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'subitem_wide',
			array(
				'label' => __( 'Wide Menu Item', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'sub_wide_item_hover_clr',
			array(
				'label'     => __( 'Color on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .wide li.sub li.menu-item:hover > a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'sub_wide_item_hover_bgc',
			array(
				'label'     => __( 'Background on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .wide li.menu-item:hover > a' => 'background-color: {{VALUE}}; text-decoration: none;',
				),
			)
		);

		$this->add_control(
			'sub_wide_item_hover_td',
			array(
				'label'     => __( 'Text decoration on hover', 'porto-functionality' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					''             => __( 'Default', 'porto-functionality' ),
					'none'         => 'none',
					'underline'    => 'underline',
					'overline'     => 'overline',
					'line-through' => 'line-through',
					'blink'        => 'blink',
				),
				'selectors' => array(
					'.elementor-element-{{ID}} .wide li.menu-item > a:hover' => 'text-decoration: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'sidebar_tip_style',
			array(
				'label'       => __( 'Tip', 'porto-functionality' ),
				'tab'         => Controls_Manager::TAB_STYLE,
				'qa_selector' => '.tip',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tip_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .tip',
			)
		);

		$this->add_control(
			'tip_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} .tip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_control(
			'tip_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .tip' => 'background-color: {{VALUE}};',
					'.elementor-element-{{ID}} .tip' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'tip_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} .tip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_sidebar_menu' ) ) {
			include $template;
		}
	}
}
