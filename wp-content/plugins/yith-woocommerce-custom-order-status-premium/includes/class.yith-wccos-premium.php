<?php
/**
 * Main Premium Class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS_Premium' ) ) {
	/**
	 * Main Premium Class
	 *
	 * @deprecated 1.1.0
	 */
	class YITH_WCCOS_Premium extends YITH_WCCOS {

	}
}

/**
 * Unique access to instance of YITH_WCCOS_Premium class
 *
 * @return YITH_WCCOS
 * @deprecated 1.1.0 | yith_wccos instead
 */
function yith_wccos_premium() {
	return yith_wccos();
}
