<?php

namespace WCML\Compatibility\WpBakery;

use WCML\Compatibility\ComponentFactory;
use WCML_Wpb_Vc;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Wpb_Vc();
	}
}
