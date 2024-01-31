<?php

namespace Yoast\WP\SEO\Premium\Exceptions\Remote_Request;

use Throwable;

/**
 * Class to manage a 402 - payment required response.
 */
class Payment_Required_Exception extends Remote_Request_Exception {

	/**
	 * The missing plugin licenses.
	 *
	 * @var array
	 */
	private $missing_licenses;

	/**
	 * Payment_Required_Exception constructor.
	 *
	 * @param string    $message          The error message.
	 * @param int       $code             The error status code.
	 * @param Throwable $previous         The previously thrown exception.
	 * @param array     $missing_licenses The missing plugin licenses.
	 */
	public function __construct( $message = '', $code = 0, $previous = null, $missing_licenses = [] ) {
		$this->missing_licenses = $missing_licenses;
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Gets the missing plugin licences.
	 *
	 * @return array The missing plugin licenses.
	 */
	public function get_missing_licenses() {
		return $this->missing_licenses;
	}
}
