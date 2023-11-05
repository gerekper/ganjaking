<?php

namespace Smush\Core;

class Backup_Size {
	private $dir;

	private $file;

	private $width;

	private $height;

	public function __construct( $dir ) {
		$this->dir = $dir;
	}

	/**
	 * @return mixed
	 */
	public function get_file() {
		return $this->file;
	}

	/**
	 * @param mixed $file
	 */
	public function set_file( $file ) {
		$this->file = $file;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_width() {
		return $this->width;
	}

	/**
	 * @param mixed $width
	 */
	public function set_width( $width ) {
		$this->width = $width;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_height() {
		return $this->height;
	}

	/**
	 * @param mixed $height
	 */
	public function set_height( $height ) {
		$this->height = $height;

		return $this;
	}

	public function from_array( $array ) {
		$this->set_file( (string) $this->get_array_value( $array, 'file' ) );
		$this->set_width( (int) $this->get_array_value( $array, 'width' ) );
		$this->set_height( (int) $this->get_array_value( $array, 'height' ) );
	}

	public function to_array() {
		return array(
			'file'   => $this->get_file(),
			'width'  => $this->get_width(),
			'height' => $this->get_height(),
		);
	}

	public function get_file_path() {
		$file_name = $this->get_file();

		return path_join( $this->dir, $file_name );
	}

	private function get_array_value( $array, $key ) {
		return isset( $array[ $key ] ) ? $array[ $key ] : null;
	}

	public function file_exists() {
		return file_exists( $this->get_file_path() );
	}
}