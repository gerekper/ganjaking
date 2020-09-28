<?php
/**
 * Elementor Pro Compatibility class
 *
 * @since 5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Porto_Elementor_Pro_Compatibility' ) ) :
	class Porto_Elementor_Pro_Compatibility {

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'elementor/theme/before_do_header', array( $this, 'header_builder_before' ), 1 );
			add_action( 'elementor/theme/after_do_header', array( $this, 'header_builder' ) );
			add_action( 'elementor/theme/before_do_footer', array( $this, 'header_builder_before' ) );
			add_action( 'elementor/theme/after_do_footer', array( $this, 'footer_builder' ) );
		}

		public function header_builder_before() {
			ob_start();
		}

		public function header_builder() {
			global $porto_settings;
			$header_is_side = porto_header_type_is_side();
			$header_content = ob_get_clean();
			$header_content = '<header id="header" class="header-builder' . ( $header_is_side ? ' header-side sticky-menu-header' : '' ) . ( $porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url'] ? ' logo-overlay-header' : '' ) . '"' . ( $header_is_side ? ' data-plugin-sticky data-plugin-options="' . esc_attr( '{"autoInit": true, "minWidth": 992, "containerSelector": ".page-wrapper","autoFit":true, "paddingOffsetBottom": 0, "paddingOffsetTop": 0}' ) . '"' : '' ) . '>' . $header_content . '</header>';
			global $porto_settings;
			$porto_settings['header-type-select'] = '';
			$porto_settings['mobile-panel-type']  = '';
			if ( $header_is_side ) {
				$porto_settings['header-type'] = 'side';
			} else {
				$porto_settings['header-type'] = '';
			}
			porto_get_template_part(
				'header/header_before',
				null,
				array(
					'porto_header_escaped' => $header_content,
				)
			);
		}

		public function footer_builder() {
			$footer_content = ob_get_clean();
			porto_get_template_part(
				'footer',
				null,
				array(
					'porto_footer_escaped' => $footer_content,
				)
			);
		}
	}
endif;

new Porto_Elementor_Pro_Compatibility;
