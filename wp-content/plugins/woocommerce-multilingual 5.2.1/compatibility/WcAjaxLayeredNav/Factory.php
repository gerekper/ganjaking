<?php

namespace WCML\Compatibility\WcAjaxLayeredNav;

use WCML\Compatibility\ComponentFactory;
use WCML_Ajax_Layered_Nav_Widget;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Ajax_Layered_Nav_Widget();
	}
}
