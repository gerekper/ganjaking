<?php
/**
 * The template for displaying the description of the option choice
 * (Internal use only)
 *
 * This template is NOT meant to be overridden
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates
 * @version 6.4
 * phpcs:disable WordPress.Files.FileName
 */

defined( 'ABSPATH' ) || exit;

$located = wc_locate_template( 'tm-element-choice-description.php', THEMECOMPLETE_EPO_DISPLAY()->get_template_path(), apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_DISPLAY()->get_default_path(), null, null ) );

if ( ! file_exists( $located ) ) {
	/* translators: %s: file name */
	wc_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( '%s does not exist.', 'woocommerce' ), '<code>' . $located . '</code>' ), '2.1' );

	return;
}

require $located;
