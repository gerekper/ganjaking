<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

if ( ! function_exists( 'groovy_menu_prevent_output_for_not_public_post_types' ) ) {

	/**
	 * Prevent output Groovy Menu for not public post types.
	 *
	 * @param $prevent bool if return true - groovy menu will disapear.
	 *
	 * @return bool
	 */
	function groovy_menu_prevent_output_for_not_public_post_types( $prevent ) {

		$all_post_types = \GroovyMenuUtils::getPostTypes( true );

		$post_type = strval( get_post_type() );

		if ( ! isset( $all_post_types[ $post_type ] ) ) {
			$prevent = true;
		}

		return $prevent;
	}
}

//add_filter( 'groovy_menu_prevent_output_html', 'groovy_menu_prevent_output_for_not_public_post_types', 10, 1 );
