<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class Search extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_search', __( 'Search', 'codepress-admin-columns' ) );
	}

}