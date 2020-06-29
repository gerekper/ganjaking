<?php
/**
 * Mix and Match Item Table Row
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-item.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.0.6
 * @deprecated 1.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<tr class="mnm_item" data-regular_price="<?php echo $product->is_priced_per_product() ? wc_get_price_to_display( $mnm_product, $mnm_product->get_regular_price() ) : 0; ?>" data-price="<?php echo $product->is_priced_per_product() ? wc_get_price_to_display( $mnm_product, $mnm_product->get_price() ) : 0; ?>">

	<td class="product-thumbnail">

		<?php

			/**
			 * woocommerce_mnm_item_thumbnail hook.
			 *
			 * @see woocommerce_template_mnm_product_thumbnail - 10
			 */
			do_action( 'woocommerce_mnm_row_item_thumbnail', $mnm_product, $product );
		?>

	</td>

	<td class="product-name">
		<?php
			/**
			 * woocommerce_mnm_row_item_column_two hook.
			 *
			 * @see woocommerce_template_mnm_product_title - 10
			 * @see woocommerce_template_mnm_product_attributes - 20
			 */
			do_action( 'woocommerce_mnm_row_item_description', $mnm_product, $product );
		?>

	</td>

	<td class="product-quantity">
		<?php
			/**
			 * woocommerce_mnm_row_item_column_three hook.
			 *
			 * @see woocommerce_template_mnm_product_quantity - 10
			 */
			do_action( 'woocommerce_mnm_row_item_quantity', $mnm_product, $product );
		?>

	</td>

</tr>
