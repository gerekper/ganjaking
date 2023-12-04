<?php
/**
 * Maintenance class for performing any DB operations or other version / upgrade dependent actions.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Maintenance' ) ) {

	/**
	 * Maintenance class. For adding DB transforms or other version update dependencies and checks.
	 */
	class NS_MCF_Maintenance extends NS_MCF_Integration {

	} // class.
}
