<?php

namespace ACA\BP\Column\Group;

use AC;
use ACP;

class Creator extends AC\Column implements ACP\ConditionalFormat\Formattable {

	use ACP\ConditionalFormat\ConditionalFormatTrait;

	public function __construct() {
		$this->set_type( 'column-group_creator' );
		$this->set_label( __( 'Creator', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $group_id ) {
		return groups_get_group( $group_id )->creator_id;
	}

	public function register_settings() {
		$this->add_setting( new ACP\Settings\Column\User( $this ) );
	}

}