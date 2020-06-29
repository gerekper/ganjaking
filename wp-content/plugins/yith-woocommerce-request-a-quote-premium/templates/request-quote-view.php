<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * @package YITH Woocommerce Request A Quote
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Table view to Request A Quote
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 *
 * @var  array $raq_content
 */

$colspan                = 'yes' === get_option( 'ywraq_hide_total_column', 'yes' ) || 'yes' === get_option( 'ywraq_hide_price' ) ? '2' : '3';
$colspan                = apply_filters( 'ywraq_item_thumbnail', true ) ? $colspan : $colspan - 1;
$tax_display_list       = apply_filters( 'ywraq_tax_display_list', get_option( 'woocommerce_tax_display_cart' ) );
$total_tax              = 0;
$product_column_colspan = apply_filters( 'ywraq_item_thumbnail', ! wp_is_mobile() ) ? 2 : 1;
$quote_id               = isset( $_GET['quote_id'] ) ? wp_unslash( $_GET['quote_id'] ) : false;
$notices                = isset( $notices ) ? $notices : 0;

if ( $quote_id && wc_get_order( $quote_id ) ) :
	$shortcode = '[yith_ywraq_single_view_quote order_id="' . $quote_id . '"]';
	echo wp_kses_post( is_callable( 'apply_shortcodes' ) ? apply_shortcodes( $shortcode ) : do_shortcode( $shortcode ) );
elseif ( count( $raq_content ) === 0 ) :
	if ( 0 === $notices ) {
		echo wp_kses_post( ywraq_get_list_empty_message() );
	} else {
		$shop_url             = apply_filters( 'yith_ywraq_return_to_shop_url', get_option( 'ywraq_return_to_shop_url' ) );
		$label_return_to_shop = apply_filters( 'yith_ywraq_return_to_shop_label', get_option( 'ywraq_return_to_shop_label' ) );
		?>
		<a class="button wc-backward"
			href="<?php echo esc_url( apply_filters( 'yith_ywraq_return_to_shop_url', $shop_url ) ); ?>"><?php echo esc_html( $label_return_to_shop ); ?></a>
		<?php
	}
else :
	?>
	<form id="yith-ywraq-form" name="yith-ywraq-form"
		action="<?php echo esc_url( YITH_Request_Quote()->get_raq_page_url() ); ?>" method="post">

		<?php do_action( 'ywraq_before_list_table' ); ?>

		<table class="shop_table cart  shop_table_responsive" id="yith-ywrq-table-list">
			<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-name"
					colspan="<?php echo esc_attr( $product_column_colspan ); ?>"><?php esc_html_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?></th>
				<?php if ( 'no' === get_option( 'ywraq_hide_total_column', 'yes' ) && 'yes' !== get_option( 'ywraq_hide_price' ) ) : ?>
					<th class="product-subtotal"><?php esc_html_e( 'Total', 'yith-woocommerce-request-a-quote' ); ?></th>
				<?php endif ?>
			</tr>
			</thead>
			<tbody>
			<?php

			$total     = 0;
			$total_exc = 0;
			$total_inc = 0;
			foreach ( $raq_content as $key => $raq ) :
				$product_id = ( ! empty( $raq['variation_id'] ) && $raq['variation_id'] > 0 ) ? $raq['variation_id'] : $raq['product_id'];
				$_product   = wc_get_product( $product_id );

				if ( ! $_product ) {
					continue;
				}

				$show_price = true;

				do_action( 'ywraq_before_request_quote_view_item', $raq_content, $key );

				if ( ! empty( $raq['yith_wapo_individual_item'] ) && 1 === $raq['yith_wapo_individual_item'] && ! empty( $raq['yith_wapo_parent_key'] ) ) :
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'yith_ywraq_item_class', 'cart_item', $raq_content, $key ) ); ?>"
						data-wapo_parent_key="<?php echo esc_attr( $raq['yith_wapo_parent_key'] ); ?>" <?php echo esc_attr( apply_filters( 'yith_ywraq_item_attributes', '', $raq_content, $key ) ); ?>>

						<td class="product-remove"></td>
						<?php if ( apply_filters( 'ywraq_item_thumbnail', true ) ) : ?>
							<td class="product-thumbnail"></td>
						<?php endif; ?>

						<td class="product-name"
							data-title="<?php esc_attr_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?>">
							<?php
							// Meta data.
							$item_data = array();

							foreach ( $raq['yith_wapo_options'] as $individual_item ) {
								$individual_wapo_item_price = '';
								if ( $show_price && $individual_item['price'] > 0 && 'yes' === get_option( 'ywraq_hide_price' ) ) {
									$individual_wapo_item_price = ' ( +' . strip_tags( wc_price( $individual_item['price'] ) ) . ' ) ';
								}

								if ( class_exists( 'YITH_WAPO_WPML' ) ) {
									$key = YITH_WAPO_WPML::string_translate( $individual_item['name'] );
									if ( strpos( $individual_item['value'], 'Attached file' ) ) {
										$array = new SimpleXMLElement( $individual_item['value'] );
										$link  = $array['href'];
										$value = '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Attached file', 'yith-woocommerce-product-add-ons' ) . '</a>';
									} else {
										$value = YITH_WAPO_WPML::string_translate( $individual_item['value'] );
									}
								} else {
									$key   = $individual_item['name'];
									$value = $individual_item['value'];
								}

								$item_data[] = array(
									'key'   => $key . $individual_wapo_item_price,
									'value' => urldecode( $value ),
								);
							}

							// Output flat or in list format.
							if ( count( $item_data ) > 0 ) {
								echo '<ul style="margin-left: 10px;">';
								foreach ( $item_data as $data ) {
									echo '<li><strong>' . esc_html( $data['key'] ) . '</strong>: ' . wp_kses_post( $data['value'] ) . '</li><br>';
								}
								echo '</ul>';
							}
							?>
						</td>

						<td class="product-quantity"
							data-title="<?php esc_attr_e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?>">
							<?php echo esc_html( $raq['quantity'] ); ?>
						</td>

						<?php if ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'no' && get_option( 'ywraq_hide_price' ) !== 'yes' ) : ?>
							<td class="product-subtotal"
								data-title="<?php esc_attr_e( 'Price', 'yith-woocommerce-request-a-quote' ); ?>">
								<?php
								do_action( 'ywraq_quote_adjust_price', $raq, $_product );
								$price = ( 'incl' === $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => 1 ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => 1 ) );

								if ( $price ) {
									$price_with_tax    = wc_get_price_including_tax( $_product, array( 'qty' => 1 ) );
									$price_without_tax = wc_get_price_excluding_tax( $_product, array( 'qty' => 1 ) );
									$total            += floatval( $price );
									$total_tax        += floatval( $price_with_tax - $price_without_tax );
									$price             = apply_filters( 'yith_ywraq_product_price_html', WC()->cart->get_product_subtotal( $_product, 1 ), $_product, $raq );
								} else {
									$price = wc_price( 0 );
								}

								echo wp_kses_post( apply_filters( 'yith_ywraq_hide_price_template', $price, $product_id, $raq ) );
								?>
							</td>
						<?php endif; ?>
					</tr>
				<?php else : ?>
					<tr class="<?php echo esc_attr( apply_filters( 'yith_ywraq_item_class', 'cart_item', $raq_content, $key ) ); ?>" <?php echo esc_attr( apply_filters( 'yith_ywraq_item_attributes', '', $raq_content, $key ) ); ?>>

						<td class="product-remove">
							<?php echo apply_filters( 'yith_ywraq_item_remove_link', sprintf( '<a href="#"  data-remove-item="%s" data-wp_nonce="%s"  data-product_id="%d" class="yith-ywraq-item-remove remove" title="%s">&times;</a>', esc_attr( $key ), esc_attr( wp_create_nonce( 'remove-request-quote-' . $product_id ) ), esc_attr( $product_id ), esc_attr__( 'Remove this item', 'yith-woocommerce-request-a-quote' ) ), $key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</td>
						<?php if ( apply_filters( 'ywraq_item_thumbnail', true ) ) : ?>
							<td class="product-thumbnail">
								<?php
								$thumbnail = $_product->get_image();

								if ( ! $_product->is_visible() || ! apply_filters( 'ywraq_list_show_product_permalinks', true, 'quote-view' ) ) {
									echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink() ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</td>
						<?php endif; ?>

						<td class="product-name"
							data-title="<?php esc_attr_e( 'Product', 'yith-woocommerce-request-a-quote' ); ?>">
							<?php
							$title = $_product->get_title();

							if ( '' !== $_product->get_sku() && 'yes' === get_option( 'ywraq_show_sku' ) ) {
								$sku    = apply_filters( 'ywraq_sku_label', __( ' SKU:', 'yith-woocommerce-request-a-quote' ) ) . $_product->get_sku();
								$title .= ' ' . apply_filters( 'ywraq_sku_label_html', $sku, $_product );
							}

							if ( ! $_product->is_visible() || ! apply_filters( 'ywraq_list_show_product_permalinks', true, 'quote-view' ) ) :
								?>
								<?php echo wp_kses_post( apply_filters( 'ywraq_quote_item_name', $title, $raq, $key ) ); ?>
							<?php else : ?>
								<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo wp_kses_post( apply_filters( 'ywraq_quote_item_name', $title, $raq, $key ) ); ?></a>
							<?php endif ?>

							<?php
							// Meta data.
							$item_data = array();

							// Variation data.
							if ( ! empty( $raq['variation_id'] ) && is_array( $raq['variations'] ) ) {

								foreach ( $raq['variations'] as $name => $value ) {
									$label = '';

									if ( '' === $value ) {
										continue;
									}

									$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

									// If this is a term slug, get the term's nice name.
									if ( taxonomy_exists( $taxonomy ) ) {
										$term = get_term_by( 'slug', $value, $taxonomy );
										if ( ! is_wp_error( $term ) && $term && $term->name ) {
											$value = $term->name;
										}
										$label = wc_attribute_label( $taxonomy );

									} else {

										if ( strpos( $name, 'attribute_' ) !== false ) {
											$custom_att = str_replace( 'attribute_', '', $name );

											if ( '' !== $custom_att ) {
												$label = wc_attribute_label( $custom_att, $_product );
											} else {
												$label = $name;
											}
										}
									}

									$item_data[] = array(
										'key'   => $label,
										'value' => $value,
									);
								}
							}

							$item_data = apply_filters( 'ywraq_request_quote_view_item_data', $item_data, $raq, $_product, $show_price );


							// Output flat or in list format.
							if ( count( $item_data ) > 0 ) {
								echo '<br><ul style="margin-left: 10px;">';
								foreach ( $item_data as $data ) {
									echo '<li><strong>' . esc_html( $data['key'] ) . '</strong>: ' . wp_kses_post( $data['value'] ) . '</li><br>';
								}
								echo '</ul>';
							}

							?>
						</td>


						<td class="product-quantity"
							data-title="<?php esc_attr_e( 'Quantity', 'yith-woocommerce-request-a-quote' ); ?>">
							<?php

							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="raq[%s][qty]" value="1" />', $key );
							} else {

								$product_quantity = woocommerce_quantity_input(
									array(
										'input_name'  => "raq[{$key}][qty]",
										'input_value' => apply_filters( 'ywraq_quantity_input_value', $raq['quantity'] ),
										'max_value'   => apply_filters( 'ywraq_quantity_max_value', $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(), $_product ),
										'min_value'   => apply_filters( 'ywraq_quantity_min_value', 0, $_product ),
										'step'        => apply_filters( 'ywraq_quantity_step_value', 1, $_product ),
									),
									$_product,
									false
								);

							}

							echo $product_quantity; //@phpcs:ignore
							?>
						</td>

						<?php if ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'no' && get_option( 'ywraq_hide_price' ) !== 'yes' ) : ?>
							<td class="product-subtotal"
								data-title="<?php esc_attr_e( 'Price', 'yith-woocommerce-request-a-quote' ); ?>">
								<?php
								do_action( 'ywraq_quote_adjust_price', $raq, $_product );

								$price = ( 'incl' === $tax_display_list ) ? wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) ) : wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );

								if ( $price ) {
									$price_with_tax    = wc_get_price_including_tax( $_product, array( 'qty' => $raq['quantity'] ) );
									$price_without_tax = wc_get_price_excluding_tax( $_product, array( 'qty' => $raq['quantity'] ) );
									$total            += floatval( $price );
									$total_tax        += floatval( $price_with_tax - $price_without_tax );
									$price             = apply_filters( 'yith_ywraq_product_price_html', WC()->cart->get_product_subtotal( $_product, $raq['quantity'] ), $_product, $raq );
								} else {
									$price = wc_price( 0 );
								}

								echo wp_kses_post( apply_filters( 'yith_ywraq_hide_price_template', $price, $product_id, $raq ) );
								?>
							</td>
						<?php endif ?>
					</tr>
				<?php endif; ?>

				<?php do_action( 'ywraq_after_request_quote_view_item', $raq_content, $key ); ?>

			<?php endforeach ?>
			<?php
			if ( get_option( 'ywraq_hide_total_column', 'yes' ) === 'no' && get_option( 'ywraq_show_total_in_list', 'no' ) === 'yes' && get_option( 'ywraq_hide_price' ) !== 'yes' ) :
				?>
				<?php
				if ( $total_tax > 0 && 'incl' !== $tax_display_list && apply_filters( 'ywraq_show_taxes_quote_list', false ) ) :
					$total += $total_tax;
					?>
					<tr>
						<td colspan="<?php echo esc_attr( $colspan ); ?>"></td>
						<th>
							<?php echo esc_html( WC()->countries->tax_or_vat() ); ?>
						</th>
						<td class="raq-totals">
							<?php echo wp_kses_post( wc_price( $total_tax ) ); ?>
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<th colspan="<?php echo esc_attr( $colspan ); ?>"></th>
					<th>
						<?php esc_html_e( 'Total:', 'yith-woocommerce-request-a-quote' ); ?>
					</th>
					<td class="raq-totals"
						data-title="<?php esc_attr_e( 'Total', 'yith-woocommerce-request-a-quote' ); ?>">
						<?php
						echo wp_kses_post( wc_price( $total ) );
						if ( $total_tax > 0 && 'incl' === $tax_display_list && apply_filters( 'ywraq_show_taxes_quote_list', false ) ) {
							echo wp_kses_post( '<br><small class="includes_tax">' . sprintf( '%1$s %2$s %3$s', __( 'includes', 'yith-woocommerce-request-a-quote' ), wp_kses_post( wc_price( $total_tax ) ), wp_kses_post( WC()->countries->tax_or_vat() ) ) . '</small>' );
						}
						?>
					</td>
				</tr>
				<?php

			endif
			?>

			<tr>
				<td colspan="<?php echo esc_attr( $colspan + 2 ); ?>" class="actions">
					<?php if ( get_option( 'ywraq_show_update_list' ) === 'yes' ) : ?>
						<input type="submit" class="button" name="update_raq"
							value="<?php echo esc_attr( get_option( 'ywraq_update_list_label' ) ); ?>">
					<?php endif ?>
					<input type="hidden" id="update_raq_wpnonce" name="update_raq_wpnonce"
						value="<?php echo esc_attr( wp_create_nonce( 'update-request-quote-quantity' ) ); ?>">
				</td>
			</tr>

			</tbody>
		</table>
		<?php do_action( 'ywraq_after_list_table' ); ?>
	</form>
<?php endif ?>
