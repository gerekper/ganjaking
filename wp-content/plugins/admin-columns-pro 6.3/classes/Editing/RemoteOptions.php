<?php

namespace ACP\Editing;

use AC;

interface RemoteOptions {

	/**
	 * @param int|null $id
	 *
	 * @return AC\Helper\Select\Options
	 */
	public function get_remote_options( $id = null );

}