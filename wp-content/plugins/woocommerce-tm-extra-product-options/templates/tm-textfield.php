<?php
/**
 * The template for displaying the textfield element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-textfield.php
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
?>
<li class="tmcp-field-wrap">
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php' ); ?>
    <label class="tm-epo-field-label" for="<?php echo esc_attr( $id ); ?>">
        <input<?php
		if ( isset( $placeholder ) && $placeholder !== '' ) {
			echo ' placeholder="' . esc_attr( $placeholder ) . '"';
		}
		if ( isset( $min_chars ) && $min_chars !== '' ) {
			echo ' minlength="' . esc_attr( $min_chars ) . '"';
		}
		if ( isset( $max_chars ) && $max_chars !== '' ) {
			echo ' maxlength="' . esc_attr( $max_chars ) . '"';
		}
		?> class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-textfield"
           name="<?php echo esc_attr( $name ); ?>"
           data-price=""
           data-rules="<?php echo esc_attr( $rules ); ?>"
           data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
           data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
           data-freechars="<?php echo esc_attr( $freechars ); ?>"
           value="<?php echo esc_attr( $get_default_value ); ?>"
           id="<?php echo esc_attr( $id ); ?>"
			<?php if ( ! empty( $tax_obj ) ) {
				echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
			} ?>
           type="<?php echo esc_attr( $input_type ); ?>"<?php
		if ( $input_type == "number" ) {
			echo ' step="any" pattern="[0-9]" inputmode="numeric"';
			if ( isset( $min ) && $min !== '' ) {
				echo ' min="' . esc_attr( $min ) . '"';
			}
			if ( isset( $max ) && $max !== '' ) {
				echo ' max="' . esc_attr( $max ) . '"';
			}
		}
		?> /></label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>