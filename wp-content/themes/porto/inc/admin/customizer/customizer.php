<?php
/**
 * Porto Customizer Config
 *
 * @author     Porto Themes
 * @category   Admin Functions
 * @since      4.8.0
 */

defined( 'ABSPATH' ) || exit;

function porto_customizer_enqueue_stylesheets() {
	wp_enqueue_script( 'jquery-ui-sortable', PORTO_JS . '/admin/jquery-ui.sortable.min.js', array( 'jquery' ), PORTO_VERSION, true );
	wp_enqueue_script( 'porto-customizer-admin-js', PORTO_JS . '/admin/customizer-admin.min.js', array( 'jquery-ui-sortable' ), PORTO_VERSION, 'all' );
	wp_localize_script(
		'porto-customizer-admin-js',
		'customizer_admin_vars',
		array(
			'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'    => wp_create_nonce( 'porto-customizer' ),
		)
	);

	global $porto_settings;
	echo '<style>';
	echo '.header-builder span, .header-builder .element-infinite { background:' . esc_html( $porto_settings['skin-color'] ) . '}.header-builder-wrapper .element-cont { background: none; }';
	echo '</style>';
}
add_action( 'customize_controls_print_styles', 'porto_customizer_enqueue_stylesheets' );

function porto_customizer_live_scripts() {
	wp_enqueue_script( 'porto-customizer-preview-js', PORTO_JS . '/admin/customizer-preview.min.js', null, PORTO_VERSION, 'all' );

	if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
		global $reduxPortoSettings;
		$css_vars = $reduxPortoSettings->get_css_vars();
		wp_localize_script(
			'porto-customizer-preview-js',
			'porto_cp_vars',
			array(
				'css_vars' => json_encode( $css_vars ),
			)
		);

		wp_enqueue_style( 'porto-customizer-preview-css', PORTO_CSS . '/customizer-preview.css', false, PORTO_VERSION, 'all' );

		global $porto_settings;
		$header_bg = false;
		if ( isset( $porto_settings['header-bg'] ) && $porto_settings['header-bg'] && $porto_settings['header-bg']['background-color'] && 'transparent' != $porto_settings['header-bg']['background-color'] ) {
			$header_bg = $porto_settings['header-bg']['background-color'];
		} elseif ( isset( $porto_settings['header-wrap-bg'] ) && $porto_settings['header-wrap-bg'] && $porto_settings['header-wrap-bg']['background-color'] && 'transparent' != $porto_settings['header-wrap-bg']['background-color'] ) {
			$header_bg = $porto_settings['header-wrap-bg']['background-color'];
		} elseif ( isset( $porto_settings['sticky-header-bg'] ) && $porto_settings['sticky-header-bg'] && $porto_settings['sticky-header-bg']['background-color'] && 'transparent' != $porto_settings['sticky-header-bg']['background-color'] ) {
			$header_bg = $porto_settings['sticky-header-bg']['background-color'];
		}
		if ( $header_bg ) {
			require_once PORTO_LIB . '/lib/color-lib.php';
			$porto_color_lib = PortoColorLib::getInstance();
			$rgb             = $porto_color_lib->hexToRGB( $header_bg, false );
			if ( $rgb[0] <= 10 && $rgb[1] >= 126 && $rgb[1] <= 146 && $rgb[2] >= 194 && $rgb[2] <= 214 ) {
				$custom_css = '.porto-tooltip { background: rgba(26, 26, 26, 0.7); }.porto-tooltip:hover { background: rgba(26, 26, 26, 1); }';
				wp_add_inline_style( 'porto-customizer-preview-css', $custom_css );
			}
		}
	}
}
add_action( 'customize_preview_init', 'porto_customizer_live_scripts' );
