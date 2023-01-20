<?php
/**
 * Mix and Match Product Quantity
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-quantity.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 2.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $child_item->get_product()->is_purchasable() || ! $child_item->get_product()->is_in_stock() ) {
	echo wp_kses_post( $child_item->get_availability_html() );
	return;
}

// Checkbox input
if ( $input_args[ 'step' ] === $input_args[ 'max_value' ] && $input_args[ 'min_value' ] !== $input_args[ 'max_value' ] ) { ?>

	<div class="quantity mnm-checkbox-qty">
		<input id="<?php echo esc_attr( $input_args[ 'input_id' ] );?>" type="checkbox" class="mnm-quantity mnm-checkbox qty" name="<?php echo esc_attr( $child_item->get_input_name() );?>" value="<?php echo esc_attr( $input_args[ 'max_value' ] );?>" <?php checked( $input_args[ 'max_value' ] === $child_item->get_quantity( 'value' ), true );?>/>
		<label for="<?php echo esc_attr( $input_args[ 'input_id' ] );?>"><?php echo wp_kses_post( $input_args[ 'checkbox_label' ] );?></label>
	</div>

	<?php

// Default number input.
} else {

	if ( $input_args[ 'max_value' ] && $input_args[ 'min_value' ] === $input_args[ 'max_value' ] ) { ?>

		<p class="required-quantity"><?php echo wp_kses_post( $input_args[ 'required_text' ] ); ?></span></p>
		
	<?php
	}

	woocommerce_quantity_input( $input_args, $child_item->get_product() );

}
