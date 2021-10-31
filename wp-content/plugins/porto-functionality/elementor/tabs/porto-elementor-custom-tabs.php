<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Porto_Elementor_Editor_Custom_Tabs
 *
 * register new custom tabs to elementor editor
 *
 * @since 6.2.0
 */

use Elementor\Controls_Manager;
// Mouse Parallax
class Porto_Elementor_Editor_Custom_Tabs {
	const TAB_CUSTOM = 'porto_custom_tab';

	private $custom_tabs;

	public function __construct() {
		$this->init_custom_tabs();

		$this->register_custom_tabs();
	}

	private function init_custom_tabs() {
		$this->custom_tabs = array();

		$this->custom_tabs[ $this::TAB_CUSTOM ] = esc_html__( 'Porto Options', 'porto-functionality' );
	}

	public function register_custom_tabs() {
		foreach ( $this->custom_tabs as $key => $value ) {
			Elementor\Controls_Manager::add_tab( $key, $value );
		}
	}
}

new Porto_Elementor_Editor_Custom_Tabs;
