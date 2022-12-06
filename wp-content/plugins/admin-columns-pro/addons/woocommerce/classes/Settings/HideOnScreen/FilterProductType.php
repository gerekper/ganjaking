<?php

namespace ACA\WC\Settings\HideOnScreen;

use ACP;

class FilterProductType extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filter_product_type';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Product Type', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ ACP\Settings\ListScreen\HideOnScreen\Filters::NAME ];
	}

}