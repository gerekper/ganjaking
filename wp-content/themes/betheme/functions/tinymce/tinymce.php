<?php
/* ----------------------------------------------------------------------------------- *
 *	WordPress uses TinyMCE 4 since 3.9
 *	For safety reasons no support for TinyMCE 3 ( WordPress 3.8 )
 * ----------------------------------------------------------------------------------- */
$mfn_wp_version = floatval( get_bloginfo( 'version' ) );

if( $mfn_wp_version >= 3.9 ){

	if( ! function_exists( 'mfn_mce_init' ) )
	{
		function mfn_mce_init() {
			global $page_handle;
			if ( ! current_user_can ( 'edit_posts' ) || ! current_user_can ( 'edit_pages' )) return false;

			if (get_user_option ( 'rich_editing' ) == 'true') {
				add_filter ( "mce_external_plugins", 'mfn_mce_plugin' );
				add_filter ( 'mce_buttons', 'mfn_mce_buttons' );
			}
		}
	}
	add_action ( 'init', 'mfn_mce_init' );

	if( ! function_exists( 'mfn_mce_plugin' ) )
	{
		function mfn_mce_plugin( $array ){
			$array ['mfnsc'] = get_theme_file_uri('/functions/tinymce/plugin.js');
			return $array;
		}
	}

	if( ! function_exists( 'mfn_mce_buttons' ) )
	{
		function mfn_mce_buttons( $buttons ){
			array_push ( $buttons, 'mfnsc' );
			return $buttons;
		}
	}

}
