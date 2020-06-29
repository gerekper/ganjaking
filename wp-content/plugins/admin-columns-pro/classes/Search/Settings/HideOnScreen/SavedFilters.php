<?php

namespace ACP\Search\Settings\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class SavedFilters extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_segments', __( 'Saved Filters', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ SmartFilters::NAME ];
	}

}