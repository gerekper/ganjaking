<?php
/**
 * HTML for product reviews report
 *
 * @type \WC_Product[] $products
 */
?>
<div id="poststuff" class="woocommerce-reports-wide wc-product-reviews-pro-report">
	<table class="wp-list-table widefat fixed product-reviews">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Product', 'woocommerce-product-reviews-pro' ); ?></th>
				<th><?php esc_html_e( 'Reviews', 'woocommerce-product-reviews-pro' ); ?></th>
				<th><?php esc_html_e( 'Highest Rating', 'woocommerce-product-reviews-pro' ); ?></th>
				<th><?php esc_html_e( 'Lowest Rating', 'woocommerce-product-reviews-pro' ); ?></th>
				<th><?php esc_html_e( 'Average Rating', 'woocommerce-product-reviews-pro' ); ?></th>
				<th><?php esc_html_e( 'Actions', 'woocommerce-product-reviews-pro' ); ?></th>
			</tr>
		</thead>
		<tbody>

			<?php if ( ! empty( $products ) ) : ?>

				<?php foreach ( $products as $product ) : ?>

					<tr>
						<td><?php echo esc_html( get_the_title( $product->get_id() ) ); ?></td>
						<td><?php echo esc_html( $product->review_count ); ?></td>
						<td><?php echo esc_html( $product->highest_rating ); ?></td>
						<td><?php echo esc_html( $product->lowest_rating ); ?></td>
						<td><?php echo esc_html( $product->average_rating ); ?></td>
						<td>
							<a href="<?php echo get_edit_post_link( $product->get_id() ); ?>">
								<?php esc_html_e( 'View Product', 'woocommerce-product-reviews-pro' ); ?>
							</a> |
							<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'reviews',  'p' => $product->get_id(), 'comment_type' => 'review' ), admin_url( 'admin.php' ) ) ); ?>">
								<?php esc_html_e( 'View Reviews', 'woocommerce-product-reviews-pro' ); ?>
							</a>
						</td>
					</tr>

				<?php endforeach; ?>

			<?php endif; ?>

		</tbody>
	</table>
</div>
