<?php

namespace ACA\MLA\ListScreenFactory;

use AC;
use AC\ThirdParty\MediaLibraryAssistant\ListScreenFactory;
use ACA\MLA\ListScreen\MediaLibrary;

class MediaLibraryFactory extends ListScreenFactory {

	protected function create_list_screen(): AC\ThirdParty\MediaLibraryAssistant\ListScreen\MediaLibrary {
		return new MediaLibrary();
	}

}