<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\Response;

abstract class TableRows extends Request {

	/**
	 * @var AC\ListScreenWP
	 */
	protected $list_screen;

	/**
	 * @param AC\Request      $request
	 * @param AC\ListScreenWP $list_screen
	 */
	public function __construct( AC\Request $request, AC\ListScreenWP $list_screen ) {
		parent::__construct( $request );

		$this->list_screen = $list_screen;
	}

	/**
	 * @return string
	 */
	protected function get_action() {
		return 'get_table_rows';
	}

	public function handle_request() {
		remove_action( 'parse_term_query', [ $this, __FUNCTION__ ] );

		$this->check_nonce();

		$ids = $this->request->filter( 'ac_ids', [], FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY );

		$response = new Response\Json();

		if ( ! $ids ) {
			$response->error();
		}

		$rows = [];

		foreach ( $ids as $id ) {
			$rows[ $id ] = $this->list_screen->get_single_row( $id );
		}

		$response->set_parameter( 'table_rows', $rows )
		         ->success();
	}

}