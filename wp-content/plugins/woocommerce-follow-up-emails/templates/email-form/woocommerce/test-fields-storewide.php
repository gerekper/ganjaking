<p class="form-field">
	<select id="test_type">
		<option value="product" selected><?php esc_html_e( 'as a Product', 'follow_up_emails' ); ?></option>
		<option value="order"><?php esc_html_e( 'as an Order', 'follow_up_emails' ); ?></option>
	</select>
</p>

<p class="form-field">
	<span id="test_email_order">
		<?php esc_html_e( 'with Order Number: ', 'follow_up_emails' ); ?>
		<input type="number" id="order_id" placeholder="e.g. 105" size="5" class="test-email-field" min="1" />
	</span>
</p>

<p class="form-field">
	<span id="test_email_product">
		<select type="hidden" class="ajax_select2_products_and_variations test-email-field" data-key="product_id" data-placeholder="Select a Product"></select>
	</span>
</p>
