<?php

namespace WCML\Compatibility\WcCheckoutFieldEditor;

use WCML\Compatibility\ComponentFactory;
use WCML_Checkout_Field_Editor;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Checkout_Field_Editor();
	}
}