<?php

class WC_Conditional_Content_Rule_Store_Order_Count extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'store_order_count' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>' => __( "is greater than", 'wc_conditional_content' ),
			'<' => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'<=' => __( "is less or equal to", 'wc_conditional_content' )
		);
		return $operators;
	}

	public function get_condition_input_type() {
		return 'Order_Status';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$value = $rule_data['condition']['qty'];
			$status = $rule_data['condition']['status'];

			$count = $this->get_order_count($status);

			switch ( $rule_data['operator'] ) {
				case '==' :
					$result = $count == $value;
					break;
				case '!=' :
					$result = $count != $value;
					break;
				case '>' :
					$result = $count > $value;
					break;
				case '<' :
					$result = $count < $value;
					break;
				case '>=' :
					$result = $count >= $value;
					break;
				case '<=' :
					$result = $count <= $value;
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

	private function get_order_count($status) {
		return count(wc_get_orders( array(
			'status' => $status,
			'return' => 'ids',
			'limit' => -1,
		)));
	}

}
