<table class="widefat wp-list-table">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Ticket Product', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Sold Tickets', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Stock', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Price', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Total Sales', 'woocommerce-box-office' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php if ( ! empty( $rows ) ) : ?>
		<?php foreach ( $rows as $index => $row ) : ?>
		<tr class="<?php echo $index % 2 === 0 ? 'alternate' : ''; ?>">
			<td>
				<a href="<?php echo esc_url( $row['product_link'] ); ?>">
					<?php echo esc_html( $row['product_title'] ); ?>
				</a>
			</td>
			<td><?php echo esc_html( $row['sold'] ); ?></td>
			<td><?php echo wp_kses_post( $row['stock'] ); ?></td>
			<td><?php echo wp_kses_post( $row['price'] ); ?></td>
			<td><strong><?php echo wp_kses_post( wc_price( $row['total_sales'] ) ); ?></strong></td>
		</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="5">
				<?php esc_html_e( 'No data to show. Start selling your tickets!', 'woocommerce-box-office' ); ?>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
