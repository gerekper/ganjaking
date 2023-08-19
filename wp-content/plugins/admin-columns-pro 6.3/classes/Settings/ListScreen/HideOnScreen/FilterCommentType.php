<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterCommentType extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_comment_type', __( 'Comment Types', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}