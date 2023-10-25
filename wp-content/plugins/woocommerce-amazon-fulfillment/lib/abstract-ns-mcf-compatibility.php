<?php
/**
 * Compatibility class addon compatibility.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Integration' ) ) {
	return;
}

if ( ! class_exists( 'NS_MCF_Abstract_Compatibility' ) ) {

	/**
	 * Compatibility class.
	 */
	class NS_MCF_Abstract_Compatibility extends NS_MCF_Integration {

		/**
		 * Initializes the module. Always executed even if the module is deactivated.
		 *
		 * Do not use init() in subclasses, use maybe_apply_compatibility() instead.
		 */
		public function init() {
			$this->maybe_apply_compatibility();
		}

		/**
		 * This method is used by the subclass to implement compatibility checks and change normal behaviour.
		 * This is only executed is the function `is_active()` returns true.
		 *
		 * @return void
		 */
		public function maybe_apply_compatibility() {

		}
	} // class.
}
