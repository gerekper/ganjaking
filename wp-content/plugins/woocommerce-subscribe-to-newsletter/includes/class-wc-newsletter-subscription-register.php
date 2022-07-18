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
		add_action( 'woocommerce_created_customer', array( $this, 'process_register' ), 10, 1 );
		add_filter( 'wp_new_user_notification_email_admin', array( $this, 'new_user_admin_email' ), 10, 2 );
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

		$fields = array(
			'subscribe_to_newsletter' => array(
				'type'        => 'checkbox',
				'label'       => wc_newsletter_subscription_get_checkbox_label(),
				'default'     => intval( 'checked' === get_option( 'woocommerce_newsletter_checkbox_status' ) ),
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
	 * @param int $customer_id Customer ID.
	 */
	public function process_register( $customer_id ) {
		if ( defined( 'WOOCOMMERCE_CHECKOUT' ) || ! isset( $_POST['subscribe_to_newsletter'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		if ( ! wc_newsletter_subscription_provider_has_list() ) {
			return;
		}

		try {
			$customer = new WC_Customer( $customer_id );
		} catch ( Exception $e ) {
			return;
		}

		$subscribed = wc_newsletter_subscription_subscribe(
			$customer->get_email(),
			array(
				'first_name' => $customer->get_first_name(),
				'last_name'  => $customer->get_last_name(),
			)
		);

		if ( $subscribed ) {
			update_user_meta( $customer_id, 'subscribed_on_register', 'yes' );
		}
	}

	/**
	 * Adds the new customer subscription value to the administrator's info mail.
	 *
	 * @since 3.3.5
	 *
	 * @param array   $email Used to build wp_mail().
	 * @param WP_User $user  User object for new user.
	 * @return array
	 */
	public function new_user_admin_email( $email, $user ) {
		$subscribed = get_user_meta( $user->ID, 'subscribed_on_register', true );
		$label      = wc_newsletter_subscription_get_checkbox_label();
		$value      = wc_newsletter_subscription_bool_to_string( 'yes' === $subscribed );

		$email['message'] .= "\r\n{$label}: {$value}\r\n";

		return $email;
	}
}

return new WC_Newsletter_Subscription_Register();
