<?php

class WC_Conditional_Content_Rule_Wpml_Language extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'wpml_language' );
	}


	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$value = $rule_data['condition'];
			switch ( $rule_data['operator'] ) {
				case 'in' :
					$result = ICL_LANGUAGE_CODE == $value;
					break;
				case 'notin' :
					$result = ICL_LANGUAGE_CODE != $value;
					break;
				default :
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}
}
