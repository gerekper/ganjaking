<?php

namespace WCML\Compatibility\WcProductBundles;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_Product_Bundles;
use WCML_WC_Product_Bundles_Items;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

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
			$hooks[] = new WCML_Product_Bundles( getSitePress(), getWooCommerceWpml(), new WCML_WC_Product_Bundles_Items(), self::getWpdb() );
		}

		return $hooks;
	}
}
