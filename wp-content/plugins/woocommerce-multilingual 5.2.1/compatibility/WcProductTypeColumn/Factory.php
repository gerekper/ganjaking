<?php

namespace WCML\Compatibility\WcProductTypeColumn;

use WCML\Compatibility\ComponentFactory;
use WCML_WC_Product_Type_Column;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_WC_Product_Type_Column();
	}
}
