<?php

namespace ACP\Export\HideOnScreen;

use ACP;

class Export extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_export', __( 'Export', 'codepress-admin-columns' ) );
	}

}