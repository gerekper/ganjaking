<?php
namespace ACP\Settings\ListScreen\HideOnScreen;

use ACP\Settings\ListScreen\HideOnScreen;

class InlineEdit extends HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_inline_edit', __( 'Inline Edit', 'codepress-admin-columns' ) );
	}

}