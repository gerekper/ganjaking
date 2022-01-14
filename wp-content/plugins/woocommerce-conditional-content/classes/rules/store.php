<?php

class WC_Conditional_Content_Rule_Store_Order_Count extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'store_order_count' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
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
			$value  = $rule_data['condition']['qty'];
			$status = $rule_data['condition']['status'];

			$count = $this->get_order_count( $status );

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

	private function get_order_count( $status ) {
		return count( wc_get_orders( array(
			'status' => $status,
			'return' => 'ids',
			'limit'  => - 1,
		) ) );
	}

}


class WC_Conditional_Content_Rule_Store_Order_History extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'store_order_history' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "has purchased", 'wc_conditional_content' ),
			'notin' => __( 'has not purchased', 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Product_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result  = false;
		$customer_billing_email = null;

		// check if this user purchased a product as a guest user.
		if (get_current_user_id() === 0) {
			try {
				$customer               = new WC_Customer( get_current_user_id(), true );

				// since billing is required use that for the purchase check.
				$customer_billing_email = $customer->get_billing_email();
			} catch(Exception $e) {

			}
		}

		$bought = false;
		if ( isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			foreach ( $rule_data['condition'] as $product_id ) {

				// check for variation parent, since those can be selected in the UI.
				$product = wc_get_product($product_id);
				if ($product->is_type('variation')) {
					if ( wc_customer_bought_product( $customer_billing_email, get_current_user_id(), $product->get_parent_id() ) ) {
						$bought = true;
					}
				}

				// check for the specific product, variation or not.
				if ( wc_customer_bought_product( $customer_billing_email, get_current_user_id(), $product_id ) ) {
					$bought = true;
				}
			}
			$result = $rule_data['operator'] == 'in' ? $bought : !$bought;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
