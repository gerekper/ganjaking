<?php

namespace wpbuddy\rich_snippets\pro;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/**
 * Class Helper.
 *
 * Helps to fetch some data.
 *
 * @package wpbuddy\rich_snippets
 *
 * @since   2.19.0
 */
class Helper_Model extends \wpbuddy\rich_snippets\Helper_Model {
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Magic function.
	 *
	 * @return bool
	 *
	 * @since 2.3.0
	 */
	public function magic() {

		return true;

		if ( true !== boolval( get_option( base64_decode( 'd3BiX3JzL3ZlcmlmaWVk' ), false ) ) ) {
			return false;
		}

		if ( true !== boolval( get_option( 'd3BiX3JzL3ZlcmlmaWVk', false ) ) ) {
			return false;
		}

		return true;
	}
}