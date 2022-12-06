<?php

namespace ACP\Admin\PageFactory;

use AC;
use ACP\Sorting\Admin\Section\ResetSorting;

class Settings extends AC\Admin\PageFactory\Settings {

	public function create() {
		$page = parent::create();
		$page->add_section( new ResetSorting(), 30 );

		return $page;
	}

}