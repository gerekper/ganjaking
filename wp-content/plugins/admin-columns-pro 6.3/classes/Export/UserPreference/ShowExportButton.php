<?php

namespace ACP\Export\UserPreference;

use AC\Preferences\Site;

class ShowExportButton extends Site {

	public function __construct() {
		parent::__construct( 'show_export_button' );
	}

}