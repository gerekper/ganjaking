<?php

namespace ACP\Editing;

use ACP;

interface Strategy {

	/**
	 * @param int $id
	 *
	 * @return bool
	 */
	public function user_can_edit_item( $id );

	/**
	 * @return bool
	 */
	public function user_can_edit();

	/**
	 * Fetch rows
	 * @return RequestHandler
	 */
	public function get_query_request_handler();

}