<?php

namespace ACP\Editing\ApplyFilter;

use AC\ApplyFilter;
use AC\Request;

class RowsPerIteration implements ApplyFilter {

	/**
	 * @var Request
	 */
	private $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	public function apply_filters( $value ) {
		/**
		 * @deprecated 5.8
		 */
		$value = (int) apply_filters( 'acp/editing/bulk/editable_rows_per_iteration', (int) $value );

		return (int) apply_filters( 'acp/editing/rows_per_iteration', $value, $this->request );
	}

}