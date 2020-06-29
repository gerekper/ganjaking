<?php
/**
 * Component Title template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/component-title.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$component_title = $step ? apply_filters( 'woocommerce_composite_step_title', sprintf( __( '<span class="step_index">%d</span> <span class="step_title">%s</span>', 'woocommerce-composite-products' ), $step, $title ), $title, $step ) : $title;

?>
<h2 class="step_title_wrapper component_title <?php echo $is_toggled ? 'component_title_toggled' : ''; ?>">
	<span class="aria_title" aria-label="<?php echo esc_attr( $title ); ?>" tabindex="-1"><?php echo $title; ?></span>
	<span class="component_title_text step_title_text"><?php echo $component_title; ?></span><?php

	// Add button to assist screen-readers.
	if ( $is_toggled ) {
		?><button class="component_title_button aria_button" aria-label="<?php echo sprintf( __( 'Toggle %s', 'woocommerce-composite-products' ), $title ); ?>" aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"></button><?php
	}

?></h2>
