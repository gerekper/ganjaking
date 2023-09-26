<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class FilterMediaItem extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_filter_media_type', __( 'Media Items', 'codepress-admin-columns' ), HideOnScreen\Filters::NAME );
	}

}