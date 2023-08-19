<?php

namespace ACP\Editing\HideOnScreen;

use ACP;

class InlineEdit extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct( 'hide_inline_edit', __( 'Inline Edit', 'codepress-admin-columns' ) );
	}

}