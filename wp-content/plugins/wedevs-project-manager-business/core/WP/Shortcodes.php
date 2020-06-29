<?php

namespace WeDevs\PM_Pro\Core\WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		if ( is_admin() ) {
			return;
		}
		$shortcodes = array(
			'cpm' => __CLASS__ . '::pm_shortcode', /*deprecated*/
			'pm'  => __CLASS__ . '::pm_shortcode',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Shortcode Wrapper.
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'pm-pro-wrap',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		call_user_func( $function, $atts );
		echo empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		return ob_get_clean();
	}

	/**
	 * shortcode.
	 *
	 * @param mixed $atts
	 * 
	 * @return string
	 */
	public static function pm_shortcode( $atts ) {
		return self::shortcode_wrapper( 
			array( 'WeDevs\\PM_Pro\\Core\\Shortcodes\\PM_Shortcode', 'output' ), 
			$atts 
		);
	}
}
