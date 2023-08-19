<?php

namespace ACA\BP\Helper\Select\Formatter;

use AC;
use BP_Groups_Group;

class Group extends AC\Helper\Select\Formatter {

	/**
	 * @param BP_Groups_Group $group
	 *
	 * @return string
	 */
	public function get_label( $group ) {
		return sprintf( '#%s - %s', $group->id, $group->name );
	}

}