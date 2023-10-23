<?php
/**
 * Premium Admin class
 *
 * @package YITH\CustomOrderStatus
 */

defined( 'YITH_WCCOS' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCCOS_Admin_Premium' ) ) {
	/**
	 * Admin class.
	 * The class manage all the admin behaviors.
	 *
	 * @deprecated 1.2.14
	 */
	class YITH_WCCOS_Admin_Premium extends YITH_WCCOS_Admin {

	}
}

/**
 * Unique access to instance of YITH_WCCOS_Admin_Premium class
 *
 * @return YITH_WCCOS_Admin
 * @deprecated 1.1.0 | use yith_wccos_admin instead
 */
function yith_wccos_admin_premium() {
	return yith_wccos_admin();
}
