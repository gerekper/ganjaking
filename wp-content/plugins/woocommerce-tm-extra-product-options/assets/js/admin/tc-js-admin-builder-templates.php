<?php
/**
 * The admin javascript-based template for the builder mode
 *
 * NOTE that this file is not meant to be overriden
 *
 * @see           https://codex.wordpress.org/Javascript_Reference/wp.template
 * @author        themeComplete
 * @package       WooCommerce Extra Product Options/Templates
 * @version       4.8.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'THEMECOMPLETE_EPO_BUILDER' ) || ! function_exists( 'THEMECOMPLETE_EPO_ADMIN_GLOBAL' ) || ! function_exists( 'THEMECOMPLETE_EPO_WPML' ) ) {
	return;
}

global $post;

if ( ! $post ) {

	if ( THEMECOMPLETE_EPO_ADMIN_GLOBAL()->post !== FALSE ) {
		$the_post = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->post;
	} else {
		return;
	}

} else {
	$the_post = $post;
}

$basetype = THEMECOMPLETE_EPO_ADMIN_GLOBAL()->basetype;
if ( $basetype === FALSE ) {
	$basetype = THEMECOMPLETE_EPO_GLOBAL_POST_TYPE;
}

$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $the_post->ID, $the_post->post_type, $basetype );

// The template_elements and template_section_elements functions contain HTML code generated internally. 
?>
<script class="tm-hidden" type="text/template" id="tmpl-tc-builder-elements"><?php THEMECOMPLETE_EPO_BUILDER()->template_elements(); ?></script>
<script class="tm-hidden" type="text/template" id="tmpl-tc-builder-section"><?php THEMECOMPLETE_EPO_BUILDER()->template_section_elements( $wpml_is_original_product ); ?></script>
