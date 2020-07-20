<?php
/**
 * Form template
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.3.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( empty( $product ) ) {
	global $product;
}

if ( ! isset( $products ) ) {
	return;
}
/**
 * @type $product WC_Product
 */
// set query
$url           = ! is_null( $product ) ? $product->get_permalink() : '';
$url           = add_query_arg( 'action', 'yith_bought_together', $url );
$url           = wp_nonce_url( $url, 'yith_bought_together' );
$metas         = yith_wfbt_get_meta( $product );
$meta_products = isset( $metas['products'] ) ? $metas['products'] : array();
?>

<div class="yith-wfbt-section woocommerce">
	<?php if ( $title ) {
		echo '<h3>' . esc_html( $title ) . '</h3>';
	}

	if ( ! empty( $additional_text ) ) {
		echo '<p class="additional-text">' . wp_kses_post( $additional_text ) . '</p>';
	}
	?>

	<form class="yith-wfbt-form" method="post" action="<?php echo esc_url( $url ) ?>">
		<?php if ( ! $show_unchecked ) : ?>
			<table class="yith-wfbt-images">
				<tbody>
				<tr>
					<?php $i = 0;
					foreach ( $products as $product ) :
						if ( in_array( $product->get_id(), $unchecked ) || $product instanceof WC_Product_Variable ) {
							continue;
						}
						?>

						<?php if ( $i > 0 ) : ?>
						<td class="image_plus image_plus_<?php echo esc_attr( $i ); ?>" data-rel="offeringID_<?php echo esc_attr( $i ); ?>">+</td>
						<?php endif; ?>
						<td class="image-td" data-rel="offeringID_<?php echo esc_attr( $i ) ?>">
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo $product->get_image( 'yith_wfbt_image_size' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
							</a>
						</td>
						<?php $i++; endforeach; ?>
				</tr>
				</tbody>
			</table>
		<?php endif; ?>

		<?php if ( ! $is_empty ) : ?>
			<div class="yith-wfbt-submit-block">
				<div class="price_text">
                    <span class="total_price_label">
                        <?php echo esc_html( $label_total ); ?>:
                    </span>
					&nbsp;
					<span class="total_price">
                        <?php echo $total; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
                    </span>
				</div>

				<input type="submit" class="yith-wfbt-submit-button button" value="<?php echo esc_html( $label ); ?>">
			</div>
		<?php endif; ?>

		<ul class="yith-wfbt-items">
			<?php $j                 = 0;
			foreach ( $products as $product ) :
				$product_id = $product->get_id();
				$is_variable         = $product instanceof WC_Product_Variable;
				$is_choise_variation = $product instanceof WC_Product_Variation && in_array( $product->get_parent_id(), $meta_products ) ? true : false;
				$variable_product_id = $is_variable ? $product_id : $product->get_parent_id();
				?>
				<li class="yith-wfbt-item <?php echo $is_variable ? 'choise-variation' : ''; ?>">
					<label for="offeringID_<?php echo esc_attr( $j ); ?>">
						<input type="checkbox" name="offeringID[]" id="offeringID_<?php echo esc_attr( $j ); ?>" class="active"
							value="<?php echo esc_attr( $product_id ); ?>"
							<?php echo ( ! in_array( $product_id, $unchecked ) && ! $show_unchecked && ! $is_variable ) ? 'checked="checked"' : ''; ?>
							<?php echo $is_variable ? 'disabled' : ''; ?>
							data-variable_product_id= <?php echo esc_attr( $variable_product_id ); ?>
						>
						<?php if ( $product_id != $main_product_id ) : ?>
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php endif ?>

							<span class="product-name">
                            <?php
							$this_product_label = apply_filters( 'yith_wfbt_this_product_label', __( 'This Product', 'yith-woocommerce-frequently-bought-together' ) . ': ' );
							echo ( ( $product_id == $main_product_id ) ? esc_html( $this_product_label ) : '' ) . sprintf( '%1$s %2$s', $product->get_title(), wc_get_formatted_variation( $product, true ) ); ?>
                        </span>

							<?php if ( $product_id != $main_product_id ) : ?>
						</a>
					<?php endif; ?>

						- <span class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?></span>

						<?php if ( $is_variable || $is_choise_variation ): ?>
							<button class="yith-wfbt-open-modal"
								data-product_id="<?php echo esc_attr( $variable_product_id ); ?>"><?php echo esc_html( $popup_button_label ); ?></button>
						<?php endif; ?>

					</label>
					<?php do_action( 'yith_wfbt_end_item', $product ); ?>
				</li>
				<?php $j++; endforeach; ?>
		</ul>

		<input type="hidden" name="yith-wfbt-main-product" value="<?php echo esc_attr( $main_product_id ); ?>">
	</form>
</div>

<div id="yith-wfbt-modal" class="modal">
	<a href="#" rel="modal:close"><?php esc_html_e( 'Close', 'yith-woocommerce-frequently-bought-together' ) ?></a>
</div>