<h2 class="nav-tab-wrapper woo-nav-tab-wrapper reports-overview-tabs">
	<a href="#opens" class="nav-tab nav-tab-active"><?php esc_html_e('Top Emails by Opens', 'follow_up_emails'); ?></a>
	<a href="#clicks" class="nav-tab"><?php esc_html_e('Top Emails by Clicks', 'follow_up_emails'); ?></a>
	<a href="#ctor" class="nav-tab"><?php esc_html_e('Top Emails by CTOR', 'follow_up_emails'); ?></a>
</h2>

<div class="chart_sections">
	<div class="chart_section" id="opens">
		<div id="opens_chart" class="chart_container"><h3>No data</h3></div>
	</div>
	<div class="chart_section" id="clicks">
		<div id="clicks_chart" class="chart_container"><h3>No data</h3></div>
	</div>
	<div class="chart_section" id="ctor">
		<div id="ctor_chart" class="chart_container"><h3>No data</h3></div>
	</div>
</div>

<?php
$json_encode_options = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>

<script>
var clicks_json = <?php echo wp_json_encode( $clicks_data, $json_encode_options ); ?>;
var opens_json = <?php echo wp_json_encode( $opens_data, $json_encode_options ); ?>;
var ctor_json = <?php echo wp_json_encode( $ctor_data, $json_encode_options ); ?>;

var clicks_rendered = opens_rendered = ctor_rendered = false;
</script>

<h4><?php esc_html_e('Stats', 'follow_up_emails'); ?></h4>

<ul class="reports-stats">
	<li>
		<div
			id="sent_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Emails Sent', 'follow_up_emails'); ?>"
			data-value="<?php echo esc_attr( $total_sent ); ?>"
			data-max="<?php echo esc_attr( max( $total_sent, 1 ) ); ?>"
			></div>
	</li>
	<li>
		<div
			id="opens_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Emails Opened', 'follow_up_emails'); ?>"
			data-label="(<?php echo esc_attr( number_format($total_opened) ); ?>)"
			data-value="<?php echo esc_attr( $open_pct ); ?>"
			data-symbol="%"
			></div>
	</li>
	<li>
		<div
			id="bounces_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Bounces', 'follow_up_emails'); ?>"
			data-label="(<?php echo esc_attr( number_format( $total_bounces ) ); ?>)"
			data-value="<?php echo esc_attr( $bounce_pct ); ?>"
			data-symbol="%"
			></div>
	</li>
	<li>
		<div
			id="clicks_gauge"
			class="gauge"
			data-title="<?php esc_attr_e('Clicks', 'follow_up_emails'); ?>"
			data-label="(<?php echo esc_attr( number_format($total_clicks) ); ?>)"
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
		<li><?php esc_html_e('No data', 'follow_up_emails'); ?></li>
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
		$user_country = sanitize_text_field( $country->user_country );
		?>
		<strong><?php echo esc_html( $country->percentage ); ?>%</strong>
		(<?php echo empty( $user_country ) ? esc_html__('Unknown', 'follow_up_emails') : esc_html( $user_country ); ?>)
	</li>
	<?php
		endforeach;
	endif;
	?>
</ul>
<div class="clear"></div>

<h3><?php esc_html_e('Emails', 'follow_up_emails'); ?></h3>
<form action="admin-post.php" method="post">
	<table class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<td scope="col" id="cb" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th scope="col" id="type" class="manage-column column-type" style=""><?php esc_html_e('Email Name', 'follow_up_emails'); ?></th>
				<th scope="col" id="usage_count" class="manage-column column-usage_count" style=""><?php esc_html_e('Emails Sent', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('The number of individual emails sent using this follow-up email', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></th>
				<th scope="col" id="opened" class="manage-column column-opens" style=""><?php esc_html_e('Opens', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('The number of times the this specific follow-up emails has been opened', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></th>
				<th scope="col" id="clicked" class="manage-column column-clicks" style=""><?php esc_html_e('Clicks', 'follow_up_emails'); ?> <img class="help_tip" width="16" height="16" title="<?php esc_attr_e('The number of times links in this follow-up email have been clicked', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" /></th>
				<th scope="col" id="bounces" class="manage-column column-bounces" style=""><?php esc_html_e('Bounces', 'follow_up_emails'); ?></th>
				<th scope="col" id="web_views" class="manage-column column-web-views" style=""><?php esc_html_e('Web Version Views', 'follow_up_emails'); ?></th>
				<th scope="col" id="conversions" class="manage-column column-conversions" style=""><?php esc_html_e('Conversions', 'follow_up_emails'); ?></th>
			</tr>
		</thead>
		<tbody id="the_list">
			<?php
			if (empty($email_reports)) {
				?>
				<tr scope="row">
					<th colspan="7"><?php esc_html_e('No reports available', 'follow_up_emails'); ?></th>
				</tr><?php
			} else {
				foreach ( $email_reports as $report ) {
					$sent       = FUE_Reports::count_email_sends( $report->email_id );
					$opened     = FUE_Reports::count_event_occurences( $report->email_id, 'open' );
					$web_opened = FUE_Reports::count_event_occurences( $report->email_id, 'web_open' );
					$clicked    = FUE_Reports::count_event_occurences( $report->email_id, 'click' );
					$bounces    = FUE_Reports::count_total_bounces( $report->email_id );
					$meta       = '';

					if ( class_exists( 'WooCommerce' ) ) {
						$conversions = FUE_Reports::get_conversion_reports( array( 'email_id' => $report->email_id ) );

						$num_conversions   = count( $conversions );
						$conversions_total = 0;

						foreach ( $conversions as $conversion ) {
							$conversions_total += WC_FUE_Compatibility::get_order_prop( $conversion['order'], 'order_total' );
						}
					}

					$email_row = new FUE_Email( $report->email_id );

					?><tr scope="row">
						<th scope="row" class="check-column">
							<input id="cb-select-106" type="checkbox" name="email_id[]" value="<?php echo esc_attr( $report->email_id ); ?>">
							<div class="locked-indicator"></div>
						</th>
						<td class="post-title column-title">
							<strong><?php echo esc_html( wp_unslash( $report->email_name ) ); ?></strong>
							<em><?php echo esc_html( apply_filters( 'fue_report_email_trigger', $report->email_trigger, $email_row ) ); ?></em><br/>
							<a href="admin.php?page=followup-emails-reports&tab=reportview&eid=<?php echo urlencode($report->email_id); ?>"><?php esc_html_e('View Report', 'follow_up_emails'); ?></a>
						</td>
						<td><a class="row-title" href="admin.php?page=followup-emails-reports&tab=reportview&eid=<?php echo urlencode($report->email_id); ?>"><span class="dashicons-before dashicons-email-alt"></span> <?php echo esc_html( $sent ); ?></a></td>
						<td><a class="row-title" href="admin.php?page=followup-emails-reports&tab=emailopen_view&eid=<?php echo urlencode($report->email_id); ?>&ename=<?php echo urlencode($report->email_name); ?>"><span class="dashicons-before dashicons-visibility"></span> <?php echo esc_html( $opened ); ?></a></td>
						<td><a class="row-title" href="admin.php?page=followup-emails-reports&tab=linkclick_view&eid=<?php echo urlencode($report->email_id); ?>&ename=<?php echo urlencode($report->email_name); ?>"><span class="dashicons-before dashicons-yes"></span> <?php echo esc_html( $clicked ); ?></a></td>
						<td><a class="row-title" href="admin.php?page=followup-emails-reports&tab=bounces_view&eid=<?php echo urlencode($report->email_id); ?>&ename=<?php echo urlencode($report->email_name); ?>"><span class="dashicons-before dashicons-flag"></span> <?php echo esc_html( $bounces ); ?></a></td>
						<td><span class="dashicons-before dashicons-desktop"></span> <?php echo esc_html( $web_opened ); ?></td>
						<td><?php echo class_exists( 'WooCommerce' ) ? wp_kses_post( sprintf( '%d (%s)', $num_conversions, wc_price( $conversions_total ) ) ) : ''; ?></td>
					</tr><?php
				}
			}
			?>
		</tbody>
	</table>
	<div class="tablenav bottom">
		<div class="alignleft actions bulkactions">
			<input type="hidden" name="action" value="fue_reset_reports" />
			<input type="hidden" name="type" value="emails" />
			<?php wp_nonce_field( 'fue-reset-reports') ?>
			<select name="emails_action">
				<option value="-1" selected="selected"><?php esc_html_e('Bulk Actions', 'wordpress'); ?></option>
				<option value="trash"><?php esc_html_e('Delete Selected', 'follow_up_emails'); ?></option>
			</select>
			<input type="submit" name="" id="doaction2" class="button action" value="Apply">
		</div>
	</div>
</form>
