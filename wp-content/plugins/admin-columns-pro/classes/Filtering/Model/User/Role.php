<?php

namespace ACP\Filtering\Model\User;

use ACP\Filtering\Model;
use WP_Roles;

class Role extends Model {

	public function get_filtering_vars( $vars ) {
		$vars['role'] = $this->get_filter_value();

		return $vars;
	}

	public function get_filtering_data() {
		$data = [];
		$roles = new WP_Roles();

		foreach ( $roles->roles as $key => $role ) {
			$data['options'][ $key ] = $role['name'];
		}

		return $data;
	}

}