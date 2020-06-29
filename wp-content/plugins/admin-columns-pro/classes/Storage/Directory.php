<?php

namespace ACP\Storage;

use ACP\Exception;
use ACP\Exception\FailedToCreateDirectoryException;
use DirectoryIterator;
use SplFileInfo;

final class Directory {

	/**
	 * @var SplFileInfo
	 */
	private $directory;

	/**
	 * @param string $path
	 */
	public function __construct( $path ) {
		$this->directory = new SplFileInfo( $path );
	}

	/**
	 * @return bool
	 */
	public function exists() {
		return $this->directory->getRealPath() !== false;
	}

	/**
	 * @return bool
	 */
	public function is_readable() {
		return $this->exists() && is_readable( $this->directory->getRealPath() );
	}

	/**
	 * @return SplFileInfo
	 */
	public function get_info() {
		return $this->directory;
	}

	/**
	 * @return void
	 */
	public function create() {
		if ( $this->exists() ) {
			return;
		}

		// Try to set the permissions too 0755 unless the system has wider permissions, recursive too
		$result = @mkdir( $this->directory->getPathname(), ( fileperms( ABSPATH ) & 0777 | 0755 ), true );

		if ( ! $result ) {
			throw new FailedToCreateDirectoryException( $this->directory->getPathname() );
		}
	}

	/**
	 * @param string $path
	 *
	 * @return bool
	 */
	public function has_path( $path ) {
		return false !== strpos( $this->directory->getPathname(), $path );
	}

	/**
	 * Proxy method to get the (real) path from the directory
	 * @return string
	 */
	public function get_path() {
		return $this->directory->getRealPath();
	}

	/**
	 * Proxy method to get an iterator for the directory contents
	 * @return DirectoryIterator
	 */
	public function get_files() {
		if ( ! $this->is_readable() ) {
			throw new Exception\FailedToReadDirectoryException( $this->directory->getPathname() );
		}

		return new DirectoryIterator( $this->get_path() );
	}

}