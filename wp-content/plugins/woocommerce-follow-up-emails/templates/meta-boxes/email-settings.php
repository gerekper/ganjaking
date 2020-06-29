<div class="options_group">
	<?php if ( $email->type == 'storewide' ): ?>
		<p class="form-field">
			<label for="always_send">
				<?php esc_html_e('Always Send', 'follow_up_emails'); ?>
			</label>
			<input type="hidden" name="always_send" id="always_send_off" value="0" />
			<input type="checkbox" class="checkbox" name="always_send" id="always_send" value="1" <?php if ($email->always_send == 1) echo 'checked'; ?> />
			<span class="description"><?php esc_html_e('Always send this email, regardless of other initial rules. Use carefully, as this could result in multiple emails being sent per order.', 'follow_up_emails'); ?></span>
		</p>
	<?php else: ?>
		<input type="hidden" name="always_send" id="always_send_off" value="1" />
	<?php endif; ?>

	<?php if ( ! in_array( $email->type, array( 'signup', 'manual' ) ) ): ?>
		<p class="form-field">
			<label for="meta_one_time">
				<?php esc_html_e('Send once per customer', 'follow_up_emails'); ?>
			</label>
			<input type="hidden" name="meta[one_time]" id="meta_one_time_off" value="no" />
			<input type="checkbox" class="checkbox" name="meta[one_time]" id="meta_one_time" value="yes" <?php if (isset($email->meta['one_time']) && $email->meta['one_time'] == 'yes') echo 'checked'; ?> />
			<span class="description"><?php esc_html_e('A customer will only receive this email once, even if purchased multiple times at different dates', 'follow_up_emails'); ?></span>
		</p>

		<p class="form-field">
			<label for="adjust_date">
				<?php esc_html_e('Delay existing email', 'follow_up_emails'); ?>
			</label>
			<input type="hidden" name="meta[adjust_date]" id="adjust_date_off" value="no" />
			<input type="checkbox" class="checkbox" name="meta[adjust_date]" id="adjust_date" value="yes" <?php if (isset($email->meta['adjust_date']) && $email->meta['adjust_date'] == 'yes') echo 'checked'; ?> />
			<span class="description"><?php esc_html_e('If the customer already has this email scheduled, it will delay that scheduled email to the new future date.', 'follow_up_emails'); ?></span>
		</p>
	<?php endif; ?>

	<?php if ( 'twitter' === $email->type ): ?>
		<p class="form-field">
			<label for="require_twitter_handle">
				<?php esc_html_e( 'Require twitter handle', 'follow_up_emails'); ?>
			</label>
			<input type="hidden" name="meta[require_twitter_handle]" id="require_twitter_handle_off" value="no" />
			<input type="checkbox" class="checkbox" name="meta[require_twitter_handle]" id="require_twitter_handle" value="yes" <?php if ( isset( $email->meta['require_twitter_handle'] ) && 'yes' === $email->meta['require_twitter_handle'] ) echo 'checked'; ?> />
			<span class="description"><?php esc_html_e( 'Only tweet when a customer has twitter handle.', 'follow_up_emails' ); ?></span>
		</p>
	<?php endif; ?>
</div><!-- /options_group -->

<?php do_action( 'fue_email_form_settings', $email ); ?>
