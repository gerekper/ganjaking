<?php

namespace ACA\GravityForms\Editing\TableRows;

use ACA\GravityForms\Utils\Hooks;
use ACP;

class Entry extends ACP\Editing\Ajax\TableRows {

	public function register() {
		add_action( Hooks::get_load_form_entries(), [ $this, 'handle_request' ] );
	}

}