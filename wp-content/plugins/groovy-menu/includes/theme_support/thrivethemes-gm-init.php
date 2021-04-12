<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Groovy Menu Plugin Widget for Thrive Themes - https://thrivethemes.com.
 *
 * @since 2.4.12
 */

add_action( 'plugins_loaded', 'groovy_menu_thrivethemes_menu_element_init' );

if ( ! function_exists( 'groovy_menu_thrivethemes_menu_element_init' ) ) {
	function groovy_menu_thrivethemes_menu_element_init() {
		if ( class_exists( 'TCB_Element_Abstract' ) ) {
			require_once 'thrivethemes-gm-element.php';
			add_filter( 'tcb_element_instances', 'groovy_menu_thrivethemes_menu_add_instance', 50, 2 );
		}
	}
}

if ( ! function_exists( 'groovy_menu_thrivethemes_menu_add_instance' ) ) {
	function groovy_menu_thrivethemes_menu_add_instance( $instanses, $text ) {

		if ( empty( $instanses ) || ! is_array( $instanses ) ) {
			$instanses = array();
		}

		if ( class_exists( 'GroovyMenu_ThriveThemes_Element' ) ) {
			$tag = 'groovy_menu_plugin';
			/** @var TCB_Element_Abstract $instance */
			$GM_instance = new GroovyMenu_ThriveThemes_Element( $tag );
			if ( $GM_instance->is_available() ) {
				$instanses[ $tag ] = $GM_instance;
			}
		}

		return $instanses;
	}
}