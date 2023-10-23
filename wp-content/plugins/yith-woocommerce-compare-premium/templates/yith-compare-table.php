<?php
/**
 * Woocommerce Compare page
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.1.4
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

global $product, $yith_woocompare;

?>

<div id="yith-woocompare" class="woocommerce <?php echo $fixed ? esc_attr( 'fixed-compare-table' ) : ''; ?>">

	<?php
	if ( empty( $products ) ) :
		/**
		 * APPLY_FILTERS: yith_woocompare_empty_compare_message
		 *
		 * Filters the message shown when the comparison table is emtpy.
		 *
		 * @param string $message Message.
		 *
		 * @return string
		 */
		echo '<p>' . esc_html( apply_filters( 'yith_woocompare_empty_compare_message', __( 'No products added in the comparison table.', 'yith-woocommerce-compare' ) ) ) . '</p>';
	else :
		/**
		 * DO_ACTION: yith_woocompare_before_main_table
		 *
		 * Allows to render some content before the comparison table.
		 *
		 * @param array $products Products to show.
		 * @param bool  $fixed    Whether are products to show or not.
		 */
		do_action( 'yith_woocompare_before_main_table', $products, $fixed );

		?>

		<table id="yith-woocompare-table" class="compare-list has-background">
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
						$product_class = ( ( 0 === ( $index % 2 ) ) ? 'odd' : 'even' ) . ' product_' . $product_id
						?>
						<td class="<?php echo esc_attr( $product_class ); ?>">
							<a href="<?php echo esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ); ?>"
									data-iframe="<?php echo esc_attr( $iframe ); ?>"
									data-product_id="<?php echo esc_attr( $product_id ); ?>"><span
										class="remove">x</span><?php esc_html_e( 'Remove', 'yith-woocommerce-compare' ); ?>
							</a>
						</td>
						<?php
						++ $index;
					endforeach;
					?>
				</tr>
			<?php endif; ?>

			<?php foreach ( $fields as $field => $name ) : ?>

				<tr class="<?php echo ! in_array( $field, $different, true ) ? esc_attr( $field ) : esc_attr( $field ) . ' different'; ?>">

					<th>
						<?php echo esc_html( $name ); ?>
						<?php
						if ( 'product_info' === $field ) {
							echo '<div class="fixed-th"></div>';
						}
						?>
					</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						// Set td class.
						$product_class = ( ( 0 === ( $index % 2 ) ) ? 'odd' : 'even' ) . ' product_' . $product_id;
						if ( 'stock' === $field ) {
							$availability   = $product->get_availability();
							$product_class .= ' ' . ( empty( $availability['class'] ) ? 'in-stock' : $availability['class'] );
						}
						?>

						<td class="<?php echo esc_attr( $product_class ); ?>">
							<?php
							switch ( $field ) {

								case 'product_info':
									if ( ! $fixed ) {
										/**
										 * APPLY_FILTERS: yith_woocompare_remove_icon
										 *
										 * Filters the icon used to remove the product from the comparison table.
										 *
										 * @param string $icon Icon to remove product from comparison.
										 *
										 * @return string
										 */
										/**
										 * APPLY_FILTERS: yith_woocompare_remove_label
										 *
										 * Filters the label to remove the product from the comparison table.
										 *
										 * @param string $label Label to remove product from comparison.
										 *
										 * @return string
										 */
										echo '<div class="remove"><a href="' . esc_url( $yith_woocompare->obj->remove_product_url( $product_id ) ) . '" data-iframe="' . esc_attr( $iframe ) . '" data-product_id="' . esc_attr( $product_id ) . '"><span class="remove">' . wp_kses_post( apply_filters( 'yith_woocompare_remove_icon', 'x' ) ) . '</span>' . wp_kses_post( apply_filters( 'yith_woocompare_remove_label', esc_html__( 'Remove', 'yith-woocommerce-compare' ) ) ) . '</a></div>';
									}

									if ( $show_image || $show_title ) {
										echo '<a href="' . esc_attr( $product->get_permalink() ) . '">';
										if ( $show_image ) {
											echo '<div class="image-wrap">' . $product->get_image( 'yith-woocompare-image' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										}
										if ( $show_title ) {
											echo '<h4 class="product_title">' . esc_html( $product->get_title() ) . '</h4>';
										}
										echo '</a>';

										echo wp_kses_post( yith_woocompare_get_vendor_name( $product ) );
									}

									if ( $product->is_type( 'bundle' ) ) {
										$bundled_items = $product->get_bundled_items();

										if ( ! empty( $bundled_items ) ) {

											echo '<div class="bundled_product_list">';

											foreach ( $bundled_items as $bundled_item ) {
												// wc_bundles_bundled_item_details hook.
												do_action( 'wc_bundles_bundled_item_details', $bundled_item, $product );
											}

											echo '</div>';
										}
									}

									if ( $show_add_cart ) {
										if ( class_exists( 'YITH_WCCL_Frontend' ) && defined( 'YITH_WCCL_PREMIUM' ) && YITH_WCCL_PREMIUM ) {
											$priority = has_filter( 'woocommerce_loop_add_to_cart_link', array( YITH_WCCL_Frontend(), 'add_select_options' ) );
											false !== $priority && remove_filter( 'woocommerce_loop_add_to_cart_link', array( YITH_WCCL_Frontend(), 'add_select_options' ), $priority );
										}
										echo '<div class="add_to_cart_wrap">';
										woocommerce_template_loop_add_to_cart();
										echo '</div>';
									}

									if ( shortcode_exists( 'yith_ywraq_button_quote' ) && $product->is_type( 'simple' ) && 'yes' === $show_request_quote ) {
										echo do_shortcode( '[yith_ywraq_button_quote product="' . $product->get_id() . '"]' );
									}

									break;

								case 'rating':
									$rating = function_exists( 'wc_get_rating_html' ) ? wc_get_rating_html( $product->get_average_rating() ) : $product->get_rating_html();
									echo $rating ? '<div class="woocommerce-product-rating">' . wp_kses_post( $rating ) . '</div>' : '-';
									break;

								default:
									/**
									 * APPLY_FILTERS: yith_woocompare_support_show_single_variations
									 *
									 * Filters whether to show the single variations in the comparison table.
									 *
									 * @param bool $show_single_variations Whether to show the single variations or not.
									 *
									 * @return bool
									 */
									if ( $product instanceof WC_Product_Variation && apply_filters( 'yith_woocompare_support_show_single_variations', true ) ) {
										$parent_product    = wc_get_product( $product->get_parent_id() );
										$attributes        = $product->get_attributes();
										$parent_attributes = wp_get_post_terms( $product->get_parent_id(), $field );

										if ( strpos( $field, 'pa_' ) !== false ) {

											if ( isset( $attributes[ $field ] ) && '' !== $attributes[ $field ] ) {
												$attributes[ $field ];
											} elseif ( ! empty( $parent_attributes ) ) {
												$n_attributes = count( $parent_attributes );
												$i            = 1;
												foreach ( $parent_attributes as $attribute ) {
													echo esc_html( $attribute->name . ' ' );
													if ( $i < $n_attributes ) {
														echo ', ';
													}
													$i ++;
												}
											} else {
												echo '-';
											}
										} else {
											/**
											 * APPLY_FILTERS: yith_woocompare_single_variation_field_value
											 *
											 * Filters the field value for the single variation in the comparison table.
											 *
											 * @param string     $value   Field value.
											 * @param WC_Product $product Product object.
											 * @param string     $field   Field id to show.
											 *
											 * @return string
											 */
											echo wp_kses_post( apply_filters( 'yith_woocompare_single_variation_field_value', do_shortcode( $product->fields[ $field ] ), $product, $field ) );
										}
									} else {
										/**
										 * APPLY_FILTERS: yith_woocompare_value_default_field
										 *
										 * Filters the default value for the field in the comparison table.
										 *
										 * @param string     $value   Field value.
										 * @param WC_Product $product Product object.
										 * @param string     $field   Field id to show.
										 *
										 * @return string
										 */
										echo wp_kses_post( apply_filters( 'yith_woocompare_value_default_field', empty( $product->fields[ $field ] ) ? '-' : do_shortcode( $product->fields[ $field ] ), $product, $field ) );
									}
									break;
							}
							?>
						</td>
						<?php
						++ $index;
					endforeach
					?>
				</tr>
			<?php endforeach; ?>

			<?php if ( 'yes' === $repeat_price && isset( $fields['price'] ) ) : ?>
				<tr class="price repeated">
					<th>
						<?php echo wp_kses_post( $fields['price'] ); ?>
					</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						$product_class = ( ( 0 === ( $index % 2 ) ) ? 'odd' : 'even' ) . ' product_' . $product_id
						?>
						<td class="<?php echo esc_attr( $product_class ); ?>"><?php echo wp_kses_post( $product->fields['price'] ); ?></td>
						<?php
						++ $index;
					endforeach;
					?>
				</tr>
			<?php endif; ?>

			<?php if ( 'yes' === $repeat_add_to_cart ) : ?>
				<tr class="add-to-cart repeated">
					<th>&nbsp;</th>

					<?php
					$index = 0;
					foreach ( $products as $product_id => $product ) :
						$product_class = ( ( 0 === ( $index % 2 ) ) ? 'odd' : 'even' ) . ' product_' . $product_id
						?>
						<td class="<?php echo esc_attr( $product_class ); ?>">
							<?php woocommerce_template_loop_add_to_cart(); ?>
						</td>
						<?php
						++ $index;
					endforeach;
					?>
				</tr>
			<?php endif; ?>

			</tbody>
		</table>

		<?php
		/**
		 * DO_ACTION: yith_woocompare_after_main_table
		 *
		 * Allows to render some content after the comparison table.
		 *
		 * @param array $products Products to show.
		 * @param bool  $fixed    Whether are products to show or not.
		 */
		do_action( 'yith_woocompare_after_main_table', $products, $fixed );
		?>

	<?php endif; ?>

</div>
