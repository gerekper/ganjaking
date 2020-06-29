<?php

class WC_Conditional_Content_Rule_Cart_Total extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_total' );
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
		global $woocommerce;

		$result = false;

		if ( !$woocommerce->cart->prices_include_tax ) {
			$price = $woocommerce->cart->cart_contents_total;
		} else {
			$price = $woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total;
		}

		$value = (float) $rule_data['condition'];
		switch ( $rule_data['operator'] ) {
			case '==' :
				$result = $price == $value;
				break;
			case '!=' :
				$result = $price != $value;
				break;
			case '>' :
				$result = $price > $value;
				break;
			case '<' :
				$result = $price < $value;
				break;
			case '>=' :
				$result = $price >= $value;
				break;
			case '<=' :
				$result = $price <= $value;
				break;
			default:
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class WC_Conditional_Content_Rule_Cart_Product extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_product' );
	}

	public function get_possibile_rule_operators() {

		$operators = array(
		    '<' => __( "contains less than", 'wc_conditional_content' ),
		    '>' => __( "contains at least", 'wc_conditional_content' ),
		    '==' => __( "contains exactly", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Cart_Product_Select';
	}

	public function is_match( $rule_data ) {
		global $woocommerce;

		$result = false;
		$cart_contents = $woocommerce->cart->get_cart();

		$products = $rule_data['condition']['products'];
		$quantity = $rule_data['condition']['qty'];
		$type = $rule_data['operator'];

		$found_quantity = 0;

		if ( $cart_contents && count( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				if ( in_array( $cart_item['product_id'], $products ) || (isset( $cart_item['variation_id'] ) && in_array( $cart_item['variation_id'], $products )) ) {
					$found_quantity += $cart_item['quantity'];
				}
			}
		}

		switch ( $type ) {
			case '<' :
				$result = $quantity >= $found_quantity;
				break;
			case '>' :
				$result = $quantity <= $found_quantity;
				break;
			case '==' :
				$result = $quantity == $found_quantity;
				break;
			default :
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}

class WC_Conditional_Content_Rule_Cart_Category extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'cart_category' );
	}

	public function get_possibile_rule_operators() {

		$operators = array(
		    '<' => __( "contains less than", 'wc_conditional_content' ),
		    '>' => __( "contains at least", 'wc_conditional_content' ),
		    '==' => __( "contains exactly", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$terms = get_terms( 'product_cat', array('hide_empty' => false) );
		if ( $terms && !is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[$term->term_id] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Cart_Category_Select';
	}

	public function is_match( $rule_data ) {
		global $woocommerce;

		$result = false;
		$cart_contents = $woocommerce->cart->get_cart();

		$categories = $rule_data['condition']['categories'];
		$quantity = $rule_data['condition']['qty'];
		$type = $rule_data['operator'];

		$found_quantity = 0;

		if ( $cart_contents && count( $cart_contents ) ) {
			foreach ( $cart_contents as $cart_item_key => $cart_item ) {
				$terms = wp_get_object_terms( $cart_item['product_id'], 'product_cat', array('fields' => 'ids') );
				if ( $terms && !is_wp_error( $terms ) && count( array_intersect( $terms, $categories ) ) > 0 ) {
					$found_quantity += $cart_item['quantity'];
				}
			}
		}

		switch ( $type ) {
			case '<' :
				$result = $quantity > $found_quantity;
				break;
			case '>' :
				$result = $quantity <= $found_quantity;
				break;
			case '==' :
				$result = $quantity == $found_quantity;
				break;
			default :
				$result = false;
				break;
		}

		return $this->return_is_match( $result, $rule_data );
	}

}