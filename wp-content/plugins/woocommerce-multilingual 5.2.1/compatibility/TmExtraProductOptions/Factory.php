<?php

namespace WCML\Compatibility\TmExtraProductOptions;

use WCML\Compatibility\ComponentFactory;
use WCML_Extra_Product_Options;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Extra_Product_Options();
	}
}