<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH Custom ThankYou Page for Woocommerce
 */

/**
 * PDF Order Table Template
 *
 * Override this template by copying it to [your theme folder]/woocommerce/ctpw_pdf/order_infos_pdf_template_order_table.php
 *
 * @author        Yithemes
 * @package       YITH Custom ThankYou Page for Woocommerce
 * @version       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$downloads      = $order->get_downloadable_items();
$show_downloads = $order->has_downloadable_item() && $order->is_download_permitted();
$show_title     = true;

if ( $show_downloads ) { ?>
	<section class="woocommerce-order-downloads">
		<?php if ( isset( $show_title ) ) : ?>
			<h2 class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
		<?php endif; ?>

		<table class="woocommerce-table woocommerce-table--order-downloads shop_table shop_table_responsive order_details">
			<thead>
			<tr>
				<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
					<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
				<?php endforeach; ?>
			</tr>
			</thead>

			<?php foreach ( $downloads as $download ) : ?>
				<tr>
					<?php foreach ( wc_get_account_downloads_columns() as $column_id => $column_name ) : ?>
						<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
							<?php
							if ( has_action( 'woocommerce_account_downloads_column_' . $column_id ) ) {
								do_action( 'woocommerce_account_downloads_column_' . $column_id, $download );
							} else {
								switch ( $column_id ) {
									case 'download-product':
										if ( $download['product_url'] ) {
											echo '<a href="' . esc_url( $download['product_url'] ) . '">' . esc_html( $download['product_name'] ) . '</a>';
										} else {
											echo esc_html( $download['product_name'] );
										}
										break;
									case 'download-file':
										echo '<a href="' . esc_url( $download['download_url'] ) . '" class="woocommerce-MyAccount-downloads-file button alt">' . esc_html( $download['download_name'] ) . '</a>';
										break;
									case 'download-remaining':
										echo is_numeric( $download['downloads_remaining'] ) ? esc_html( $download['downloads_remaining'] ) : esc_html__( '&infin;', 'woocommerce' );
										break;
									case 'download-expires':
										if ( ! empty( $download['access_expires'] ) ) {
											echo '<time datetime="' . esc_attr( date( 'Y-m-d', strtotime( $download['access_expires'] ) ) ) . '" title="' . esc_attr( strtotime( $download['access_expires'] ) ) . '">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) ) . '</time>';
										} else {
											esc_html_e( 'Never', 'woocommerce' );
										}
										break;
								}
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</table>
	</section>
	<?php
}
?>


<h2 class="woocommerce-order-details__title order_details_title">
	<?php
	// APPLY_FILTER ctpw_order_details_title: change the Order Details Table title.
	echo wp_kses_post( apply_filters( 'ctpw_order_details_title', esc_html__( 'Order details', 'yith-custom-thankyou-page-for-woocommerce' ) ) );
	?>
</h2>
<table class="shop_table order_details">
	<thead>
	<tr>
		<th class="product-name"><?php esc_html_e( 'Product', 'yith-custom-thankyou-page-for-woocommerce' ); ?></th>
		<th class="product-total"><?php esc_html_e( 'Total', 'yith-custom-thankyou-page-for-woocommerce' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( count( $order->get_items() ) > 0 ) {

		foreach ( $order->get_items() as $item ) {
			$_product = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );

			/* woocommerce 3.1 compatibility */
			if ( version_compare( WC()->version, '3.1', '<' ) ) {
				$item_meta = new WC_Order_Item_Meta( $item, $_product );
			} else {
				$item_meta = new WC_Order_Item_Product( $_product );
			}

			?>
			<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
				<td class="product-name">
					<?php
					// APPLY_FILTER: yith_ctwp_show_product_thumb : set to true to show product thumbnail in order details table: value can be true or false.
					$ctpw_show_product_thumb = apply_filters( 'yith_ctwp_show_product_thumb_pdf', false );
					if ( $ctpw_show_product_thumb ) {
							$thumbsize           = apply_filters( 'yith_ctpw_pdf_thumb_size', array( 100, 100 ) );
							$pdf_thumb_max_width = apply_filters( 'yith_ctpw_pdf_thumb_max_size', '90' );
							$image_id            = $_product->get_image_id();
						if ( $image_id ) {
								$thumbnail_id  = $image_id;
								$thumbnail_url = get_attached_file( $thumbnail_id );
						} else {
								$thumbnail_url = function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src() : '';
						}

						echo '<img class="yith_ctpw_thumb" style="max-width: ' . intval( $pdf_thumb_max_width ) . 'px" src="' . $thumbnail_url . '" />';
					}
					?>
					<?php

					if ( $_product && ! $_product->is_visible() ) {
						echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}

					echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

					$formatted_meta_data = $item->get_formatted_meta_data();
					if ( $formatted_meta_data ) {
						echo '<ul class="yctpw-item-meta wc-item-meta">';

						foreach ( $formatted_meta_data as $data => $dobj ) {
							echo '<li><strong class="wc-item-meta-label">' . $dobj->display_key . ':</strong><p>' . $dobj->display_value . '</p></li>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						echo '</ul>';
					}

					?>
				</td>
				<td class="woocommerce-table__product-total product-total">
					<?php echo $order->get_formatted_line_subtotal( $item ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</td>
			</tr>
			<?php
			$show_purchase_note = $order->has_status(
				apply_filters(
					'woocommerce_purchase_note_order_statuses',
					array(
						'completed',
						'processing',
					)
				)
			);
			if ( $show_purchase_note ) {
				?>
				<tr class="product-purchase-note">
					<td colspan="2"><?php echo wpautop( do_shortcode( wp_kses_post( $_product->get_purchase_note() ) ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>

				<?php
			}
		} // end for.
	}

	do_action( 'woocommerce_order_items_table', $order );
	?>
	</tbody>
	<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
	<tr>
		<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
		<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
	</tr>
				<?php
			}
			?>
	<?php if ( $order->get_customer_note() ) : ?>
	<tr>
		<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
		<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
	</tr>
	<?php endif; ?>
	</tfoot>
</table>

