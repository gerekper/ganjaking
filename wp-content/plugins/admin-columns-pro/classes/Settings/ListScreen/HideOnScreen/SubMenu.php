<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class SubMenu extends HideOnScreen {

	public function __construct( $label ) {
		parent::__construct( 'hide_submenu', sprintf( '%s (Quick Links)', $label ) );
	}

}