<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterPostFormat extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_post_format', __( 'Post Format', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}