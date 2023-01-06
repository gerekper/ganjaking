<?php

namespace WCML\Compatibility\WcTabManager;

use WCML\Compatibility\ComponentFactory;
use WCML_Tab_Manager;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Tab_Manager( getSitePress(), self::getWooCommerce(), getWooCommerceWpml(), self::getWpdb(), self::getElementTranslationPackage() );
	}
}
