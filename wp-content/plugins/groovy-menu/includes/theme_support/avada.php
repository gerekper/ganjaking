<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'groovy_menu_avada_builder_support' ) ) {

	/**
	 * Prevent output Groovy Menu for fusion builder.
	 *
	 * @param $prevent bool if return true - groovy menu will disapear.
	 *
	 * @return bool
	 */
	function groovy_menu_avada_builder_support( $prevent ) {

		if ( defined('AVADA_VERSION') && ! empty( $_GET['fb-edit'] ) ) { // @codingStandardsIgnoreLine
			$prevent = true;
		}

		return $prevent;
	}
}

add_filter( 'groovy_menu_prevent_output_html', 'groovy_menu_avada_builder_support', 10, 1 );
