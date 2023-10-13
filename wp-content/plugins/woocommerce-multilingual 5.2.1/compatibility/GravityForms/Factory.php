<?php

namespace WCML\Compatibility\GravityForms;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;
use WCML_gravityforms;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;
use function WCML\functions\isStandAlone;

/**
 * @see https://www.gravityforms.com/
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
			$hooks[] = new WCML_gravityforms( getSitePress(), getWooCommerceWpml() );
		}

		return $hooks;
	}

}
