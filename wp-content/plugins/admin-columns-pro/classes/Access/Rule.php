<?php

namespace ACP\Access;

interface Rule {

	/**
	 * @return Permissions
	 */
	public function get_permissions();

}