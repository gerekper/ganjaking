<?php

namespace ACP\Editing;

interface Strategy {

	public function user_can_edit_item( int $id ): bool;

	public function user_can_edit(): bool;

	public function get_query_request_handler(): RequestHandler;

}