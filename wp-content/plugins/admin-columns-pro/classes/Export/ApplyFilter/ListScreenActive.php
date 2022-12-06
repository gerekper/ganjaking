<?php

namespace ACP\Export\ApplyFilter;

use AC\ApplyFilter;
use AC\ListScreen;

class ListScreenActive implements ApplyFilter {

	/**
	 * @var ListScreen
	 */
	private $list_screen;

	public function __construct( ListScreen $list_screen ) {
		$this->list_screen = $list_screen;
	}

	public function apply_filters( $value ) {
		return (bool) apply_filters( 'acp/export/is_active', $value, $this->list_screen );
	}

}