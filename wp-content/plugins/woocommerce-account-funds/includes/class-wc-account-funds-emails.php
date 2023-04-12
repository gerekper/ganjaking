<?php
/**
 * Class to handle the plugin emails.
 *
 * @package WC_Account_Funds/Classes
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Account_Funds_Emails class.
 */
class WC_Account_Funds_Emails {

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_actions', array( $this, 'email_actions' ) );
		add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );
		add_filter( 'woocommerce_email_styles', array( $this, 'email_styles' ), 10, 2 );
	}

	/**
	 * Registers custom emails actions.
	 *
	 * @since 2.8.0
	 *
	 * @param array $actions The email actions.
	 * @return array
	 */
	public function email_actions( $actions ) {
		$actions[] = 'wc_account_funds_customer_funds_increased';

		return $actions;
	}

	/**
	 * Registers custom emails classes.
	 *
	 * @since 2.8.0
	 *
	 * @param array $emails The email classes.
	 * @return array
	 */
	public function email_classes( $emails ) {
		$emails['WC_Account_Funds_Email_Account_Funds_Increase'] = include 'emails/class-wc-account-funds-email-account-funds-increase.php';

		return $emails;
	}

	/**
	 * Adds styles to the emails.
	 *
	 * @since 2.8.0
	 *
	 * @param string   $css   The email styles.
	 * @param WC_Email $email Optional. Email object. Default null.
	 * @return string
	 */
	public function email_styles( $css, $email = null ) {
		// AutomateWoo re-declares the filter hook 'woocommerce_email_styles' but it doesn't include the $email argument.
		if ( ! $email instanceof WC_Email || false === strpos( $email->id, 'wc_account_funds_' ) ) {
			return $css;
		}

		ob_start();
		wc_account_funds_get_template( 'emails/account-funds-styles.php' );
		$styles = ob_get_clean();

		/**
		 * Filters the styles for the Account Funds emails.
		 *
		 * @since 2.8.0
		 *
		 * @param string   $styles The email styles.
		 * @param WC_Email $email  Email object.
		 */
		$css .= apply_filters( 'wc_account_funds_email_styles', $styles, $email );

		return $css;
	}
}

return new WC_Account_Funds_Emails();
