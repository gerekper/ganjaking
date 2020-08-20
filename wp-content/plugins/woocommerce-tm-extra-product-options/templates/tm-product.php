<?php
/**
 * The template for displaying the product element for the builder mode
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/tm-product.php
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

$args['args'] = $args;
if ( isset( $args ) && isset( $layout_mode ) && ! empty( $layout_mode ) ) {

	do_action( 'wc_epo_before_product_element' );

	if ( is_readable( apply_filters( 'wc_epo_template_path_product_element', THEMECOMPLETE_EPO_TEMPLATE_PATH ) . apply_filters( 'wc_epo_template_element', 'products/template-' . $layout_mode . '.php', 'product', array() ) ) ) {
		wc_get_template(
			apply_filters( 'wc_epo_template_element', 'products/template-' . $layout_mode . '.php', 'product', array() ),
			$args,
			THEMECOMPLETE_EPO_DISPLAY()->get_namespace(),
			apply_filters( 'wc_epo_template_path_product_element', THEMECOMPLETE_EPO_TEMPLATE_PATH )
		);
	}

	do_action( 'wc_epo_after_product_element' );

}

do_action( 'tm_after_element', isset( $tm_element_settings ) ? $tm_element_settings : array() );
