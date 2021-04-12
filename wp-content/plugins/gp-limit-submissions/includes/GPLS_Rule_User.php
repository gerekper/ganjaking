<?php

class GPLS_Rule_User extends GPLS_Rule {
	private $user_id;

	public static function load( $ruleData, $form_id = false ) {
		$rule          = new self;
		$rule->user_id = $ruleData['rule_user'];

		return $rule;
	}

	public function context() {

		// rule targets specific user, if this user is different we don't need to test the rule
		if ( $this->user_id != 'all' ) {
			if ( $this->user_id != get_current_user_id() ) {
				return false;
			}
		}

		return true;
	}

	public function query() {
		global $wpdb;

		if ( $this->user_id == 'all' ) {
			$user_id_to_check = get_current_user_id();
		} elseif ( $this->user_id == 'anon' ) {
			$user_id_to_check = null;
		} else {
			$user_id_to_check = $this->user_id;
		}

		if ( $user_id_to_check == null ) {
			$sql = 'e.created_by is NULL';
		} else {
			$sql = $wpdb->prepare( 'e.created_by = %s', $user_id_to_check );
		}

		return $sql;
	}

	public function render_option_fields( $gfaddon ) {
		$gfaddon->settings_select(
			array(
				'label'   => __( 'User', 'gp-limit-submissions' ),
				'name'    => 'rule_user_{i}',
				'class'   => 'rule_value_selector rule_user rule_user_{i} gpls-secondary-field',
				'choices' => $this->get_user_list(),
			)
		);
	}

	public function get_user_list() {

		$all = array(
			'label' => __( 'Each User', 'gp-limit-submissions' ),
			'value' => 'all',
		);

		$anonymous = array(
			'label' => __( 'Anonymous', 'gp-limit-submissions' ),
			'value' => 'anon',
		);

		/**
		 * Filter the arguments that will be passed to [get_users()](https://codex.wordpress.org/Function_Reference/get_users)
		 * when fetching users to display in the User rule.
		 *
		 * @since 1.0
		 *
		 * @param array $args An array of WP_User_Query args.
		 */
		$args  = apply_filters( 'gpls_rules_get_users_args', array( 'number' => 3000 ) );
		$users = get_users( $args );

		$choices   = array();
		$choices[] = $all;
		$choices[] = $anonymous;

		foreach ( $users as $u ) {
			$choice    = array(
				'label' => $u->display_name . ' (' . $u->user_email . ')',
				'value' => $u->ID,
			);
			$choices[] = $choice;
		}

		return $choices;
	}

	public function get_type() {
		return 'user';
	}
}
