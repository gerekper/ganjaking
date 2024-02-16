<?php

abstract class UEHttp{

	/**
	 * Create a new request instance.
	 *
	 * @return UEHttpRequest
	 */
	public static function make(){

		return new UEHttpRequest();
	}

}
