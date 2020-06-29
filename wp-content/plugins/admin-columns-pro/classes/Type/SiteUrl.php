<?php

namespace ACP\Type;

class SiteUrl {

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var bool
	 */
	private $is_network;

	public function __construct( $url, $is_network ) {
		$this->url = (string) $url;
		$this->is_network = (bool) $is_network;
	}

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @return bool
	 */
	public function is_network() {
		return $this->is_network;
	}

}