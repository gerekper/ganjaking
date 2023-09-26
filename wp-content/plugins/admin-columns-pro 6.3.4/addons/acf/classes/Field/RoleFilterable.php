<?php

namespace ACA\ACF\Field;

interface RoleFilterable {

	/**
	 * @return array
	 */
	public function get_roles();

	/**
	 * @return bool
	 */
	public function has_roles();

}