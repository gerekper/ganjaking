<?php
/**
 * The Template for displaying custom price field.
 *
 * @version 3.0.0
 */

$field_name    = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key     = 'addon-' . sanitize_title( $field_name );
$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ] ) ? wc_clean( $_POST[ $addon_key ] ) : '';
$restrictions  = ! empty( $addon['restrictions'] ) ? $addon['restrictions'] : '';
$max           = ! empty( $addon['max'] ) ? $addon['max'] : '';
$min           = ! empty( $addon['min'] ) ? $addon['min'] : '';
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
	<input type="number" step="any" class="input-text wc-pao-addon-field wc-pao-addon-custom-price" name="<?php echo esc_attr( $addon_key ); ?>" id="<?php echo esc_attr( $addon_key ); ?>" data-price-type="flat_fee" value="" 
	<?php if ( ! empty( $min ) && 1 === $restrictions ) echo 'min="' . $min .'"'; ?> <?php if ( ! empty( $max ) && 1 === $restrictions ) echo 'max="' . $max .'"'; ?>
	<?php if ( WC_Product_Addons_Helper::is_addon_required( $addon ) ) { echo 'required'; } ?>
	/>
</p>
