<?php

namespace ACA\BP\Helper\Select\Groups\LabelFormatter;

use ACA\BP\Helper\Select\Groups\LabelFormatter;
use BP_Groups_Group;

class GroupName implements LabelFormatter {

	public function format_label( BP_Groups_Group $group ): string {
		return $group->name;
	}
}