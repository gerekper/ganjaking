<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuActions
 */
class GroovyMenuActions {

	/**
	 * Compile shortcodes for action
	 */
	public static function __callStatic( $method, $arguments ) {
		global $groovyMenuActions;
		if ( ! empty( $groovyMenuActions['custom_preset'][ $method ] ) ) {
			foreach ( $groovyMenuActions['custom_preset'][ $method ] as $action_content ) {
				echo do_shortcode( $action_content );
			}
		}
	}




}
