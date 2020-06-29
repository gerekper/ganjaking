<?php
/**
 * Multiple Shipping Addresses Compatibility
 *
 * @author   Kathy Darling
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  1.0.5
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Ship_Multiple_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Multiple Shipping Addresses.
 */
class WC_MNM_Ship_Multiple_Compatibility {

	public static function init() {

		// Product title filter.
		add_filter( 'wcms_product_title', array( __CLASS__, 'add_mnm_config'), 10, 2 );

	}

	/**
	 * Add a list of MNM config to the Multiple Shipping address' product title
	 *
	 * @param  string $title
	 * @param  array  $item
	 * @return string
	 */
	public static function add_mnm_config( $title, $item ){
	    if( isset( $item['mnm_config'] ) && is_array( $item['mnm_config'] ) ){
	        $list = '<ul class="wcsm-config">';
	        foreach( $item['mnm_config'] as $mnm_id => $data ){
	            $product = wc_get_product( $mnm_id );
	            $list .= "<li>" . $product->get_title() . ' x ' . $data['quantity'] . '</li>';
	        }
	        $list .= '</ul>';
	        $title .= $list;
	    }
	    return $title;
	}

}

WC_MNM_Ship_Multiple_Compatibility::init();
