<?php

namespace ACP\Exception;

use RuntimeException;

class DirectoryNotWritableException extends RuntimeException {

	/**
	 * @var string
	 */
	private $path;

	public function __construct( $path, $code = 0 ) {
		parent::__construct( sprintf( 'Directory with path %s is not writable.', $path ), $code );

		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

}