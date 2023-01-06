<?php

namespace WCML\Compatibility\MaxStorePro;

use WCML\Compatibility\ComponentFactory;
use WCML_MaxStore;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_MaxStore();
	}
}
