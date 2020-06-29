<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings       = new VI_WBOOSTSALES_Data();
$attribute_keys = array_keys( $attributes );
$action         = get_permalink();
if ( $settings->get_option( 'go_to_cart' ) ) {
	$action = wc_get_cart_url();
}
?>
<form class="wbs-variations_form cart" action="<?php esc_attr_e( $action ) ?>" method="post"
      enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>"
      data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
        <p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
	<?php else : ?>
        <table class="variations" cellspacing="0">
            <tbody>
			<?php foreach ( $attributes as $attribute_name => $options ) : ?>
                <tr>
                    <td class="label"><label
                                for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
                    </td>
                    <td class="value">
						<?php
						$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
						if ( function_exists( 'wbs_wc_dropdown_variation_attribute_options' ) ) {
							wbs_wc_dropdown_variation_attribute_options( array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'selected'  => $selected
							) );
						} else {
							wc_dropdown_variation_attribute_options( array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'selected'  => $selected
							) );
						}
						echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) : '';
						?>
                    </td>
                </tr>
			<?php endforeach; ?>
            </tbody>
        </table>

        <div class="single_variation_wrap">
			<?php


			/**
			 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action( 'woocommerce_boost_sales_single_variation',$product );

			?>
        </div>
	<?php endif; ?>

</form>

