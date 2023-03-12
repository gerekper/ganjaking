<?php

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;
use ACA\MLA\ListScreen;

class ListScreens implements Registerable {

	public function register() {
		add_action( 'ac/list_screens', [ $this, 'register_list_screens' ], 11 );
	}

	public function register_list_screens() {
		AC\ListScreenTypes::instance()->register_list_screen( new ListScreen\MediaLibrary() );
	}

}