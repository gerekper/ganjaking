<?php
/**
 * Smart Coupons Compatibility
 *
 * @author   Kathy Darling
 * @package  WooCommerce Free Gift Coupons/Compatibility
 * @since    2.1.0
 * @version  2.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_FGC_Smart_Coupons_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Cost of Goods.
 */
class WC_FGC_Smart_Coupons_Compatibility {

	public static function init() {

		// Smart Coupons export headers.
		add_filter( 'wc_smart_coupons_export_headers', array( __CLASS__, 'smart_coupon_export_headers' ) );

		// Smart Coupons import meta fields.
		add_filter( 'smart_coupons_parser_postmeta_defaults', array( __CLASS__, 'postmeta_defaults' ) );		

		// Smart Coupons email heading.
		add_filter( 'wc_sc_email_heading', array( __CLASS__, 'smart_coupon_email_heading' ), 10, 2 );

		// Smart Coupons description.
		add_filter( 'wc_sc_coupon_description', array( __CLASS__, 'smart_coupon_description' ), 10, 2 );

		// Smart Coupons cart coupon title.
		add_filter( 'wc_smart_coupons_display_discount_title', array( __CLASS__, 'smart_coupon_discount_title' ), 10, 2 );

		// Include FGC meta in Smart Coupons bulk generation.
		add_filter( 'sc_generate_coupon_meta', array( __CLASS__, 'coupon_meta' ), 10, 2 );

		// Include FGC meta in Smart Coupons auto-generation.
		add_action( 'wc_sc_new_coupon_generated', array( __CLASS__, 'new_coupon_meta' ) );

		// Include FGC meta in Smart Coupons auto-generation.
		add_filter( 'wc_sc_is_auto_generate', array( __CLASS__, 'is_auto_generate' ), 10, 2 );

	}

	/**
	 * Include FGC data when using Smart Coupons export.
	 *
	 * @param  array     $headers
	 * @return array
	 */
	public static function smart_coupon_export_headers( $headers ) {
		$headers['_wc_free_gift_coupon_data']          = __( 'Free Gift Data', 'wc_free_gift_coupons' );
		$headers['_wc_free_gift_coupon_free_shipping'] = __( 'Free Gift Shipping', 'wc_free_gift_coupons' );
		return $headers;
	}

	/**
	 * Include FGC data when using Smart Coupons export.
	 *
	 * @param  array     $headers
	 * @return array
	 */
	public static function postmeta_defaults( $headers ) {
		$headers['_wc_free_gift_coupon_data']          = '';
		$headers['_wc_free_gift_coupon_free_shipping'] = '';
		return $headers;
	}


	/**
	 * Modify Smart Coupons email heading for FGC type.
	 *
	 * @param  array     $heading
	 * @param  obj  WC_Coupon $coupon
	 * @return array
	 */
	public static function smart_coupon_email_heading( $heading, $coupon ) {
		if ( $coupon->is_type( 'free_gift' ) ) {
			$heading = __( 'You have received a coupon for a free gift!', 'wc_free_gift_coupons' );
		}

		return $heading;
	}


	/**
	 * Modify Smart Coupons description for FGC type.
	 *
	 * @param  string     $description
	 * @param  obj  WC_Coupon $coupon
	 * @return array
	 */
	public static function smart_coupon_description( $description, $coupon ) {
		if ( $coupon->is_type( 'free_gift' ) ) {
			
			$gift_data = WC_Free_Gift_Coupons::get_gift_data( $coupon->get_id(), true );

			if ( count( $gift_data ) === 1 ) {
				$product_titles = implode( ', ', wp_list_pluck( $gift_data, 'title' ) );
				
				/* translators: %s: List of titles of free products gifted by coupon */
				$description = sprintf(__( 'Free %s', 'wc_free_gift_coupons' ), $product_titles );
			} elseif ( count( $gift_data ) > 1 ) {
				$description = __( 'Free Gifts', 'wc_free_gift_coupons' );
			}
			
		}

		return $description;
	}

	/**
	 * Modify Smart Coupons cart coupon title for FGC type.
	 *
	 * @param  string     $description
	 * @param  obj  WC_Coupon $coupon
	 * @return array
	 */
	public static function smart_coupon_discount_title( $description, $coupon ) {
		if ( $coupon->is_type( 'free_gift' ) ) {
			$description = __( 'Free Gift!', 'wc_free_gift_coupons' );
		}

		return $description;
	}

	/**
	 * Include FGC data when using Smart Coupons bulk generate.
	 *
	 * @param  array $data
	 * @param  array $post
	 * @return array
	 */
	public static function coupon_meta( $data, $post  ) {

		if ( isset( $post['discount_type'] ) && $post['discount_type'] === 'free_gift' ) {
			$data['_wc_free_gift_coupon_data']          = isset( $post['wc_free_gift_coupons_data'] ) ? maybe_serialize( WC_Free_Gift_Coupons_Admin::sanitize_free_gift_meta( $post['wc_free_gift_coupons_data'] ) ) : '';
			$data['_wc_free_gift_coupon_free_shipping'] = isset( $post['wc_free_gift_coupon_free_shipping'] ) ? 'yes' : 'no';
		}

		return $data;
	}

	/**
	 * Include FGC data when using Smart Coupons auto-generate.
	 * 
	 * @param  array  $args
	 */
	public static function new_coupon_meta( $args = array() ) {

		if ( ! empty( $args['new_coupon_id'] ) && ! empty( $args['ref_coupon'] ) ) {
			$prev_wc_free_gift_coupon_data          = ( is_object( $args['ref_coupon'] ) && is_callable( array( $args['ref_coupon'], 'get_meta' ) ) ) ? (array) $args['ref_coupon']->get_meta( '_wc_free_gift_coupon_data' ) : array();
			$prev_wc_free_gift_coupon_free_shipping = ( is_object( $args['ref_coupon'] ) && is_callable( array( $args['ref_coupon'], 'get_meta' ) ) ) ? (array) $args['ref_coupon']->get_meta( '_wc_free_gift_coupon_free_shipping' ) : 'no';
			
			update_post_meta( $args['new_coupon_id'], '_wc_free_gift_coupon_data', $prev_wc_free_gift_coupon_data );
			update_post_meta( $args['new_coupon_id'], '_wc_free_gift_coupon_free_shipping', $prev_wc_free_gift_coupon_free_shipping );
		}

	}

	/**
	 * Function to decide whether coupon needs to be auto-generated or not
	 * 
	 * @param  boolean $is_auto_generate
	 * @param  array   $args
	 * @return boolean
	 */
	public static function is_auto_generate( $is_auto_generate = false, $args = array() ) {

		$coupon = ( ! empty( $args['coupon_obj'] ) ) ? $args['coupon_obj'] : null;

		if ( is_a( $coupon, 'WC_Coupon' ) && $coupon->is_type( 'free_gift' ) ) {
			$is_coupon_auto_generate = ( ! empty( $args['auto_generate'] ) ) ? $args['auto_generate'] : 'no';
			if ( 'yes' === $is_coupon_auto_generate ) {
				return true;
			}
		}

		return $is_auto_generate;
	}
}

WC_FGC_Smart_Coupons_Compatibility::init();
