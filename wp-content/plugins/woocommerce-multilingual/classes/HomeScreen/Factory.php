<?php

namespace WCML\HomeScreen;

use WCML\StandAlone\IStandAloneAction;

class Factory implements \IWPML_Backend_Action_Loader, IStandAloneAction {

	/**
	 * @return \IWPML_Action|null
	 */
	public function create() {
		if ( wcml_is_multi_currency_on() ) {
			return new Hooks();
		}

		return null;
	}

}
