<?php

namespace WCML\Compatibility\Flatsome;

use WCML\Compatibility\ComponentFactory;
use WCML_Flatsome;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Flatsome();
	}
}
