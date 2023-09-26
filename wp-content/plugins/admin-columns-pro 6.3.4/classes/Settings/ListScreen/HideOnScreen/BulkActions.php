<?php

namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class BulkActions extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_bulk_actions', __( 'Bulk Actions', 'codepress-admin-columns' ) );
	}

}