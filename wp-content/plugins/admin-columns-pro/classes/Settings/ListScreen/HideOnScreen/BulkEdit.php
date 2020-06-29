<?php
namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class BulkEdit extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_bulk_edit', __( 'Bulk Edit', 'codepress-admin-columns' ) );
	}

}