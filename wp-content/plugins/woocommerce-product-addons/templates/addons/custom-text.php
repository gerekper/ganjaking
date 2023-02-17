<?php
/**
 * The Template for displaying custom text/short text field.
 *
 * @version 6.0.0
 * @package woocommerce-product-addons
 */

$field_name        = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key         = 'addon-' . sanitize_title( $field_name );
$max               = ! empty( $addon['max'] ) ? $addon['max'] : '';
$restrictions_type = ! empty( $addon['restrictions_type'] ) ? $addon['restrictions_type'] : '';
$price             = ! empty( $addon['price'] ) ? $addon['price'] : '';
$adjust_price      = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price_type        = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$restriction_data  = WC_Product_Addons_Helper::get_restriction_data( $addon );
$price_raw         = apply_filters( 'woocommerce_product_addons_price_raw', $adjust_price && $price ? $price : '', $addon );
$price_display     = apply_filters(
	'woocommerce_product_addons_price',
	$adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'custom_text'
);

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<div class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<input
		type="text"
		class="input-text wc-pao-addon-field wc-pao-addon-custom-text"
		data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
		data-price="<?php echo esc_attr( $price_display ); ?>"
		name="<?php echo esc_attr( $addon_key ); ?>"
		id="<?php echo esc_attr( $addon_key ); ?>"
		data-price-type="<?php echo esc_attr( $price_type ); ?>"
		value=""
		data-restrictions="<?php echo esc_attr( json_encode( $restriction_data ) ) ?>"
	/>
</div>
