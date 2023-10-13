<?php

namespace WCML\Compatibility\YikesCustomProductTabs;

use WCML\Compatibility\ComponentFactory;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new \WCML_YIKES_Custom_Product_Tabs( getWooCommerceWpml(), getSitePress(), self::getElementTranslationPackage() );
	}
}
