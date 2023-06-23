<?php
/**
 * Simple Bundled Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-product-simple.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?><div class="cart" data-title="<?php echo esc_attr( $bundled_item->get_title() ); ?>" data-product_title="<?php echo esc_attr( $bundled_item->get_product()->get_title() ); ?>" data-visible="<?php echo $bundled_item->is_visible() ? 'yes' : 'no'; ?>" data-optional_suffix="<?php echo esc_attr( $bundled_item->get_optional_suffix() ); ?>" data-optional="<?php echo $bundled_item->is_optional() ? 'yes' : 'no'; ?>" data-type="<?php echo esc_attr( $bundled_item->get_product()->get_type() ); ?>" data-bundled_item_id="<?php echo esc_attr( $bundled_item->get_id() ); ?>" data-custom_data="<?php echo wc_esc_json( json_encode( $custom_product_data ) ); ?>" data-product_id="<?php echo esc_attr( $bundled_item->get_product()->get_id() ); ?>" data-bundle_id="<?php echo esc_attr( $bundle->get_id() ); ?>">
	<div class="bundled_item_wrap">
		<div class="bundled_item_cart_content" <?php echo $bundled_item->is_optional() && ! $bundled_item->is_optional_checked() ? 'style="display:none"' : ''; ?>>
			<div class="bundled_item_cart_details"><?php

				if ( ! $bundled_item->is_optional() ) {
					wc_get_template( 'single-product/bundled-item-price.php', array(
						'bundled_item' => $bundled_item
					), false, WC_PB()->plugin_path() . '/templates/' );
				}

				// Availability html.
				echo $bundled_item->get_availability_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

				/**
				 * 'woocommerce_bundled_product_add_to_cart' hook.
				 *
				 * Used to output content normally hooked to 'woocommerce_before_add_to_cart_button'.
				 *
				 * @param mixed           $bundled_product_id
				 * @param WC_Bundled_Item $bundled_item
				 */
				do_action( 'woocommerce_bundled_product_add_to_cart', $bundled_item->get_product()->get_id(), $bundled_item );

			?></div>
			<div class="bundled_item_after_cart_details bundled_item_button"><?php

				/**
				 * 'woocommerce_after_bundled_item_cart_details' hook.
				 *
				 * @since 5.0.0
				 *
				 * @param WC_Bundled_Item $bundled_item
				 *
				 * @hooked wc_pb_template_default_bundled_item_qty - 10
				 */
				do_action( 'woocommerce_after_bundled_item_cart_details', $bundled_item );

			?></div>
		</div>
	</div>
</div>
