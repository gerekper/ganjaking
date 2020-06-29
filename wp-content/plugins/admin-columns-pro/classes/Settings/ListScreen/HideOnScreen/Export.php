<?php
namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class Export extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_export', __( 'Export', 'codepress-admin-columns' ) );
	}

}