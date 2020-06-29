<div class="followup-checkout">
	<p class="form-row form-row-wide">
		<label>
			<input type="checkbox" name="fue_subscribe" value="yes" <?php checked( 'checked', get_option('fue_checkout_subscription_default', 'unchecked') ); ?> />
			<?php echo esc_html( get_option( 'fue_checkout_subscription_field_label', 'Send me promos and product updates.' ) ); ?>
		</label>
	</p>
</div>