<?php

namespace ACP\Settings\ListScreen\HideOnScreen\SubMenu;

use ACP\Settings\ListScreen\HideOnScreen\SubMenu;

class PostStatus extends SubMenu {

	public function __construct() {
		parent::__construct( __( 'Status', 'codepress-admin-columns' ) );
	}

}