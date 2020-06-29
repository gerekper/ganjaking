<?php $logs = get_post_meta( $post->ID, 'wc_box_office_log', true ); ?>

<div class="panel woocommerce_options_panel">
	<table class="widefat">
		<thead>
			<tr>
				<th><?php _e( 'Date', 'woocommerce-box-office' ); ?></th>
				<th><?php _e( 'Log Message', 'woocommerce-box-office' ); ?></th>
		</thead>
		<tbody>
			<?php foreach ( $logs as $log ) : ?>
			<tr>
				<td><?php echo esc_html( date( 'Y-md-d H:i:s', intval( $log['timestamp'] ) ) ); ?></td>
				<td><?php echo esc_html( $log['message'] ); ?></td>
			</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
