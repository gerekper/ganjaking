<?php

class GPLS_Rule_Role extends GPLS_Rule {
	private $role;

	public static function load( $ruleData, $form_id = false ) {

		$rule       = new self;
		$rule->role = $ruleData['rule_role'];

		return $rule;
	}

	public function context() {

		$current_user = wp_get_current_user();

		// test specific role set
		if ( $this->role != 'anonymous' ) {
			if ( ! in_array( $this->role, (array) $current_user->roles ) ) {
				// user does not have this role, so this limit cannot be applied to them
				return false;
			}
		}
		// test anonymous
		if ( $this->role == 'anonymous' && is_user_logged_in() ) {
			// rule only applies to anonymous users, but this user is logged in
			return false;
		}

		return true;
	}

	public function query() {
		global $wpdb;

		/**
		 * Apply role-specific limits per user or collectively for all users with this role.
		 *
		 * @since 1.0
		 *
		 * @param bool $is_per_user Deafults to true.
		 */
		$per_user = apply_filters( 'gpls_apply_role_limit_per_user', true );
		if ( $per_user ) {

			return $wpdb->prepare( 'e.created_by = %d', get_current_user_id() );

		} else {

			// Anonymous users are only supported as collective group (no way to identify individual anonymous users).
			if ( $this->role == 'anonymous' ) {
				return 'e.created_by IS NULL';
			}

			// get only users with this role
			$users = get_users( array( 'role' => $this->role ) );
			if ( empty( $users ) ) {
				return '';
			}

			// form an IN statement
			$user_count = count( $users );
			$user_list  = array();
			foreach ( $users as $u ) {
				$user_list[] = $u->ID;
			}

			$placeholders = array_fill( 0, $user_count, '%d' );
			$format       = implode( ', ', $placeholders );

			return $wpdb->prepare( "e.created_by IN($format)", $user_list );
		}

	}

	public function render_option_fields( $gfaddon ) {
		$gfaddon->settings_select(
			array(
				'label'   => __( 'Role', 'gp-limit-submissions' ),
				'name'    => 'rule_role_{i}',
				'class'   => 'rule_value_selector rule_role rule_role_{i} gpls-secondary-field',
				'choices' => $this->get_roles_list(),
			)
		);
	}

	public function get_roles_list() {

		$choices = array();
		/** This filter is documented in /includes/GPLS_Rule_Role.php */
		if ( apply_filters( 'gpls_apply_role_limit_per_user', true ) == false ) {
			$choices[] = array(
				'label' => 'Anonymous',
				'value' => 'anonymous',
			);
		}

		$roles = get_editable_roles();
		foreach ( $roles as $slug => $r ) {
			$choice    = array(
				'label' => $r['name'],
				'value' => $slug,
			);
			$choices[] = $choice;
		}

		return $choices;
	}

	public function get_type() {
		return 'role';
	}
}
