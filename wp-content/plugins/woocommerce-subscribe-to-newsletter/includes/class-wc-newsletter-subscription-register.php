<?php
/**
 * Register functionality
 *
 * This class handles the subscription process in the register form.
 *
 * @package WC_Newsletter_Subscription
 * @since   2.9.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_Register.
 */
class WC_Newsletter_Subscription_Register {

	/**
	 * Constructor.
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		add_action( 'woocommerce_register_form', array( $this, 'register_content' ) );
		add_action( 'woocommerce_created_customer', array( $this, 'process_register' ), 10, 2 );
	}

	/**
	 * Outputs the newsletter subscription fields.
	 *
	 * @since 2.9.0
	 */
	public function register_content() {
		if ( ! wc_newsletter_subscription_provider_has_list() ) {
			return;
		}

		$value = ( 'checked' === get_option( 'woocommerce_newsletter_checkbox_status' ) ? 1 : 0 );
		$label = get_option( 'woocommerce_newsletter_label' );

		if ( ! $label ) {
			$label = _x( 'Subscribe to our newsletter', 'subscription checkbox label', 'woocommerce-subscribe-to-newsletter' );
		}

		$fields = array(
			'subscribe_to_newsletter' => array(
				'type'        => 'checkbox',
				'label'       => $label,
				'default'     => $value,
				'class'       => array( 'woocommerce-form-row', 'woocommerce-form-row--wide' ),
				'label_class' => array( 'woocommerce-form__label', 'woocommerce-form__label-for-checkbox' ),
				'input_class' => array( 'woocommerce-form__input', 'woocommerce-form__input-checkbox' ),
			),
		);

		wc_newsletter_subscription_get_template( 'myaccount/newsletter-register.php', array( 'fields' => $fields ) );
	}

	/**
	 * Processes register form.
	 *
	 * @since 2.9.0
	 *
	 * @param int   $customer_id       Customer ID.
	 * @param array $new_customer_data Customer data.
	 */
	public function process_register( $customer_id, $new_customer_data ) {
		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || ! isset( $_REQUEST['subscribe_to_newsletter'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( ! wc_newsletter_subscription_provider_has_list() ) {
			return;
		}

		$subscriber = array(
			'first_name' => '',
			'last_name'  => '',
			'email'      => $new_customer_data['user_email'],
		);

		if ( version_compare( WC_VERSION, '3.0', '>=' ) ) {
			try {
				$customer   = new WC_Customer( $customer_id );
				$subscriber = wp_parse_args(
					array(
						'first_name' => $customer->get_first_name(),
						'last_name'  => $customer->get_last_name(),
					),
					$subscriber
				);
			} catch ( Exception $e ) {
				return;
			}
		}

		wc_newsletter_subscription_subscribe(
			$subscriber['email'],
			array(
				'first_name' => $subscriber['first_name'],
				'last_name'  => $subscriber['last_name'],
			)
		);
	}
}

return new WC_Newsletter_Subscription_Register();
