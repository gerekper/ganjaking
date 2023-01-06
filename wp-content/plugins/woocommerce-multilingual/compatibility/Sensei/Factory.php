<?php

namespace WCML\Compatibility\Sensei;

use WCML\Compatibility\ComponentFactory;
use WCML_Sensei;
use WPML_Custom_Columns;
use function WCML\functions\getSitePress;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Sensei( getSitePress(), self::getWpdb(), new WPML_Custom_Columns( getSitePress() ) );
	}
}
