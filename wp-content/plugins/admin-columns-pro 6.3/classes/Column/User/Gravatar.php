<?php

namespace ACP\Column\User;

use AC;
use ACP;

/**
 * @since 2.0
 */
class Gravatar extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-gravatar' );
		$this->set_label( __( 'Profile Picture', 'codepress-admin-columns' ) );
	}

	public function get_raw_value( $user_id ) {
		return get_avatar_url( $user_id );
	}

	public function register_settings() {
		$this->add_setting( new ACP\Settings\Column\Gravatar( $this ) );
	}

}