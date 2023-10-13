<?php

namespace WCML\Compatibility\WcExporter;

use WCML\Compatibility\ComponentFactory;
use WCML_wcExporter;
use function WCML\functions\getSitePress;
use function WCML\functions\getWooCommerceWpml;

/**
 * @see https://br.wordpress.org/plugins/woocommerce-exporter/
 */
class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_wcExporter( getSitePress(), getWooCommerceWpml() );
	}
}
