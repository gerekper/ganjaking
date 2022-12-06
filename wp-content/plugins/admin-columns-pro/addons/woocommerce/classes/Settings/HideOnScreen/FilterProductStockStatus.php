<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterProductStockStatus extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filter_product_stock_status';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Stock Status', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ ACP\Settings\ListScreen\HideOnScreen\Filters::NAME ];
	}

}