<?php
/**
 * The Template for displaying upload field.
 *
 * @version 3.0.0
 * @package woocommerce-product-addons
 */

$field_name    = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key     = 'addon-' . sanitize_title( $field_name );
$max           = ! empty( $addon['max'] ) ? $addon['max'] : '';
$attr          = ! empty( $max ) ? 'maxlength="' . esc_attr( $max ) . '"' : '';
$adjust_price  = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price         = ! empty( $addon['price'] ) ? $addon['price'] : '';
$price_type    = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$price_raw     = apply_filters( 'woocommerce_product_addons_price_raw', $adjust_price && $price ? $price : '', $addon );
$price_display = apply_filters(
	'woocommerce_product_addons_price',
	$adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'file_upload'
);

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>">
	<input
		type="file"
		class="wc-pao-addon-file-upload input-text wc-pao-addon-field"
		data-raw-price="<?php echo esc_attr( $price_raw ); ?>"
		data-price="<?php echo esc_attr( $price_display ); ?>"
		data-price-type="<?php echo esc_attr( $price_type ); ?>"
		name="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		id="addon-<?php echo esc_attr( sanitize_title( $field_name ) ); ?>"
		<?php echo WC_Product_Addons_Helper::is_addon_required( $addon ) ? 'required' : ''; ?>
		/> <small>
		<?php
			// translators: %s file size.
			echo wp_kses_post( sprintf( __( '(max file size %s)', 'woocommerce-product-addons' ), $max_size ) );
		?>
		</small>
</p>
