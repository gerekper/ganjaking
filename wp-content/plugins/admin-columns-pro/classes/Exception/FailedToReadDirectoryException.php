<?php

namespace ACP\Exception;

use RuntimeException;

class FailedToReadDirectoryException extends RuntimeException {

	/**
	 * @var string
	 */
	private $path;

	public function __construct( $path, $code = 0 ) {
		parent::__construct( sprintf( 'Could not read directory %s.', $path ), $code );

		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

}