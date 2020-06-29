<?php

/**
 * FUE_Twitter_Frontend class
 */
class FUE_Addon_Twitter_Frontend {

	/**
	 * @var FUE_Addon_Twitter
	 */
	private $fue_twitter;

	/**
	 * @param FUE_Addon_Twitter $fue_twitter
	 */
	public function __construct( FUE_Addon_Twitter $fue_twitter ) {
		$this->fue_twitter = $fue_twitter;

		$this->register_hooks();
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// checkout form
		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'checkout_form_fields' ) );

		// account form
		add_action( 'woocommerce_after_my_account', array( $this, 'account_display_handle' ) );
		add_action( 'woocommerce_edit_account_form', array( $this, 'edit_account_form' ) );

		// store the twitter handle
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'store_twitter_handle' ) );
		add_action( 'woocommerce_save_account_details', array( $this, 'update_account' ) );
	}

	/**
	 * Render the checkout fields
	 */
	public function checkout_form_fields() {
		if ( $this->fue_twitter->settings['checkout_fields'] ) {
			fue_get_template( '/add-ons/twitter/checkout-form.php', array(), 'follow-up-emails', FUE_TEMPLATES_DIR );
		}
	}

	public function account_display_handle() {
		if ( $this->fue_twitter->settings['account_fields'] ) {
			fue_get_template( '/add-ons/twitter/account-display-handle.php', array(), 'follow-up-emails', FUE_TEMPLATES_DIR );
		}
	}

	public function edit_account_form() {
		if ( $this->fue_twitter->settings['account_fields'] ) {
			fue_get_template( '/add-ons/twitter/account-form.php', array(), 'follow-up-emails', FUE_TEMPLATES_DIR );
		}
	}

	/**
	 * Store the passed twitter handle from the checkout form
	 *
	 * @param int $order_id
	 */
	public function store_twitter_handle( $order_id ) {
		if ( !$this->fue_twitter->settings['checkout_fields'] ) {
			return;
		}

		if ( !empty( $_POST['twitter_handle'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			$handle = ltrim( sanitize_text_field( wp_unslash( $_POST['twitter_handle'] ) ), '@' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			update_post_meta( $order_id, '_twitter_handle', $handle );
		}
	}

	/**
	 * Update the twitter handle from the edit account form
	 * @param int $user_id
	 */
	public function update_account( $user_id ) {
		if ( !$this->fue_twitter->settings['account_fields'] ) {
			return;
		}

		if ( !empty( $_POST['twitter_handle'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			$handle = ltrim( sanitize_text_field( wp_unslash( $_POST['twitter_handle'] ) ), '@' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			update_user_meta( $user_id, 'twitter_handle', $handle );
		}
	}

}
