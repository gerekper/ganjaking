<?php

namespace WCML\Compatibility\PerProductShipping;

use WCML\Compatibility\ComponentFactory;
use WCML_Per_Product_Shipping;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Per_Product_Shipping();
	}
}
