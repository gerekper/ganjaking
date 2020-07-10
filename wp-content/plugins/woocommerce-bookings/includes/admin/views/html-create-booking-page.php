<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Add Booking', 'woocommerce-bookings' ); ?></h2>

	<p><?php esc_html_e( 'You can create a new booking for a customer here. This form will create a booking for the user, and optionally an associated order. Created orders will be marked as pending payment.', 'woocommerce-bookings' ); ?></p>

	<?php $this->show_errors(); ?>

	<form method="POST" data-nonce="<?php echo esc_attr( wp_create_nonce( 'find-booked-day-blocks' ) ); ?>">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="customer_id"><?php esc_html_e( 'Customer', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<?php if ( version_compare( WC_VERSION, '3.0', '<' ) ) : ?>
							<input type="hidden" name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php esc_attr_e( 'Guest', 'woocommerce-bookings' ); ?>" data-allow_clear="true" />
						<?php else : ?>
							<select name="customer_id" id="customer_id" class="wc-customer-search" data-placeholder="<?php esc_attr_e( 'Guest', 'woocommerce-bookings' ); ?>" data-allow_clear="true">
							</select>
						<?php endif; ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="bookable_product_id"><?php esc_html_e( 'Bookable Product', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<select id="bookable_product_id" name="bookable_product_id" class="chosen_select" style="width: 300px">
							<option value=""><?php esc_html_e( 'Select a bookable product...', 'woocommerce-bookings' ); ?></option>
							<?php foreach ( WC_Bookings_Admin::get_booking_products() as $product ) : ?>
								<option value="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo esc_html( sprintf( '%s (#%s)', $product->get_name(), $product->get_id() ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="create_order"><?php esc_html_e( 'Create Order', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<p>
							<label>
								<input type="radio" name="booking_order" value="new" class="checkbox" />
								<?php esc_html_e( 'Create a new corresponding order for this new booking. Please note - the booking will not be active until the order is processed/completed.', 'woocommerce-bookings' ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="radio" name="booking_order" value="existing" class="checkbox" />
								<?php esc_html_e( 'Assign this booking to an existing order with this ID:', 'woocommerce-bookings' ); ?>
								<?php if ( class_exists( 'WC_Seq_Order_Number_Pro' ) ) : ?>
									<input type="text" name="booking_order_id" value="" class="text" size="15" />
								<?php else : ?>
									<input type="number" name="booking_order_id" value="" class="text" size="10" />
								<?php endif; ?>
							</label>
						</p>
						<p>
							<label>
								<input type="radio" name="booking_order" value="" class="checkbox" checked="checked" />
								<?php esc_html_e( 'Don\'t create an order for this booking.', 'woocommerce-bookings' ); ?>
							</label>
						</p>
					</td>
				</tr>
				<?php do_action( 'woocommerce_bookings_after_create_booking_page' ); ?>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<input type="submit" name="create_booking" class="button-primary" value="<?php esc_attr_e( 'Next', 'woocommerce-bookings' ); ?>" />
						<?php wp_nonce_field( 'create_booking_notification' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
