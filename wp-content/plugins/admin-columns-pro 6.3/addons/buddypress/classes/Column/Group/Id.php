<?php

namespace ACA\BP\Column\Group;

use AC;
use ACP;

class Id extends AC\Column implements ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\IntegerFormattableTrait;

	public function __construct() {
		$this->set_type( 'column-group_id' );
		$this->set_label( __( 'ID', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $group_id ) {
		return $group_id;
	}

}