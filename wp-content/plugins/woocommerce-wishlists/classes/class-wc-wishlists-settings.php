<?php

class WC_Wishlists_Settings {

	public static function get_setting( $key, $default = null ) {
		return get_option( $key, $default );
	}

	public static function set_setting( $key, $value ) {
		return update_option( $key, $value );
	}

}