<?php
/**
 * Product Bundle add-to-cart buttons wrapper template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/add-to-cart/bundle-add-to-cart.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 6.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="cart bundle_data bundle_data_<?php echo $product_id; ?>" data-bundle_form_data="<?php echo esc_attr( json_encode( $bundle_form_data ) ); ?>" data-bundle_id="<?php echo $product_id; ?>"><?php

	if ( $is_purchasable ) {

		/** WC Core action. */
		do_action( 'woocommerce_before_add_to_cart_button' );

		?><div class="bundle_wrap">
			<div class="bundle_price"></div>
			<?php
				/**
				 * 'woocommerce_bundles_after_bundle_price' action.
				 *
				 * @since 6.7.6
				 */
				do_action( 'woocommerce_after_bundle_price' );
			?>
			<div class="bundle_error" style="display:none">
				<div class="woocommerce-info">
					<ul class="msg"></ul>
				</div>
			</div>
			<?php
				/**
				 * 'woocommerce_bundles_after_bundle_price' action.
				 *
				 * @since 6.7.6
				 */
				do_action( 'woocommerce_before_bundle_availability' );
			?>
			<div class="bundle_availability">
			<?php
				// Availability html.
				echo $availability_html;
			?>
			</div>
			<?php
				/**
				 * 'woocommerce_bundles_after_bundle_price' action.
				 *
				 * @since 6.7.6
				 */
				do_action( 'woocommerce_before_bundle_add_to_cart_button' );
			?>
			<div class="bundle_button"><?php

				/**
				 * woocommerce_bundles_add_to_cart_button hook.
				 *
				 * @hooked wc_pb_template_add_to_cart_button - 10
				 */
				do_action( 'woocommerce_bundles_add_to_cart_button', $product );

			?></div><?php
			// No longer needed as this has been moved to the 'add-to-cart/composite-button.php' template. Leaving this here for back-compat.
			?><input type="hidden" name="add-to-cart" value="<?php echo $product_id; ?>" />
		</div><?php

		/** WC Core action. */
		do_action( 'woocommerce_after_add_to_cart_button' );

	} else {

		?><div class="bundle_unavailable woocommerce-info"><?php
			echo $purchasable_notice;
		?></div><?php
	}

?></div>
