<?php

class WC_Conditional_Content_Rule_Product_Select extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_select' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Product_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result  = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$in     = in_array( $product->get_id(), $rule_data['condition'] );
			$result = $rule_data['operator'] == 'in' ? $in : ! $in;
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Type extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_type' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "is", 'wc_conditional_content' ),
			'notin' => __( "is not", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$terms = wc_conditional_content_get_product_types();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {

		$result  = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$product_types = wp_get_post_terms( $product->get_id(), 'product_type', array( 'fields' => 'ids' ) );
			if ($product_types && !is_wp_error($product_types)) {
				$in     = count( array_intersect( $product_types, $rule_data['condition'] ) ) > 0;
				$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Category extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_category' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "in", 'wc_conditional_content' ),
			'notin' => __( "not in", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$terms = wc_conditional_content_get_all_product_categories();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$product = wc_get_product( get_the_ID() );
		$result  = false;

		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$terms  = $product->get_category_ids();
			if ($terms && !is_wp_error($terms) && is_array($terms)) {
				$in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
				$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Attribute extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_attribute' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "has", 'wc_conditional_content' ),
			'notin' => __( "does not have", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		global $woocommerce;

		$result = array();

		$attribute_taxonomies = WC_Conditional_Content_Compatibility::wc_get_attribute_taxonomies();

		if ( $attribute_taxonomies ) {
			//usort($attribute_taxonomies, array(&$this, 'sort_attribute_taxonomies'));

			foreach ( $attribute_taxonomies as $tax ) {
				$attribute_taxonomy_name = WC_Conditional_Content_Compatibility::wc_attribute_taxonomy_name( $tax->attribute_name );
				if ( taxonomy_exists( $attribute_taxonomy_name ) ) {
					$terms = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
					if ( $terms && !is_wp_error($terms) ) {
						foreach ( $terms as $term ) {
							$result[ $attribute_taxonomy_name . '|' . $term->term_id ] = $tax->attribute_name . ': ' . $term->name;
						}
					}
				}
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function sort_attribute_taxonomies( $taxa, $taxb ) {
		return strcmp( $taxa->attribute_name, $taxb->attribute_name );
	}

	public function is_match( $rule_data, $arguments = null ) {
		$product = wc_get_product( get_the_ID() );
		$result  = false;

		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {

			foreach ( $rule_data['condition'] as $condition ) {

				$term_data = explode( '|', $condition );

				$attribute_taxonomy_name = $term_data[0];
				$term_id                 = $term_data[1];

				$post_terms = wp_get_post_terms( $product->get_id(), $attribute_taxonomy_name, array( 'fields' => 'ids' ) );
				if ( $post_terms && ! is_wp_error( $post_terms ) ) {
					$in     = in_array( $term_id, $post_terms );
					$result = $rule_data['operator'] == 'in' ? $in : ! $in;
				} else {
					$result = false;
				}
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}


class WC_Conditional_Content_Rule_Product_Tag extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_tag' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'in'    => __( "in", 'wc_conditional_content' ),
			'notin' => __( "not in", 'wc_conditional_content' ),
		);

		return $operators;
	}

	public function get_possibile_rule_values() {
		$result = array();

		$terms = wc_conditional_content_get_all_product_tags();
		if ( $terms && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$result[ $term->term_id ] = $term->name;
			}
		}

		return $result;
	}

	public function get_condition_input_type() {
		return 'Chosen_Select';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$product = wc_get_product( get_the_ID() );
		$result  = false;

		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$terms  = $product->get_tag_ids();
			if ($terms && !is_wp_error($terms) && is_array($terms)) {
				$in     = count( array_intersect( $terms, $rule_data['condition'] ) ) > 0;
				$result = $rule_data['operator'] == 'in' ? $in : ! $in;
			}
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}

class WC_Conditional_Content_Rule_Product_Price extends WC_Conditional_Content_Rule_Base {

	public function __construct() {
		parent::__construct( 'product_price' );
	}

	public function get_possibile_rule_operators() {
		$operators = array(
			'==' => __( "is equal to", 'wc_conditional_content' ),
			'!=' => __( "is not equal to", 'wc_conditional_content' ),
			'>'  => __( "is greater than", 'wc_conditional_content' ),
			'<'  => __( "is less than", 'wc_conditional_content' ),
			'>=' => __( "is greater or equal to", 'wc_conditional_content' ),
			'=<' => __( "is less or equal to", 'wc_conditional_content' )
		);

		return $operators;
	}

	public function get_condition_input_type() {
		return 'Text';
	}

	public function is_match( $rule_data, $arguments = null ) {
		$result  = false;
		$product = wc_get_product( get_the_ID() );
		if ( $product && isset( $rule_data['condition'] ) && isset( $rule_data['operator'] ) ) {
			$price = $product->get_price();
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
		}

		return $this->return_is_match( $result, $rule_data, $arguments );
	}

}
