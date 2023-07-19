<h3><?php esc_html_e( 'Previously sent emails', 'woocommerce-box-office' ); ?></h3>

<table class="widefat past-emails">
	<thead>
		<tr>
			<th class="row-title"><?php esc_html_e( 'Subject', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Product', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Sent', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Remaining', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Total targetted', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Date sent', 'woocommerce-box-office' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		$emails = get_posts( array(
			'post_type'      => 'event_ticket_email',
			'posts_per_page' => -1,
			'post_status'    => array( 'publish', 'pending' ),
		) );

		$counter = 0;
		?>

		<?php if ( ! empty( $emails ) ) : ?>
			<?php foreach ( $emails as $email ) : ?>
				<?php
				$product_id = absint( get_post_meta( $email->ID, '_product_id', true ) );
				$targets    = get_post_meta( $email->ID, '_ticket_ids', true );
				$remaining  = (array) get_post_meta( $email->ID, '_ticket_id' );
				$sent       = count( $targets ) - count( $remaining );
				$product    = wc_get_product( $product_id );
				$is_variant = 'variation' === $product->get_type();
				$parent_id  = $is_variant ? $product->get_parent_id() : false;
				?>

				<tr class="<?php echo esc_attr( ++$counter % 2 === 0 ? '' : 'alt' ); ?>">
					<td class="row-title">
						<a href="<?php echo esc_url( admin_url( 'post.php?post=' . esc_attr( $email->ID ) . '&action=edit' ) ); ?>">
							<?php echo esc_html( $email->post_title ); ?>
						</a>
					</td>
					<td>
						<a href="<?php echo esc_url( admin_url( sprintf( 'post.php?post=%s&action=edit', $parent_id ? $parent_id : $product_id ) ) ); ?>">
							<?php echo esc_html( get_the_title( $product_id ) ); ?>
						</a>
					</td>

					<td><?php echo esc_html( $sent ); ?></td>
					<td><?php echo intval( count( $remaining ) ); ?></td>
					<td><?php echo intval( count( $targets ) ); ?></td>
					<td><?php echo esc_html( $email->post_date ); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td colspan="6"><em><?php esc_html_e( 'No emails have been sent yet.', 'woocommerce-box-office' ); ?></em></td>
			</tr>
		<?php endif; ?>
	</tbody>

	<tfoot>
		<tr>
			<th class="row-title"><?php esc_html_e( 'Subject', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Product', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Sent', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Remaining', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Total targetted', 'woocommerce-box-office' ); ?></th>
			<th><?php esc_html_e( 'Date sent', 'woocommerce-box-office' ); ?></th>
		</tr>
	</tfoot>
</table>
