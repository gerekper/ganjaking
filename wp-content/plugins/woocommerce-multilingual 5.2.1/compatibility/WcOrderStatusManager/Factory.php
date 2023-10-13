<?php

namespace WCML\Compatibility\WcOrderStatusManager;

use WCML\Compatibility\ComponentFactory;
use WCML_Order_Status_Manager;
use WP_Query;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Order_Status_Manager( new WP_Query() );
	}
}
