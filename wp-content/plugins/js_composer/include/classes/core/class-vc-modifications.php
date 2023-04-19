<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
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
			echo '<' . $tag . ' type="text/html" id="wpb-modifications"> window.wpbCustomElement = 1; </' . $tag . '>';
		}
	}
}
