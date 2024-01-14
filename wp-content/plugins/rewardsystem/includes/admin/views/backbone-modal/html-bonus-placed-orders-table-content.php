<?php
/**
 * Bonus Point Placed Orders Table Content.
 * */
defined( 'ABSPATH' ) || exit;
?>

<div class="rs-bonus-point-placed-orders-table-popup-wrapper">
	<div class="rs-bonus-point-placed-order-count-message">
		<b><?php esc_html_e( 'Total Orders: ' . $order_count, 'rewardsystem' ); ?></b>
	</div>

	<table class="rs-bonus-point-placed-orders-table-popup striped widefat">
		<thead>
			<tr>
				<th><b><?php esc_html_e( 'Order ID', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Order Status', 'rewardsystem' ); ?></b></th>
				<th><b><?php esc_html_e( 'Ordered Date', 'rewardsystem' ); ?></b></th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $order_ids as $order_id ) :
				$_order = wc_get_order( $order_id );
				if ( ! is_object( $_order ) ) :
					continue;
				endif;
				?>
				<tr>
					<td><?php echo wp_kses_post( sprintf( '<a href = "%s" target = "_blank">%s</a > ', esc_url( get_edit_post_link( $_order->get_id() ) ), esc_html( '#' . $_order->get_id() ) ) ); ?></td>
					<td><?php echo wp_kses_post( sprintf( '<mark class="order-status status-%s"><span>%s</span></mark>', $_order->get_status(), ucfirst( $_order->get_status() ) ) ); ?></td>
					<td>
						<?php
						$order_timestamp = '' != $_order->get_date_created() ? $_order->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
						echo wp_kses_post( '' != $order_timestamp ? SRP_Date_Time::get_wp_format_datetime_from_gmt( $_order->get_date_created() ) : '-' );
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>

		<?php if ( 1 < $order_total_pages ) : ?>
			<tfoot>
				<tr>
					<td colspan="3">
						<?php // Class 'variations-pagenav' given for CSS purpose. ?>
						<div class="rs-bonus-placed-orders-content-pagenav variations-pagenav">
							<span class="rs-pagination-links">
								<a class="rs-first-page disabled" title="<?php esc_attr_e( 'Go to the first page', 'rewardsystem' ); ?>" href="#">&laquo;</a>
								<a class="rs-prev-page disabled" title="<?php esc_attr_e( 'Go to the previous page', 'rewardsystem' ); ?>" href="#">&lsaquo;</a>
								<span class="paging-select">
									<label class="screen-reader-text">
										<?php esc_html_e( 'Select Page', 'rewardsystem' ); ?>
									</label>

									<select class="rs-page-selector" title="<?php esc_attr_e( 'Current page', 'rewardsystem' ); ?>" style="width:auto !important;">
										<?php for ( $i = 1; $i <= $order_total_pages; $i++ ) : ?>
											<option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_attr( $i ); ?></option>
										<?php endfor; ?>
									</select>

									<?php echo esc_html__( 'of', 'rewardsystem' ); ?> <span class="rs-total-pages"><?php echo esc_html( $order_total_pages ); ?></span>
								</span>
								<a class="rs-next-page" title="<?php esc_attr_e( 'Go to the next page', 'rewardsystem' ); ?>" href="#">&rsaquo;</a>
								<a class="rs-last-page" title="<?php esc_attr_e( 'Go to the last page', 'rewardsystem' ); ?>" href="#">&raquo;</a>
								<input type="hidden" class="rs-total-orders" value="<?php echo esc_attr( $order_total_pages ); ?>">
							</span>
						</div>
					</td>
				</tr>
			</tfoot>
		<?php endif; ?>
	</table>
</div>
<?php
