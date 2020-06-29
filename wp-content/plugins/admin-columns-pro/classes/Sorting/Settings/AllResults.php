<?php

namespace ACP\Sorting\Settings;

use AC\Settings\Option;

class AllResults extends Option {

	public function __construct() {
		parent::__construct( 'show_all_results' );
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return '1' === $this->get();
	}

}