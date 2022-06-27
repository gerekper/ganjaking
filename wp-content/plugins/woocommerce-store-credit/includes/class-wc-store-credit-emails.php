<?php
/**
 * Class to handle the plugin emails.
 *
 * @package WC_Store_Credit/Classes
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Emails class.
 */
class WC_Store_Credit_Emails {

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );
		add_filter( 'woocommerce_email_styles', array( $this, 'email_styles' ), 10, 2 );
	}

	/**
	 * Registers custom emails actions.
	 *
	 * @since 3.0.0
	 *
	 * @param array $actions The email actions.
	 * @return array
	 */
	public function email_actions( $actions ) {
		$actions[] = 'wc_store_credit_send_credit_to_customer';

		return $actions;
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @since 3.0.0
	 *
	 * @param array $emails The email classes.
	 * @return array
	 */
	public function email_classes( $emails ) {
		$emails['WC_Store_Credit_Email_Send_Credit'] = include 'emails/class-wc-store-credit-email-send-credit.php';

		return $emails;
	}

	/**
	 * Adds styles to the emails.
	 *
	 * @since 3.7.0
	 * @since 4.0.1 The `$email` argument is optional.
	 *
	 * @param string   $css   The email styles.
	 * @param WC_Email $email Optional. Email object. Default null.
	 * @return string
	 */
	public function email_styles( $css, $email = null ) {
		// AutomateWoo re-declares the filter hook 'woocommerce_email_styles' but it doesn't include the $email argument.
		if ( ! $email instanceof WC_Email || 'wc_store_credit_send_credit' !== $email->id ) {
			return $css;
		}

		ob_start();
		wc_store_credit_get_template( 'emails/store-credit-styles.php' );
		$styles = ob_get_clean();

		/**
		 * Filters the styles for the Store Credit emails.
		 *
		 * @since 3.7.0
		 *
		 * @param string   $styles The email styles.
		 * @param WC_Email $email  Email object.
		 */
		$css .= apply_filters( 'wc_store_credit_email_styles', $styles, $email );

		return $css;
	}
}

return new WC_Store_Credit_Emails();
