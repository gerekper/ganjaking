<?php
/**
 * The template for displaying the upload element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-upload.php
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
    <label class="tm-epo-field-label<?php echo esc_attr( $style ); ?>" for="<?php echo esc_attr( $id ); ?>">
    <?php 
    if ( ! empty($upload_text) ){
        echo '<span>' . esc_html( $upload_text ) . '</span>';
    }
    ?>
        <input type="file" class="<?php echo esc_attr( $fieldtype ); ?> tm-epo-field tmcp-upload"
               data-file="<?php echo esc_attr( $saved_value ); ?>"
               data-filename="<?php echo esc_attr( basename( $saved_value ) ); ?>"
               data-price=""
               data-rules="<?php echo esc_attr( $rules ); ?>"
               data-original-rules="<?php echo esc_attr( $original_rules ); ?>"
               data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
               id="<?php echo esc_attr( $id ); ?>"
               name="<?php echo esc_attr( $name ); ?>"/>
    </label>
    <small><?php echo sprintf( esc_html__( '(max file size %s)', 'woocommerce-tm-extra-product-options' ), $max_size ) ?></small>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_price.php' ); ?>
	<?php include( THEMECOMPLETE_EPO_TEMPLATE_PATH . '_quantity_end.php' ); ?>
	<?php do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() ); ?>
</li>