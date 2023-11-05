<?php

namespace Smush\Core;

class Smush_File {
	/**
	 * @var array
	 */
	private $supported_mime_types = array(
		'image/jpg',
		'image/jpeg',
		'image/x-citrix-jpeg',
		'image/gif',
		'image/png',
		'image/x-png',
	);

	public function get_supported_mime_types() {
		return $this->supported_mime_types;
	}
}