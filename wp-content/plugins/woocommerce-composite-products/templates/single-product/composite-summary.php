<?php
/**
 * Composite paged mode Summary template
 *
 * By default, this template is hooked on the 'woocommerce_before_add_to_cart_button' action, found inside the composite add-to-cart template (composite-add-to-cart.php).
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/composite-summary.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @since    3.0.0
 * @version  6.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$summary_elements = count( $components );

/**
 * Filter the max number columns displayed in the summary.
 *
 * @param  int                   $num
 * @param  WC_Product_Composite  $product
 */
$max_columns = apply_filters( 'woocommerce_composite_component_summary_max_columns', 4, $product );

$summary_columns = min( $max_columns, $summary_elements );
$summary_classes = 'columns-' . $summary_columns;

/**
 * Filter to enable vertical display styles for the summary contents.
 *
 * @param  boolean               $force_vertical
 * @param  WC_Product_Composite  $product
 */
if ( apply_filters( 'woocommerce_composite_summary_vertical_style', false, $product ) ) {
	$summary_classes .= ' force_vertical';
}

?><div id="composite_summary_<?php echo $product_id; ?>" class="composite_summary <?php echo esc_attr( $summary_classes ); ?>" data-summary_columns="<?php echo esc_attr( $summary_columns ); ?>"><?php

	if ( $product->get_composite_layout_style_variation() === 'componentized' ) {

		?><h2 class="summary_title step_title_wrapper">
			<span class="aria_title" aria-label="<?php echo __( 'Your Selections', 'woocommerce-composite-products' ); ?>" tabindex="-1"><?php echo __( 'Your Selections', 'woocommerce-composite-products' ); ?></span>
			<span class="step_title_text"><?php echo __( 'Your Selections', 'woocommerce-composite-products' ); ?></span>
		</h2><?php

	} else {

		?><h2 class="summary_title step_title_wrapper"><?php
			$final_step = count( $components ) + 1;
			$title      = __( 'Review and Purchase', 'woocommerce-composite-products' );
			echo apply_filters( 'woocommerce_composite_step_title', sprintf( __( '<span class="step_index">%d</span> <span class="step_title">%s</span>', 'woocommerce-composite-products' ), $final_step, $title ), $title, $final_step );
		?></h2><?php
	}

	wc_get_template( 'single-product/composite-summary-content.php', array(
		'summary_columns'  => $summary_columns,
		'summary_elements' => $summary_elements,
		'components'       => $components,
		'product'          => $product,
	), '', WC_CP()->plugin_path() . '/templates/' );

?></div>
