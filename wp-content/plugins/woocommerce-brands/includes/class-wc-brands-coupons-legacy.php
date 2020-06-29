<?php

/**
 * WC_Brands_Coupons_Legacy class.
 */
class WC_Brands_Coupons_Legacy {

	const E_WC_COUPON_EXCLUDED_BRANDS = 115;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Coupon validation and error handling.
		add_filter( 'woocommerce_coupon_is_valid', array( $this, 'validate_coupon' ), null, 2 );
		add_filter( 'woocommerce_coupon_error', array( $this, 'add_coupon_error_message' ), null, 3 );
		add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'maybe_apply_discount' ), null, 5 );
	}

	/**
	 * This validate coupon based on included and/or excluded product brands on
	 * a given coupon.
	 *
	 * If followings conditions are met, exception will be thrown and displayed
	 * as error notice on the cart page:
	 *
	 * * Coupon has Product Brands restriction set and no item in the cart is associated
	 *   with the Product Brands.
	 * * For cart-based discount, NOT all items are in Product Brands.
	 * * Coupon has Exclude Brands restriction set and all items in the cart are associated
	 *   with the Exclude Brands.
	 * * For cart-based discount, part of cart items are in the Exclude Brands restriction.
	 *
	 * @throws Exception
	 *
	 * @param bool      $valid      Whether the coupon is valid
	 * @param WC_Coupon $coupon_obj Coupon object
	 *
	 * @return bool True if coupon is valid, otherwise Exception will be thrown
	 */
	public function validate_coupon( $valid, $coupon_obj ) {
		$this->_set_brand_settings_on_coupon( $coupon_obj );

		// Only check if coupon still valid.
		if ( $valid ) {
			$valid = $this->_validate_included_product_brands( $valid, $coupon_obj );
			$valid = $this->_validate_excluded_product_brands( $valid, $coupon_obj );
		}

		return $valid;
	}

	/**
	 * Set brand settings as properties on coupon object. These properties are
	 * list of included product brand IDs and list of excluded brand IDs.
	 *
	 * @param WC_Coupon $coupon_obj Coupon object
	 *
	 * @return void
	 */
	private function _set_brand_settings_on_coupon( $coupon_obj ) {
		if ( isset( $coupon_obj->included_brands ) && isset( $coupon_obj->excluded_brands ) ) {
			return;
		}

		$coupon_id = is_callable( array( $coupon_obj, 'get_id' ) ) ? $coupon_obj->get_id() : $coupon_obj->id;
		$included_product_brands = get_post_meta( $coupon_id, 'product_brands', true );
		if ( empty( $included_product_brands ) ) {
			$included_product_brands = array();
		}
		$excluded_product_brands = get_post_meta( $coupon_id, 'exclude_product_brands', true );
		if ( empty( $excluded_product_brands ) ) {
			$excluded_product_brands = array();
		}

		// Store these for later, to avoid multiple look-ups when we filter on the discount.
		$coupon_obj->included_brands = $included_product_brands;
		$coupon_obj->excluded_brands = $excluded_product_brands;
	}

	/**
	 * Validate whether cart items are in Product Brands restriction. If no item
	 * is in Product Brands then Exception will be thrown. Or, if coupon is cart-
	 * based discount, Exception will be thrown if NOT all items are in Product Brands.
	 *
	 * @throws Exception
	 *
	 * @param bool      $valid
	 * @param WC_Coupon $coupon_obj
	 *
	 * @return bool
	 */
	private function _validate_included_product_brands( $valid, $coupon_obj ) {
		if ( sizeof( $coupon_obj->included_brands ) > 0 ) {
			$num_items_match_included = 0;
			if ( ! WC()->cart->is_empty() ) {
				foreach( WC()->cart->get_cart() as $cart_item ) {
					$product_brands = wp_get_post_terms( $cart_item['product_id'], 'product_brand', array( 'fields' => 'ids' ) );
					if ( $this->_product_has_brands( $product_brands, $coupon_obj->included_brands ) ) {
						$num_items_match_included++;
					}
				}
			}

			// For cart-based discount, all items MUST BE in Product Brands.
			if ( $coupon_obj->is_type( array( 'fixed_cart', 'percent' ) ) && $num_items_match_included < sizeof( WC()->cart->get_cart() ) ) {
				throw new Exception( $coupon_obj::E_WC_COUPON_NOT_APPLICABLE );
			}

			// No item in Product Brands.
			if ( $num_items_match_included === 0 ) {
				throw new Exception( $coupon_obj::E_WC_COUPON_NOT_APPLICABLE );
			}
		}

		return $valid;
	}

	/**
	 * Validate whether cart items are in the Exclude Brands restriction.
	 *
	 * If coupon has Exclude Brands restriction set and all items in the cart are associated
	 * with the Exclude Brands then Exception will be thrown.
	 *
	 * For cart-based discount, if part of cart items are in the Exclude Brands restriction
	 * then Exception will be thrown.
	 *
	 * @throws Exception
	 *
	 * @param bool      $valid
	 * @param WC_Coupon $coupon_obj
	 *
	 * @return bool
	 */
	private function _validate_excluded_product_brands( $valid, $coupon_obj ) {
		if ( sizeof( $coupon_obj->excluded_brands ) > 0 ) {
			$num_items_match_excluded = 0;
			if ( ! WC()->cart->is_empty() ) {
				foreach( WC()->cart->get_cart() as $cart_item ) {
					$product_brands = wp_get_post_terms( $cart_item['product_id'], 'product_brand', array( 'fields' => 'ids' ) );
					if ( $this->_product_has_brands( $product_brands, $coupon_obj->excluded_brands ) ) {
						$num_items_match_excluded++;
					}
				}
			}

			// If all items in the cart are in Exclude Brands properties, coupon
			// is not applicable.
			if ( sizeof( WC()->cart->get_cart() ) === $num_items_match_excluded ) {
				throw new Exception( self::E_WC_COUPON_EXCLUDED_BRANDS );
			}

			// For cart-based discount, if at least on item in the Exclude Brands then
			// coupon is not applicable.
			if ( $coupon_obj->is_type( array( 'fixed_cart', 'percent' ) ) && $num_items_match_excluded > 0 ) {
				throw new Exception( self::E_WC_COUPON_EXCLUDED_BRANDS );
			}
		}

		return $valid;
	}

	/**
	 * Display a specific error message if the coupon doesn't validate because of a brands-related element.
	 *
	 * @access public
	 * @since  1.3.0
	 * @param  string $err        The error message
	 * @param  string $err_code   The error code
	 * @param  object $coupon_obj Cart object
	 * @return string
	 */
	public function add_coupon_error_message( $err, $err_code, $coupon_obj ) {
		if ( self::E_WC_COUPON_EXCLUDED_BRANDS == $err_code ) {
			$this->_set_brand_settings_on_coupon( $coupon_obj );

			$brands = array();
			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
				foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					$product_brands = wp_get_post_terms( $cart_item['product_id'], 'product_brand', array( 'fields' => 'ids' ) );
					if ( sizeof( $intersect = array_intersect( $product_brands, $coupon_obj->excluded_brands ) ) > 0 ) {
						foreach( $intersect as $cat_id) {
							$cat = get_term( $cat_id, 'product_brand' );
							$brands[] = $cat->name;
						}
					}
				}
			}

			$err = sprintf( __( 'Sorry, this coupon is not applicable to the brands: %s.', 'wc_brands' ), implode( ', ', array_unique( $brands ) ) );
		}
		return $err;
	}

	/**
	 * Conditionally apply brands discounts.
	 *
	 * @access private
	 * @since  1.3.1
	 * @return  void
	 */
	public function maybe_apply_discount( $discount, $discounting_amount, $cart_item, $single, $this_obj ) {
		// Deal only with product-centric coupons.
		if ( ! is_a( $this_obj, 'WC_Coupon' ) || ! $this_obj->is_type( array( 'fixed_product', 'percent_product' ) ) ) {
			return $discount;
		}

		$product_brands = wp_get_post_terms( $cart_item['product_id'], 'product_brand', array( 'fields' => 'ids' ) );

		// If our coupon brands aren't present in the products in our cart, don't assign the discount.
		if ( ! empty( $this_obj->included_brands ) && ! $this->_product_has_brands( $product_brands, $this_obj->included_brands ) ) {
			$discount = 0;
		}

		// If our excluded coupon brands are present in the products in our cart, don't assign the discount.
		if ( ! empty( $this_obj->excluded_brands ) && $this->_product_has_brands( $product_brands, $this_obj->excluded_brands ) ) {
			$discount = 0;
		}

		return $discount;
	}

	/**
	 * Check whether given product brands are assigned to the current coupon being inspected.
	 *
	 * @access private
	 * @since  1.3.1
	 * @return  void
	 */
	private function _product_has_brands( $product_brands, $coupon_brands ) {
		return sizeof( array_intersect( $product_brands, $coupon_brands ) ) > 0;
	}

}

new WC_Brands_Coupons_Legacy();
