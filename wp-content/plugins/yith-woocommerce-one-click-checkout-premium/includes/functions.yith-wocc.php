<?php

if( ! function_exists( 'yith_wocc_product_is_excluded' ) ) {
	/**
	 * Check if product or one of categories is in exclusions
	 *
	 * @since 1.0.0
	 * @param object $product
	 * @return bool
	 * @author Francesco Licandro
	 */
	function yith_wocc_product_is_excluded( $product ){

		$product_id = $product->get_id();
		$inverted   = get_option( 'yith-wocc-exclusion-inverted' ) == 'yes';

		// first check for categories
        $excluded_categories = get_option( 'yith-wocc-excluded-cat' );
		! is_array( $excluded_categories ) && $excluded_categories = explode( ',', $excluded_categories );
		// remove empty
        $excluded_categories = array_filter( $excluded_categories );
		$product_categories  = get_the_terms( $product_id, 'product_cat' );
		$in_list = false;

		if( ! empty( $excluded_categories ) && is_array( $product_categories ) ) {
			foreach( $product_categories as $key => $category ) {
				if( in_array( $category->term_id, $excluded_categories ) ) {
					$in_list = true;
					break;
				}
			}
		}

		if( $in_list ) {
			return $inverted ? false : true;
		}
		// else check for single product
		else {
			$meta_exist = (bool) yit_get_prop( $product, '_yith_wocc_exclude_list', true );
			return $inverted ? ! $meta_exist : $meta_exist;
		}
	}
}

if( ! function_exists( 'yith_wocc_get_custom_address' ) ) {
	/**
	 * Get custom address for passed user.
	 *
	 * @since 1.0.0
	 * @param int|string $id
	 * @return array
	 * @author Francesco Licandro
	 */
	function yith_wocc_get_custom_address( $id ){
		return maybe_unserialize( get_user_meta( $id, 'yith-wocc-user-custom-address', true ) );
	}
}

if( ! function_exists( 'yith_wocc_save_custom_address' ) ) {
	/**
	 * Save custom address for passed user.
	 *
	 * @since 1.0.0
	 * @param int|string $id
	 * @param array $value
	 * @return bool|int
	 * @author Francesco Licandro
	 */
	function yith_wocc_save_custom_address( $id, $value ){
		return update_user_meta( $id, 'yith-wocc-user-custom-address', $value );
	}
}

if( ! function_exists( 'yith_wocc_enabled_shipping' ) ) {
	/**
	 * Check if shipping is enabled on shop
	 *
	 * @since 1.0.0
	 * @return bool
	 * @author Francesco Licandro
	 */
	function yith_wocc_enabled_shipping() {
		return ( get_option( 'woocommerce_calc_shipping' ) === 'yes' && ! wc_ship_to_billing_address_only() );
	}
}

if( ! function_exists( 'yith_wocc_is_stripe_enabled' ) ) {
	/**
	 * Check if plugin YITH Stripe is active and available
	 *
	 * @access public
	 * @since 1.0.0
	 * @param int $user_id The user ID
	 * @param string $user_meta The user meta name
	 * @return bool
	 * @author Francesco Licandro
	 */
	function yith_wocc_is_stripe_enabled( $user_id = 0, $user_meta = '' ) {

		// first check if stripe premium is active
		if( ! ( defined( 'YITH_WCSTRIPE_PREMIUM' ) && YITH_WCSTRIPE_PREMIUM ) ) {
			return false;
		}
		// then check in plugin option
		if( get_option('yith-wocc-stripe-integration') != 'yes' ) {
			return false;
		}

		// check also in user options if params are set
		$check_user = true;
		if( $user_id && $user_meta ) {
			$is_active = get_user_meta( $user_id, $user_meta, true );

			$check_user = isset( $is_active['use-stripe'] ) && $is_active['use-stripe'];
		}

		$class_stripe = YITH_WCStripe()->get_gateway();

		if( ! is_object( $class_stripe ) ) {
			return false;
		}

		return $class_stripe->is_available() && $check_user;
	}
}

/**
 * Check if WooCommerce version is >= of 2.6
 * 
 * @since 1.0.4
 * @author Francesco Licandro
 * @deprecated
 * @return boolean
 */
function ywocc_is_wc_26(){
	return version_compare( WC()->version, '2.6', '>=' );
}

if( ! function_exists( 'yith_wocc_get_custom_style' ) ){
	/**
	 * Get plugin custom style
	 *
	 * @since 1.0.5
	 * @author Francesco Licandro
	 * @return string
	 */
	function yith_wocc_get_custom_style(){

		$bkg        = get_option( 'yith-wocc-button-background' );
		$color      = get_option( 'yith-wocc-button-text' );
		$bkg_h      = get_option( 'yith-wocc-button-background-hover' );
		$color_h    = get_option( 'yith-wocc-button-text-hover' );
		
		$custom = ".yith-wocc-button{background-color:{$bkg} !important;color:{$color} !important;}
                .yith-wocc-button:hover{background-color:{$bkg_h} !important;color:{$color_h} !important;}";

		return apply_filters( 'yith_wocc_get_custom_style', $custom );
	}
}