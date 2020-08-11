<?php
/**
 * The template for displaying the start of the quantity selector of an option
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-element-quantity-start.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 5.0
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $tm_element_settings ) && ! empty( $quantity ) ) {

	$__min_value     = $tm_element_settings['quantity_min'];
	$__max_value     = $tm_element_settings['quantity_max'];
	$__step          = floatval( $tm_element_settings['quantity_step'] );
	$__default_value = $tm_element_settings['quantity_default_value'];

	if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no"  ) {
		if ( isset( $_POST[ $name . '_quantity' ] ) ) {
			$__default_value = stripslashes( $_POST[ $name . '_quantity' ] );
		} elseif ( isset( $_GET[ $name . '_quantity' ] ) ) {
			$__default_value = stripslashes( $_GET[ $name . '_quantity' ] );
		}
	}

	$__default_value = apply_filters( 'wc_epo_quantity_default_value', $__default_value, isset( $tm_element_settings ) ? $tm_element_settings : array(), isset( $value ) ? $value : NULL, isset( $choice_counter ) ? $choice_counter : NULL );

	if ( $__default_value == '' || ! is_numeric( $__default_value ) ) {
		$__default_value = 1;
	}

	if ( $__min_value != '' ) {
		$__min_value = floatval( $__min_value );
	} else {
		$__min_value = 0;
	}
	if ( $__max_value != '' ) {
		$__max_value = floatval( $__max_value );
	}

	if ( empty( $__step ) ) {
		$__step = 'any';
	}

	if ( is_numeric( $__min_value ) || is_numeric( $__max_value ) ) {

		if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) && $__min_value > $__max_value ) {
			$__max_value = $__min_value + $__step;
		}
		if ( is_numeric( $__max_value ) && $__default_value > $__max_value ) {
			$__default_value = $__max_value;
		}
		if ( is_numeric( $__min_value ) && $__default_value < $__min_value ) {
			$__default_value = $__min_value;
		}
	}
?>
<div class="tc-container nopadding">
	<div class="tc-row <?php echo "tc-quantity-" . esc_attr( $quantity ); ?>">
		<div class="tc-cell tc-col-auto tm-quantity tm-<?php echo esc_attr( $quantity ); ?>">
			<?php do_action( 'wc_epo_quantity_selector_before_input', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
			<input type="number" step="<?php echo esc_attr( $__step ); ?>" 
			<?php 
			if ( is_numeric( $__min_value ) ){
				echo ' min="' . esc_attr( $__min_value ) . '"';
			}
			if ( is_numeric( $__max_value ) ){
				echo ' max="' . esc_attr( $__max_value ) . '"';
			}
			?> name="<?php echo esc_attr( $name ); ?>_quantity" value="<?php echo esc_attr( $__default_value ); ?>" 
			title="<?php echo esc_attr_x( 'Qty', 'element quantity input tooltip', 'woocommerce-tm-extra-product-options' ); ?>" 
			class="tm-qty tm-bsbb" size="4" /> 
			<?php do_action( 'wc_epo_quantity_selector_after_input', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
		</div>
		<div class="tc-cell tc-col tc-field-display">
	<?php
}