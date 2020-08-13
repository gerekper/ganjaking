<?php
/**
 * The template for displaying the price of an option
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-element-price.php
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

if ( ! isset( $hide_amount ) || ! isset( $amount ) || ! isset( $original_amount ) ) {
	return;
}

if ( ! isset( $textbeforeprice ) ){
	$textbeforeprice = "";
}
if ( ! isset( $textafterprice ) ){
	$textafterprice = "";
}

?>
<span class="tc-price-wrap">
<?php 
if ( isset( $hide_amount ) && isset( $textbeforeprice ) && $textbeforeprice != '' ) : ?>
	<span class="before-amount<?php if ( ! empty( $hide_amount ) ){ echo esc_attr( $hide_amount ); } ?>"><?php echo apply_filters( 'wc_epo_kses', esc_html( $textbeforeprice ), $textbeforeprice ); ?></span>
<?php endif; ?>
    <span class="price tc-price<?php echo esc_attr( $hide_amount ); ?>">
	<span class="amount"><?php echo esc_html( $amount ); ?></span>
</span>
<?php 
if ( isset( $textafterprice ) && $textafterprice != '' ) : ?>
	<span class="after-amount"><?php echo apply_filters( 'wc_epo_kses', esc_html( $textafterprice ), $textafterprice ); ?></span>
<?php endif; ?>
</span>
<?php
if ( isset( $tm_element_settings ) && isset( $tm_element_settings['cdescription'] ) && isset( $field_counter ) && isset( $tm_element_settings['cdescription'][ $field_counter ] ) ) {
	if ( ! empty( $tm_element_settings['cdescription'][ $field_counter ] ) || ( ( isset( $tm_element_settings['cdescription'] ) && is_array( $tm_element_settings['cdescription'] ) && count( $tm_element_settings['cdescription'] ) > 1 ) && ( isset( $tm_element_settings['type'] ) && $tm_element_settings['type'] == 'select' ) ) ) {
		if ( THEMECOMPLETE_EPO()->tm_epo_description_inline == 'yes' ) {
			echo '<div class="tc-inline-description">' . apply_filters( 'wc_epo_kses', wp_kses_post( $tm_element_settings['cdescription'][ $field_counter ] ), $tm_element_settings['cdescription'][ $field_counter ] ) . '</div>';
		} else {
			echo '<i data-tm-tooltip-html="' . esc_attr( apply_filters( 'wc_epo_kses', $tm_element_settings['cdescription'][ $field_counter ], $tm_element_settings['cdescription'][ $field_counter ] ) ). '" class="tm-tooltip tc-tooltip tcfa tcfa-question-circle"></i>';
		}
	}
}
