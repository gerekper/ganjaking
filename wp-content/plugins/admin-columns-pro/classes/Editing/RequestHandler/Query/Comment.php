<?php

namespace ACP\Editing\RequestHandler\Query;

use AC;
use AC\Request;
use ACP\Editing\ApplyFilter\RowsPerIteration;
use ACP\Editing\RequestHandler;
use ACP\Editing\Response;
use WP_Comment_Query;

final class Comment implements RequestHandler {

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
		$handler->set_action( 'pre_get_comments' )
		        ->set_callback( [ $this, 'send' ] )
		        ->set_priority( PHP_INT_MAX - 100 );

		$this->handler = $handler;
	}

	public function handle( Request $request ) {
		$this->request = $request;
		$this->handler->register();
	}

	public function send( WP_Comment_Query $query ) {
		check_ajax_referer( 'ac-ajax' );

		$this->handler->deregister();

		$query->query_vars['fields'] = '*';
		$query->query_vars['number'] = $this->get_rows_per_iteration();
		$query->query_vars['offset'] = $this->get_offset();

		$comments = $query->get_comments();
		$comment_ids = wp_list_pluck( $comments, 'comment_ID' );
		$comment_ids = array_map( 'intval', $comment_ids );

		$response = new Response\QueryRows( $comment_ids, $this->get_rows_per_iteration() );
		$response->success();
	}

	private function get_rows_per_iteration(): int {
		return ( new RowsPerIteration( $this->request ) )->apply_filters( 2000 );
	}

	protected function get_offset(): int {
		$page = (int) $this->request->filter( 'ac_page', 1, FILTER_SANITIZE_NUMBER_INT );

		return ( $page - 1 ) * $this->get_rows_per_iteration();
	}

}