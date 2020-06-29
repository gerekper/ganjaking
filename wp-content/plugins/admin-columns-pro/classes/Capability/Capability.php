<?php

namespace ACP\Capability;

use WP_User;

abstract class Capability {

	/**
	 * @var WP_User
	 */
	protected $user;

	public function __construct( WP_User $user = null ) {
		if ( null === $user ) {
			$user = wp_get_current_user();
		}

		$this->user = $user;
	}

	/**
	 * @return bool
	 */
	public function is_administrator() {
		return is_super_admin( $this->user->ID ) || $this->user->has_cap( 'administrator' );
	}

}