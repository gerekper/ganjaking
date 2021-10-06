<?php

/**
 * Logic for WooCommerce dashboard display.
 */
class WC_Bookings_Report_Dashboard {

	/**
	 * Hook in additional reporting to WooCommerce dashboard widget
	 */
	public function __construct() {

			// Add the dashboard widget text
			add_action( 'woocommerce_after_dashboard_status_widget', __CLASS__ . '::add_stats_to_dashboard' );
	}

	/**
	 * Add the Bookings specific details to the bottom of the dashboard widget
	 */
	public static function add_stats_to_dashboard() {
		global $wpdb;

		$new_bookings = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT wcbookings.ID) AS count
				FROM {$wpdb->posts} AS wcbookings
				INNER JOIN {$wpdb->posts} AS wcorder
					ON wcbookings.post_parent = wcorder.ID
				WHERE wcorder.post_type IN ( 'shop_order' )
					AND wcbookings.post_type IN ( 'wc_booking' )
					AND wcorder.post_status IN ( 'wc-completed', 'wc-processing', 'wc-on-hold', 'wc-refunded' )
					AND wcorder.post_date >= '%s'
					AND wcorder.post_date < '%s'",
			date( 'Y-m-01', current_time( 'timestamp' ) ),
			date( 'Y-m-d H:i:s', current_time( 'timestamp' ) )
		) );

		$require_confirmation = $wpdb->get_var( "SELECT COUNT(DISTINCT wcbookings.ID) AS count
				FROM {$wpdb->posts} AS wcbookings
					WHERE wcbookings.post_type IN ( 'wc_booking' )
					AND wcbookings.post_status = 'pending-confirmation'" );

		?>
		<li class="processing-orders">
			<a href="<?php echo esc_html( admin_url( 'edit.php?post_type=wc_booking&post_status=paid' ) ); ?>">
				<?php
				/* translators: 0: number of bookings, 1: number of bookings */
				printf( wp_kses_post( _n( '<strong>%s booking(s)</strong> new bookings this month', '<strong>%s booking(s)</strong> new bookings this month', $new_bookings, 'woocommerce-bookings' ) ), esc_html( $new_bookings ) );
				?>
			</a>
		</li>
		<li class="low-in-stock">
			<a href="<?php echo esc_html( admin_url( 'edit.php?post_type=wc_booking&post_status=pending-confirmation' ) ); ?> ">
				<?php
				/* translators: 0: number of bookings that require confirmation, 1: number of bookings that require confirmation */
				printf( wp_kses_post( _n( '<strong>%s booking(s)</strong> require confirmation', '<strong>%s booking(s)</strong> require confirmation', $require_confirmation, 'woocommerce-bookings' ) ), esc_html( $require_confirmation ) );
				?>
			</a>
		</li>
		<?php
	}
}
