<?php

namespace ACA\MLA\ListScreenFactory;

use AC\ListScreen;
use AC\ThirdParty\MediaLibraryAssistant\ListScreenFactory;
use ACA\MLA\ListScreen\MediaLibrary;

class MediaLibraryFactory extends ListScreenFactory {

	protected function create_list_screen( string $key ): ListScreen {
		return new MediaLibrary();
	}

}