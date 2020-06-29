<?php
/**
 * Composite Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/add-to-cart/composite.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    2.4.0
 * @version  3.12.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form method="post" enctype="multipart/form-data" class="cart cart_group composite_form cp-no-js <?php echo esc_attr( $classes ); ?>"><?php

	$loop  = 0;
	$steps = count( $components );

	/**
	 * woocommerce_composite_before_components hook
	 *
	 * @hooked wc_cp_before_components - 10
	 */
	do_action( 'woocommerce_composite_before_components', $components, $product );

	foreach ( $components as $component_id => $component ) {

		$loop++;

		$tmpl_args = array(
			'product'           => $product,
			'component_id'      => $component_id,
			'component'         => $component,
			'component_data'    => $component->get_data(),
			'component_classes' => $component->get_classes(),
			'step'              => $loop,
			'steps'             => $steps,
		);

		if ( $navigation_style === 'single' ) {
			wc_get_template( 'single-product/component-single-page.php', $tmpl_args, '', WC_CP()->plugin_path() . '/templates/' );
		} elseif ( $navigation_style === 'progressive' ) {
			wc_get_template( 'single-product/component-single-page-progressive.php', $tmpl_args, '', WC_CP()->plugin_path() . '/templates/' );
		} else {
			wc_get_template( 'single-product/component-multi-page.php', $tmpl_args, '', WC_CP()->plugin_path() . '/templates/' );
		}
	}

	/**
	 * woocommerce_composite_after_components hook
	 *
	 * @hooked wc_cp_after_components - 10
	 * @hooked wc_cp_no_js_msg        - 15
	 */
	do_action( 'woocommerce_composite_after_components', $components, $product );

?></form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
