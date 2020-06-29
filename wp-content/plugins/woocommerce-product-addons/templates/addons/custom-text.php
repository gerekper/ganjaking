<?php
/**
 * The Template for displaying custom text/short text field.
 *
 * @version 3.0.0
 */

$field_name        = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key         = 'addon-' . sanitize_title( $field_name );
$max               = ! empty( $addon['max'] ) ? $addon['max'] : '';
$restrictions_type = ! empty( $addon['restrictions_type'] ) ? $addon['restrictions_type'] : '';
$current_value     = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ] ) ? $_POST[ $addon_key ] : '';
$attr              = ! empty( $max ) ? 'maxlength="' . esc_attr( $max ) . '"' : '';
$price             = ! empty( $addon['price'] ) ? $addon['price'] : '';
$adjust_price      = ! empty( $addon['adjust_price'] ) ? $addon['adjust_price'] : '';
$price_type        = ! empty( $addon['price_type'] ) ? $addon['price_type'] : '';
$price_raw         = apply_filters( 'woocommerce_product_addons_price_raw', '1' == $adjust_price && $price ? $price : '', $addon );
$price_display     = apply_filters( 'woocommerce_product_addons_price',
	'1' == $adjust_price && $price_raw ? WC_Product_Addons_Helper::get_product_addon_price_for_display( $price_raw ) : '',
	$addon,
	0,
	'custom_text'
);

switch ( $restrictions_type ) {
	case 'only_letters':
		$attr .= ' pattern="[A-Za-z]+"';
		$attr .= ' title="' . __( 'Only letters', 'woocommerce-product-addons' ) . '"';
		break;
	case 'only_numbers':
		$attr .= ' pattern="[0-9]+"';
		$attr .= ' title="' . __( 'Only numbers', 'woocommerce-product-addons' ) . '"';
		break;
	case 'only_letters_numbers':
		$attr .= 'pattern="[A-Za-z0-9-]+"';
		$attr .= ' title="' . __( 'Only letters and numbers', 'woocommerce-product-addons' ) . '"';
		break;
}

if ( 'percentage_based' === $price_type ) {
	$price_display = $price_raw;
}
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
		<?php if ( 'email' === $restrictions_type ) { ?>
			<input type="email" class="input-text wc-pao-addon-field wc-pao-addon-custom-text" data-raw-price="<?php echo esc_attr( $price_raw ); ?>" data-price="<?php echo esc_attr( $price_display ); ?>" name="<?php echo esc_attr( $addon_key ); ?>" id="<?php echo esc_attr( $addon_key ); ?>" data-price-type="<?php echo esc_attr( $price_type ); ?>" value="" <?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?> />
		<?php } else { ?>
			<input type="text" class="input-text wc-pao-addon-field wc-pao-addon-custom-text" data-raw-price="<?php echo esc_attr( $price_raw ); ?>" data-price="<?php echo esc_attr( $price_display ); ?>" name="<?php echo esc_attr( $addon_key ); ?>" id="<?php echo esc_attr( $addon_key ); ?>" data-price-type="<?php echo esc_attr( $price_type ); ?>" value="" <?php echo $attr; ?> <?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?> />
		<?php } ?>
</p>
