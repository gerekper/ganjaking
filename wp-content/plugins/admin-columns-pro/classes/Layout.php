<?php

namespace ACP;

class Layout {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string Nice name
	 */
	private $name;

	/**
	 * @var string[] User roles
	 */
	private $roles = array();

	/**
	 * @var int[] User ID's
	 */
	private $users = array();

	/**
	 * @var bool
	 */
	private $read_only = false;

	public function __construct( $args ) {
		$this->set_name( __( 'Default' ) );
		$this->populate( $args );
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	private function populate( $args ) {
		foreach ( $args as $key => $value ) {
			$method = 'set_' . $key;

			if ( method_exists( $this, $method ) ) {
				call_user_func( array( $this, $method ), $value );
			}
		}

		return $this;
	}

	/**
	 * @return bool
	 */
	public function is_read_only() {
		return $this->read_only;
	}

	/**
	 * @param bool $read_only
	 *
	 * @return $this
	 */
	public function set_read_only( $read_only ) {
		$this->read_only = (bool) $read_only;

		return $this;
	}

	/**
	 * @param $id
	 */
	public function set_id( $id ) {
		$this->id = (string) $id;
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		if ( is_scalar( $name ) ) {
			$this->name = $name;
		}
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	public function set_roles( $roles ) {
		if ( $roles && is_array( $roles ) ) {
			$this->roles = array_map( 'strval', array_filter( $roles ) );
		}
	}

	public function get_roles() {
		return $this->roles;
	}

	public function set_users( $users ) {
		if ( $users && is_array( $users ) ) {
			$this->users = array_map( 'intval', array_filter( $users ) );
		}
	}

	public function get_users() {
		return $this->users;
	}

	/**
	 * @return bool True when eligible
	 */
	public function is_current_user_eligible() {

		// Roles
		if ( ! empty( $this->roles ) ) {
			foreach ( $this->roles as $role ) {
				if ( current_user_can( $role ) ) {
					return true;
				}
			}
		}

		// Users
		if ( ! empty( $this->users ) ) {
			foreach ( $this->users as $user_id ) {
				if ( $user_id === get_current_user_id() ) {
					return true;
				}
			}
		}

		// Both
		if ( empty( $this->roles ) && empty( $this->users ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param \WP_User $user
	 *
	 * @return string
	 */
	private function get_user_name( $user ) {
		return ucfirst( ac_helper()->user->get_display_name( $user, 'first_last_name' ) );
	}

	/**
	 * @return string
	 */
	public function get_title_description() {
		$description = array();

		if ( ! empty( $this->roles ) ) {
			if ( 1 == count( $this->roles ) ) {
				$_roles = get_editable_roles();
				$role = $this->roles[0];
				$description[] = isset( $_roles[ $role ] ) ? $_roles[ $role ]['name'] : $role;
			} else {
				$description[] = __( 'Roles', 'codepress-admin-columns' );
			}
		}

		if ( ! empty( $this->users ) ) {
			if ( 1 == count( $this->users ) ) {
				$user = get_userdata( $this->users[0] );
				$description[] = $user ? $this->get_user_name( $user ) : __( 'User', 'codepress-admin-columns' );
			} else {
				$description[] = __( 'Users' );
			}
		}

		return implode( ' & ', array_filter( $description ) );
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

}