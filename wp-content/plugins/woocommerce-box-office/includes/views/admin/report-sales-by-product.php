<table class="widefat wp-list-table">
	<thead>
		<tr>
			<th><?php _e( 'Ticket Product', 'woocommerce-box-office' ); ?></th>
			<th><?php _e( 'Sold Tickets', 'woocommerce-box-office' ); ?></th>
			<th><?php _e( 'Stock', 'woocommerce-box-office' ); ?></th>
			<th><?php _e( 'Price', 'woocommerce-box-office' ); ?></th>
			<th><?php _e( 'Total Sales', 'woocommerce-box-office' ); ?></th>
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
			<td><?php echo $row['stock']; // xss ok ?></td>
			<td><?php echo $row['price']; // xss ok ?></td>
			<td><strong><?php echo wc_price( $row['total_sales'] ); ?></strong></td>
		</tr>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="5">
				<?php _e( 'No data to show. Start selling your tickets!', 'woocommerce-box-office' ); ?>
			</td>
		</tr>
	<?php endif; ?>
	</tbody>
</table>
