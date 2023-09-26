<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterPostDate extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_post_date', __( 'Date', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}