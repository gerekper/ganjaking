<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP;
use ACP\Settings\ListScreen\HideOnScreen;

class FilterPostDate extends HideOnScreen {

	const NAME = 'hide_filter_post_date';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Date', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}