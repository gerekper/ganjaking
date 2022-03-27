<?php

namespace ACP\Access;

final class PermissionChecker {

	/**
	 * @var PermissionsStorage
	 */
	private $permissions_storage;

	/**
	 * @var Rule[]
	 */
	private $rules;

	public function __construct( PermissionsStorage $permissions_storage ) {
		$this->permissions_storage = $permissions_storage;
	}

	public function add_rule( Rule $rule ) {
		$this->rules[] = $rule;

		return $this;
	}

	public function apply() {
		$permissions = new Permissions();

		foreach ( $this->rules as $rule ) {
			foreach ( $rule->get_permissions()->to_array() as $permission ) {
				$permissions = $permissions->with_permission( $permission );
			}
		}

		$this->permissions_storage->save( $permissions );
	}

}