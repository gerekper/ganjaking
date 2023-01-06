<?php

namespace WCML\Compatibility\WpSeo;

use WCML\Compatibility\ComponentFactory;
use WCML_WPSEO;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_WPSEO();
	}
}
