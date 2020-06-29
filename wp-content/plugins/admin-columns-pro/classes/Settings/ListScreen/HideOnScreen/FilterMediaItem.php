<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP;
use ACP\Settings\ListScreen\HideOnScreen;

class FilterMediaItem extends HideOnScreen {

	const NAME = 'hide_filter_media_type';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Media Items', 'codepress-admin-columns' ) );
	}

	public function get_dependent_on() {
		return [ HideOnScreen\Filters::NAME ];
	}

}