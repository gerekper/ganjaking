<?php
/**
 * WAPO Template
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 4.0.0
 *
 * @var WC_Product $product
 * @var WC_Product_Variation $variation
 * @var string $total_price_box
 * @var float $blocks_product_price
 */
$suffix          = '';
$suffix_callback = '';

// translators: [FRONT] Add-ons table shown on the product page
$product_price_label = apply_filters( 'yith_wapo_table_product_price_label', __( 'Product price', 'yith-woocommerce-product-add-ons' ) );
// translators: [FRONT] Add-ons table shown on the product page
$total_options_label = apply_filters( 'yith_wapo_table_total_options_label', __( 'Total options', 'yith-woocommerce-product-add-ons' ) );
// translators: [FRONT] Add-ons table shown on the product page
$order_total_label   = apply_filters( 'yith_wapo_table_order_total_label', __( 'Order total', 'yith-woocommerce-product-add-ons' ) );

$price_display_suffix = get_option( 'woocommerce_price_display_suffix', '' );
$price_suffix         = ' <small>' . $price_display_suffix . '</small>';

if ( $price_display_suffix ) {
	if ( strpos( $price_display_suffix, '{price_including_tax}' ) !== false ) {
		$suffix          = '{price_including_tax}';
		$suffix_callback = 'wc_get_price_including_tax';
	} elseif ( strpos( $price_display_suffix, '{price_excluding_tax}' ) !== false ) {
		$suffix          = '{price_excluding_tax}';
		$suffix_callback = 'wc_get_price_excluding_tax';
	}
	if ( $suffix_callback ) {
		$price_callback       = $suffix_callback( $product );
		$price_callback       = wc_price( $price_callback );
		$price_display_suffix = str_replace(
			$suffix,
			$price_callback,
			$price_display_suffix
		);
		$price_suffix         = $price_display_suffix;
	}
}

?>

<div id="wapo-total-price-table">
	<table class="<?php echo esc_attr( $total_price_box ); ?>">
		<?php if ( $blocks_product_price > 0 ) : ?>
			<tr class="wapo-product-price" style="<?php echo esc_attr( 'only_final' === $total_price_box ? 'display: none;' : '' ); ?>">
				<th><?php echo esc_html( $product_price_label ); ?>:</th>
				<td id="wapo-total-product-price"><?php echo wp_kses_post( wc_price( $blocks_product_price ) ); ?><?php echo wc_tax_enabled() ? wp_kses_post( $price_suffix ) : ''; ?></td>
			</tr>
		<?php endif; ?>
		<tr class="wapo-total-options" style="<?php echo esc_attr( 'all' !== $total_price_box ? 'display: none;' : '' ); ?>">
			<th><?php echo esc_html( $total_options_label ); ?>:</th>
			<td id="wapo-total-options-price"></td>
		</tr>
		<?php if ( apply_filters( 'yith_wapo_table_hide_total_order', true ) ) { ?>
			<tr class="wapo-total-order">
				<th><?php echo esc_html( $order_total_label ); ?>:</th>
				<td id="wapo-total-order-price"></td>
			</tr>
		<?php } ?>
	</table>
</div>
