<?php

namespace ACP\Type;

use AC\Type\Url;

class SiteUrl implements Url {

	/**
	 * @var string
	 */
	private $url;

	public function __construct( $url ) {
		$this->url = (string) $url;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

}