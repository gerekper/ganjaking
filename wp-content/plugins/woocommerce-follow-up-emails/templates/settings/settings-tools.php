<form action="admin-post.php" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

	<h3><?php esc_html_e('Backup &amp; Restore', 'follow_up_emails'); ?></h3>

	<p>
		<?php esc_html_e('Backup your emails using the WordPress import and export functionality.', 'follow_up_emails' ); ?>
		<br />
		<a href="<?php echo esc_url( admin_url('import.php?import=wordpress') ); ?>"><?php esc_html_e('Import', 'follow_up_email'); ?></a> |
		<a href="<?php echo esc_url( admin_url('export.php') ); ?>"><?php esc_html_e('Export', 'follow_up_emails'); ?></a>
	</p>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<td colspan="2">
				<a class="button" href="<?php echo esc_url( wp_nonce_url('admin-post.php?action=fue_backup_settings', 'fue_backup') ); ?>"><?php esc_html_e('Download a Backup of the Settings', 'follow_up_emails'); ?></a>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<strong><?php esc_html_e('Restore Backup', 'follow_up_emails'); ?></strong>
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<td width="200"><label for="json_file"><?php esc_html_e('Emails JSON', 'follow_up_emails'); ?></label></td>
						<td><input type="file" name="emails_json" id="emails_json" /></td>
					</tr>
					<tr valign="top">
						<td width="200"><label for="emails_file"><?php esc_html_e('Emails CSV from pre-4.0 installs only', 'follow_up_emails'); ?></label></td>
						<td><input type="file" name="emails_file" id="emails_file" /></td>
					</tr>
					<tr valign="top">
						<td><label for="settings_file"><?php esc_html_e('Settings CSV from all versions', 'follow_up_emails'); ?></label></td>
						<td><input type="file" name="settings_file" id="settings_file" /></td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		</tbody>
	</table>

	<hr/>

	<h3><?php esc_html_e('Export Mailing List', 'follow_up_emails'); ?></h3>

	<p>
		<label for="export_list"><?php esc_html_e('List', 'follow_up_emails'); ?></label>
		<br/>
		<select id="export_list">
			<option value="0"><?php esc_html_e('All lists', 'follow_up_emails'); ?></option>
			<?php foreach (fue_get_subscription_lists() as $list ): ?>
				<option value="<?php echo esc_attr( $list['id'] ); ?>"><?php echo esc_html( $list['list_name'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p>
		<input type="hidden" id="export_list_id" value="<?php echo esc_attr( time() . get_current_user_id() ); ?>" />
		<input
			type="button"
			id="export_list_btn"
			class="button-secondary updater-action"
			value="<?php esc_attr_e('Export Emails', 'follow_up_emails'); ?>"
			data-callback="fue_init_export_list"
			/>

		<span id="export_list_status" style="display: none;">
			<img id="export_list_loader" src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/ajax-loader.gif'; ?>" />
			<span id="export_list_message"><?php esc_html_e('Please wait...', 'follow_up_emails'); ?></span>
		</span>
	</p>

	<hr/>

	<h3><?php esc_html_e('Action Scheduler Logging', 'follow_up_emails'); ?></h3>
	<p><strong><?php esc_html_e('The Action Scheduler, by default, logs completed actions to the comments for debugging purposes. Some users have inquired, but this is not a bug. You can turn off, and/or delete the actions log with the settings below.', 'follow_up_emails'); ?></strong></p>
	<p>
		<input type="checkbox" name="action_scheduler_disable_logging" id="action_scheduler_disable_logging" value="1" <?php checked( 1, $disable_logging ); ?> />
		<label for="action_scheduler_disable_logging"><?php esc_html_e( 'Disable email logging', 'follow_up_emails' ) ?></label>
	</p>

	<p>
		<input type="checkbox" name="action_scheduler_delete_logs" id="action_scheduler_delete_logs" value="1" />
		<label for="action_scheduler_delete_logs"><?php esc_html_e( 'Delete existing logs', 'follow_up_emails' ) ?></label>
	</p>

	<hr/>

	<h3><?php esc_html_e('Remove Old Daily Summary Data', 'follow_up_emails'); ?></h3>

	<p>
		<input class="button updater-action" data-callback="fue_init_delete_daily_summary" type="button" value="<?php esc_attr_e('Delete Old Summary Data', 'follow_up_emails'); ?>" />

		<span id="clean_daily_summary_status" style="display: none;">
			<img id="clean_daily_summary_loader" src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/ajax-loader.gif'; ?>" />
			<span id="clean_daily_summary_message"><?php esc_html_e('Please wait...', 'follow_up_emails'); ?></span>
		</span>
	</p>

	<hr/>

	<h3><?php esc_html_e('Reset Stats Data', 'follow_up_emails'); ?></h3>

	<p>
		<input class="button updater-action" type="button" data-callback="fue_init_delete_stats_data" value="<?php esc_attr_e('Delete Stats Data', 'follow_up_emails'); ?>" />

		<span id="clean_stats_status" style="display: none;">
			<img id="clean_stats_loader" src="<?php echo esc_url( FUE_TEMPLATES_URL ) .'/images/ajax-loader.gif'; ?>" />
			<span id="clean_stats_message"><?php esc_html_e('Please wait...', 'follow_up_emails'); ?></span>
		</span>
	</p>

	<hr/>

	<h3><?php esc_html_e( 'Debug logging', 'follow_up_emails' ); ?></h3>
	<p>
		<?php $logging = get_option( 'fue_logging', null ); ?>
		<input type="checkbox" name="fue_logging" id="fue_logging" value="1" <?php checked( 1, $logging ); ?> />
		<label for="fue_logging"><?php esc_html_e( 'Enable logging (requires WooCommerce).', 'follow_up_emails' ); ?></label>
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'wc-status', 'tab' => 'logs' ), 'admin.php' ) ); ?>"><?php esc_html_e( 'View logs', 'follow_up_emails' ); ?></a>
		<br/>
		<strong><?php esc_html_e( 'Note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'follow_up_emails' ); ?></strong>
	</p>

	<?php do_action( 'fue_settings_tools' ); ?>

	<p class="submit">
		<input type="hidden" name="action" value="fue_followup_save_settings" />
		<input type="hidden" name="section" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="submit" name="save" value="<?php esc_attr_e('Save Settings', 'follow_up_emails'); ?>" class="button-primary" />
	</p>
</form>
<script>
	(function($) {
		var fue_summary_posts = 0;
		var fue_summary_deleted = 0;

		$( '.updater-action' ).on( 'click', function( e ) {
			e.preventDefault();
			$( this ).prop( 'disabled', true );

			var cb = $(this).data("callback");
			var fn = eval(cb);
			if (typeof fn == 'function') {
				fn();
			}
		} );

		function fue_init_delete_stats_data() {
			$("#clean_stats_status").show();
			$("#clean_stats_message").html("Please wait...");
			fue_delete_stats_data();
		}

		function fue_delete_stats_data() {
			$.post(ajaxurl, {action: "fue_delete_stats_data"}, function(resp) {
				if ( resp.status && resp.status == 'processing' ) {
					fue_delete_stats_data();
				} else {
					// done
					$("#clean_stats_message").html("Completed!");
					$("#clean_stats_loader").hide();
				}
			});
		}

		function fue_init_delete_daily_summary() {
			$("#clean_daily_summary_status").show();

			$.post(ajaxurl, {action: "fue_count_daily_summary_posts"}, function(resp) {
				fue_summary_posts = resp.count;
				fue_delete_daily_summary();
			});
		}

		function fue_delete_daily_summary() {
			$.post(ajaxurl, {action: "fue_delete_daily_summary"}, function(resp) {
				if ( resp.count && resp.count > 0 ) {
					var remaining = resp.count;
					fue_summary_deleted = fue_summary_posts - remaining;
					percent = Math.round( (fue_summary_deleted / fue_summary_posts) * 100 );
					$("#clean_daily_summary_message").html("Please wait... ("+ percent +"%)");

					fue_delete_daily_summary();
				} else {
					// done
					$("#clean_daily_summary_message").html("Completed!");
					$("#clean_daily_summary_loader").hide();
				}
			});
		}

		function fue_init_export_list() {
			$("#export_list_status").show();
			fue_export_list();
		}

		function fue_export_list() {
			var list_id     = $("#export_list").val();
			var export_id   = $("#export_list_id").val();
			var data = {
				action: "fue_build_export_list",
				list:   list_id,
				id:     export_id
			};

			$.post(ajaxurl, data, function(resp) {
				if ( resp.status == "processing" ) {
					fue_export_list();
				} else if ( resp.status == "error") {
					$("#export_list_message").html("Error: "+ resp.message);
					$("#export_list_loader").hide();
					$( '#export_list_btn' ).prop( 'disabled', false );
				} else {
					$("#export_list_status").hide();
					$( '#export_list_btn' ).prop( 'disabled', false );

					var url = <?php echo wp_json_encode( 'admin-post.php?action=fue_followup_export_list&_wpnonce=' . wp_create_nonce( 'fue-export' ) ); ?>;
					window.location.href = url + '&id='+ export_id;
				}
			});
		}
	})(jQuery);
</script>
