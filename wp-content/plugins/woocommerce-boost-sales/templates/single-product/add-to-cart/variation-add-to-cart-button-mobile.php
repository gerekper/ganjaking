<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php
	if(function_exists('wbs_woocommerce_quantity_input')){
		wbs_woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? 1 : $product->get_min_purchase_quantity(),
			),$product
		);
	}else{
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? 1 : $product->get_min_purchase_quantity(),
			),$product
		);
	}

	?>
	<button type="submit" class="wbs-single_add_to_cart_button button alt"><?php echo esc_html__( 'Add to cart','woocommerce-boost-sales') ; ?></button>
	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />
	<input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
