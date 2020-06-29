<div class="main">
	<p>
		<select id="fue_dash_period">
			<option value="7d" <?php selected( $period, '7d' ); ?>><?php esc_html_e('Last 7 days', 'follow_up_emails'); ?></option>
			<option value="30d" <?php selected( $period, '30d' ); ?>><?php esc_html_e('Last 30 days', 'follow_up_emails'); ?></option>
			<option value="60d" <?php selected( $period, '60d' ); ?>><?php esc_html_e('Last 60 days', 'follow_up_emails'); ?></option>
			<option value="90d" <?php selected( $period, '90d' ); ?>><?php esc_html_e('Last 90 days', 'follow_up_emails'); ?></option>
			<option value="year" <?php selected( $period, 'year' ); ?>><?php esc_html_e('This year', 'follow_up_emails'); ?></option>
			<option value="all_time" <?php selected( $period, 'all_time' ); ?>><?php esc_html_e('All time', 'follow_up_emails'); ?></option>
		</select>
	</p>
	<ul id="fue-donuts">
		<li>
			<div
				id="sent_total_gauge"
				class="gauge"
				data-label="<?php esc_attr_e('Emails Sent', 'follow_up_emails'); ?>"
				data-value="<?php echo esc_attr( $stats['total_emails_sent'] ); ?>"
			/>
		</li>
		<li>
			<div
				id="sent_today_gauge"
				class="gauge"
				data-label="<?php esc_attr_e('Sent Today', 'follow_up_emails'); ?>"
				data-value="<?php echo esc_attr( $stats['emails_sent_today'] ); ?>"
			/>
		</li>
		<li>
			<div
				id="scheduled_emails_gauge"
				class="gauge"
				data-label="<?php esc_attr_e('Scheduled', 'follow_up_emails'); ?>"
				data-value="<?php echo esc_attr( $stats['emails_scheduled_total'] ); ?>"
			/>
		</li>
		<li>
			<div
				id="opens_gauge"
				class="gauge"
				data-label="<?php esc_attr_e('Opens Percentage', 'follow_up_emails'); ?>"
				data-value="<?php echo esc_attr( $stats['open_pct'] ); ?>"
				/>
		</li>
	</ul>
	<div class="clear"></div>
</div>
