<?php

namespace ACP\Editing\Ajax\TableRows;

use ACP\Editing\Ajax\TableRows;
use WP_Query;

final class Media extends TableRows {

	public function register() {
		add_action( 'pre_get_posts', [ $this, 'pre_handle_request' ] );
	}

	/**
	 * @param WP_Query $query
	 */
	public function pre_handle_request( WP_Query $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		$this->handle_request();
	}

}