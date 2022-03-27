<?php

namespace ACP\Service;

use AC\Registrable;
use ACP\Access\PermissionChecker;
use ACP\Access\PermissionsStorage;

class Permissions implements Registrable {

	/**
	 * @var PermissionsStorage
	 */
	private $permission_storage;

	/**
	 * @var PermissionChecker
	 */
	private $permission_checker;

	public function __construct( PermissionsStorage $permission_storage, PermissionChecker $permission_checker ) {
		$this->permission_storage = $permission_storage;
		$this->permission_checker = $permission_checker;
	}

	public function register() {
		$this->set_permissions();
	}

	public function set_permissions() {
		if ( $this->permission_storage->exists() ) {
			return;
		}

		$this->permission_checker->apply();
	}

}