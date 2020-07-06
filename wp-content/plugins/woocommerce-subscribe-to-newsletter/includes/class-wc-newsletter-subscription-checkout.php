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
	 *
	 * @return string
	 */
	protected function get_content_location() {
		/**
		 * Filters the location of the checkout content.
		 *
		 * @since 2.9.0
		 *
		 * @param string $location Checkout content location.
		 */
		return apply_filters( 'wc_newsletter_subscription_checkout_content_location', 'after_terms' );
	}

	/**
	 * Init.
	 *
	 * @since 2.9.0
	 */
	public function init() {
		$location = $this->get_content_location();

		if ( 'after_billing' === $location ) {
			add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'checkout_content' ) );
		} else {
			add_action( 'woocommerce_review_order_before_submit', array( $this, 'checkout_content' ) );
		}
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
			return; // They don't want to subscribe.
		}

		if ( ! wc_newsletter_subscription_provider_has_list() ) {
			return;
		}

		wc_newsletter_subscription_subscribe(
			$order->get_billing_email(),
			array(
				'first_name' => $order->get_billing_first_name(),
				'last_name'  => $order->get_billing_last_name(),
			)
		);

		if ( is_user_logged_in() ) {
			update_user_meta( get_current_user_id(), '_wc_subscribed_to_newsletter', 1 );
		}
	}
}

return new WC_Newsletter_Subscription_Checkout();
