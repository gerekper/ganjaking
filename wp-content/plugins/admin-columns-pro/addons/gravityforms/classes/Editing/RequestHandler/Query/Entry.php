<?php

namespace ACA\GravityForms\Editing\RequestHandler\Query;

use AC\Request;
use ACA\GravityForms\Utils\Hooks;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response\QueryRows;
use GF_Entry_List_Table;

class Entry implements RequestHandler {

	/**
	 * @var GF_Entry_List_Table
	 */
	private $list_table;

	/**
	 * @var Request
	 */
	private $request;

	public function __construct( GF_Entry_List_Table $list_table ) {
		$this->list_table = $list_table;
	}

	public function handle( Request $request ) {
		$this->request = $request;

		$this->register();
	}

	public function register() {
		add_filter( 'gform_get_entries_args_entry_list', [ $this, 'set_query_vars' ] );
		add_action( Hooks::get_load_form_entries(), [ $this, 'send' ] );
	}

	/**
	 * @return int
	 */
	private function get_rows_per_iteration() {
		return ( new RowsPerIteration( $this->request ) )->apply_filters( 2000 );
	}

	/**
	 * @return int
	 */
	protected function get_offset() {
		$page = (int) $this->request->filter( 'ac_page', 1, FILTER_SANITIZE_NUMBER_INT );

		return ( $page - 1 ) * $this->get_rows_per_iteration();
	}

	public function send() {
		$this->list_table->prepare_items();

		$ids = wp_list_pluck( $this->list_table->items, 'id' );

		$response = new QueryRows( $ids, $this->get_rows_per_iteration() );
		$response->success();
	}

	public function set_query_vars( $args ) {
		$args['paging'] = [
			'offset'    => $this->get_offset(),
			'page_size' => $this->get_rows_per_iteration(),
		];

		return $args;
	}
}