<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterProductStockStatus extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_product_stock_status', __( 'Stock Status', 'codepress-admin-columns' ), ACP\Settings\ListScreen\HideOnScreen\Filters::NAME );
	}

}