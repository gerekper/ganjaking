<?php
/**
 * The template for displaying the start of an element for the local mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-field-start.php
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
<li id="<?php echo esc_attr( $field_id ); ?>" class="cpf_hide_element tm-extra-product-options-field tc-row tc-cell<?php
if ( $required ) {
	echo ' tm-epo-has-required';
}
if ( isset( $li_class ) ) {
	echo ' ' . esc_attr( $li_class );
} ?>">
    <span class="tm-epo-element-label"><?php echo esc_html( $title ); ?><?php if ( $required ) { ?><span
                class="tm-required">*</span><?php } ?></span>
    <div class="tm-extra-product-options-container">
        <ul data-original-rules="<?php echo esc_attr( $original_rules ); ?>" data-rules="<?php echo esc_attr( $rules ); ?>" data-rulestype="<?php echo esc_attr( $rules_type ); ?>"
            class="tmcp-ul-wrap tmcp-attributes tm-extra-product-options-<?php echo esc_attr( $type ); ?>">