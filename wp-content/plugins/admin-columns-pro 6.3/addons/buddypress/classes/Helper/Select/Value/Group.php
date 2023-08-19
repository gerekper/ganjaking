<?php

namespace ACA\BP\Helper\Select\Value;

use AC;
use BP_Groups_Group;

final class Group
	implements AC\Helper\Select\Value {

	/**
	 * @param BP_Groups_Group $group
	 *
	 * @return int
	 */
	public function get_value( $group ) {
		return $group->id;
	}

}