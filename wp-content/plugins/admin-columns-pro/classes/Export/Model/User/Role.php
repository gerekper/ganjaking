<?php

namespace ACP\Export\Model\User;

use AC\Column;
use ACP\RolesFactory;
use ACP\Export\Model;

/**
 * Role (default column) exportability model
 * @since 4.1
 */
class Role extends Model {

	/**
	 * @var bool
	 */
	private $allow_all_roles;

	public function __construct( Column $column, bool $allow_all_roles ) {
		parent::__construct( $column );
		$this->allow_all_roles = $allow_all_roles;
	}

	private function is_site_role( $role ) {
		$roles = ( new RolesFactory() )->create( $this->allow_all_roles );

		return in_array( $role, $roles, true );
	}

	public function get_value( $id ) {
		$user = get_userdata( $id );

		$roles = array_filter( $user->roles, [ $this, 'is_site_role' ] );

		return implode( ', ', ac_helper()->user->translate_roles( $roles ) );
	}

}