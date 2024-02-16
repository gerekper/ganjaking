<?php

class UEHttpResponse{

	private $status;
	private $headers;
	private $body;

	/**
	 * Create a new class instance.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function __construct($data){

		$this->status = UniteFunctionsUC::getVal($data, "status", 0);
		$this->headers = UniteFunctionsUC::getVal($data, "headers", array());
		$this->body = UniteFunctionsUC::getVal($data, "body");
	}

	/**
	 * Get the status code of the response.
	 *
	 * @return int
	 */
	public function status(){

		return $this->status;
	}

	/**
	 * Get the headers of the response.
	 *
	 * @return array
	 */
	public function headers(){

		return $this->headers;
	}

	/**
	 * Get the raw body of the response.
	 *
	 * @return string
	 */
	public function body(){

		return $this->body;
	}

	/**
	 * Get the JSON decoded body of the response.
	 *
	 * @return mixed
	 * @throws UEHttpResponseException
	 */
	public function json(){

		$data = json_decode($this->body(), true);

		if($data === null)
			throw new UEHttpResponseException("Unable to parse the JSON body.", $this);

		return $data;
	}

}
