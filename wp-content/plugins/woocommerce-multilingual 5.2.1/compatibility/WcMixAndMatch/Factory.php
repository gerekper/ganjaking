<?php

namespace WCML\Compatibility\WcMixAndMatch;

use WCML\Compatibility\ComponentFactory;
use WCML\StandAlone\IStandAloneAction;

use function WCML\functions\getSitePress;
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
			$hooks[] = new \WCML_Mix_And_Match_Products( getSitePress() );
		}

		return $hooks;
	}

}
