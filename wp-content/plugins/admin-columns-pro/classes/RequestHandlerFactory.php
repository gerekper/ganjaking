<?php

namespace ACP;

use AC\Request;
use LogicException;

class RequestHandlerFactory {

	/**
	 * @var RequestHandler[]
	 */
	private $request_handlers;

	/**
	 * @var Request
	 */
	private $request;

	public function __construct( Request $request ) {
		$this->request = $request;
	}

	public function add( $action, RequestHandler $request_handler ) {
		$this->request_handlers[ $action ] = $request_handler;

		return $this;
	}

	public function is_request() {
		return null !== $this->get_request_handler();
	}

	/**
	 * @return RequestHandler|null
	 */
	private function get_request_handler() {
		$action = $this->request->get( 'action' );

		return isset( $this->request_handlers[ $action ] )
			? $this->request_handlers[ $action ]
			: null;
	}

	/**
	 * @return RequestHandler
	 */
	public function create() {
		if ( ! $this->is_request() ) {
			throw new LogicException( 'Invalid request.' );
		}

		return $this->get_request_handler();
	}

}