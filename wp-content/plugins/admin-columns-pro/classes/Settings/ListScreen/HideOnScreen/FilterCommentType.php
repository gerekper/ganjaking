<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP;
use ACP\Settings\ListScreen\HideOnScreen;

class FilterCommentType extends HideOnScreen {

	const NAME = 'hide_filter_comment_type';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Comment Types', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}