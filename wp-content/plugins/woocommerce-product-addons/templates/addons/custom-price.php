<?php
/**
 * The Template for displaying custom price field.
 *
 * @version 3.0.0
 */

$field_name       = ! empty( $addon['field_name'] ) ? $addon['field_name'] : '';
$addon_key        = 'addon-' . sanitize_title( $field_name );
$current_value    = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ] ) ? wc_clean( $_POST[ $addon_key ] ) : '';
$is_required      = WC_Product_Addons_Helper::is_addon_required( $addon );
$has_restrictions = ! empty( $addon['restrictions'] );
$max              = ! empty( $addon['max'] ) ? $addon['max'] : '';
$min              = $addon['min'] > 0 ? $addon['min'] : 0;
?>

<p class="form-row form-row-wide wc-pao-addon-wrap wc-pao-addon-<?php echo sanitize_title( $field_name ); ?>">
	<input
		type="number"
		step="any"
		class="input-text wc-pao-addon-field wc-pao-addon-custom-price"
		name="<?php echo esc_attr( $addon_key ); ?>"
		id="<?php echo esc_attr( $addon_key ); ?>"
		data-price-type="flat_fee"
		<?php echo $is_required ? 'required' : ''; ?>
		<?php echo $has_restrictions && $max !== '' ? 'max="' . esc_attr( $max ) . '"' : ''; ?>
		<?php echo $has_restrictions ? 'min="' . esc_attr( $min ) . '"' : ''; ?>
		<?php echo $has_restrictions ? 'oninput="this.value = this.value < 0 ? Math.abs(this.value) : this.value"' : ''; ?>
	/>
</p>
