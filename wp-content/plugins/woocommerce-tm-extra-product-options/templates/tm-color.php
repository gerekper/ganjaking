<?php
/**
 * The template for displaying the color picker element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-color.php
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
        <input class="<?php echo esc_attr( $fieldtype ); ?> tm-color-picker tm-epo-field tmcp-textfield"
               name="<?php echo esc_attr( $name ); ?>"
               data-show-input="true"
               data-show-initial="true"
               data-allow-empty="true"
               data-show-alpha="false"
               data-show-palette="false"
               data-clickout-fires-change="false"
               data-show-buttons="true"
               data-preferred-format="hex"
               data-price=""
               data-rules="<?php echo esc_attr( $rules ); ?>"
               data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
               data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
               value="<?php echo esc_attr( $get_default_value ); ?>"
               id="<?php echo esc_attr( $id ); ?>"
			<?php if ( ! empty( $tax_obj ) ) {
				echo 'data-tax-obj="' . esc_attr( $tax_obj ) . '" ';
			} ?>
               type="text"/>
    </label>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>