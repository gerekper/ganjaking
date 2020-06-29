<?php

class WC_Conditional_Content_Rule_Stock_Status extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'stock_status' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
		    '==' => __( "is", 'wc_conditional_content' ),
		    '!=' => __( "is not", 'wc_conditional_content' ),
		);
		return $operators;
	}

	public function get_possibile_rule_values() {
		$options = array(
		    '0' => __( 'Out of Stock', 'wc_conditional_content' ),
		    '1' => __( 'In Stock', 'wc_conditional_content' )
		);

		return $options;
	}

	public function get_condition_input_type() {
		return 'Select';
	}

	public function is_match( $rule_data ) {
		global $post;

		$result = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in = $product->is_in_stock();
			if ( $rule_data['operator'] == '==' ) {
				$result = $rule_data['condition'] == 1 ? $in : !$in;
			}

			if ( $rule_data['operator'] == '!=' ) {
				$result = !($rule_data['condition'] == 1 ? $in : !$in);
			}
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class WC_Conditional_Content_Rule_Stock_Level extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'stock_level' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
		    '==' => __( "is equal to", 'wc_conditional_content' ),
		    '!=' => __( "is not equal to", 'wc_conditional_content' ),
		    '>' => __( "is greater than", 'wc_conditional_content' ),
		    '<' => __( "is less than", 'wc_conditional_content' ),
		    '>=' => __( "is greater or equal to", 'wc_conditional_content' ),
		    '=<' => __( "is less or equal to", 'wc_conditional_content' )
		);
		return $operators;
	}

	public function get_condition_input_type() {
		return 'Text';
	}

	public function is_match( $rule_data ) {
		global $post;
		$result = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$stock = $product->get_stock_quantity();
			$value = (float) $rule_data['condition'];

			switch ( $rule_data['operator'] ) {
				case '==' :
					$result = $stock == $value;
					break;
				case '!=' :
					$result = $stock != $value;
					break;
				case '>' :
					$result = $stock > $value;
					break;
				case '<' :
					$result = $stock < $value;
					break;
				case '>=' :
					$result = $stock >= $value;
					break;
				case '<=' :
					$result = $stock <= $value;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data );
	}

}