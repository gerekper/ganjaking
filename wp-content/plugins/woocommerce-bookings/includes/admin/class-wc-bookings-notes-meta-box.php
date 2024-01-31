<?php
/**
 * A bookings notes meta-box class file.
 *
 * @package WooCommerce Bookings
 */

/**
 * WC_Bookings_Notes_Meta_Box class.
 */
class WC_Bookings_Notes_Meta_Box {

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
	 *
	 * @var array
	 */
	public $post_types;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id         = 'woocommerce-notes-data';
		$this->title      = __( 'Notes', 'woocommerce-bookings' );
		$this->context    = 'side';
		$this->priority   = 'default';
		$this->post_types = array( 'wc_booking' );
	}

	/**
	 * Render inner part of meta box.
	 *
	 * @since 2.0.8
	 *
	 * @param object $post Post object.
	 */
	public function meta_box_inner( $post ) {
		// Get the global database access class.
		global $wpdb;
		global $booking;

		if ( ! is_a( $booking, 'WC_Booking' ) || $booking->get_id() !== $post->ID ) {
			$booking = new WC_Booking( $post->ID );
		}

		$order_id = $booking->get_order_id();
		$order    = $order_id ? wc_get_order( $order_id ) : false;

		// Fetch and display order notes.
		if ( $order_id && $order ) {

			// Custom query to retrieve order notes,
			// where comment_content has the current booking id.
			$notes = $wpdb->get_results(
				/* translators: %d: order ID, %s: booking ID with # prefix */
				$wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}comments
					WHERE comment_post_ID = %d
					AND comment_type = 'order_note'
					AND comment_content LIKE %s
					ORDER BY comment_date_gmt DESC",
					$order_id,
					'%#' . $post->ID . '%'
				)
			);

			if ( $notes ) {
				?>
				<ul class="order_notes">
					<?php foreach ( $notes as $note ) { ?>
						<li class="system-note">
							<div class="note_content">
								<p><?php echo wp_kses_post( $note->comment_content ); ?></p>
							</div>
							<p class="meta">
								<abbr class="exact-date" title="<?php echo esc_attr( date_i18n( 'c', strtotime( $note->comment_date ) ) ); ?>"><?php echo esc_html( date_i18n( wc_date_format(), strtotime( $note->comment_date ) ) ); ?></abbr>
								<?php echo ' ' . esc_html__( 'at', 'woocommerce-bookings' ) . ' ' . esc_html( date_i18n( 'h:i a', strtotime( $note->comment_date ) ) ); ?>
							</p>
						</li>
					<?php } ?>
				</ul>
				<?php
			}
		}
	}
}

