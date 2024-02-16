<?php

class UEHttpRequestException extends UEHttpException{

	private $request;

	/**
	 * Create a new class instance.
	 *
	 * @param string $message
	 * @param UEHttpRequest $request
	 *
	 * @return void
	 */
	public function __construct($message, $request){

		$this->request = $request;

		parent::__construct($message);
	}

	/**
	 * Get the request instance.
	 *
	 * @return UEHttpRequest
	 */
	public function getRequest(){

		return $this->request;
	}

}
