<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP;
use ACP\Settings\ListScreen\HideOnScreen;

class FilterCategory extends HideOnScreen {

	const NAME = 'hide_filter_category';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Category', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}