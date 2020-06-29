<h3><?php echo esc_html( sprintf( __( 'Email Link Clicks Report for %s', 'follow_up_emails' ), $name ) ); ?></h3>

<table class="wp-list-table widefat fixed posts">
	<thead>
	<tr>
		<th scope="col" id="user_email" class="manage-column column-user_email" style=""><?php esc_html_e('Email', 'follow_up_emails'); ?></th>
		<th scope="col" id="user_email" class="manage-column column-user_email" style=""><?php esc_html_e('Target URL', 'follow_up_emails'); ?></th>
		<th scope="col" id="date_sent" class="manage-column column-date_sent" style=""><?php esc_html_e('Date Clicked', 'follow_up_emails'); ?></th>
	</tr>
	</thead>
	<tbody id="the_list">
	<?php
	if ( empty($reports) ):
	?>
		<tr scope="row">
			<th colspan="3"><?php esc_html_e('No reports available', 'follow_up_emails'); ?></th>
		</tr>
	<?php
	else:
		foreach ($reports as $report):
			$user_email = $report->user_email;

			if ( empty( $user_email ) ) {
				$item = new FUE_Sending_Queue_Item( $report->email_order_id );
				$user_email = $item->user_email;
			}
	?>
		<tr scope="row">
			<td><?php echo esc_html($user_email); ?></td>
			<td><a href="<?php echo esc_attr($report->target_url); ?>" target="_blank"><?php echo esc_html($report->target_url); ?></a></td>
			<td><?php echo esc_html( date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_added)) ); ?></td>
		</tr>
	<?php
		endforeach;
	endif;
	?>
	</tbody>
</table>
