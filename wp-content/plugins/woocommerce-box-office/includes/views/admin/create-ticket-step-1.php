<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$data = $posted_data;

$data['product_id']          = ( ! empty( $data['product_id'] ) ) ? $data['product_id'] : '';
$data['create_order_method'] = ( ! empty( $data['create_order_method'] ) ) ? $data['create_order_method'] : 'no_order';
$data['ticket_order_id']     = ( ! empty( $data['ticket_order_id'] ) ) ? $data['ticket_order_id'] : '';
?>

<div class="wrap woocommerce">
	<h2><?php _e( 'Create Ticket', 'woocommerce-box-office' ); ?></h2>

	<p>
		<?php _e( 'You can create a new ticket for a customer here. This form will create a ticket for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'woocommerce-box-office' ); ?>
	</p>

	<?php $this->maybe_print_errors(); ?>

	<form method="POST">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="customer_id"><?php _e( 'Customer', 'woocommerce-box-office' ); ?></label>
				</th>
				<td>
					<?php if ( version_compare( WC_VERSION, '3.0', '>=' ) ) : ?>
					<select id="customer_id" class="wc-customer-search" name="customer_id" style="width:300px" data-placeholder="<?php esc_attr_e( 'Guest', 'woocommerce-box-office' ) ?>">
							<option value=""><?php _e( 'Guest', 'woocommerce-box-office' ) ?></option>
						</select>
					<?php else : ?>
						<input type="hidden" class="wc-customer-search" id="customer_id" name="customer_id" data-placeholder="<?php _e( 'Guest', 'woocommerce-box-office' ); ?>" data-allow_clear="true" />
					<?php endif; ?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="product_id"><?php _e( 'Ticket-enabled Product', 'woocommerce-box-office' ); ?></label>
				</th>
				<td>
					<select id="product_id" name="product_id" class="chosen_select" style="width:300px">
						<option value=""><?php _e( 'Select a ticket-enabled product...', 'woocommerce-box-office' ); ?></option>
						<?php foreach ( wc_box_office_get_all_ticket_products() as $product ) : ?>
							<option value="<?php echo $product->ID; ?>" <?php selected( $product->ID === $data['product_id'] ) ?>><?php echo $product->post_title; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="quantity"><?php _e( 'Ticket Quantity', 'woocommerce-box-office' ); ?></label>
				</th>
				<td>
					<input type="number" step="1" min="1" id="quantity" name="quantity" value="<?php echo esc_attr( ! empty( $data['quantity'] ) ? absint( $data['quantity'] ) : 1 ); ?>" title="<?php _e( 'Qty', 'woocommerce-box-office' ); ?>" class="input-text qty text" size="4">
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="create_order_method"><?php _e( 'Create Order Method', 'woocommerce-box-office' ); ?></label>
				</th>
				<td>
					<p>
						<label>
							<input type="radio" name="create_order_method" value="new" class="checkbox" <?php checked( 'new' === $data['create_order_method'] ); ?> />
							<?php _e( 'Create a new corresponding order for this new ticket. Please note - the ticket will not be active until the order is processed/completed.', 'woocommerce-box-office' ); ?>
						</label>
					</p>
					<p>
						<label>
							<input type="radio" name="create_order_method" value="existing" class="checkbox" <?php checked( 'existing' === $data['create_order_method'] ); ?> />
							<?php _e( 'Assign ticket(s) to an existing order with this ID:', 'woocommerce-box-office' ); ?>
							<input type="number" name="ticket_order_id" value="<?php echo esc_attr( $data['ticket_order_id'] ); ?>" class="text" size="3" />
						</label>
					</p>
					<p>
						<label>
							<input type="radio" name="create_order_method" value="no_order" class="checkbox" <?php checked( 'no_order' === $data['create_order_method'] ); ?> />
							<?php _e( 'Don\'t create an order for this ticket.', 'woocommerce-box-office' ); ?>
						</label>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">&nbsp;</th>
				<td>
					<input type="submit" name="submit_create_ticket" class="button-primary" value="<?php _e( 'Next', 'woocommerce-box-office' ); ?>" />

					<input type="hidden" name="create_ticket_step" value="1" />
					<?php wp_nonce_field( 'create_event_ticket' ); ?>
				</td>
			</tr>
		</table>
	</form>
</div>

<?php
if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
	// Ajax Chosen Customer Selectors JS
	wc_enqueue_js( "
		jQuery('select#customer_id').ajaxChosen({
			method: 		'GET',
			url: 			'" . admin_url('admin-ajax.php') . "',
			dataType: 		'json',
			afterTypeDelay: 100,
			minTermLength: 	1,
			data:		{
				action: 	'woocommerce_json_search_customers',
				security: 	'" . wp_create_nonce("search-customers") . "'
			}
		}, function (data) {

			var terms = {};

			$.each(data, function (i, val) {
				terms[i] = val;
			});

			return terms;
		});

		jQuery('select.chosen_select').chosen();
	" );
}
