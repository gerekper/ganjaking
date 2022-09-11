<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YWSBS_Subscription Legacy Abstract Class.
 *
 * @class   YWSBS_Subscription_Legacy
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class YWSBS_Subscription_Legacy
 */
abstract class YWSBS_Subscription_Legacy {

	/**
	|--------------------------------------------------------------------------
	| Deprecated Methods
	|--------------------------------------------------------------------------
	 **/

	/**
	 * Populate the subscription
	 *
	 * @return     void
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function populate() {
		_deprecated_function( 'YWSBS_Subscription::populate', '2.0.0' );

		$this->set( 'post', get_post( $this->get( 'id' ) ) );

		foreach ( $this->get_subscription_meta() as $key => $value ) {
			$this->set( $key, $value );
		}

		do_action( 'ywsbs_subscription_loaded', $this );
	}

	/**
	 * Fill the default metadata with the post meta stored in db
	 *
	 * @return     array
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_subscription_meta() {
		_deprecated_function( 'YWSBS_Subscription::get_subscription_meta', '2.0.0' );

		$subscription_meta = array();
		foreach ( $this->get_default_meta_data() as $key => $value ) {
			$subscription_meta[ $key ] = get_post_meta( $this->id, $key, true );
		}

		return $subscription_meta;
	}

	/**
	 * Return an array of pause options
	 *
	 * @return     array|void
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	private function get_subscription_product_pause_options() {
		_deprecated_function( 'YWSBS_Subscription::get_subscription_product_pause_options', '2.0.0', 'YWSBS_Subscription_Helper()->get_subscription_product_pause_options' );
		YWSBS_Subscription_Helper()->get_subscription_product_pause_options( $this );
	}

	/**
	 * Calculate taxes.
	 *
	 * @return     void
	 * @deprecated 2.0.0
	 */
	private function calculate_taxes() {
		_deprecated_function( 'YWSBS_Subscription::calculate_taxes', '2.0.0', 'YWSBS_Subscription_Helper()->calculate_taxes' );
		YWSBS_Subscription_Helper()->calculate_taxes( $this );
	}

	/**
	 * Get tax location for this order.
	 *
	 * @param array $args array Override the location.
	 *
	 * @return array
	 *
	 * @since      1.4.5
	 * @deprecated 2.0.0
	 */
	protected function get_tax_location( $args = array() ) {
		_deprecated_function( 'YWSBS_Subscription::get_tax_location', '2.0.0', 'YWSBS_Subscription_Helper()->get_tax_location' );
		return YWSBS_Subscription_Helper()->get_tax_location( $this, $args );
	}

	/**
	 * Change the total amount meta on a subscription after a change without recalculate taxes.
	 *
	 * @deprecated 2.0.0
	 */
	public function calculate_totals_from_changes() {
		_deprecated_function( 'YWSBS_Subscription::calculate_totals_from_changes', '2.0.0', 'YWSBS_Subscription_Helper()->calculate_totals_from_changes' );
		YWSBS_Subscription_Helper()->calculate_totals_from_changes( $this );
	}

	/**
	 * Check if the subscription has failed attempts
	 *
	 * @deprecated 2.0.0
	 */
	public function has_failed_attemps() {
		_deprecated_function( 'YWSBS_Subscription::has_failed_attemps', '2.0.0', 'YWSBS_Subscription()->has_failed_attempts' );
		$this->has_failed_attempts();
	}

	/**
	 * Get the next payment due date.
	 *
	 * @return     int
	 * @deprecated 2.0.0
	 */
	public function get_payment_due_date_paused_offset() {
		_deprecated_function( 'YWSBS_Subscription::get_payment_due_date_paused_offset', '2.0.0', 'YWSBS_Subscription_Helper()->get_payment_due_date_paused_offset' );
		return YWSBS_Subscription_Helper()->get_payment_due_date_paused_offset( $this );
	}

	/**
	 * Return the timestamp from activation of subscription excluding pauses
	 *
	 * @param bool $exclude_pauses Exclude pauses.
	 *
	 * @return     float|int
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_activity_period( $exclude_pauses = true ) {
		_deprecated_function( 'YWSBS_Subscription::get_activity_period', '2.0.0', 'YWSBS_Subscription_Helper()->get_activity_period' );
		return YWSBS_Subscription_Helper()->get_activity_period( $this, $exclude_pauses );
	}

	/**
	 * Calculate the gap payment in the upgrade processing
	 *
	 * @param int $variation_id Variation ID.
	 *
	 * @return     float
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function calculate_gap_payment( $variation_id ) {
		_deprecated_function( 'YWSBS_Subscription::calculate_gap_payment', '2.0.0', 'YWSBS_Subscription_Helper()->calculate_gap_payment' );
		return YWSBS_Subscription_Helper()->calculate_gap_payment( $variation_id, $this );
	}

	/**
	 * Return the subscription detail page url
	 *
	 * @param bool $admin Admin.
	 *
	 * @return     string
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_view_subscription_url( $admin = false ) {
		_deprecated_function( 'YWSBS_Subscription::get_view_subscription_url', '2.0.0', 'ywsbs_get_view_subscription_url' );
		return ywsbs_get_view_subscription_url( $this->get_id(), $admin );
	}

	/**
	 * Return the a link for change the status of subscription
	 *
	 * @param string $status Status.
	 *
	 * @return     string
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_change_status_link( $status ) {
		_deprecated_function( 'YWSBS_Subscription::get_change_status_link', '2.0.0', 'ywsbs_get_change_status_link' );
		return ywsbs_get_change_status_link( $this->get_id(), $status );
	}

	/**
	 * Return the subscription recurring price formatted
	 *
	 * @param string $tax_display      Display tax.
	 * @param bool   $show_time_option Show time option.
	 *
	 * @return     string
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_formatted_recurring( $tax_display = '', $show_time_option = true ) {
		_deprecated_function( 'YWSBS_Subscription::get_formatted_recurring', '2.0.0', 'YWSBS_Subscription_Helper()->get_formatted_recurring' );
		return YWSBS_Subscription_Helper()->get_formatted_recurring( $this, $tax_display, $show_time_option );
	}

	/**
	 * Return the next payment due date if there are rates not payed
	 *
	 * @return     int
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function get_left_time_to_next_payment() {
		_deprecated_function( 'YWSBS_Subscription::get_left_time_to_next_payment', '2.0.0', 'YWSBS_Subscription_Helper()->get_left_time_to_next_payment' );
		return YWSBS_Subscription_Helper()->get_left_time_to_next_payment( $this );
	}


}
