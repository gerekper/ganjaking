<p class="form-field">
	<label for="storewide_type"><?php esc_html_e('Enable for', 'follow_up_emails'); ?></label>
	<select name="meta[storewide_type]" id="storewide_type" class="">
		<option value="all" <?php selected( $storewide_type, 'all' ); ?>><?php esc_html_e('All subscription products', 'follow_up_emails'); ?></option>
		<option value="products" <?php selected( $storewide_type, 'products' ); ?>><?php esc_html_e('A specific subscription product', 'follow_up_emails'); ?></option>
		<option value="categories" <?php selected( $storewide_type, 'categories' ); ?>><?php esc_html_e('A specific category', 'follow_up_emails'); ?></option>
	</select>
	<input type="hidden" name="storewide_type" id="storewide_type_hidden" disabled value="" />
</p>

<div class="non-signup reminder hideable <?php do_action( 'fue_form_product_tr_class', $email ); ?> product_tr">
	<p class="form-field hideable subscription_product_tr">
		<label for="subscription_product_id"><?php esc_html_e( 'Enable for', 'follow_up_emails' ); ?></label>
		<select
			id="product_id"
			name="product_id"
			class="ajax_select2_products_and_variations"
			data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_wc_json_search_subscription_products"
			data-allow_clear="true"
		>
		<?php
			$product_id = ( ! empty( $email->product_id ) ) ? $email->product_id : '';

			if ( ! empty( $product_id ) ) {
				$product      = WC_FUE_Compatibility::wc_get_product( $product_id );
				$product_name = $product ? htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) : '';
		?>
			<option value="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $product_name ); ?></option>
		<?php
			}
		?>
		</select>
	</p>
	<?php
	$display        = 'display: none;';
	$has_variations = (!empty($email->product_id) && FUE_Addon_Woocommerce::product_has_children($email->product_id)) ? true : false;

	if ($has_variations) $display = 'display: inline-block;';
	?>
	<p class="form-field product_include_variations" style="<?php echo esc_attr( $display ); ?>">
		<input type="checkbox" name="meta[include_variations]" id="include_variations" value="yes" <?php if (isset($email->meta['include_variations']) && $email->meta['include_variations'] == 'yes') echo 'checked'; ?> />
		<label for="include_variations" class="inline"><?php esc_html_e('Include variations', 'follow_up_emails'); ?></label>
	</p>
</div>

<div class="non-signup hideable <?php do_action('fue_form_category_tr_class', $email); ?> category_tr">
	<p class="form-field">
		<label for="category_id"><?php esc_html_e('Category', 'follow_up_emails'); ?></label>

		<select id="category_id" name="category_id" class="select2" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'follow_up_emails' ); ?>" style="width: 100%">
			<option value="0"><?php esc_html_e( 'Any Category', 'follow_up_emails' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $email->category_id, $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
</div>
