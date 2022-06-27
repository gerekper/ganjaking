<?php
/**
 * Helper class for integrating with the Amazon Inventory API
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_FBA_Maintenance' ) ) {

	/**
	 * Maintenance class. For adding DB transforms or other version update dependencies and checks.
	 */
	class NS_FBA_Maintenance {

		/**
		 * The NS_FBA object.
		 *
		 * @var NS_FBA
		 */
		private $ns_fba;

		/**
		 * Constructor.
		 *
		 * @param NS_FBA $ns_fba The main NS_FBA object.
		 */
		public function __construct( $ns_fba ) {
			// local reference to the main ns_fba object.
			$this->ns_fba = $ns_fba;
		}

	} // class.
}
