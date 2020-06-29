<?php
/**
 * Template for bundles
 * @version 4.8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/** @var WC_Product_Wbs_Bundle $product */
global $product;
?>

<?php
// Availability
$availability      = $product->get_availability();
$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';

echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );

?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="woocommerce-boost-sales-cart-form" method="post" enctype='multipart/form-data'>


		<?php
		$bundled_items = $product->get_bundled_items();
		if ( $bundled_items ) {
			echo '<table class="wbs-wcpb-product-bundled-items">';
			foreach ( $bundled_items as $bundled_item ) {
				/**
				 * @var WBS_WC_Bundled_Item $bundled_item
				 */
				$bundled_product = $bundled_item->get_product();
				$bundled_post    = get_post( $bundled_product->get_id() );
				$quantity        = $bundled_item->get_quantity();
				?>
				<tr>
					<td class="wbs-wcpb-product-bundled-item-image"> <?php echo $bundled_product->get_image(); ?></td>
					<td class="wbs-wcpb-product-bundled-item-data">
						<div><a href="<?php echo $bundled_product->get_permalink(); ?>">
								<?php echo $quantity . ' x ' . $bundled_product->get_title(); ?>
							</a>
						</div>
						<p><?php echo $bundled_post->post_excerpt; ?></p>

						<?php
						if ( $bundled_product->has_enough_stock( $quantity ) && $bundled_product->is_in_stock() ) {
							echo '<div class="wbs-wcpb-product-bundled-item-instock">' . __( 'In stock', 'woocommerce-boost-sales' ) . '</div>';
						} else {
							echo '<div class="wbs-wcpb-product-bundled-item-outofstock">' . __( 'Out of stock', 'woocommerce-boost-sales' ) . '</div>';
						}
						?>

					</td>
				</tr>
				<?php
			}
			echo '</table>';
		}
		?>

		<?php
		if ( ! $product->is_sold_individually() ) {
			woocommerce_quantity_input(
				array(
					'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product ),
					'input_value' => isset( $_POST['quantity'] ) ? 1 : $product->get_min_purchase_quantity(),
				)
			);
		}
		?>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

		<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html__( 'Add to cart','woocommerce-boost-sales') ; ?></button>

	</form>


<?php endif; ?>