<?php

namespace WCML\Compatibility\WcDynamicPricing;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use function WCML\functions\getSitePress;
use function WCML\functions\isStandAlone;

/**
 * @see https://woocommerce.com/products/dynamic-pricing/
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
			$hooks[] = new \WCML_Dynamic_Pricing( getSitePress() );
		}

		return $hooks;
	}
}
