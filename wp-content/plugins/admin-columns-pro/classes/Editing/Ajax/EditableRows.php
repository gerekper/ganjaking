<?php

namespace ACP\Editing\Ajax;

use AC;
use AC\Response;
use ACP\Editing\Strategy;

abstract class EditableRows extends Request {

	/**
	 * @var Strategy
	 */
	protected $strategy;

	/**
	 * @param AC\Request $request
	 * @param Strategy   $strategy
	 */
	public function __construct( AC\Request $request, Strategy $strategy ) {
		parent::__construct( $request );

		$this->strategy = $strategy;
	}

	protected function get_action() {
		return 'get_editable_rows';
	}

	/**
	 * @param int $number
	 *
	 * @return int
	 */
	protected function get_offset( $number = null ) {
		if ( $number === null ) {
			$number = $this->get_editable_rows_per_iteration();
		}

		$page = $this->request->filter( 'ac_page', 1, FILTER_SANITIZE_NUMBER_INT );

		return ( $page - 1 ) * $number;
	}

	/**
	 * @return int
	 */
	protected function get_editable_rows_per_iteration() {
		return (int) apply_filters( 'acp/editing/bulk/editable_rows_per_iteration', 2000 );
	}

	/**
	 * @param int[] $rows
	 */
	protected function success( array $rows ) {
		$response = new Response\Json;
		$response->set_parameter( 'editable_rows', $rows )
		         ->set_parameter( 'rows_per_iteration', $this->get_editable_rows_per_iteration() )
		         ->success();
	}

	/**
	 * @param array $parameters
	 */
	protected function error( $parameters = [] ) {
		$response = new Response\Json;

		if ( $parameters ) {
			$response->set_parameters( $parameters );
		}

		$response->error();
	}

}