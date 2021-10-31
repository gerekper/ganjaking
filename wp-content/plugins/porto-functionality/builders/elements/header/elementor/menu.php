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

	protected function _register_controls() {

		$this->start_controls_section(
			'section_hb_menu',
			array(
				'label' => __( 'Menu', 'porto-functionality' ),
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
				'label'     => __( 'Top Level Typograhy', 'porto-functionality' ),
				'selector'  => '#header .top-links > li.menu-item > a',
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
				'label'     => __( 'Top Level Typograhy', 'porto-functionality' ),
				'selector'  => '.elementor-element-{{ID}} #main-toggle-menu .menu-title',
				'condition' => array(
					'location' => 'main-toggle-menu',
				),
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
					'#header .top-links > li.menu-item > a' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
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
					'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'padding-left: {{SIZE}}{{UNIT}};padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition'  => array(
					'location' => 'main-toggle-menu',
				),
			)
		);

		$this->add_control(
			'toggle_menu_top_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Top Level Color', 'porto-functionality' ),
				'default'   => '',
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
				'default'   => '',
				'selectors' => array(
					'#header .top-links > li.menu-item > a' => 'color: {{VALUE}};',
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
				'label'     => __( 'Top Level Background Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} #main-toggle-menu .menu-title' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'location' => 'main-toggle-menu',
				),
			)
		);

		$this->add_control(
			'toggle_menu_top_hover_color',
			array(
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Top Level Hover Color', 'porto-functionality' ),
				'default'   => '',
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
				'default'   => '',
				'selectors' => array(
					'#header .top-links > li.menu-item:hover > a' => 'color: {{VALUE}};',
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
				'label'     => __( 'Top Level Hover Background Color', 'porto-functionality' ),
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} #main-toggle-menu .menu-title:hover' => 'background-color: {{VALUE}};',
				),
				'condition' => array(
					'location' => 'main-toggle-menu',
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
