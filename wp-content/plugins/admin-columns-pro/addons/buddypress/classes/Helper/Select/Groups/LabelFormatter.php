<?php
declare( strict_types=1 );

namespace ACA\BP\Helper\Select\Groups;

use BP_Groups_Group;

interface LabelFormatter {

	public function format_label( BP_Groups_Group $group ): string;

}