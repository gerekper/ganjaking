<?php
declare( strict_types=1 );

namespace ACA\MLA\Editing;

use ACP;
use ACP\Editing\RequestHandler;

class Strategy extends ACP\Editing\Strategy\Post {

	public function get_query_request_handler(): RequestHandler {
		global $wp_list_table;

		// Re-execute the query because the table object can be shared with custom plugins using the MLA filters/actions
		$wp_list_table->prepare_items();

		return parent::get_query_request_handler();
	}

}