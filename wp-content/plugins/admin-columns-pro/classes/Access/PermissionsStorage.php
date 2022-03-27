<?php

namespace ACP\Access;

use AC\Storage\KeyValueFactory;
use AC\Storage\KeyValuePair;
use AC\Storage\Option;

final class PermissionsStorage {

	/**
	 * @var KeyValuePair
	 */
	private $storage;

	public function __construct( KeyValueFactory $storage_factory ) {
		$this->storage = $storage_factory->create( '_acp_access_permissions' );
	}

	/**
	 * @return Permissions
	 */
	public function retrieve() {
		$permissions = $this->storage->get( [
			Option::OPTION_DEFAULT => [],
		] );

		return new Permissions( $permissions ?: [] );
	}

	public function exists() {
		return false !== $this->storage->get();
	}

	public function save( Permissions $permissions ) {
		$this->storage->save( $permissions->to_array() );
	}

}