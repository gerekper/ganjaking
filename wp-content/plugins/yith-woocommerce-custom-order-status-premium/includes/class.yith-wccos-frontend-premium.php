<?php
/**
 * Frontend class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.


if ( ! class_exists( 'YITH_WCCOS_Frontend_Premium' ) ) {
	/**
	 * Frontend class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @deprecated 1.2.14
	 */
	class YITH_WCCOS_Frontend_Premium extends YITH_WCCOS_Frontend {
	}
}
/**
 * Unique access to instance of YITH_WCCOS_Frontend_Premium class
 *
 * @return YITH_WCCOS_Frontend
 * @deprecated 1.1.0 | use yith_wccos_frontend() instead
 *
 * @since      1.0.0
 */
function yith_wccos_frontend_premium() {
	return yith_wccos_frontend();
}
