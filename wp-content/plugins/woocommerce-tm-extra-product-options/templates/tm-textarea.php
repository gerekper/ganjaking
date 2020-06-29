<?php
/**
 * The template for displaying the textarea element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-textarea.php
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
    <textarea<?php
    if ( isset( $placeholder ) ) {
	    echo ' placeholder="' . esc_attr( $placeholder ) . '"';
    }
    if ( isset( $min_chars ) && $min_chars != '' ) {
	    echo ' minlength="' . esc_attr( $min_chars ) . '"';
    }
    if ( isset( $max_chars ) && $max_chars != '' ) {
	    echo ' maxlength="' . esc_attr( $max_chars ) . '"';
    }
    ?> class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-textarea"
       name="<?php echo esc_attr( $name ); ?>"
       data-price=""
       data-rules="<?php echo esc_attr( $rules ); ?>"
       data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
       data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
       data-freechars="<?php echo esc_attr( $freechars ); ?>"
       id="<?php echo esc_attr( $id ); ?>"
       rows="5"
	    <?php if ( ! empty( $tax_obj ) ) {
		    echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
	    } ?>
       cols="20"><?php echo esc_textarea( $get_default_value ); ?></textarea></label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>