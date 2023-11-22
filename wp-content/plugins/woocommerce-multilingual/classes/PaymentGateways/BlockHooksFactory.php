<?php

namespace WCML\PaymentGateways;

class BlockHooksFactory implements \IWPML_Frontend_Action_Loader, \IWPML_Deferred_Action_Loader {

	/**
	 * @return \IWPML_Action
	 */
	public function create() {
		global $woocommerce_wpml;

		return new BlockHooks( $woocommerce_wpml );
	}

	public function get_load_action() {
		return 'plugins_loaded';
	}

}
