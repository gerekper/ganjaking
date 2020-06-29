<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

class LS_Config {

	public static $config 	= array();
	public static $forced 	= array();
	public static $forcedBy = array();

	private function __construct() {}


	public static function init() {

		self::$config = array(
			'theme_bundle' => false,
			'autoupdate' => true,
			'notices' => true,
			'promotions' => true,
			'purchase_url' => get_option('ls-p-url', 'https://kreaturamedia.com/cart/ls-wp/')
		);
	}


	public static function has( $feature ) {
		return isset( self::$config[ $feature ] );
	}


	public static function get( $feature ) {

		if( isset( self::$config[ $feature ] ) ) {
			return self::$config[ $feature ];
		}

		return null;
	}


	public static function set( $keys, $value = null ) {

		if( is_string( $keys ) ) {
			$keys = array( "$keys" => $value );
		}

		if( is_array( $keys ) ) {
			foreach( $keys as $key => $val ) {
				self::$config[ $key ] = $val;
			}
		}
	}


	public static function setAsTheme() {

		self::set( array(
			'theme_bundle' 	=> true,
			'autoupdate' 	=> false,
			'notices' 		=> false
		) );
	}


	public static function checkCompatibility() {

		if( isset( $GLOBALS['lsAutoUpdateBox'] ) && $GLOBALS['lsAutoUpdateBox'] === false ) {
			self::set('autoupdate', false);
		}
	}


	public static function forceSettings( $name = 'Unknown', $keys, $value = null ) {

		if( is_string( $keys ) ) {
			$keys = array( "$keys" => $value );
		}

		if( is_array( $keys) ) {
			foreach( $keys as $key => $val ) {

				if( get_option( 'ls_'.$key ) != $val ) {
					update_option( 'ls_'.$key, $val );
				}

				self::$forced[ $key ] = $val;
				self::$forcedBy[ $key ] = $name;
			}
		}
	}


	public static function isActivatedSite() {

		$activated 	= get_option( 'layerslider-authorized-site', false );
		$code 		= trim( get_option( 'layerslider-purchase-code', '' ) );


		if( empty( $code ) || ! $activated ) {
			return false;
		}

		if( get_option( 'layerslider-activated_by_the7', false ) ) {
			delete_option( 'layerslider-authorized-site' );
			delete_option( 'layerslider-purchase-code' );
			delete_option( 'layerslider-activated_by_the7' );
			return false;
		}


		// Test for code length
		if( strlen( $code ) < 36 ) {
			return false;
		}


		// Test for pattern
		preg_match( '/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', $code, $matches );
		if( empty( $matches ) ) {
			return false;
		}


		return true;
	}
}