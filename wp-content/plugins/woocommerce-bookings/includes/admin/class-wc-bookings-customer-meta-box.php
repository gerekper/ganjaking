<?php

/**
 * WC_Bookings_Customer_Meta_Box class.
 */
class WC_Bookings_Customer_Meta_Box {

	/**
	 * Meta box ID.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Meta box title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Meta box context.
	 *
	 * @var string
	 */
	public $context;

	/**
	 * Meta box priority.
	 *
	 * @var string
	 */
	public $priority;

	/**
	 * Meta box post types.
	 * @var array
	 */
	public $post_types;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id         = 'woocommerce-customer-data';
		$this->title      = __( 'Customer details', 'woocommerce-bookings' );
		$this->context    = 'side';
		$this->priority   = 'default';
		$this->post_types = array( 'wc_booking' );
	}

	/**
	 * Meta box content.
	 */
	public function meta_box_inner( $post ) {
		global $booking;

		if ( ! is_a( $booking, 'WC_Booking' ) || $booking->get_id() !== $post->ID ) {
			$booking = new WC_Booking( $post->ID );
		}
		$has_data = false;
		?>
		<table class="booking-customer-details">
		<?php
		$booking_customer_id = $booking->get_customer_id();
		$user                = $booking_customer_id ? get_user_by( 'id', $booking_customer_id ) : false;

		if ( $booking_customer_id && $user ) {
			?>
			<tr>
				<th><?php esc_html_e( 'Name:', 'woocommerce-bookings' ); ?></th>
				<td><?php echo esc_html( $user->last_name && $user->first_name ? $user->first_name . ' ' . $user->last_name : '&mdash;' ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Email:', 'woocommerce-bookings' ); ?></th>
				<td><?php echo wp_kses_post( make_clickable( sanitize_email( $user->user_email ) ) ); ?></td>
			</tr>
			<tr class="view">
				<th>&nbsp;</th>
				<td><a class="button button-small" target="_blank" href="<?php echo esc_url( admin_url( 'user-edit.php?user_id=' . absint( $user->ID ) ) ); ?>"><?php echo esc_html( 'View User', 'woocommerce-bookings' ); ?></a></td>
			</tr>
			<?php
			$has_data = true;
		}

		$booking_order_id = $booking->get_order_id();
		$order            = $booking_order_id ? wc_get_order( $booking_order_id ) : false;

		if ( $booking_order_id && $order ) {
			?>
			<tr>
				<th><?php esc_html_e( 'Address:', 'woocommerce-bookings' ); ?></th>
				<td><?php echo wp_kses( $order->get_formatted_billing_address() ? $order->get_formatted_billing_address() : __( 'No billing address set.', 'woocommerce-bookings' ), array( 'br' => array() ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Email:', 'woocommerce-bookings' ); ?></th>
				<td><?php echo wp_kses_post( make_clickable( sanitize_email( is_callable( array( $order, 'get_billing_email' ) ) ? $order->get_billing_email() : $order->billing_email ) ) ); ?></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Phone:', 'woocommerce-bookings' ); ?></th>
				<td><?php echo esc_html( is_callable( array( $order, 'get_billing_phone' ) ) ? $order->get_billing_phone() : $order->billing_phone ); ?></td>
			</tr>
			<tr class="view">
				<th>&nbsp;</th>
				<td><a class="button button-small" target="_blank" href="<?php echo esc_url( apply_filters( 'woocommerce_bookings_admin_view_order_url', admin_url( 'post.php?post=' . absint( $booking_order_id ) . '&action=edit' ), $booking_order_id, $order ) ); ?>"><?php echo esc_html( 'View Order', 'woocommerce-bookings' ); ?></a></td>
			</tr>
			<?php
			$has_data = true;
		}

		if ( ! $has_data ) {
			?>
			<tr>
				<td colspan="2"><?php esc_html_e( 'N/A', 'woocommerce-bookings' ); ?></td>
			</tr>
			<?php
		}
			?>
		</table>
		<?php
	}
}

