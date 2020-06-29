<?php

namespace ACP\Editing\Ajax\EditableRows;

use AC;
use ACP;
use ACP\Editing\Ajax\EditableRows;
use ACP\Editing\Strategy;
use WP_Term_Query;

final class Taxonomy extends EditableRows {

	/**
	 * @var AC\Ajax\Handler
	 */
	private $handler;

	/**
	 * @param AC\Request $request
	 * @param Strategy   $strategy
	 */
	public function __construct( AC\Request $request, Strategy $strategy ) {
		$handler = new AC\Ajax\Handler( false );
		$handler->set_action( 'parse_term_query' )
		        ->set_callback( [ $this, 'send_editable_rows' ] )
		        ->set_priority( PHP_INT_MAX - 100 );

		$this->handler = $handler;

		parent::__construct( $request, $strategy );
	}

	public function register() {
		$this->handler->register();
	}

	/**
	 * @param WP_Term_Query $query
	 */
	public function send_editable_rows( WP_Term_Query $query ) {
		$this->check_nonce();

		$this->handler->deregister();

		$query->query_vars['number'] = $this->get_editable_rows_per_iteration();
		$query->query_vars['offset'] = $this->get_offset();
		$query->query_vars['fields'] = 'all';

		$query = new WP_Term_Query( $query->query_vars );

		$editable_rows = [];

		foreach ( $query->get_terms() as $term ) {
			if ( $this->strategy->user_has_write_permission( $term ) ) {
				$editable_rows[] = $term->term_id;
			}
		}

		$this->success( $editable_rows );
	}

}