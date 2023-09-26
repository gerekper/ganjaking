<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class ColumnResize extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'resize_columns', __( 'Resize Columns', 'codepress-admin-columns' ) );
	}

}