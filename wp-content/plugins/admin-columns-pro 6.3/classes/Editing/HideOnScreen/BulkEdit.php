<?php

namespace ACP\Editing\HideOnScreen;

use ACP;

class BulkEdit extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_bulk_edit', __( 'Bulk Edit', 'codepress-admin-columns' ) );
	}

}