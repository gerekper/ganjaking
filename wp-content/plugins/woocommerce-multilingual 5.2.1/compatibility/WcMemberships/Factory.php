<?php

namespace WCML\Compatibility\WcMemberships;

use WCML\Compatibility\ComponentFactory;

class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new \WCML_WC_Memberships( new \WPML_WP_API() );
	}
}
