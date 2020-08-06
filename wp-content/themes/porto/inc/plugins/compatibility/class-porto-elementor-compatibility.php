<?php
/**
 * Elementor Compatibility class
 *
 * @since 5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Porto_Elementor_Compatibility {
	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'elementor_notice_js' ) );

		add_action( 'wp_ajax_porto_elementor_disable_default_styles', array( $this, 'disable_default_styles' ) );

		add_action( 'porto_admin_save_theme_settings', array( $this, 'init_options' ) );
		add_action( 'customize_save_after', array( $this, 'init_options' ), 99 );
	}

	public function elementor_notice_js() {
		if ( 'no' == get_option( 'porto_settings_elementor', 'no' ) && $this->is_in_edit() ) {
			wp_enqueue_script( 'porto-elementor-notice', PORTO_JS . '/admin/porto-elementor-notice.js', array( 'jquery' ), PORTO_VERSION );
			wp_localize_script(
				'porto-elementor-notice',
				'portoElementorNotice',
				array(
					'nonce' => wp_create_nonce( 'porto_elementor_notice_nonce' ),
				)
			);
		}
	}

	/**
	 * Disables Elementor default styles
	 */
	public function disable_default_styles() {
		if ( ! check_ajax_referer( 'porto_elementor_notice_nonce', 'nonce' ) ) {
			die();
		}
		$option = $_POST['option'];
		if ( ! empty( $option ) ) {
			if ( 'yes' == $option ) {
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
			}
			update_option( 'porto_settings_elementor', 'yes' );
		}
		die();
	}

	/**
	 * Check if we're in Elementor Edit Page
	 *
	 * @return bool
	 */
	private function is_in_edit() {
		if ( ( isset( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] ) || isset( $_REQUEST['elementor-preview'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Intialize Elementor style variables which are able to updated in Elementor -> Settings -> Style.
	 */
	public function init_options() {
		global $porto_settings;
		$changed = false;
		if ( isset( $porto_settings['body-font'] ) && isset( $porto_settings['body-font']['font-family'] ) && get_option( 'elementor_default_generic_fonts', '' ) != $porto_settings['body-font']['font-family'] ) {
			update_option( 'elementor_default_generic_fonts', esc_html( $porto_settings['body-font']['font-family'] ) );
			$changed = true;
		}

		if ( ! empty( $porto_settings['container-width'] ) && get_option( 'elementor_container_width', '1200' ) != $porto_settings['container-width'] ) {
			update_option( 'elementor_container_width', (int) $porto_settings['container-width'] );
			$changed = true;
		}

		if ( false === get_option( 'elementor_space_between_widgets', false ) && ! empty( $porto_settings['grid-gutter-width'] ) && get_option( 'elementor_space_between_widgets', '20' ) != $porto_settings['grid-gutter-width'] ) {
			update_option( 'elementor_space_between_widgets', (int) $porto_settings['grid-gutter-width'] );
			$changed = true;
		}

		if ( 'h1.page-title' != get_option( 'elementor_page_title_selector', '' ) ) {
			update_option( 'elementor_page_title_selector', 'h1.page-title' );
			$changed = true;
		}
		if ( '992' != get_option( 'elementor_viewport_lg', '1025' ) ) {
			update_option( 'elementor_viewport_lg', '992' );
			$changed = true;
		}

		if ( $changed ) {
			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( Exception $e ) {
			}
		}
	}
}

new Porto_Elementor_Compatibility();
