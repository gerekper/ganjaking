<?php

class WC_Conditional_Content_Rule_Users_Role extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'users_role' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
		    'in' => __( "is", 'wc_conditional_content' ),
		    'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$editable_roles = get_editable_roles();

		if ( $editable_roles ) {
			foreach ( $editable_roles as $role => $details ) {
				$name = translate_user_role( $details['name'] );
				$result[$role] = $name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data ) {
		$result = false;
		if ( $rule_data['condition'] && is_array( $rule_data['condition'] ) ) {
			foreach ( $rule_data['condition'] as $role ) {
				$result |= current_user_can( $role );
			}
		}

		$result = $rule_data['operator'] == 'in' ? $result : !$result;
		return $this->return_is_match( $result, $rule_data );
	}

	public function sort_attribute_taxonomies( $taxa, $taxb ) {
		return strcmp( $taxa->attribute_name, $taxb->attribute_name );
	}

}

class WC_Conditional_Content_Rule_Users_User extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'users_user' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
		    'in' => __( "is", 'wc_conditional_content' ),
		    'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$users = get_users();

		if ( $users ) {
			foreach ( $users as $user ) {
				$result[$user->ID] = $user->display_name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data ) {
		$result = in_array( get_current_user_id(), $rule_data['condition'] );
		$result = $rule_data['operator'] == 'in' ? $result : !$result;

		return $this->return_is_match( $result, $rule_data );
	}
}