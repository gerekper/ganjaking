<?php
/**
 * Table pricing template.
 *
 * @package YITH WooCommerce Dynamic Pricing and Discounts Premium
 * @since   1.0.0
 * @version 1.6.0
 * @author  YITH
 *
 * @var string $label_table
 * @var string $until
 * @var string $label_quantity
 * @var string $label_price
 * @var array  $main_rule
 * @var string  $note
 */

if ( ! defined( 'ABSPATH' ) && ! isset( $product_id ) ) {
	exit;
}

remove_filter(
	'woocommerce_' . YITH_WC_Dynamic_Pricing_Frontend()->get_product_filter . 'get_price',
	array(
		YITH_WC_Dynamic_Pricing_Frontend(),
		'get_price',
	)
);
remove_filter(
	'woocommerce_' . YITH_WC_Dynamic_Pricing_Frontend()->get_product_filter . 'variation_get_price',
	array(
		YITH_WC_Dynamic_Pricing_Frontend(),
		'get_price',
	)
);
$template = YITH_WC_Dynamic_Pricing()->get_option( 'quantity_table_orientation' );

$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
if ( '' !== $label_table ) :
	?>

	<p class="ywdpd-table-discounts-label"><strong><?php echo wp_kses_post( $label_table ); ?></strong>
		<?php
		if ( '' !== $until ) {
			echo '<span>' . esc_html( $until ) . '</span>';
		}
		?>
	</p>
<?php endif; ?>
<table id="ywdpd-table-discounts" class="<?php echo $template;?>">
	<?php if ( 'horizontal' === $template ) : ?>
		<tr class="quantity_row">
			<th><?php echo wp_kses_post( $label_quantity ); ?></th>
			<?php foreach ( $rules as $rule ) : ?>
				<td class="qty-info" data-qtymin="<?php echo esc_attr( $rule['min_quantity'] ); ?>"
					data-qtymax="<?php echo esc_attr( $rule['max_quantity'] ); ?>">
					<?php echo esc_html( $rule['min_quantity'] ); ?>
					<?php
					if ( $rule['max_quantity'] !== $rule['min_quantity'] ) {
						echo esc_html( ( '*' !== $rule['max_quantity'] ) ? '-' . $rule['max_quantity'] : '+' );
					}
					?>
				</td>
			<?php endforeach ?>
		</tr>
		<tr class="price_row">
			<th><?php echo wp_kses_post( $label_price ); ?></th>
			<?php
			foreach ( $rules as $rule ) :

				if ( $product->is_type( 'variable' ) ) {

					$prices = apply_filters( 'ywdpd_get_variable_prices', $product->get_variation_prices(), $product );
					$prices = isset( $prices['price'] ) ? $prices['price'] : array();

					if ( $prices ) {

						$min_price = current( $prices );
						$min_key   = array_search( $min_price, $prices );

						if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $min_key ) ) && ! empty( $min_price ) ) {
							$discount_min_price = ywdpd_get_discounted_price_table( $min_price, $rule );
						} else {
							$discount_min_price = $min_price;
						}

						$discount_min_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_min_price, $min_key, $min_price, $rule );

						$max_price = end( $prices );
						$max_key   = array_search( $max_price, $prices );
						if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $max_key ) ) && ! empty( $max_price ) ) {
							$discount_max_price = ywdpd_get_discounted_price_table( $max_price, $rule );
						} else {
							$discount_max_price = $max_price;
						}

						$discount_max_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_max_price, $max_key, $max_price, $rule );

						$price_1 = wc_price( wc_get_price_to_display( $product, array( 'price' => $discount_min_price ) ) );

						if ( $discount_min_price !== $discount_max_price ) {
							$price_2 = wc_price( wc_get_price_to_display( $product, array( 'price' => $discount_max_price ) ) );
							$html    = $discount_min_price < $discount_max_price ?
								/* translators: 1: price from 2: price to */
								sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $price_1, $price_2 ) : // WPCS: XSS ok.
								/* translators: 1: price from 2: price to */
								sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $price_2, $price_1 ); // WPCS: XSS ok.
						} else {
							$html = $price_1;
						}
					}
				} else {

					$price = $product->get_price();


					// check if the product or the variation has discount.
					if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, $product, false ) && ! empty( $price ) ) {
						$discount_price = ywdpd_get_discounted_price_table( $price, $rule );
					} else {
						$discount_price = $price;
					}

					$discount_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_price, $product->get_id(), $price, $rule );


					$product_price_exc_tax = wc_get_price_excluding_tax(
						$product,
						array(
							'qty'   => 1,
							'price' => $discount_price,
						)
					);
					$product_price_inc_tax = wc_get_price_including_tax(
						$product,
						array(
							'qty'   => 1,
							'price' => $discount_price,
						)
					);

					$discount_price = ( 'excl' === $tax_display_mode ) ? $product_price_exc_tax : $product_price_inc_tax;

					$html = wc_price( $discount_price );
				}
				?>
				<td data-qtymin="<?php echo esc_attr( $rule['min_quantity'] ); ?>"
					data-qtymax="<?php echo esc_attr( $rule['max_quantity'] ); ?>"
					class="qty-price-info"><?php echo apply_filters( 'ywdpd_show_price_on_table_pricing', $html, $rule, $product ); //phpcs:ignore
					?>
				</td>
			<?php endforeach ?>
		</tr>
	<?php else : ?>
		<tr>
			<th><?php echo wp_kses_post( $label_quantity ); ?></th>
			<th><?php echo wp_kses_post( $label_price ); ?></th>
		</tr>
		<?php foreach ( $rules as $rule ) : ?>
			<tr>
				<td class="qty-info"><?php echo esc_html( $rule['min_quantity'] ); ?>
					<?php
					if ( $rule['max_quantity'] !== $rule['min_quantity'] ) {
						echo esc_html( ( '*' !== $rule['max_quantity'] ) ? '-' . $rule['max_quantity'] : '+' );
					}
					?>
				</td>
				<td class="qty-price-info" data-qtymin="<?php echo esc_attr( $rule['min_quantity'] ); ?>"
					data-qtymax="<?php echo esc_attr( $rule['max_quantity'] ); ?>">
					<?php
					if ( $product->is_type( 'variable' ) ) {
						$prices = apply_filters( 'ywdpd_get_variable_prices', $product->get_variation_prices(), $product );

						$prices = isset( $prices['price'] ) ? $prices['price'] : array();

						if ( $prices ) {

							$min_price = current( $prices );
							$min_key   = array_search( $min_price, $prices );

							if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $min_key ) ) && ! empty( $min_price ) ) {
								$discount_min_price = ywdpd_get_discounted_price_table( $min_price, $rule );
							} else {
								$discount_min_price = $min_price;
							}

							$discount_min_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_min_price, $min_key, $min_price, $rule );

							$max_price = end( $prices );
							$max_key   = array_search( $max_price, $prices );
							if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, wc_get_product( $max_key ) ) && ! empty( $max_price ) ) {
								$discount_max_price = ywdpd_get_discounted_price_table( $max_price, $rule );
							} else {
								$discount_max_price = $max_price;
							}

							$discount_max_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_max_price, $max_key, $max_price, $rule );
							$price_1            = wc_price( wc_get_price_to_display( $product, array( 'price' => $discount_min_price ) ) );

							if ( $discount_min_price !== $discount_max_price ) {
								$price_2 = wc_price( wc_get_price_to_display( $product, array( 'price' => $discount_max_price ) ) );
								$html    = $discount_min_price < $discount_max_price ?
									sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $price_1, $price_2 ) : //phpcs:ignore
									sprintf( _x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), $price_2, $price_1 ); //phpcs:ignore
							} else {
								$html = $price_1;
							}
						}
					} else {

						$price = $product->get_price();

						// check if the product or the variation has discount.
						if ( YITH_WC_Dynamic_Pricing_Helper()->valid_product_to_apply_bulk( $main_rule, $product, false ) && ! empty( $price ) ) {
							$discount_price = ywdpd_get_discounted_price_table( $price, $rule );
						} else {
							$discount_price = $price;
						}
						$discount_price = apply_filters( 'yith_ywdpd_get_discount_price', $discount_price, $product->get_id(), $price, $rule );


						$product_price_exc_tax = wc_get_price_excluding_tax(
							$product,
							array(
								'qty'   => 1,
								'price' => $discount_price,
							)
						);
						$product_price_inc_tax = wc_get_price_including_tax(
							$product,
							array(
								'qty'   => 1,
								'price' => $discount_price,
							)
						);

						$discount_price = ( 'excl' === $tax_display_mode ) ? $product_price_exc_tax : $product_price_inc_tax;

						$html = wc_price( $discount_price );
					}
					echo apply_filters( 'ywdpd_show_price_on_table_pricing', $html, $rule, $product ); //phpcs:ignore
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
</table>

<?php
if ( '' !== $note ) :
	?>
	<p class="ywdpd-table-discounts-note"><?php echo wp_kses_post( $note ); ?></p>
	<?php
endif;
?>

<?php
add_filter(
	'woocommerce_' . YITH_WC_Dynamic_Pricing_Frontend()->get_product_filter . 'get_price',
	array(
		YITH_WC_Dynamic_Pricing_Frontend(),
		'get_price',
	),
	10,
	2
);
add_filter(
	'woocommerce_' . YITH_WC_Dynamic_Pricing_Frontend()->get_product_filter . 'variation_get_price',
	array(
		YITH_WC_Dynamic_Pricing_Frontend(),
		'get_price',
	),
	10,
	2
);
?>
