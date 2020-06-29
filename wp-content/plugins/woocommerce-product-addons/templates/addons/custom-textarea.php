<?php
/**
 * The Template for displaying custom textarea field.
 *
 * @version 3.0.0
 */

$field_name    = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key     = 'addon-' . sanitize_title( $field_name );
$max           = ! empty( $addon['max'] ) ? $addon['max'] : '';
$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ] ) ? $_POST[ $addon_key ] : '';
$attr          = ! empty( $max ) ? 'maxlength="' . esc_attr( $max ) . '"' : '';
$adjust_price  = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price         = ! empty( $addon['price'] ) ? $addon['price'] : '';
$price_type    = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$price_raw     = apply_filters( 'woocommerce_product_addons_price_raw', '1' == $adjust_price && $price ? $price : '', $addon );
$price_display = apply_filters( 'woocommerce_product_addons_price',
	'1' == $adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'custom_textarea'
);

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
	<textarea type="text" class="input-text wc-pao-addon-field wc-pao-addon-custom-textarea" data-raw-price="<?php echo esc_attr( $price_raw ); ?>" data-price="<?php echo esc_attr( $price_display ); ?>" data-price-type="<?php echo esc_attr( $price_type ); ?>" name="<?php echo esc_attr( $addon_key ); ?>" id="<?php echo esc_attr( $addon_key ); ?>" rows="4" cols="20" <?php if ( ! empty( $max ) ) echo 'maxlength="' . $max . '"'; ?> <?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?>></textarea>
</p>
