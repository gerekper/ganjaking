<form action="" method="get">
	<input type="hidden" name="post_type" value="event_ticket">
	<input type="hidden" name="page" value="ticket_tools">
	<input type="hidden" name="tab" value="export">
	<input type="hidden" name="action" value="export_tickets">

	<p><?php esc_html_e( 'Export attendee data for the following chosen tickets:', 'woocommerce-box-office' ); ?></p>

	<select name="tickets[]" class="chosen_select ticket-product-select" style="width:300px" required multiple>
		<?php foreach ( wc_box_office_get_all_ticket_products( true ) as $product ) : ?>
			<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
			<?php foreach ( $product->variations as $variation ): ?>
				<option value="<?php echo esc_attr( $variation->ID ); ?>"><?php echo esc_html( $variation->variation_title ); ?></option>
			<?php endforeach; ?>
		<?php endforeach ?>
	</select>

	<p>
		<label>
			<input type="checkbox" name="only_published_tickets" checked />
			<?php esc_html_e( 'Only export published tickets', 'woocommerce-box-office' ); ?>
		</label>
	</p>

	<p class="buttons">
		<input type="submit" value="<?php esc_attr_e( 'Download Export File (CSV)', 'woocommerce-box-office' ); ?>" class="button-primary">
	</p>
</form>
