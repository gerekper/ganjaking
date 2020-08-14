<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Pending Order Content
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.1.0
 * @author YITH
 *
 * @var bool $has_translation
 * @var string $lang
 */

$order_items            = $order->get_items();
$icl_t                  = function_exists( 'icl_t' );
$thumbnail_label        = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_thumbnail', 'Thumbnail', $has_translation, false, $lang ) : __( 'Thumbnail', 'yith-woocommerce-recover-abandoned-cart' );
$product_label          = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_product', 'Product', $has_translation, false, $lang ) : __( 'Product', 'yith-woocommerce-recover-abandoned-cart' );
$product_price_label    = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_product_price', 'Product Price', $has_translation, false, $lang ) : __( 'Product Price', 'yith-woocommerce-recover-abandoned-cart' );
$product_quantity_label = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_quantity', 'Quantity', $has_translation, false, $lang ) : __( 'Quantity', 'yith-woocommerce-recover-abandoned-cart' );
$product_subtotal_label = ( $icl_t ) ? icl_t( 'yith-woocommerce-recover-abandoned-cart', 'ywrac_cart_template_cart_subtotal', 'Total', $has_translation, false, $lang ) : __( 'Total', 'yith-woocommerce-recover-abandoned-cart' );
?>

<table class="shop_table cart" id="yith-ywrac-table-list" cellspacing="0" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
	<tr>
		<th class="product-thumbnail"><?php echo esc_html( $thumbnail_label ); ?></th>
		<th class="product-name"><?php echo esc_html( $product_label ); ?></th>
		<th class="product-single"><?php echo esc_html( $product_price_label ); ?></th>
		<th class="product-quantity"><?php echo esc_html( $product_quantity_label ); ?></th>
		<th class="product-subtotal"><?php echo esc_html( $product_subtotal_label ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $order_items as $order_item ) :
		$product_id = ( isset( $order_item['variation_id'] ) && $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];
		$_product   = wc_get_product( $product_id );
		if ( $_product ) :
			?>
			<tr class="cart_item">
				<td class="product-thumbnail">
					<?php
					$image = '';

					if ( has_post_thumbnail( $product_id ) ) {
						$product_image                = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'shop_thumbnail' );
						list( $src, $width, $height ) = $product_image;
						$image                        = $src;
					} elseif ( wc_placeholder_img_src() ) {
						$image = wc_placeholder_img_src();
					}
					?>

					<a style="width:50px;height:auto; display: inline-block;" class="product-image"
						href="<?php echo esc_url( $_product->get_permalink() ); ?>"><img style=" width: 100%; height: auto;"
							src="<?php echo esc_url( $image ); ?>"/></a>
				</td>

				<td class="product-name">
					<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo wp_kses_post( $order_item['name'] ); ?></a>
					<?php
					// Meta data
					$item_data = array();

					// Variation data
					if ( $order_item['variation_id'] && isset( $order_item['variation'] ) && is_array( $order_item['variation'] ) ) {
						foreach ( $order_item['variation'] as $name => $value ) {
							$label = '';
							if ( '' === $value ) {
								continue;
							}
							$taxonomy = wc_attribute_taxonomy_name( str_replace( 'pa_', '', urldecode( $name ) ) );

							// If this is a term slug, get the term's nice name
							if ( taxonomy_exists( $taxonomy ) ) {
								$term = get_term_by( 'slug', $value, $taxonomy );
								if ( ! is_wp_error( $term ) && $term && $term->name ) {
									$value = $term->name;
								}
								$label = wc_attribute_label( $taxonomy );

							} else {

								if ( strpos( $name, 'attribute_' ) !== false ) {
									$custom_att = str_replace( 'attribute_', '', $name );

									if ( $custom_att != '' ) {
										$label = wc_attribute_label( $custom_att );
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

					// Output flat or in list format
					if ( sizeof( $item_data ) > 0 ) {
						foreach ( $item_data as $data ) {
							echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
						}
					}
					?>
				</td>
				<td class="product-price">
					<?php
					echo wp_kses_post( wc_price( $order_item['line_subtotal'] / $order_item['qty'], array( 'currency' => $currency ) ) );
					?>
				</td>

				<td class="product-quantity">
					<?php echo esc_html( $order_item['qty'] ); ?>
				</td>

				<td class="product-subtotal">
					<?php
					echo wp_kses_post( wc_price( $order_item['line_subtotal'], array( 'currency' => $currency ) ) );
					?>
				</td>
			</tr>

			<?php
		endif;
	endforeach
	?>

	<?php


	foreach ( $order->get_order_item_totals() as $key => $total ) {
		?>
		<tr>
			<th scope="col" colspan="4"
				style="text-align:right;border: 1px solid #eee;"><?php echo wp_kses_post( $total['label'] ); ?></th>
			<td scope="col" style="text-align:right;border: 1px solid #eee;"><?php echo wp_kses_post( $total['value'] ); ?></td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>
