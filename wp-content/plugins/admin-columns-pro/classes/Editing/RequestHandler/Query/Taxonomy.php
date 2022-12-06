<?php

namespace ACP\Editing\RequestHandler\Query;

use AC;
use AC\Request;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response;
use WP_Term_Query;

final class Taxonomy implements RequestHandler {

	/**
	 * @var Request
	 */
	private $request;

	/**
	 * @var AC\Ajax\Handler
	 */
	private $handler;

	public function __construct() {
		$handler = new AC\Ajax\Handler( false );
		$handler->set_action( 'parse_term_query' )
		        ->set_callback( [ $this, 'send' ] )
		        ->set_priority( PHP_INT_MAX - 100 );

		$this->handler = $handler;
	}

	public function handle( Request $request ) {
		$this->request = $request;

		$this->handler->register();
	}

	public function send( WP_Term_Query $query ) {
		check_ajax_referer( 'ac-ajax' );

		$this->handler->deregister();

		$query->query_vars['number'] = $this->get_rows_per_iteration();
		$query->query_vars['offset'] = $this->get_offset();
		$query->query_vars['fields'] = 'all';

		$query = new WP_Term_Query( $query->query_vars );

		$terms = $query->get_terms();
		$term_ids = wp_list_pluck( $terms, 'term_id' );

		$response = new Response\QueryRows( $term_ids, $this->get_rows_per_iteration() );
		$response->success();
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

}