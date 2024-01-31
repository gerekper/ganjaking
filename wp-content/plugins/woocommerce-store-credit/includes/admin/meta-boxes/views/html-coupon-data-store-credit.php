<?php
/**
 * Coupon Data - Store Credit
 *
 * @package WC_Store_Credit/Admin/Meta_Boxes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Coupon $coupon Coupon object.
 */
$inc_tax           = wc_store_credit_coupon_include_tax( $coupon );
$apply_to_shipping = wc_store_credit_coupon_apply_to_shipping( $coupon );
?>
<div id="store_credit_general_options" class="options_group">
	<?php
	if ( wc_store_credit_coupons_can_inc_tax() || $inc_tax ) :
		woocommerce_wp_checkbox(
			array(
				'id'          => 'store_credit_inc_tax',
				'label'       => _x( 'Include tax', 'coupon: field label', 'woocommerce-store-credit' ),
				'description' => _x( 'Check this box if the coupon amount includes taxes.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'value'       => wc_bool_to_string( $inc_tax ),
			)
		);
	endif;

	if ( wc_shipping_enabled() || $apply_to_shipping ) :
		woocommerce_wp_checkbox(
			array(
				'id'          => 'store_credit_apply_to_shipping',
				'label'       => _x( 'Apply to shipping', 'coupon: field label', 'woocommerce-store-credit' ),
				'description' => _x( 'Check this box to apply the remaining coupon amount to the shipping costs.', 'coupon: field desc', 'woocommerce-store-credit' ),
				'value'       => wc_bool_to_string( $apply_to_shipping ),
			)
		);
	endif;
	?>
</div>
