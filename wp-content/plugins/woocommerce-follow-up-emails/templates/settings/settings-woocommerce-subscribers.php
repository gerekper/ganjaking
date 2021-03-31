<hr>

<h3><?php esc_html_e('Email List Preferences', 'follow_up_emails'); ?></h3>

<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

<table class="form-table">
	<tr>
		<th colspan="2">
			<label for="enable_account_subscription">
				<input type="checkbox" name="enable_account_subscription" id="enable_account_subscription" value="1" <?php checked( 1, get_option('fue_enable_account_subscription', 0) ); ?> />
				<?php esc_html_e('Allow customers to manage their email list preferences from their account', 'follow_up_emails'); ?>
			</label>
		</th>
	</tr>
	<tr class="show-if-account-subscription">
		<th scope="row">
			<label for="email_subscriptions_page_title"><?php esc_html_e('Display Title', 'follow_up_emails'); ?></label>
		</th>
		<td>
			<input type="text" name="email_subscriptions_page_title" id="email_subscriptions_page_title" value="<?php echo esc_attr( get_option( 'fue_email_subscriptions_page_title', 'Email Subscriptions' ) ); ?>" />
		</td>
	</tr>
	<tr class="show-if-account-subscription">
		<th scope="row">
			<label for="email_subscriptions_button_text"><?php esc_html_e('Update Button Text', 'follow_up_emails'); ?></label>
		</th>
		<td>
			<input type="text" name="email_subscriptions_button_text" id="email_subscriptions_button_text" value="<?php echo esc_attr( get_option( 'fue_email_subscriptions_button_text', 'Update Subscriptions' ) ); ?>" />
		</td>
	</tr>
</table>

<hr>

<h3><?php esc_html_e('Checkout Subscription', 'follow_up_emails'); ?></h3>

<table class="form-table">
	<tr>
		<th colspan="2">
			<label for="enable_checkout_subscription">
				<input type="checkbox" name="enable_checkout_subscription" id="enable_checkout_subscription" value="1" <?php if (1 == get_option('fue_enable_checkout_subscription', 1)) echo 'checked'; ?> />
				<?php esc_html_e('Allow customers to subscribe to the newsletter on the checkout form', 'follow_up_emails'); ?>
			</label>
		</th>
	</tr>
	<tr class="checkout-subscription">
		<th>
			<label for="checkout_subscription_list"><?php esc_html_e('Add subscribers to this list', 'follow_up_emails'); ?></label>
		</th>
		<td>
			<select name="checkout_subscription_list" id="checkout_subscription_list">
				<?php $selected_filter = get_option('fue_checkout_subscription_list', ''); ?>
				<option value="" <?php selected( $selected_filter, '' ); ?>><?php esc_html_e('Uncategorized', 'follow_up_emails'); ?></option>
				<?php foreach ( Follow_Up_Emails::instance()->newsletter->get_lists() as $list ): ?>
					<option value="<?php echo esc_attr( $list['id'] ); ?>" <?php selected( $selected_filter, $list['id'] ); ?>><?php echo esc_html( $list['list_name'] ); ?></option>
				<?php endforeach; ?>
			</select>
		</td>
	</tr>
	<tr class="checkout-subscription">
		<th>
			<label for="checkout_message">
				<?php esc_html_e('Checkout Field Label', 'follow_up_emails'); ?>
			</label>
		</th>
		<td>
			<?php
			$label = get_option( 'fue_checkout_subscription_field_label', 'Send me promos and product updates.' );
			?>
			<input type="text" name="checkout_subscription_field_label" id="checkout_message" value="<?php echo esc_attr( $label ); ?>" size="50" />
		</td>
	</tr>
	<tr class="checkout-subscription">
		<th>
			<label for="checkout_subscription_default">
				<?php esc_html_e('Default Checkbox State', 'follow_up_emails'); ?>
			</label>
		</th>
		<td>
			<?php
			$checked = get_option( 'fue_checkout_subscription_default', 'unchecked' );
			?>
			<select name="checkout_subscription_default" id="checkout_subscription_default">
				<option value="checked" <?php selected( $checked, 'checked' ); ?>><?php esc_html_e('Checked', 'follow_up_emails'); ?></option>
				<option value="unchecked" <?php selected( $checked, 'unchecked' ); ?>><?php esc_html_e('Unchecked', 'follow_up_emails'); ?></option>
			</select>
		</td>
	</tr>
</table>
<script>
	jQuery(document).ready(function($) {
		$( '#enable_checkout_subscription' ).on( 'change', function() {
			if ( $(this).is(":checked") ) {
				$("tr.checkout-subscription").show();
			} else {
				$("tr.checkout-subscription").hide();
			}
		} ).trigger( 'change' );

		$( '#enable_account_subscription' ).on( 'change', function() {
			if ( $(this).is(":checked") ) {
				$("tr.show-if-account-subscription").show();
			} else {
				$("tr.show-if-account-subscription").hide();
			}
		} ).trigger( 'change' );
	});
</script>
