<?php

namespace ACA\MLA\Service;

use AC;
use AC\Registerable;
use ACA\MLA\ListScreenFactory\MediaLibraryFactory;

class ListScreens implements Registerable {

	public function register(): void
    {
		AC\ListScreenFactory\Aggregate::add( new MediaLibraryFactory() );
	}

}