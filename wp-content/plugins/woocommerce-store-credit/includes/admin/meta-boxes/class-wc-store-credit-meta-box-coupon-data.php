<?php
/**
 * Meta Box: Coupon Data
 *
 * Updates the Coupon Data meta box.
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Admin_Send_Credit_Page class.
 */
class WC_Store_Credit_Meta_Box_Coupon_Data {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_action( 'woocommerce_coupon_options', array( $this, 'general_options' ), 10, 2 );
		add_action( 'woocommerce_coupon_options_save', array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Outputs additional options for the store credit coupons.
	 *
	 * @since 3.0.0
	 *
	 * @param int       $coupon_id Coupon ID.
	 * @param WC_Coupon $coupon    Coupon object.
	 */
	public function general_options( $coupon_id, $coupon ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		include 'views/html-coupon-data-store-credit.php';
	}

	/**
	 * Saves additional options for the store credit coupons.
	 *
	 * @since 3.0.0
	 *
	 * @param int       $coupon_id Coupon ID.
	 * @param WC_Coupon $coupon    Coupon object.
	 */
	public function save( $coupon_id, $coupon ) {
		if ( wc_is_store_credit_coupon( $coupon ) ) {
			$inc_tax        = ( isset( $_POST['store_credit_inc_tax'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			$apply_shipping = ( isset( $_POST['store_credit_apply_to_shipping'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

			$coupon->update_meta_data( 'store_credit_inc_tax', wc_bool_to_string( $inc_tax ) );
			$coupon->update_meta_data( 'store_credit_apply_to_shipping', wc_bool_to_string( $apply_shipping ) );
		} else {
			$coupon->delete_meta_data( 'store_credit_inc_tax' );
			$coupon->delete_meta_data( 'store_credit_apply_to_shipping' );
		}

		$coupon->save_meta_data();
	}
}

return new WC_Store_Credit_Meta_Box_Coupon_Data();
