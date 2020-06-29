<?php

namespace ACP;

use AC\Request;
use ACP\Exception\ControllerException;

abstract class Controller {

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @param Request $request
	 */
	public function __construct( Request $request ) {
		$this->request = $request;
	}

	/**
	 * @param $action
	 */
	public function dispatch( $action ) {
		$method = $action . '_action';

		if ( ! is_callable( [ $this, $method ] ) ) {
			throw ControllerException::from_invalid_action( $action );
		}

		$this->$method();
	}

}