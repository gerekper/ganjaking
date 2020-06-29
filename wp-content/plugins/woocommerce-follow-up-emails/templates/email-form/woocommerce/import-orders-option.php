<div class="options_group">
	<p class="form-field">
		<label for="import_orders" class="inline">
			<?php esc_html_e('Import Existing Orders', 'follow_up_emails'); ?>
		</label>
		<input type="hidden" name="meta[import_orders]" value="no" />
		<input type="checkbox" name="meta[import_orders]" id="import_orders" value="yes" <?php if (isset($email->meta['import_orders']) && $email->meta['import_orders'] == 'yes') echo 'checked'; ?> />
		<span class="description"><?php esc_html_e('Import existing orders that match this email criteria', 'follow_up_emails'); ?></span>
	</p>
</div>
