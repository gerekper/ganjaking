<?php

namespace WCML\Compatibility\StripePayments;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;

use function WCML\functions\getWooCommerceWpml;

/**
 * @see https://wordpress.org/plugins/woocommerce-gateway-stripe/
 */
class Factory extends ComponentFactory implements IStandAloneAction {

	/**
	 * @inheritDoc
	 */
	public function create() {
		$hooks = [];

		if ( wcml_is_multi_currency_on() ) {
			$hooks[] = new MulticurrencyHooks( getWooCommerceWpml()->get_multi_currency()->orders );
		}

		return $hooks;
	}
}
