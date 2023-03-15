<?php

namespace ACP\Access;

final class Permissions {

	const UPDATE = 'update';
	const USAGE = 'usage';

	/**
	 * @var array
	 */
	private $permissions;

	public function __construct( array $permissions = [] ) {
		$this->permissions = $permissions;
	}

	/**
	 * @param string $permission
	 *
	 * @return self
	 */
	public function with_permission( $permission ) {
		$permissions = $this->to_array();
		$permissions[] = $permission;

		return new self( $permissions );
	}

	/**
	 * @return array
	 */
	public function to_array() {
		$permissions = array_unique( $this->permissions );

		return array_filter( $permissions, function ( $permission ) {
			return in_array( $permission, [ self::USAGE, self::UPDATE ], true );
		} );
	}

	/**
	 * @param string $permission
	 *
	 * @return bool
	 */
	public function has_permission( $permission ) {
		return in_array( (string) $permission, $this->permissions, true );
	}

	/**
	 * @return bool
	 */
	public function has_usage_permission() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function has_updates_permission() {
		return $this->has_permission( self::UPDATE );
	}

}