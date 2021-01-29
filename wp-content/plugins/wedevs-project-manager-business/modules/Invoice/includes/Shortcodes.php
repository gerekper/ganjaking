<?php

namespace WeDevs\PM_Pro\Modules\Invoice\includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


class Shortcodes {

	/**
	 * Init shortcodes.
	 */
	public static function init() {
		$shortcodes = array(
			'pm_invoice' => __CLASS__ . '::invoice',
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
	public static function invoice( $atts ) {
		return self::shortcode_wrapper(
			array( 'WeDevs\\PM_Pro\\Modules\\Invoice\\includes\\shortcodes\\Invoice', 'output' ),
			$atts
		);
	}

}
