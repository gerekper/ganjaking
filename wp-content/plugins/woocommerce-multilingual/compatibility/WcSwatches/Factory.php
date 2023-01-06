<?php

namespace WCML\Compatibility\WcSwatches;

use WCML\Compatibility\ComponentFactory;
use WCML_Variation_Swatches_And_Photos;
use function WCML\functions\getWooCommerceWpml;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Variation_Swatches_And_Photos( getWooCommerceWpml() );
	}
}