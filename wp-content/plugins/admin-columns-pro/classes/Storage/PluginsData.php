<?php

namespace ACP\Storage;

use AC\Storage\Option;

class PluginsData extends Option {

	public function __construct() {
		parent::__construct( 'acp_update_plugins_data' );
	}

}