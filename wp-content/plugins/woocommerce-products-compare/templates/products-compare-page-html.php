<?php
/**
 * The compare page template file
 *
 * @version 1.0.8
 * @since 1.0.0
 * @package WC_Products_Compare
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'shop' ); ?>

<noscript><?php esc_html_e( 'Sorry, you must have Javascript enabled in your browser to use compare products', 'woocommerce-products-compare' ); ?></noscript>

<div class="woocommerce-products-compare-content woocommerce">
	<?php

	$products = WC_Products_Compare_Frontend::get_compared_products();

	if ( $products ) {
		global $product;

		$columns = count( $products );

		// Calculate each columns width in percentage.
		$column_width = floor( 100 / ( $columns + 1 ) ); // +1 to account for first header column

		// Get all row headers.
		$headers = WC_Products_Compare_Frontend::get_product_meta_headers( $products );
		?>

		<table>
			<!--thead-->
			<thead>
				<tr class="products">
					<?php do_action( 'woocommerce_before_shop_loop' ); ?>

					<th class="header-title" style="width:<?php echo esc_attr( $column_width ); ?>%">
						<h3><?php esc_html_e( 'Products', 'woocommerce-products-compare' ); ?></h3>
					</th>

					<?php
					foreach ( $products as $product ) {
						$product = wc_get_product( $product );

						if ( ! WC_Products_Compare::is_product( $product ) ) {
							continue;
						}
						?>

						<td class="product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>" style="width:<?php echo esc_attr( $column_width ); ?>%">
							<a href="#" title="<?php esc_attr_e( 'Remove Product', 'woocommerce-products-compare' ); ?>" class="remove-compare-product" data-remove-id="<?php echo esc_attr( $product->get_id() ); ?>"><?php esc_html_e( 'Remove Product', 'woocommerce-products-compare' ); ?></a>
							<a href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>" class="product-link">

								<?php woocommerce_show_product_loop_sale_flash(); ?>

								<?php echo $product->get_image( 'shop_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

								<h3><?php echo $product->get_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>

							</a>

							<?php woocommerce_template_loop_add_to_cart(); ?>
						</td>
					<?php } ?>
				</tr>

				<?php
				// Don't show rating row if all products don't have ratings.
				$show_rating = false;

				foreach ( $products as $product ) {
					$product = wc_get_product( $product );

					if ( ! WC_Products_Compare::is_product( $product ) ) {
						continue;
					}

					if ( $product->get_average_rating() > 0 ) {
						$show_rating = true;
					}
				}

				if ( $show_rating ) {
					?>
					<tr class="products ratings-row">
						<th class="header-title">
							<h3><?php esc_html_e( 'Ratings', 'woocommerce-products-compare' ); ?></h3>
						</th>

						<?php
						foreach ( $products as $product ) {
							$product = wc_get_product( $product );

							if ( ! WC_Products_Compare::is_product( $product ) ) {
								continue;
							}
							?>
							<td class="product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php
								if ( $product->get_average_rating() > 0 ) {
									woocommerce_template_loop_rating(); }
								?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>

				<tr class="products price-row">
					<th class="header-title">
						<h3><?php esc_html_e( 'Price', 'woocommerce-products-compare' ); ?></h3>
					</th>

					<?php
					foreach ( $products as $product ) {
						$product = wc_get_product( $product );

						if ( ! WC_Products_Compare::is_product( $product ) ) {
							continue;
						}
						?>
						<td class="product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
							<?php woocommerce_template_loop_price(); ?>
						</td>
					<?php } ?>
				</tr>
			</thead>
			<!--thead end-->

			<!--tfoot-->
			<tfoot>
				<tr class="products">

					<td>&nbsp;</td>

					<?php
					foreach ( $products as $product ) {
						$product = wc_get_product( $product );

						if ( ! WC_Products_Compare::is_product( $product ) ) {
							continue;
						}
						?>

						<td class="product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
							<a href="<?php echo esc_attr( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
								<h3><?php echo $product->get_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
							</a>

							<?php woocommerce_template_loop_price(); ?>

							<?php woocommerce_template_loop_add_to_cart(); ?>
						</td>
					<?php } ?>
				</tr>
			</tfoot>
			<!--tfoot end-->

			<!--tbody-->
			<tbody>

				<?php foreach ( $headers as $header ) { ?>
					<tr>
						<th>
							<?php
							if ( 'stock' === $header ) {
								esc_html_e( 'Stock', 'woocommerce-products-compare' );

							} elseif ( 'description' === $header ) {
								esc_html_e( 'Description', 'woocommerce-products-compare' );

							} elseif ( 'sku' === $header ) {
								esc_html_e( 'SKU', 'woocommerce-products-compare' );

							} else {
								echo esc_html( wc_attribute_label( $header ) );
							}
							?>
						</th>

						<?php
						foreach ( $products as $product ) {
							$product = wc_get_product( $product );

							if ( ! WC_Products_Compare::is_product( $product ) ) {
								continue;
							}

							$product_post = get_post( $product->get_id() );
							$attributes   = $product->get_attributes();
							?>
							<td class="product" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
								<?php
								if ( 'stock' === $header && $product->managing_stock() ) {
									$class        = $product->get_availability()['class'];
									$availability = $product->get_availability()['availability'];

									echo '<span class="stock-status ' . esc_attr( $class ) . '">' . $availability . '</span>' . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

								} elseif ( 'description' === $header ) {
									echo wp_kses_post( wp_strip_all_tags( $product_post->post_excerpt ) );

								} elseif ( 'sku' === $header ) {
									echo esc_html( $product->get_sku() );

								} elseif ( array_key_exists( sanitize_title( $header ), $attributes ) ) {

									if ( $attributes[ sanitize_title( $header ) ]['is_taxonomy'] ) {
										$values = wc_get_product_terms( $product->get_id(), $attributes[ sanitize_title( $header ) ]['name'], array( 'fields' => 'names' ) );
									} else {
										// Convert pipes to commas and display values.
										$values = array_map( 'trim', explode( WC_DELIMITER, $attributes[ sanitize_title( $header ) ]['value'] ) );
									}

									echo wp_kses_post( apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attributes[ sanitize_title( $header ) ], $values ) );
								}
								?>
							</td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
			<!--tbody end-->

		</table>

		<?php
	} else {

		echo WC_Products_Compare_Frontend::empty_message(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>

</div><!--.woocommerce-products-compare-content-->

<?php get_footer( 'shop' ); ?>
