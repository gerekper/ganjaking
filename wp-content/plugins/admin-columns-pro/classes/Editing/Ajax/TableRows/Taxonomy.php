<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;

final class Taxonomy extends TableRows {

	public function register() {
		add_action( 'parse_term_query', [ $this, 'handle_request' ] );
	}

}