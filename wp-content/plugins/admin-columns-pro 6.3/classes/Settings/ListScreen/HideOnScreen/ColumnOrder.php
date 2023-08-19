<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class ColumnOrder extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'column_order', __( 'Column Order', 'codepress-admin-columns' ) );
	}

}