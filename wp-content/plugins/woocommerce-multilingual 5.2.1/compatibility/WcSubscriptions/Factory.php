<?php

namespace WCML\Compatibility\WcSubscriptions;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_WC_Subscriptions;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

/**
 * @see https://www.woocommerce.com/products/woocommerce-subscriptions/
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
			$hooks[] = new MulticurrencyHooks( getWooCommerceWpml(), self::getWpdb() );
		}

		if ( ! isStandAlone() ) {
			$hooks[] = new WCML_WC_Subscriptions( getWooCommerceWpml(), self::getWpdb() );
		}

		return $hooks;
	}
}
