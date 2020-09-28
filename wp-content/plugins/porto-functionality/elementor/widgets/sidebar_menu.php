<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Elementor Sidebar Menu Widget
 *
 * Porto Elementor widget to display a sidebar menu.
 *
 * @since 5.4.0
 */

use Elementor\Controls_Manager;

class Porto_Elementor_Sidebar_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_sidebar_menu';
	}

	public function get_title() {
		return __( 'Sidebar Menu', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'theme-elements' );
	}

	public function get_keywords() {
		return array( 'sidebar', 'menu', 'navigation', 'vertical' );
	}

	protected function _register_controls() {

		$custom_menus = array();
		$menus        = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
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
				'label' => __( 'Sidebar Menu', 'porto-functionality' ),
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
	}

	protected function render() {
		$atts = $this->get_settings_for_display();
		if ( $template = porto_shortcode_template( 'porto_sidebar_menu' ) ) {
			include $template;
		}
	}
}
