<?php

namespace ACA\ACF\Field\Type;

use ACA\ACF\Field;

class User extends Field implements Field\Multiple, Field\RoleFilterable {

	use MultipleTrait;

	/**
	 * @return bool
	 */
	public function has_roles() {
		return isset( $this->settings['role'] ) && ! empty( $this->settings['role'] );
	}

	/**
	 * @return array
	 */
	public function get_roles() {
		return $this->has_roles()
			? (array) $this->settings['role']
			: [];
	}

}