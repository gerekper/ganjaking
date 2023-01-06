<?php

namespace WCML\Compatibility\WooVariationsTable;

use WCML\Compatibility\ComponentFactory;
use function WCML\functions\getSitePress;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new \WCML_Woo_Var_Table( getSitePress()->get_current_language() );
	}
}
