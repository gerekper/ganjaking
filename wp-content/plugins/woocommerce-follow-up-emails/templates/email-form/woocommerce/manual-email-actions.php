<div class="send-type-customer send-type-div">
	<select id="recipients" name="recipients[]" class="email-search-select" multiple data-placeholder="<?php esc_attr_e( 'Search by customer name or email&hellip;', 'follow_up_emails' ); ?>" style="width: 100%;"></select>
</div>
<div class="send-type-product send-type-div">
	<select id="product_ids" name="product_ids[]" class="wc-product-search" multiple data-placeholder="<?php esc_attr_e( 'Search for products&hellip;', 'follow_up_emails' ); ?>"></select>
</div>
<div class="send-type-category send-type-div">
	<select id="category_ids" name="category_ids[]" class="select2" multiple data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'follow_up_emails' ); ?>" style="width: 100%;">
		<?php foreach ( $categories as $category ) : ?>
			<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo ( $email->category_id == $category->term_id ) ? 'selected' : ''; ?>><?php echo esc_html( $category->name ); ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="send-type-timeframe send-type-div">
	<?php esc_html_e( 'From:', 'follow_up_emails' ); ?>
	<input type="text" class="" name="timeframe_from" id="timeframe_from" />

	<?php esc_html_e( 'To:', 'follow_up_emails' ); ?>
	<input type="text" class="" name="timeframe_to" id="timeframe_to" />
</div>
