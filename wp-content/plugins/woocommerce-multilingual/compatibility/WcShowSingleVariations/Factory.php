<?php

namespace WCML\Compatibility\WcShowSingleVariations;

use WCML\Compatibility\ComponentFactory;
use WCML_JCK_WSSV;

/**
 * @see https://iconicwp.com/products/woocommerce-show-single-variations/
 */
class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_JCK_WSSV();
	}
}
