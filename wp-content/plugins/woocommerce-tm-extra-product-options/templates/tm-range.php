<?php
/**
 * The template for displaying the range picker element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-range.php
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
<li class="tmcp-field-wrap<?php if ( ! empty( $show_picker_value ) ) { echo ' tm-show-picker-' . esc_attr( $show_picker_value ); } ?>">
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_start.php' ); ?>
    <div class="tm-range-picker<?php if ( $pips == "yes" ) { echo ' pips'; } ?>"
         data-min="<?php echo esc_attr( $min ); ?>"
         data-max="<?php echo esc_attr( $max ); ?>"
         data-step="<?php echo esc_attr( $step ); ?>"
         data-pips="<?php echo esc_attr( $pips ); ?>"
         data-noofpips="<?php echo esc_attr( $noofpips ); ?>"
         data-show-picker-value="<?php echo esc_attr( $show_picker_value ); ?>"
         data-field-id="<?php echo esc_attr( $id ); ?>"
         data-start="<?php echo esc_attr( $get_default_value ); ?>"></div>
    <label class="tm-epo-field-label tm-show-picker-value" for="<?php echo esc_attr( $id ); ?>"></label>
    <input<?php
	if ( isset( $placeholder ) ) {
		echo ' placeholder="' . esc_attr( $placeholder ) . '"';
	}
	if ( isset( $max_chars ) && $max_chars != '' ) {
		echo ' maxlength="' . esc_attr( $max_chars ) . '"';
	}
	?> class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-textfield tmcp-range"
       name="<?php echo esc_attr( $name ); ?>"
       data-price=""
       data-rules="<?php echo esc_attr( $rules ); ?>"
       data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
       data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
       value="<?php echo esc_attr( $get_default_value ); ?>"
       id="<?php echo esc_attr( $id ); ?>"
		<?php if ( ! empty( $tax_obj ) ) {
			echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
		} ?>
       type="hidden"/>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>