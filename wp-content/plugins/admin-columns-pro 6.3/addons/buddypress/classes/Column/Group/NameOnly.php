<?php

namespace ACA\BP\Column\Group;

use AC;
use ACA\BP\Editing;
use ACP;

class NameOnly extends AC\Column
	implements ACP\Editing\Editable, ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-group_name' );
		$this->set_label( __( 'Name Only', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $group_id ) {
		return groups_get_group( $group_id )->name;
	}

	public function editing() {
		return new Editing\Service\Group\NameOnly();
	}

}