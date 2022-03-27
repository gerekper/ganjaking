<?php

namespace ACP\QuickAdd\Admin\HideOnScreen;

use AC\ListScreen;
use ACP;

class QuickAdd extends ACP\Settings\ListScreen\HideOnScreen {

	public function __construct() {
		parent::__construct(
			'hide_new_inline',
			sprintf(
				'%s (%s)',
				__( 'Quick Add', 'codepress-admin-columns' ),
				__( 'Add Row', 'codepress-admin-columns' )
			)
		);
	}

	public function is_hidden( ListScreen $list_screen ) {
		return null === $list_screen->get_preference( $this->name ) || parent::is_hidden( $list_screen );
	}

}