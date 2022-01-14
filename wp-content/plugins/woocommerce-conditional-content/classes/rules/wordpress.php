<?php

class WC_Conditional_Content_Rule_Page_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'page_select' );
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

		$pages  = get_posts( [
			'post_type' => 'page',
			'post_status' => 'publish',
			'nopaging' => true,
			'order' => 'ASC',
			'orderby' => 'menu_order, post_title'
		] );

		if ($pages) {
			foreach($pages as $page) {
				$result[$page->ID] = $page->post_title;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;

		if ( is_page() && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in = in_array(get_the_ID(), $rule_data['condition']);
			$result = $rule_data['operator'] == 'in' ? $in : !$in;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Post_Type_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'post_type_select' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "is one of", 'wc_conditional_content' ),
			'notin' => __( "is not one of", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$types = get_post_types([
			'public' => true,
		], 'objects');

		if ( $types ) {
			foreach ( $types as $type ) {
				$result[ $type->name ] = $type->labels->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;

		// get the post type for the current global object.
		$current_post_type = get_post_type();

		// check if it is in our list.
		$in = in_array($current_post_type, $rule_data['condition']);
		$result = $rule_data['operator'] == 'in' ? $in : !$in;

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Post_Location_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'post_location_select' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = [
			'singular' => 'Singular',
			'archive' => 'Archive / Category Page',
			'both' => 'Both'
		];

		return $result;
	}

	public function get_condition_input_type() {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$location = $rule_data['condition'];

		switch ($location) {
			case 'singular' :
				$result = is_singular();
				break;
			case 'archive':
				$result = is_archive();
				break;
			case 'both' :
				$result = is_singular() || is_archive();
				break;
			default:
				$result = false;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


