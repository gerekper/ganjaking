<?php

/**
 * WC_Bookings_Save_Meta_Box.
 */
class WC_Bookings_Save_Meta_Box {

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
		$this->id         = 'woocommerce-booking-save';
		$this->title      = __( 'Booking actions', 'woocommerce-bookings' );
		$this->context    = 'side';
		$this->priority   = 'high';
		$this->post_types = array( 'wc_booking' );
	}

	/**
	 * Render inner part of meta box.
	 */
	public function meta_box_inner( $post ) {
		wp_nonce_field( 'wc_bookings_save_booking_meta_box', 'wc_bookings_save_booking_meta_box_nonce' );

		?>
		<div id="delete-action"><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php esc_html_e( 'Move to trash', 'woocommerce-bookings' ); ?></a></div>

		<input type="submit" class="button save_order button-primary tips" name="save" value="<?php esc_attr_e( 'Save Booking', 'woocommerce-bookings' ); ?>" data-tip="<?php echo wc_sanitize_tooltip( __( 'Save/update the booking', 'woocommerce-bookings' ) ); ?>" />
		<?php
	}
}
