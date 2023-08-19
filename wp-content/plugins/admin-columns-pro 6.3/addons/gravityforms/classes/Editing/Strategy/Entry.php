<?php

namespace ACA\GravityForms\Editing\Strategy;

use ACA\GravityForms;
use ACP;
use ACP\Editing\RequestHandler;
use GF_Entry_List_Table;
use GFCommon;

class Entry implements ACP\Editing\Strategy {

	/**
	 * @var GF_Entry_List_Table
	 */
	private $list_table;

	public function __construct( GF_Entry_List_Table $list_table ) {
		$this->list_table = $list_table;
	}

	public function user_can_edit(): bool {
		return GFCommon::current_user_can_any( GravityForms\Capabilities::EDIT_ENTRIES );
	}

	public function user_can_edit_item( int $id ): bool {
		return $this->user_can_edit();
	}

	public function get_query_request_handler(): RequestHandler {
		return new GravityForms\Editing\RequestHandler\Query\Entry( $this->list_table );
	}

}