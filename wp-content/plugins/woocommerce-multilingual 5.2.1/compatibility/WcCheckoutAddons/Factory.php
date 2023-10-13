<?php

namespace WCML\Compatibility\WcCheckoutAddons;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_Checkout_Addons;
use function WCML\functions\isStandAlone;

/**
 * @see http://www.woocommerce.com/products/woocommerce-checkout-add-ons/
 */
class Factory extends ComponentFactory implements IStandAloneAction {

	/**
	 * @inheritDoc
	 */
	public function create() {
		$hooks = [];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new MulticurrencyHooks();
		}

		if ( ! isStandAlone() ) {
			$hooks[] = new WCML_Checkout_Addons();
		}

		return $hooks;
	}
}
