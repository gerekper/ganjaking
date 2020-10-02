<?php

class WC_Bulk_Variation_Array_Filter {

	private $available_variations;
	public $matched = array();
	public $row_attribute_name;
	public $column_attribute_name;
	public $row_value = '';
	public $column_value = '';

	public function __construct( $row_attribute_name, $column_attribute_name, $avaialble_variations ) {
		$this->row_attribute_name    = $row_attribute_name;
		$this->column_attribute_name = $column_attribute_name;
		$this->available_variations  = $avaialble_variations;
	}

	public function get_matches( $row_value, $column_value ) {
		$this->matched = array( 0 => false, 1 => false, 2 => false );

		foreach ( $this->available_variations as $variation ) {
			if ( md5( $variation['attributes'][ $this->row_attribute_name ] ) == md5( $row_value ) && md5( $variation['attributes'][ $this->column_attribute_name ] ) == md5( $column_value ) ) {
				$this->matched[0] = $variation;
			}

			if ( $variation['attributes'][ $this->row_attribute_name ] == $row_value && empty( $variation['attributes'][ $this->column_attribute_name ] ) ) {
				$this->matched[1] = $variation;
			}

			if ( $variation['attributes'][ $this->column_attribute_name ] == $column_value && empty( $variation['attributes'][ $this->row_attribute_name ] ) ) {
				$this->matched[1] = $variation;
			}

			if ( empty( $variation['attributes'][ $this->row_attribute_name ] ) && empty( $variation['attributes'][ $this->column_attribute_name ] ) ) {
				$this->matched[2] = $variation;
			}
		}

		$matches = array_filter( $this->matched );

		return array_shift( $matches );
	}

}

function woocommerce_bulk_variations_create_matrix( $post_id ) {
	$_product = wc_get_product( $post_id );

	$attributes = $_product->get_attributes();

	$row_attribute    = ( wc_bv_get_post_meta( $_product->get_id(), '_bv_y', true ) );
	$column_attribute = ( wc_bv_get_post_meta( $_product->get_id(), '_bv_x', true ) );

	$av_temp = $_product->get_variation_attributes();

	$av = array();
	if ( isset( $attributes[ $row_attribute ] ) && $attributes[ $row_attribute ]['is_taxonomy'] ) {
		$row_term_values = wc_get_product_terms( $_product->get_id(), $row_attribute, array( 'fields' => 'all' ) );

		foreach ( $row_term_values as $row_term_value ) {
			if ( in_array( $row_term_value->slug, $av_temp[ $row_attribute ] ) ) {
				$av[ $row_attribute ][] = $row_term_value->slug;
			}
		}
	} else {
		$av[ $row_attribute ] = $av_temp[ $row_attribute ];
	}

	if ( isset( $attributes[ $column_attribute ] ) && $attributes[ $column_attribute ]['is_taxonomy'] ) {
		$column_term_values = wc_get_product_terms( $_product->get_id(), $column_attribute, array( 'fields' => 'all' ) );

		foreach ( $column_term_values as $column_term_value ) {
			if ( in_array( $column_term_value->slug, $av_temp[ $column_attribute ] ) ) {
				$av[ $column_attribute ][] = $column_term_value->slug;
			}
		}
	} else {
		$av[ $column_attribute ] = $av_temp[ $column_attribute ];
	}


	$grid = array();
	foreach ( $av[ $row_attribute ] as $row_value ) {
		foreach ( $av[ $column_attribute ] as $column_value ) {
			$grid[ ( $row_value ) ][ ( $column_value ) ] = null;
		}
	}


	//Now sanitize the attributes, since $product->get_available_variations returns the variations sanitized, but get_variation_attributes does not
	$row_attribute    = sanitize_title( $row_attribute );
	$column_attribute = sanitize_title( $column_attribute );

	$pv     = $_product->get_available_variations();
	$filter = new WC_Bulk_Variation_Array_Filter( 'attribute_' . $row_attribute, 'attribute_' . $column_attribute, $pv );

	foreach ( $grid as $row_key => &$column ) {
		foreach ( $column as $column_key => &$field_value ) {
			$matches = $filter->get_matches( $row_key, $column_key );

			$field_value = $matches;
		}
	}

	$matrix_data = array(
		'row_attribute'    => $row_attribute,
		'column_attribute' => $column_attribute,
		'matrix_columns'   => array_values( $av[ ( wc_bv_get_post_meta( $_product->get_id(), '_bv_x', true ) ) ] ),
		'matrix_rows'      => array_values( $av[ ( wc_bv_get_post_meta( $_product->get_id(), '_bv_y', true ) ) ] ),
		'matrix'           => $grid
	);

	return $matrix_data;
}

function woocommerce_bulk_variations_get_title( $taxonomy, $value ) {
	global $product;

	$attributes = $product->get_attributes();
	$result     = $value;

	if ( isset( $attributes[ $taxonomy ] ) ) {
		if ( $attributes[ $taxonomy ]['is_taxonomy'] ) {
			if ( taxonomy_exists( $taxonomy ) ) {
				$term = get_term_by( 'slug', $value, $taxonomy );
				if ( $term ) {
					$result = $term->name;
				}
			} else {
				$result = ucwords( $value );
			}
		} else {
			// For custom attributes, get the name from the slug
			$options = array_map( 'trim', explode( WC_DELIMITER, $attributes[ $taxonomy ]['value'] ) );
			foreach ( $options as $option ) {
				if ( sanitize_title( $option ) == $value ) {
					$value = $option;
					break;
				}
			}
			$result = $value;
		}
	}


	return esc_html( apply_filters( 'woocommerce_bulk_variations_get_title', $result, $taxonomy, $value ) );
}


function woocommerce_bulk_variations_get_price( $price, $args = array() ) {

	extract( shortcode_atts( array(
		'ex_tax_label' => '0'
	), $args ) );

	$return          = '';
	$num_decimals    = (int) get_option( 'woocommerce_price_num_decimals' );
	$currency_pos    = get_option( 'woocommerce_currency_pos' );
	$currency_symbol = get_woocommerce_currency_symbol();

	$price = apply_filters( 'raw_woocommerce_price', (double) $price );

	$price = number_format( $price, $num_decimals, stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) );

	if ( get_option( 'woocommerce_price_trim_zeros' ) == 'yes' && $num_decimals > 0 ) {
		$price = wc_trim_zeros( $price );
	}

	switch ( $currency_pos ) {
		case 'left' :
			$return = $currency_symbol . $price;
			break;
		case 'right' :
			$return = $price . $currency_symbol;
			break;
		case 'left_space' :
			$return = $currency_symbol . '&nbsp;' . $price . '';
			break;
		case 'right_space' :
			$return = $price . '&nbsp;' . $currency_symbol . '';
			break;
	}

	return $return;
}

function woocommerce_bulk_variations_add_to_cart_message( $count ) {
	// Output success messages
	if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) :
		$return_to = ( wp_get_referer() ) ? wp_get_referer() : home_url();
		$message   = sprintf( '<a href="%s" class="button">%s</a> %s', $return_to, __( 'Continue Shopping &rarr;', 'woocommerce-bulk-variations' ), sprintf( __( '%s products successfully added to your cart.', 'woocommerce-bulk-variations' ), $count ) );
	else :
		$message = sprintf( '<a href="%s" class="button">%s</a> %s', get_permalink( wc_get_page_id( 'cart' ) ), __( 'View Cart &rarr;', 'woocommerce' ), sprintf( __( '%s products successfully added to your cart.', 'woocommerce-bulk-variations' ), $count ) );
	endif;

	wc_add_notice( apply_filters( 'woocommerce_bv_add_to_cart_message', $message ) );
}


/**
 * Get product meta for a product, WC 2.7 compatible.
 *
 * @since 1.5.0
 *
 * @param $product_id
 * @param $meta_key
 *
 * @return mixed
 */
function wc_bv_get_post_meta( $product_id, $meta_key ) {
	if ( WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_7() ) {
		$product = wc_get_product( $product_id );
		if ( $product ) {
			return $product->get_meta( $meta_key );
		} else {
			return false;
		}
	} else {
		return get_post_meta( $product_id, $meta_key, true );
	}
}

/**
 * Update product meta for a product, WC 2.7 compatible.
 *
 * @since 1.5.0
 *
 * @param $product_id
 * @param $meta_key
 * @param $meta_value
 */
function wc_bv_update_post_meta( $product_id, $meta_key, $meta_value ) {
	if ( WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_7() ) {
		$product = wc_get_product( $product_id );
		$product->update_meta_data( $meta_key, $meta_value );
		$product->save_meta_data();
	} else {
		update_post_meta( $product_id, $meta_key, $meta_value );
	}
}

/**
 * Removes product meta for a product, WC 2.7 compatible.
 *
 * @since 1.5
 *
 * @param $product_id
 * @param $meta_key
 */
function wc_bv_delete_post_meta( $product_id, $meta_key ) {
	if ( WC_Bulk_Variations_Compatibility::is_wc_version_gte_2_7() ) {
		$product = wc_get_product( $product_id );
		$product->delete_meta_data( $meta_key );
		$product->save_meta_data();
	} else {
		delete_post_meta( $product_id, $meta_key );
	}
}
