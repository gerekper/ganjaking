<?php

namespace WCML\Compatibility\WcPip;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_Pip;
use function WCML\functions\isStandAlone;

/**
 * @see https://woocommerce.com/products/print-invoices-packing-lists/
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
			$hooks[] = new WCML_Pip();
		}

		return $hooks;
	}
}
