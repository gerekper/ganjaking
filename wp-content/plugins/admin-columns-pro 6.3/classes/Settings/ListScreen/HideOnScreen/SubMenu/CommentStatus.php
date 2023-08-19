<?php

namespace ACP\Settings\ListScreen\HideOnScreen\SubMenu;

use ACP\Settings\ListScreen\HideOnScreen\SubMenu;

class CommentStatus extends SubMenu {

	public function __construct() {
		parent::__construct( __( 'Roles', 'codepress-admin-columns' ) );
	}

}