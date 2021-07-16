<?php
/**
 * Checkout functionality
 *
 * This class handles the subscription process in the checkout form.
 *
 * @package WC_Newsletter_Subscription
 * @since   2.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Checkout.
 */
class WC_Newsletter_Subscription_Checkout {

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_filter( 'woocommerce_checkout_fields', array( $this, 'checkout_fields' ) );
		add_action( 'woocommerce_checkout_order_processed', array( $this, 'process_checkout_order' ), 10, 3 );
	}

	/**
	 * Gets the location of the checkout content.
	 *
	 * @since 2.9.0
	 * @since 3.3.0 Returns an array with the action hook and the priority.
	 *
	 * @return array
	 */
	protected function get_content_location() {
		$key = get_option( 'woocommerce_newsletter_checkout_location', 'after_terms' );

		if ( has_filter( 'wc_newsletter_subscription_checkout_content_location' ) ) {
			wc_deprecated_hook( 'wc_newsletter_subscription_checkout_content_location', '3.3.0', 'wc_newsletter_subscription_checkout_locations' );

			/**
			 * Filters the locations of the checkout content.
			 *
			 * @since 2.9.0
			 * @deprecated 3.3.0
			 *
			 * @param string $key The locations key for the checkout content.
			 */
			$key = apply_filters( 'wc_newsletter_subscription_checkout_content_location', $key );
		}

		$locations = array(
			'after_billing' => array(
				'hook'     => 'woocommerce_after_checkout_billing_form',
				'priority' => 10,
			),
			'after_terms'   => array(
				'hook'     => 'woocommerce_review_order_before_submit',
				'priority' => 10,
			),
		);

		/**
		 * Filters the locations of the subscription checkbox in the checkout form.
		 *
		 * @since 3.3.0
		 *
		 * @param array $locations An array with the locations of the checkout content.
		 */
		$locations = apply_filters( 'wc_newsletter_subscription_checkout_locations', $locations );

		return ( isset( $locations[ $key ] ) ? $locations[ $key ] : $locations['after_terms'] );
	}

	/**
	 * Init.
	 *
	 * @since 2.9.0
	 */
	public function init() {
		$location = $this->get_content_location();

		add_action( $location['hook'], array( $this, 'checkout_content' ), $location['priority'] );
	}

	/**
	 * Registers the newsletter subscription fields in the checkout form.
	 *
	 * @since 2.9.0
	 *
	 * @param array $fields Checkout fields.
	 * @return array
	 */
	public function checkout_fields( $fields ) {
		if ( ! wc_newsletter_subscription_provider_has_list() ) {
			return $fields;
		}

		$value = ( 'checked' === get_option( 'woocommerce_newsletter_checkbox_status' ) ? 1 : 0 );
		$label = get_option( 'woocommerce_newsletter_label' );

		if ( ! $label ) {
			$label = _x( 'Subscribe to our newsletter', 'subscription checkbox label', 'woocommerce-subscribe-to-newsletter' );
		}

		$fields['newsletter']['subscribe_to_newsletter'] = array(
			'type'        => 'checkbox',
			'label'       => $label,
			'default'     => $value,
			'label_class' => array( 'woocommerce-form__label', 'woocommerce-form__label-for-checkbox' ),
			'input_class' => array( 'woocommerce-form__input', 'woocommerce-form__input-checkbox' ),
		);

		return $fields;
	}

	/**
	 * Outputs the newsletter subscription fields.
	 *
	 * @since 2.9.0
	 *
	 * @param WC_Checkout $checkout Optional. Checkout instance.
	 */
	public function checkout_content( $checkout = null ) {
		if ( ! $checkout ) {
			$checkout = WC()->checkout();
		}

		wc_newsletter_subscription_get_template( 'checkout/newsletter.php', array( 'checkout' => $checkout ) );
	}

	/**
	 * Processes the checkout fields.
	 *
	 * @since 2.9.0
	 *
	 * @param int      $order_id    The order Id.
	 * @param array    $posted_data The posted data.
	 * @param WC_Order $order       Order object.
	 */
	public function process_checkout_order( $order_id, $posted_data, $order ) {
		if ( ! $posted_data['subscribe_to_newsletter'] ) {
			return;
		}

		$order = wc_get_order( $order_id );

		// Order not found.
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Adds order meta for future subscribe when order status changed.
		$order->add_meta_data( '_newsletter_subscription', 1 );
		$order->save_meta_data();
	}
}

return new WC_Newsletter_Subscription_Checkout();
