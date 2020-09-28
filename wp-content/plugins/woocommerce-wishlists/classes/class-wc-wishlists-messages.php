<?php

class WC_Wishlists_Messages {

	public static function text( $key, $default ) {
		echo self::get_text( $key, $default );
	}

	public static function get_text( $key, $default = '' ) {
		$key = sanitize_title( $key );

		return apply_filters( 'wc_wishlists_get_text_' . $key, WC_Wishlists_Settings::get_setting( 'wc_wishlists_text_' . $key, $default ) );
	}

	public static function add_wp_error( WP_Error $error ) {

	}

	public static function add_error( $message, $log = false ) {

	}

	public static function add_message( $message, $log = false ) {

	}

	private static function make_text( $key ) {
		$message = '';
		switch ( $key ) {
			case 'error_creating_list':
				$message = __( 'Error creating list.  Please try again later', 'wc_wishlist' );
				break;
			case 'label_create_list':
				$message = __( 'Create a List', 'wc_wishlist' );
				break;
			case 'label_create_list_desc' :
				$message = __( 'Create a list to save items for later.', 'wc_wishlist' );
		}

		return $message;
	}

}