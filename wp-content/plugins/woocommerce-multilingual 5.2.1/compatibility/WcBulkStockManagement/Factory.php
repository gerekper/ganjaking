<?php

namespace WCML\Compatibility\WcBulkStockManagement;

use WCML\Compatibility\ComponentFactory;
use WCML_Bulk_Stock_Management;

/**
 * @see http://www.woothemes.com/products/bulk-stock-management/
 */
class Factory extends ComponentFactory {

	/**
	 * @inheritDoc
	 */
	public function create() {
		return new WCML_Bulk_Stock_Management();
	}
}