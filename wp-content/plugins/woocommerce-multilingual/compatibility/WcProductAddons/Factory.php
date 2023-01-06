<?php

namespace WCML\Compatibility\WcProductAddons;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

/**
 * @see https://woocommerce.com/products/product-add-ons/
 */
class Factory extends ComponentFactory implements IStandAloneAction {

	/**
	 * @inheritDoc
	 */
	public function create() {
		$hooks = [
			new SharedHooks(),
		];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new MulticurrencyHooks( getWooCommerceWpml() );
		}

		if ( ! isStandAlone() ) {
			$hooks[] = new \WCML_Product_Addons( getSitePress() );
		}

		return $hooks;
	}
}
