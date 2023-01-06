<?php

namespace WCML\Compatibility\Relevanssi;

use WCML\Compatibility\ComponentFactory;
use WCML_Relevanssi;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Relevanssi();
	}
}
