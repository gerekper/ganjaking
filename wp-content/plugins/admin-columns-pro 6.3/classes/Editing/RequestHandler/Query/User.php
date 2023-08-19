<?php

namespace ACP\Editing\RequestHandler\Query;

use AC\Request;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response;
use WP_User_Query;

final class User implements RequestHandler {

	/**
	 * @var Request
	 */
	private $request;

	public function handle( Request $request ) {
		$this->request = $request;

		$this->register();
	}

	private function register() {
		add_action( 'users_list_table_query_args', [ $this, 'send' ], PHP_INT_MAX - 100 );
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

	public function send( array $args ) {
		$query = new WP_User_Query( array_merge( $args, [
			'fields' => 'all',
			'number' => $this->get_rows_per_iteration(),
			'offset' => $this->get_offset(),
		] ) );

		$users = $query->get_results();
		$user_ids = wp_list_pluck( $users, 'ID' );

		$response = new Response\QueryRows( $user_ids, $this->get_rows_per_iteration() );
		$response->success();
	}

}