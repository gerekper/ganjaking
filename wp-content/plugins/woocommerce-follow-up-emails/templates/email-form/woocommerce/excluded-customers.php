<div class="options_group storewide wc_bookings non-signup hideable <?php do_action('fue_form_excluded_customers_tr_class', $email); ?> excluded_customers_div">
	<p><?php esc_html_e('Do not send to customers who have previously purchased the selected products or products in the selected categories', 'follow_up_emails'); ?></p>

	<p class="form-field">
		<label for="exclude_customers_product_ids"><?php esc_html_e('Products', 'follow_up_emails'); ?></label>

		<?php
		$product_ids    = array();
		$json_ids       = array();

		if ( !empty($email->meta['excluded_customers_products']) ) {
			$product_ids    = array_filter( array_map( 'absint', $email->meta['excluded_customers_products'] ) );
		}

		foreach ( $product_ids as $product_id ) {
			$product = WC_FUE_Compatibility::wc_get_product( $product_id );
			$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
		}
		?>
		<input
			type="hidden"
			id="excluded_customers_products"
			name="meta[excluded_customers_products]"
			class="ajax_select2_products_and_variations"
			data-multiple="true"
			data-placeholder="<?php esc_attr_e('Search for products&hellip;', 'follow_up_emails'); ?>"
			value="<?php echo esc_attr( implode( ',', array_keys( $json_ids ) ) ); ?>"
			data-selected="<?php echo wc_esc_json( wp_json_encode( $json_ids ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"
			style="width: 400px;"
		>
	</p>

	<p class="form-field">
		<label for="excluded_customers_categories"><?php esc_html_e('Categories', 'follow_up_emails'); ?></label>

		<input type="hidden" name="meta[excluded_customers_categories]" value="" />
		<select id="excluded_customers_categories" name="meta[excluded_customers_categories][]" class="select2" data-placeholder="<?php esc_attr_e('Select categories&hellip;', 'follow_up_emails'); ?>" style="width: 400px;" data-multiple="true" multiple>
			<option></option>
			<?php
			$excluded = (!empty($email->meta['excluded_customers_categories'])) ? $email->meta['excluded_customers_categories'] : array();
			foreach ($categories as $category):
				?>
				<option value="<?php esc_attr_e($category->term_id); ?>" <?php selected( in_array($category->term_id, $excluded), true ); ?>><?php echo esc_html($category->name); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
</div>