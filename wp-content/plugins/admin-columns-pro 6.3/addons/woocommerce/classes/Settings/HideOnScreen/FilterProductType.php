<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterProductType extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_product_type', __( 'Product Type', 'codepress-admin-columns' ), ACP\Settings\ListScreen\HideOnScreen\Filters::NAME );
	}

}