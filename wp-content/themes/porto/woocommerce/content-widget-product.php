<?php
/**
 * The template for displaying product widget entries
 *
 * @version 3.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings;

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}
?>

<li>
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a aria-label="product" class="product-image" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php porto_widget_product_thumbnail(); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>

	<div class="product-details">
		<a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
			<span class="product-title"><?php echo wp_kses_post( $product->get_name() ); ?></span>
		</a>

		<?php if ( ! empty( $show_rating ) ) : ?>
			<?php echo porto_get_rating_html( $product ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php endif; ?>
		<?php echo porto_filter_output( $product->get_price_html() ); // PHPCS:Ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
