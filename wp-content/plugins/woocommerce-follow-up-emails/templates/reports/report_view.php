<?php
/** @var array $reports */

// get the name of the email
if ( empty($reports) ) {
	$report_name = sprintf(__('Email #%d', 'follow_up_emails'), $id);
} else {
	$report = $reports[0];
	$report_name = $report->email_name;
}
?>
<h3><?php echo esc_html( sprintf(__('Report for &quot;%s&quot;', 'follow_up_emails'), $report_name) ); ?></h3>

<h4><?php esc_html_e('Quick Stats', 'follow_up_emails'); ?></h4>

<ul class="reports-stats">
	<li>
		<div
			id="sent_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Emails Sent', 'follow_up_emails'); ?>"
			data-value="<?php echo esc_attr( $total_sent ); ?>"
			data-max="<?php echo esc_attr( $total_sent ); ?>"
			></div>
	</li>
	<li>
		<div
			id="opens_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Emails Opened', 'follow_up_emails'); ?>"
			data-label="(<?php echo number_format($total_opened); ?>)"
			data-value="<?php echo esc_attr( $open_pct ); ?>"
			data-symbol="%"
			></div>
	</li>
	<li>
		<div
			id="bounces_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Bounces', 'follow_up_emails'); ?>"
			data-label="(<?php echo number_format( $total_bounces ); ?>)"
			data-value="<?php echo esc_attr( $bounce_pct ); ?>"
			data-symbol="%"
			></div>
	</li>
	<li>
		<div
			id="clicks_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Clicks', 'follow_up_emails'); ?>"
			data-label="(<?php echo number_format($total_clicks); ?>)"
			data-value="<?php echo esc_attr( $click_pct ); ?>"
			data-symbol="%"
			></div>
	</li>
	<li>
		<div
			id="unsubscribes_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Unsubscribes', 'follow_up_emails'); ?>"
			data-value="<?php echo esc_attr( $total_unsubscribes ); ?>"
			></div>
	</li>
</ul>

<div class="clear"></div>

<h4><?php esc_html_e('Devices', 'follow_up_emails'); ?></h4>

<ul class="reports-stats">
	<li class="auto-width">
		<span class="dashicons-desktop dashicons"></span>
		<strong><?php echo esc_html( $device_desktop_pct ); ?>%</strong>
		<?php esc_html_e('on Desktop', 'follow_up_emails'); ?>
	</li>
	<li class="auto-width">
		<span class="dashicons-smartphone dashicons"></span>
		<strong><?php echo esc_html( $device_mobile_pct ); ?>%</strong>
		<?php esc_html_e('on Mobile', 'follow_up_emails'); ?>
	</li>
	<li class="auto-width">
		<span class="dashicons-admin-site dashicons"></span>
		<strong><?php echo esc_html( $device_web_pct ); ?>%</strong>
		<?php esc_html_e('on a Web Browser', 'follow_up_emails'); ?>
	</li>
	<li class="auto-width">
		<span class="dashicons-editor-help dashicons"></span>
		<strong><?php echo esc_html( $device_unknown_pct ); ?>%</strong>
		<?php esc_html_e('on an Unknown Device', 'follow_up_emails'); ?>
	</li>
</ul>

<div class="clear"></div>

<h4><?php esc_html_e('Geolocation', 'follow_up_emails'); ?></h4>

<ul class="reports-stats">
	<?php if ( empty( $country_data ) ): ?>
		<li style="text-align: center;"><strong><?php esc_html_e('No data', 'follow_up_emails'); ?></strong></li>
	<?php
	else:
		foreach ( $country_data as $country ): ?>
			<li class="auto-width">
				<?php
				if ( empty( $country->user_country ) ) {
					$flag = '<span class="dashicons dashicons-editor-help"></span>';
				} else {
					$flag = '<img src="'. esc_url( FUE_TEMPLATES_URL ) .'/images/blank.gif" class="flag flag-'. strtolower( $country->user_country ) .'" />';
				}

				echo wp_kses_post( $flag );
				?>
				<strong><?php echo esc_html( $country->percentage ); ?>%</strong>
				(<?php echo empty( $country->user_country ) ? esc_html__('Unknown', 'follow_up_emails') : esc_html( $country->user_country ); ?>)
			</li>
		<?php
		endforeach;
	endif;
	?>
</ul>
<div class="clear"></div>
<br/>
<table class="wp-list-table widefat fixed striped posts">
	<thead>
	<tr>
		<th scope="col" id="type" class="manage-column column-type" style=""><?php esc_html_e('Customer Name', 'follow_up_emails'); ?></th>
		<th scope="col" id="user_email" class="manage-column column-user_email" style=""><?php esc_html_e('Email', 'follow_up_emails'); ?></th>
		<th scope="col" id="product" class="manage-column column-product" style=""><?php esc_html_e('Product', 'follow_up_emails'); ?></th>
		<th scope="col" id="trigger" class="manage-column column-trigger" style=""><?php esc_html_e('Trigger', 'follow_up_emails'); ?></th>
		<th scope="col" id="order" class="manage-column column-order" style="">&nbsp;</th>
		<th scope="col" id="date_sent" class="manage-column column-date_sent" style=""><?php esc_html_e('Date Sent', 'follow_up_emails'); ?></th>
	</tr>
	</thead>
	<tbody id="the_list">
	<?php
	if ( empty($reports) ):
	?>
		<tr scope="row">
			<th colspan="6"><?php esc_html_e('No reports available', 'follow_up_emails'); ?></th>
		</tr>
	<?php
	else:
		foreach ( $reports as $report ):
			$url = esc_url( admin_url( 'admin.php?page=followup-emails-reports&tab=reportuser_view&user_id='. $report->user_id .'&email='. $report->email_address ) );

			if (! empty($report->customer_name) ) {
				$name = '<strong><a href="'. $url .'">'. stripslashes($report->customer_name) .'</a></strong>';
			} else {
				$name = '<strong><a href="'. $url .'">'. stripslashes($report->email_address) .'</a></strong>';
			}
	?>
		<tr scope="row">
			<td class="post-title column-title">
				<?php echo wp_kses_post( $name ); ?>
			</td>
			<td><?php echo esc_html( wp_unslash($report->email_address) ); ?></td>
			<td>
				<?php

				if ( $report->product_id != 0 ) {
					echo '<a href="'. esc_url( get_permalink($report->product_id) ) .'">'. esc_html( get_the_title($report->product_id) ) .'</a>';
				} else {
					echo '-';
				}
				?>
			</td>
			<td><?php echo esc_html( $report->email_trigger ); ?></td>
			<td>
				<?php
				if ($report->order_id != 0) {
					echo '<a href="post.php?post='. esc_attr( $report->order_id ) .'&action=edit">View Order</a>';
				} else {
					echo '-';
				}
				?>
			</td>
			<td><?php echo esc_html( date( get_option('date_format') .' '. get_option('time_format') , strtotime($report->date_sent)) ); ?></td>
		</tr>
	<?php
		endforeach;
	endif; //empty ($reports)
	?>
	</tbody>
</table>
