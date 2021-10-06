<?php

namespace ACP\Editing\Middleware;

use AC\Middleware;
use AC\Request;
use ACP\Editing\ApplyFilter;

class SaveValue implements Middleware {

	/**
	 * @var ApplyFilter\SaveValue
	 */
	private $filter;

	public function __construct( ApplyFilter\SaveValue $filter ) {
		$this->filter = $filter;
	}

	public function handle( Request $request ) {
		$request->get_parameters()->set( 'value', $this->filter->apply_filters( $request->get( 'value' ) ) );
	}

}