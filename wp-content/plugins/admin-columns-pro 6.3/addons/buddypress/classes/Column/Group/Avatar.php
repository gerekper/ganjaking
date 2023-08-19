<?php

namespace ACA\BP\Column\Group;

use AC;

class Avatar extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-group_avatar' );
		$this->set_label( __( 'Avatar', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $group_id ) {
		return bp_core_fetch_avatar( [
			'item_id' => $group_id,
			'object'  => 'group',
		] );
	}

}