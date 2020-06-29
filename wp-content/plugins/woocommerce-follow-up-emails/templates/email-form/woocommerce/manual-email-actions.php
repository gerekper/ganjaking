<div class="send-type-customer send-type-div">
	<input type="hidden" name="recipients[]" id="recipients" class="email-search-select" data-multiple="true" data-placeholder="Search by customer name or email..." style="width: 600px;">
</div>
<div class="send-type-product send-type-div">
	<input type="hidden" id="product_ids" name="product_ids[]" class="wc-product-search" data-action="fue_wc_json_search_products_and_variations" data-multiple="true" data-placeholder="<?php esc_attr_e('Search for a product&hellip;', 'follow_up_emails'); ?>" style="width: 600px">
</div>
<div class="send-type-category send-type-div">
	<select id="category_ids" name="category_ids[]" class="select2" multiple data-placeholder="<?php esc_attr_e('Search for a category&hellip;', 'follow_up_emails'); ?>" style="width: 600px;">
		<?php foreach ($categories as $category): ?>
			<option value="<?php esc_attr_e($category->term_id); ?>" <?php echo ($email->category_id == $category->term_id) ? 'selected' : ''; ?>><?php echo esc_html($category->name); ?></option>
		<?php endforeach; ?>
	</select>
</div>

<div class="send-type-timeframe send-type-div">
	<?php esc_html_e('From:', 'follow_up_emails'); ?>
	<input type="text" class="" name="timeframe_from" id="timeframe_from" />

	<?php esc_html_e('To:', 'follow_up_emails'); ?>
	<input type="text" class="" name="timeframe_to" id="timeframe_to" />
</div>