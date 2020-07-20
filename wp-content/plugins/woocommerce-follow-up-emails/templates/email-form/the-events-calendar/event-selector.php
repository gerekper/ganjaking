<div class="options_group wootickets-selector">
	<p class="form-field">
		<label for="wootickets_type"><?php esc_html_e('Enable for', 'follow_up_emails'); ?></label>
		<select name="meta[wootickets_type]" id="wootickets_type" class="select2">
			<option value="all" <?php selected( $wootickets_type, 'all' ); ?>>
				<?php esc_html_e('All tickets', 'follow_up_emails'); ?>
			</option>
			<option value="products" <?php selected( $wootickets_type, 'products' ); ?>>
				<?php esc_html_e('A specific ticket', 'follow_up_emails'); ?>
			</option>
			<option value="categories" <?php selected( $wootickets_type, 'categories' ); ?>>
				<?php esc_html_e('A specific category', 'follow_up_emails'); ?>
			</option>
			<option value="event_categories" <?php selected( $wootickets_type, 'event_categories' ); ?>>
				<?php esc_html_e('A specific event category', 'follow_up_emails'); ?>
			</option>
		</select>
	</p>

	<p class="form-field hideable ticket_product_tr">
		<label for="ticket_product_id"><?php esc_html_e( 'Ticket', 'follow_up_emails' ); ?></label>
		<select
			id="product_id"
			name="product_id"
			class="ajax_select2_products_and_variations"
			data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'follow_up_emails' ); ?>"
			data-action="fue_wc_json_search_ticket_products"
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

	if ($has_variations) {
		$display = 'display: inline-block;';
	}
	?>
	<p class="form-field product_include_variations" style="<?php echo esc_attr( $display ); ?>">
		<input type="checkbox" name="meta[include_variations]" id="include_variations" value="yes" <?php if (isset($email->meta['include_variations']) && $email->meta['include_variations'] == 'yes') echo 'checked'; ?> />
		<label for="include_variations" class="inline"><?php esc_html_e('Include variations', 'follow_up_emails'); ?></label>
	</p>

	<p class="form-field hideable ticket_category_tr">
		<label for="ticket_category_id"><?php esc_html_e('Category', 'follow_up_emails'); ?></label>

		<select id="ticket_category_id" name="ticket_category_id" class="select2" data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'follow_up_emails' ); ?>">
			<option value="0"><?php esc_html_e( 'Any Category', 'follow_up_emails' ); ?></option>
			<?php foreach ( $categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $email->category_id, $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<p class="form-field hideable ticket_event_category_tr">
		<?php
		$event_categories = get_terms( 'tribe_events_cat', array( 'hide_empty' => false ) );
		?>
		<label for="ticket_event_category_id"><?php esc_html_e( 'Event Category', 'follow_up_emails' ); ?></label>

		<select
			id="ticket_event_category_id"
			name="ticket_event_category_id"
			class="select2"
			data-placeholder="<?php esc_attr_e( 'Search for a category&hellip;', 'follow_up_emails' ); ?>"
			>
			<option value="0"><?php esc_html_e( 'Any Category', 'follow_up_emails' ); ?></option>
			<?php foreach ( $event_categories as $category ) : ?>
				<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $email->category_id, $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>
</div>
