<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\Response;

abstract class TableRows implements AC\Registerable {

	/**
	 * @var AC\Request
	 */
	protected $request;

	/**
	 * @var AC\ListScreenWP
	 */
	protected $list_screen;

	/**
	 * @param AC\Request      $request
	 * @param AC\ListScreenWP $list_screen
	 */
	public function __construct( AC\Request $request, AC\ListScreenWP $list_screen ) {
		$this->request = $request;
		$this->list_screen = $list_screen;
	}

	public function is_request() {
		return $this->request->get( 'ac_action' ) === 'get_table_rows';
	}

	public function handle_request() {
		check_ajax_referer( 'ac-ajax' );

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