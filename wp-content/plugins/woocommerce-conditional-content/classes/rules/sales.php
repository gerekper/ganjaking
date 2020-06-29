<?php

class WC_Conditional_Content_Rule_Sale_Status extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'sale_status' );
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
		    '0' => __( 'Not On Sale', 'wc_conditional_content' ),
		    '1' => __( 'On Sale', 'wc_conditional_content' )
		);

		return $options;
	}

	public function get_condition_input_type() {
		return 'Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		global $post;
		$result = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in = $product->is_on_sale();
			if ( $rule_data['operator'] == '==' ) {
				$result = $rule_data['condition'] == 1 ? $in : !$in;
			}

			if ( $rule_data['operator'] == '!=' ) {
				$result = !($rule_data['condition'] == 1 ? $in : !$in);
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Sale_Schedule extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'sale_schedule' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
		    '>=' => __( "starts", 'wc_conditional_content' ),
		    '=<' => __( "ends", 'wc_conditional_content' )
		);
		return $operators;
	}

	public function get_condition_input_type() {
		return 'Date';
	}

	public function is_match( $rule_data, $arguments = null ) {
		global $post;
		$product = wc_get_product( get_the_ID() );
		$result = false;
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			$start_date = get_post_meta( $product->get_id(), '_sale_price_dates_from', true );
			$end_date = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
			$date = strtotime($rule_data['condition']);

			switch ( $rule_data['operator'] ) {
				case '>=' :
					if ( $start_date ) {
						$result = $date >= strtotime( $start_date );
					}
					break;
				case '=<' :
					if ( $end_date ) {
						$result = $date <= strtotime( $end_date );
					}
					break;
				default:
					$result = false;
					break;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
