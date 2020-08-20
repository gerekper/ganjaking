<?php
/**
 * The template for displaying the start of the quantity selector of an option
 * (Internal use only)
 *
 * This template is NOT meant to be overridden
 *
 * @author  themeComplete
 * @package WooCommerce Extra Product Options/Templates
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

$located = wc_locate_template( 'tm-element-quantity-start.php', THEMECOMPLETE_EPO_DISPLAY()->get_namespace(), apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, NULL, NULL ) );

if ( ! file_exists( $located ) ) {
	wc_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'woocommerce' ), '<code>' . $located . '</code>' ), '2.1' );

	return;
}

include( $located );
