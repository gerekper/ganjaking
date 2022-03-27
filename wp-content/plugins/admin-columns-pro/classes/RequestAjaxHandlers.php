<?php

namespace ACP;

class RequestAjaxHandlers {

	/**
	 * @var RequestAjaxHandler[]
	 */
	private $request_handlers;

	public function add( $action, RequestAjaxHandler $request_handler ) {
		$this->request_handlers[ $action ] = $request_handler;

		return $this;
	}

	/**
	 * @return RequestAjaxHandler[]
	 */
	public function all() {
		return $this->request_handlers;
	}

}