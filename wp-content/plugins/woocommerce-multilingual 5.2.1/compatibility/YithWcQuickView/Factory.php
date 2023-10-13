<?php

namespace WCML\Compatibility\YithWcQuickView;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;

/**
 * @see https://wordpress.org/plugins/yith-woocommerce-quick-view/
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

		return $hooks;
	}
}
