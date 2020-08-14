<?php
/**
 * Woocommerce Compare page
 *
 * @author  Your Inspiration Themes
 * @package YITH Woocommerce Compare
 * @version 1.1.4
 */

global $product, $yith_woocompare;

?>

<div id="yith-woocompare" class="woocommerce">

	<?php
	if ( empty( $products ) ):
		echo '<p>' . esc_html( apply_filters( 'yith_woocompare_empty_compare_message', __( 'No products added in the comparison table.', 'yith-woocommerce-compare' ) ) ) . '</p>';
	else:
		?>

		<?php do_action( 'yith_woocompare_before_main_table', $products, $fixed ); ?>

		<table id="yith-woocompare-table" class="compare-list <?php if ( empty( $products ) ) echo 'empty-list' ?>">
			<thead>
			<tr>
				<th>&nbsp;</th>
				<?php foreach ( $products as $product_id => $product ) : ?>
					<td></td>
				<?php endforeach; ?>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th>&nbsp;</th>
				<?php foreach ( $products as $product_id => $product ) : ?>
					<td></td>
				<?php endforeach; ?>
			</tr>
			</tfoot>

			<tbody>

			<?php if ( ! isset( $fields['product_info'] ) && ! $fixed ) : ?>
				<tr class="remove">
					<th>&nbsp;</th>
					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						$product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product_id
						?>
						<td class="<?php echo esc_attr( $product_class ); ?>">
							<a href="<?php echo esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ); ?>"
								data-iframe="<?php echo esc_attr( $iframe ); ?>"
								data-product_id="<?php echo esc_attr( $product_id ); ?>"><span
									class="remove">x</span><?php esc_html_e( 'Remove', 'yith-woocommerce-compare' ) ?>
							</a>
						</td>
						<?php
						++$index;
					endforeach;
					?>
				</tr>
			<?php endif; ?>

			<?php foreach ( $fields as $field => $name ) : ?>

				<tr class="<?php echo ! in_array( $field, $different ) ? esc_attr( $field ) : esc_attr( $field ) . ' different' ?>">

					<th>
						<?php echo esc_html( $name ); ?>
						<?php if ( $field == 'product_info' ) echo '<div class="fixed-th"></div>'; ?>
					</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						// set td class
						$product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product_id;
						if ( $field == 'stock' ) {
							$availability  = $product->get_availability();
							$product_class .= ' ' . ( empty( $availability['class'] ) ? 'in-stock' : $availability['class'] );
						}
						?>

						<td class="<?php echo esc_attr( $product_class ); ?>"><?php
							switch ( $field ) {

								case 'product_info':

									if ( ! $fixed )
										echo '<div class="remove"><a href="' . esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ) . '" data-iframe="' . esc_attr( $iframe ) . '" data-product_id="' . esc_attr( $product_id ) . '"><span class="remove">' . wp_kses_post( apply_filters( 'yith_woocompare_remove_icon', 'x' ) ) . '</span>' . wp_kses_post( apply_filters( 'yith_woocompare_remove_label', esc_html__( 'Remove', 'yith-woocommerce-compare' ) ) ) . '</a></div>';

									if ( $show_image || $show_title ) {
										echo '<a href="' . esc_attr( $product->get_permalink() ) . '">';
										if ( $show_image )
											echo '<div class="image-wrap">' . $product->get_image( 'yith-woocompare-image' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										if ( $show_title )
											echo '<h4 class="product_title">' . esc_html( $product->get_title() ) . '</h4>';
										echo '</a>';

										echo wp_kses_post( yith_woocompare_get_vendor_name( $product ) );
									}

									if ( $product->is_type( 'bundle' ) ) {
										$bundled_items = $product->get_bundled_items();

										if ( ! empty( $bundled_items ) ) {

											echo '<div class="bundled_product_list">';

											foreach ( $bundled_items as $bundled_item ) {
												/**
												 * wc_bundles_bundled_item_details hook
												 */
												do_action( 'wc_bundles_bundled_item_details', $bundled_item, $product );
											}

											echo '</div>';
										}
									}

									if ( $show_add_cart ) {
										if ( class_exists( 'YITH_WCCL_Frontend' ) && defined( 'YITH_WCCL_PREMIUM' ) && YITH_WCCL_PREMIUM ) {
											$priority = has_filter( 'woocommerce_loop_add_to_cart_link', array( YITH_WCCL_Frontend(), 'add_select_options' ) );
											$priority !== FALSE && remove_filter( 'woocommerce_loop_add_to_cart_link', array( YITH_WCCL_Frontend(), 'add_select_options' ), $priority );
										}
										echo '<div class="add_to_cart_wrap">';
										woocommerce_template_loop_add_to_cart();
										echo '</div>';
									}

									if ( shortcode_exists( 'yith_ywraq_button_quote' ) && $product->is_type( 'simple' ) && $show_request_quote == 'yes' ) {
										echo do_shortcode( '[yith_ywraq_button_quote product="' . $product->get_id() . '"]' );
									}

									break;

								case 'rating':
									$rating = function_exists( 'wc_get_rating_html' ) ? wc_get_rating_html( $product->get_average_rating() ) : $product->get_rating_html();
									echo $rating != '' ? '<div class="woocommerce-product-rating">' . wp_kses_post( $rating ) . '</div>' : '-';
									break;

								default:

									if ( $product instanceof WC_Product_Variation && apply_filters( 'yith_woocompare_support_show_single_variations', true ) ) {
										$parent_product    = wc_get_product( $product->get_parent_id() );
										$attributes        = $product->get_attributes();
										$parent_attributes = wp_get_post_terms( $product->get_parent_id(), $field );

										if ( strpos( $field, 'pa_' ) !== false ) {

											if ( isset( $attributes[ $field ] ) && $attributes[ $field ] != '' ) {
												$attributes[ $field ];
											} elseif ( ! empty( $parent_attributes ) ) {
												$n_attributes = count( $parent_attributes );
												$i            = 1;
												foreach ( $parent_attributes as $attribute ) {
													echo esc_html( $attribute->name . ' ' );
													if ( $i < $n_attributes )
														echo ', ';
													$i++;
												}
											} else {
												echo '-';
											}

											// echo isset($attributes[$field]) && $attributes[$field] != '' ? $attributes[$field]  : '';

										} else {
											echo wp_kses_post( apply_filters( 'yith_woocompare_single_variation_field_value', do_shortcode( $product->fields[ $field ] ), $product, $field ) );
										}
									} else {
										echo empty( $product->fields[ $field ] ) ? '-' : do_shortcode( $product->fields[ $field ] );
									}
									break;
							}
							?>
						</td>
						<?php
						++$index;
					endforeach
					?>
				</tr>
			<?php endforeach; ?>

			<?php if ( $repeat_price == 'yes' && isset( $fields['price'] ) ) : ?>
				<tr class="price repeated">
					<th>
						<?php echo wp_kses_post( $fields['price'] ); ?>
					</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						$product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product_id ?>
						<td class="<?php echo esc_attr( $product_class ) ?>"><?php echo wp_kses_post( $product->fields['price'] ); ?></td>
						<?php
						++$index;
					endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( $repeat_add_to_cart == 'yes' ) : ?>
				<tr class="add-to-cart repeated">
					<th>&nbsp;</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						$product_class = ( $index % 2 == 0 ? 'odd' : 'even' ) . ' product_' . $product_id ?>
						<td class="<?php echo esc_attr( $product_class ); ?>">
							<?php woocommerce_template_loop_add_to_cart(); ?>
						</td>
						<?php
						++$index;
					endforeach; ?>
				</tr>
			<?php endif; ?>

			</tbody>
		</table>

		<?php do_action( 'yith_woocompare_after_main_table', $products, $fixed ); ?>

	<?php endif; ?>

</div>