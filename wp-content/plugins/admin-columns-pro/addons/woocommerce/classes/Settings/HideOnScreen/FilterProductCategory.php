<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterProductCategory extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filter_product_category';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Category', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ ACP\Settings\ListScreen\HideOnScreen\Filters::NAME ];
	}

}