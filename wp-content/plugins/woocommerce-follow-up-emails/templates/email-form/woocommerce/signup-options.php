<p class="form-field">
	<label for="remove_signup_emails_on_purchase" class="inline long">
		<?php esc_html_e('Unsubscribe after purchasing', 'follow_up_emails'); ?>
	</label>
	<input type="hidden" name="meta[remove_signup_emails_on_purchase]" value="no" />
	<input type="checkbox" class="checkbox" name="meta[remove_signup_emails_on_purchase]" id="remove_signup_emails_on_purchase" value="yes" <?php if (isset($values['meta']['remove_signup_emails_on_purchase']) && $values['meta']['remove_signup_emails_on_purchase'] == 'yes') echo 'checked'; ?> />
	<span class="description"><?php esc_html_e('Enabling this option will stop customers from getting this email once they have made a purchase.', 'follow_up_emails'); ?></span>
</p>