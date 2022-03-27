<?php

namespace ACP\Admin\PageFactory;

use AC;
use ACP\Sorting\Admin\Section\ResetSorting;
use ACP\Sorting\Admin\ShowAllResults;

class Settings extends AC\Admin\PageFactory\Settings {

	public function create() {
		$page = parent::create();

		$page->add_section( new ResetSorting(), 30 );

		$general_section = $page->get_section( AC\Admin\Section\General::NAME );

		if ( $general_section instanceof AC\Admin\Section\General ) {
			$general_section->add_option( new ShowAllResults() );
		}

		return $page;
	}

}