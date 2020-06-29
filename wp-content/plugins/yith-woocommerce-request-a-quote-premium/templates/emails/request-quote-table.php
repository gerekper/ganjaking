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
 *
 * @var $raq_data array
 */

$show_price        = true;
$show_total_column = ! ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'yes' );
$total             = 0;
$total_tax         = 0;
$colspan           = 1;
$quote_number      = apply_filters( 'ywraq_quote_number', $raq_data['order_id'] );
$tax_display_list  = apply_filters( 'ywraq_tax_display_list', get_option( 'woocommerce_tax_display_cart' ) );
$text_align        = is_rtl() ? 'right' : 'left';


if ( get_option( 'ywraq_enable_order_creation', 'yes' ) === 'yes' ) :
	?>
	<h2><?php printf( '%s #%s', esc_html( __( 'Request a Quote', 'yith-woocommerce-request-a-quote' ) ), esc_html( $quote_number ) ); ?></h2>
<?php else : ?>
	<h2><?php esc_html_e( 'Request a Quote', 'yith-woocommerce-request-a-quote' ); ?></h2>
<?php endif ?>

<?php do_action( 'yith_ywraq_email_before_raq_table', $raq_data ); ?>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;border-collapse: collapse;">
	<thead>
	<tr>
		<?php
		if ( get_option( 'ywraq_show_preview' ) === 'yes' ) :
			$colspan = 2;
			?>
			<th scope="col"
				style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Preview', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php endif ?>
		<th scope="col"
			style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
		<th scope="col"
			style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php if ( $show_total_column ) : ?>
			<th scope="col"
				style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-request-a-quote' ); ?></th>
		<?php endif ?>
	</tr>
	</thead>
	<tbody>
	<?php
	if ( ! empty( $raq_data['raq_content'] ) ) :
		foreach ( $raq_data['raq_content'] as $key => $item ) :

			if ( isset( $item['variation_id'] ) && $item['variation_id'] ) {
				$_product = wc_get_product( $item['variation_id'] );
			} else {
				$_product = wc_get_product( $item['product_id'] );
			}

			if ( ! $_product ) {
				continue;
			}

			$title = $_product->get_title();

			if ( $_product->get_sku() !== '' && get_option( 'ywraq_show_sku' ) === 'yes' ) {
				$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
				$title .= ' ' . apply_filters( 'ywraq_sku_label_html', $sku, $_product );
			}

			do_action( 'ywraq_before_request_quote_view_item', $raq_data, $key );


			?>

			<tr>
				<?php if ( get_option( 'ywraq_show_preview' ) === 'yes' ) : ?>
					<td scope="col" class="td" style="text-align:center;border: 1px solid #eee;">
						<?php

						$dimensions = wc_get_image_size( 'shop_thumbnail' );
						$height     = esc_attr( $dimensions['height'] );
						$width      = esc_attr( $dimensions['width'] );
						$src        = ( $_product->get_image_id() ) ? current( wp_get_attachment_image_src( $_product->get_image_id(), 'shop_thumbnail' ) ) : wc_placeholder_img_src();

						?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><img src="<?php echo esc_url( $src ); ?>" height="<?php echo esc_attr( $height ); ?>" width="<?php echo esc_attr( $width ); ?>"/></a>
					</td>
				<?php endif ?>
				<td scope="col" class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;">
					<?php if ( apply_filters( 'ywraq_list_show_product_permalinks', true, 'email_request_quote_table' ) ) : ?>
						<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo wp_kses_post( $title ); ?></a>
					<?php else : ?>
						<?php echo wp_kses_post( $title ); ?>
					<?php endif ?>

					<?php do_action( 'ywraq_request_quote_email_view_item_after_title', $item, $raq_data, $key ); ?>
					<?php if ( isset( $item['variations'] ) || isset( $item['addons'] ) || isset( $item['yith_wapo_options'] ) ) : ?>
						<small><?php echo yith_ywraq_get_product_meta( $item, true, $show_price ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></small>
					<?php endif ?>
				</td>
				<td scope="col"
					style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;"><?php echo esc_html( $item['quantity'] ); ?></td>
				<?php if ( $show_total_column ) : ?>
					<td scope="col" class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;">
						<?php
						if ( $show_price ) {
							do_action( 'ywraq_quote_adjust_price', $item, $_product );

							if ( $item instanceof WC_Order_Item_Product ) {
								$price      = wc_price( $item->get_total() );
								$total     += floatval( $item->get_total() );
								$total_tax += floatval( $item->get_total_tax() );
							} else {
								$price = ( 'incl' === $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => $item['quantity'] ) );

								if ( $price ) {
									$price_with_tax    = wc_get_price_including_tax( $_product, array( 'qty' => $item['quantity'] ) );
									$price_without_tax = wc_get_price_excluding_tax( $_product, array( 'qty' => $item['quantity'] ) );
									$total            += floatval( $price );
									$total_tax        += floatval( $price_with_tax - $price_without_tax );
									$price             = apply_filters( 'yith_ywraq_product_price_html', WC()->cart->get_product_subtotal( $_product, $item['quantity'] ), $_product, $item );
								} else {
									$price = wc_price( 0 );
								}
							}

							echo wp_kses_post( apply_filters( 'yith_ywraq_hide_price_template', $price, $_product->get_id(), $item ) );
						}
						?>
						</td>
				<?php endif ?>
			</tr>
			<?php
			do_action( 'ywraq_after_request_quote_view_item_on_email', $raq_data['raq_content'], $key );
		endforeach;
		?>

		<?php if ( $show_total_column ) : ?>
			<?php
			if ( $total_tax > 0 && 'incl' !== $tax_display_list && apply_filters( 'ywraq_show_taxes_quote_list', false ) ) :
				$total += $total_tax;
				?>
			<tr class="taxt-total">
				<td colspan="<?php echo esc_attr( $colspan ); ?>" style="text-align:right; border: 1px solid #eee;">
				</td>
				<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
				<td class="raq-totals" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;">
					<?php
					echo wp_kses_post( wc_price( $total_tax ) );
					?>
				</td>
			</tr>
			<?php endif; ?>
		<tr>
			<td colspan="<?php echo esc_attr( $colspan ); ?>" style="text-align:right; border: 1px solid #eee;"></td>
			<th style="text-align:<?php echo esc_attr( $text_align ); ?>; border: 1px solid #eee;">
				<?php esc_html_e( 'Total:', 'yith-woocommerce-request-a-quote' ); ?>
			</th>
			<td class="raq-totals" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;border: 1px solid #eee;">
				<?php
				echo wp_kses_post( wc_price( $total ) );
				if ( $total_tax > 0 && 'incl' === $tax_display_list && apply_filters( 'ywraq_show_taxes_quote_list', false ) ) {
					echo '<br><small class="includes_tax">' . sprintf( '(%s %s %s)', esc_html( __( 'includes', 'yith-woocommerce-request-a-quote' ) ), wp_kses_post( wc_price( $total_tax ) ), wp_kses_post( WC()->countries->tax_or_vat() ) ) . '</small>';
				}
				?>

			</td>
		</tr>
	<?php endif; ?>

	<?php endif; ?>
	</tbody>
</table>
<?php do_action( 'yith_ywraq_email_after_raq_table', $raq_data ); ?>
