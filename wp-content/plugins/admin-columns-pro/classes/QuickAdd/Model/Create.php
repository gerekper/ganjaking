<?php

namespace ACP\QuickAdd\Model;

use WP_User;

interface Create {

	/**
	 * @return int
	 */
	public function create();

	/**
	 * @param WP_User $user
	 *
	 * @return bool
	 */
	public function has_permission( WP_User $user );

}