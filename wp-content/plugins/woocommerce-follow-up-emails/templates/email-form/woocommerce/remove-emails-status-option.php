<div class="options_group">
	<p class="form-field">
		<label for="remove_email_status_change" class="inline">
			<?php esc_html_e('Remove on status change', 'follow_up_emails'); ?>
		</label>
		<input type="hidden" name="meta[remove_email_status_change]" value="no" />
		<input type="checkbox" name="meta[remove_email_status_change]" id="remove_email_status_change" value="yes" <?php if (isset($email->meta['remove_email_status_change']) && $email->meta['remove_email_status_change'] == 'yes') echo 'checked'; ?> />
		<span class="description"><?php esc_html_e('Remove unsent emails when an order status changes', 'follow_up_emails'); ?></span>
	</p>
</div>
