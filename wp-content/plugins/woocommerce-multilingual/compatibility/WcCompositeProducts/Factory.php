<?php

namespace WCML\Compatibility\WcCompositeProducts;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_Composite_Products;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

/**
 * @see https://woocommerce.com/products/composite-products/
 */
class Factory extends ComponentFactory implements IStandAloneAction {

	/**
	 * @inheritDoc
	 */
	public function create() {
		$hooks = [];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new MulticurrencyHooks( getWooCommerceWpml() );
		}

		if ( ! isStandAlone() ) {
			$hooks[] = new WCML_Composite_Products( getSitePress() );
		}

		return $hooks;
	}
}
