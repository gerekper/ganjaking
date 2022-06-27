<?php

namespace ACP\Search\Settings\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class SmartFilters extends HideOnScreen {

	const NAME = 'hide_smart_filters';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Smart Filters', 'codepress-admin-columns' ) );
	}

}