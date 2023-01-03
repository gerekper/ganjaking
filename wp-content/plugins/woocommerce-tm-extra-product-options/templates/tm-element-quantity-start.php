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
 * @author  ThemeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $tm_element_settings ) && ! empty( $quantity ) ) {

	$__min_value     = $tm_element_settings['quantity_min'];
	$__max_value     = $tm_element_settings['quantity_max'];
	$__step          = floatval( $tm_element_settings['quantity_step'] );
	$__default_value = $tm_element_settings['quantity_default_value'];

	if ( 'no' === THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add ) {
		if ( isset( $_REQUEST[ $posted_name . '_quantity' ] ) && ( ! isset( $checked ) || ( isset( $checked ) && ! empty( $checked ) ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$attribute_quantity = sanitize_text_field( wp_unslash( $_REQUEST[ $posted_name . '_quantity' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $get_posted_key ] ) ) {
				$attribute_quantity = $attribute_quantity[ $get_posted_key ];
			}
			$__default_value = $attribute_quantity;
		}
	}

	$__default_value = apply_filters( 'wc_epo_quantity_default_value', $__default_value, isset( $tm_element_settings ) ? $tm_element_settings : [], isset( $value ) ? $value : null, isset( $choice_counter ) ? $choice_counter : null );

	if ( '' === $__default_value || ! is_numeric( $__default_value ) ) {
		$__default_value = 1;
	}

	if ( '' !== $__min_value ) {
		$__min_value = floatval( $__min_value );
	} else {
		$__min_value = 0;
	}
	if ( '' !== $__max_value ) {
		$__max_value = floatval( $__max_value );
	}

	if ( empty( $__step ) ) {
		$__step = 'any';
	}

	if ( is_numeric( $__min_value ) || is_numeric( $__max_value ) ) {

		if ( is_numeric( $__min_value ) && is_numeric( $__max_value ) && $__min_value > $__max_value ) {
			if ( 'any' === $__step ) {
				$__max_value = $__min_value + 1;
			} else {
				$__max_value = $__min_value + $__step;
			}
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
	<div class="tc-row <?php echo 'tc-quantity-' . esc_attr( $quantity ); ?>">
		<div class="tc-cell tc-col-auto tm-quantity tm-<?php echo esc_attr( $quantity ); ?>">
			<?php do_action( 'wc_epo_quantity_selector_before_input', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
			<input type="number" step="<?php echo esc_attr( $__step ); ?>" 
			<?php
			if ( is_numeric( $__min_value ) ) {
				echo ' min="' . esc_attr( $__min_value ) . '"';
			}
			if ( is_numeric( $__max_value ) ) {
				echo ' max="' . esc_attr( $__max_value ) . '"';
			}
			?>
			name="<?php echo esc_attr( $quantity_name ); ?>" value="<?php echo esc_attr( $__default_value ); ?>" 
			title="<?php echo esc_attr_x( 'Qty', 'element quantity input tooltip', 'woocommerce-tm-extra-product-options' ); ?>" 
			class="tm-qty tc-element-qty tm-bsbb"> 
			<?php do_action( 'wc_epo_quantity_selector_after_input', isset( $tm_element_settings ) ? $tm_element_settings : [] ); ?>
		</div>
		<div class="tc-cell tc-col tc-field-display">
	<?php
}
