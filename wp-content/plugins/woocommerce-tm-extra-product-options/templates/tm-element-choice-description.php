<?php
/**
 * The template for displaying the description of the option choice
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-element-choice-description.php
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
if ( isset( $tm_element_settings ) && isset( $tm_element_settings['cdescription'] ) && isset( $field_counter ) && isset( $tm_element_settings['cdescription'][ $field_counter ] ) ) {
	if ( ! empty( $tm_element_settings['cdescription'][ $field_counter ] ) || ( ( isset( $tm_element_settings['cdescription'] ) && is_array( $tm_element_settings['cdescription'] ) && count( $tm_element_settings['cdescription'] ) > 1 ) && ( isset( $tm_element_settings['type'] ) && 'select' === $tm_element_settings['type'] ) ) ) {
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_description_inline' ) ) {
			echo '<div class="tcwidth tcwidth-100 tc-inline-description">' . apply_filters( 'wc_epo_kses', wp_kses_post( $tm_element_settings['cdescription'][ $field_counter ] ), $tm_element_settings['cdescription'][ $field_counter ] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
		} else {
			echo '<i data-tm-tooltip-html="' . esc_attr( apply_filters( 'wc_epo_kses', $tm_element_settings['cdescription'][ $field_counter ], $tm_element_settings['cdescription'][ $field_counter ] ) ) . '" class="tm-tooltip tc-tooltip tcfa tcfa-question-circle tc-epo-style-space"></i>';
		}
	}
}
