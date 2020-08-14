<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package YITH Woocommerce Request A Quote
 */

/**
 * HTML Template Email
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 */

// @var WC_Order $order_id .
$order_id   = yit_get_prop( $order, 'id', true );
$text_align = is_rtl() ? 'right' : 'left';

add_filter( 'woocommerce_is_attribute_in_product_name', '__return_false' );

?>
<?php if ( ( $before_list = yit_get_prop( $order, '_ywraq_request_response_before', true ) ) != '' ) : ?>
	<p><?php echo esc_html( apply_filters( 'ywraq_quote_before_list', $before_list, $order_id ) ); ?></p>
<?php endif; ?>

<?php
$colspan = 2;

do_action( 'yith_ywraq_email_before_raq_table', $order );
$currency = $order->get_currency();
?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;border-collapse: collapse;">
	<thead>
	<tr>
		<?php if ( 'yes' === get_option( 'ywraq_show_preview' ) ) : ?>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php endif ?>
		<th scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
		<th scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
		<th scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$items = $order->get_items();

	if ( ! empty( $items ) ) :

		foreach ( $items as $item ) :

			if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
				$_product = wc_get_product( $item['variation_id'] );
			} else {
				$_product = wc_get_product( $item['product_id'] );
			}

			if ( ! $_product ) {
				continue;
			}

			$subtotal = wc_price( $item['line_total'], array( 'currency', $currency ) );

			if ( 'yes' === get_option( 'ywraq_show_old_price' ) ) {
				$subtotal = ( $item['line_subtotal'] !== $item['line_total'] ) ? '<small><del>' . wc_price( $item['line_subtotal'], array( 'currency', $currency ) ) . '</del></small> ' . wc_price( $item['line_total'], array( 'currency', $currency ) ) : wc_price( $item['line_subtotal'], array( 'currency', $currency ) );
			}

			$title = $_product->get_title();

			if ( $_product->get_sku() !== '' && get_option( 'ywraq_show_sku' ) === 'yes' ) {
				$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
				$title .= ' ' . apply_filters( 'ywraq_sku_label_html', $sku, $_product );
			}

			// retro compatibility .
			$im = false;

			?>
			<tr>
				<?php
				if ( get_option( 'ywraq_show_preview' ) === 'yes' ) :
					$colspan = 3;
					?>
					<td scope="col" class="td" style="text-align:center;border: 1px solid #eee;">
						<?php

						$dimensions = wc_get_image_size( 'shop_thumbnail' );
						$height     = esc_attr( $dimensions['height'] );
						$width      = esc_attr( $dimensions['width'] );
						$src        = ( $_product->get_image_id() ) ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'shop_thumbnail' ) ) : wc_placeholder_img_src();

						?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><img src="<?php echo esc_url( $src ); ?>" height="<?php echo esc_attr( $height ); ?>" width="<?php echo esc_attr( $width ); ?>" /></a>
					</td>
				<?php endif ?>

				<td scope="col" class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;">
					<?php if ( apply_filters( 'ywraq_list_show_product_permalinks', true, 'pdf_quote_table' ) ) : ?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo wp_kses_post( $title ); ?></a>
					<?php else : ?>
						<?php echo wp_kses_post( $title ); ?>
					<?php endif ?>

						<small>
						<?php
						if ( $im ) {
							$im->display();
						} else {
							wc_display_item_meta( $item );
						}
						?>
							</small></td>
				<td scope="col" class="td" style="text-align:center;border: 1px solid #eee;"><?php echo esc_html( $item['qty'] ); ?></td>
				<td scope="col" class="td" style="text-align:right;border: 1px solid #eee;"><?php echo apply_filters( 'ywraq_quote_subtotal_item', wp_kses_post( ywraq_formatted_line_total( $order, $item ) ), $item['line_total'], $_product ); // phpcs:ignore ?></td>

			</tr>

			<?php
		endforeach;
		?>

		<?php
		foreach ( $order->get_order_item_totals() as $key => $total ) {
			?>
			<tr>
				<th scope="col" colspan="<?php echo esc_attr( $colspan ); ?>" style="text-align:right;border: 1px solid #eee;"><?php echo wp_kses_post( $total['label'] ); ?></th>
				<td scope="col" style="text-align:right;border: 1px solid #eee;"><?php echo wp_kses_post( $total['value'] ); ?></td>
			</tr>
			<?php
		}
		?>

	<?php endif; ?>
	</tbody>
</table>

<?php

do_action( 'yith_ywraq_email_after_raq_table', $order );
?>
