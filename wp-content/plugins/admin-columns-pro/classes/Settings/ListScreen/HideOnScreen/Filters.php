<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP;

class Filters extends ACP\Settings\ListScreen\HideOnScreen {

	const NAME = 'hide_filters';

	public function __construct() {
		parent::__construct( self::NAME, __( 'Filters', 'codepress-admin-columns' ) );
	}

}