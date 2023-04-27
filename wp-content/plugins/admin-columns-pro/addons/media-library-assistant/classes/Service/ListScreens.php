<?php

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;
use ACA\MLA\ListScreenFactory\MediaLibraryFactory;

class ListScreens implements Registerable {

	public function register() {
		AC\ListScreenFactory::add( new MediaLibraryFactory() );
	}

}