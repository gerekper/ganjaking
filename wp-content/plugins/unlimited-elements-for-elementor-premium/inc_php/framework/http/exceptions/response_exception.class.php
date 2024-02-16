<?php

class UEHttpResponseException extends UEHttpException{

	private $response;

	/**
	 * Create a new class instance.
	 *
	 * @param string $message
	 * @param UEHttpResponse $response
	 *
	 * @return void
	 */
	public function __construct($message, $response){

		$this->response = $response;

		parent::__construct($message, $response->status());
	}

	/**
	 * Get the response instance.
	 *
	 * @return UEHttpResponse
	 */
	public function getResponse(){

		return $this->response;
	}

}
