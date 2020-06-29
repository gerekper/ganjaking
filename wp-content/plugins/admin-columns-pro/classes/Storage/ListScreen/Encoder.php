<?php

namespace ACP\Storage\ListScreen;

use AC\ListScreen;

final class Encoder {

	/**
	 * @var string
	 */
	private $version;

	/**
	 * @param string $version
	 */
	public function __construct( $version ) {
		$this->version = $version;
	}

	/**
	 * @param ListScreen $list_screen
	 *
	 * @return array
	 */
	public function encode( ListScreen $list_screen ) {
		return [
			'version'  => $this->version,
			'title'    => $list_screen->get_title(),
			'type'     => $list_screen->get_key(),
			'id'       => $list_screen->get_layout_id(),
			'updated'  => $list_screen->get_updated()->getTimestamp(),
			'columns'  => $list_screen->get_settings(),
			'settings' => $list_screen->get_preferences(),
		];
	}

}