<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Account Funds Increase Email
 *
 * An email sent to the customer when account funds are manually increased.
 */
class WC_Account_Funds_Email_Account_Funds_Increase extends WC_Email {

	/**
	 * User email.
	 *
	 * @var string
	 */
	public $user_email;

	/**
	 * User's current funds.
	 *
	 * @var string
	 */
	public $current_funds;

	/**
	 * User's new funds.
	 *
	 * @var string
	 */
	public $new_funds;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id             = 'wc_account_funds_increase';
		$this->customer_email = true;
		$this->title          = __( 'Account Funds Increase', 'woocommerce-account-funds' );
		$this->description    = __( 'This email is sent to the customer when account funds are manually increased.', 'woocommerce-account-funds' );

		$this->heading        = __( 'Your Account Funds Have Increased', 'woocommerce-account-funds' );
		$this->subject        = __( 'Account Funds Increase', 'woocommerce-account-funds' );

		$this->template_base  = plugin_dir_path( WC_ACCOUNT_FUNDS_FILE ) . 'templates/';
		$this->template_html  = 'emails/customer-account-funds-increase.php';
		$this->template_plain = 'emails/plain/customer-account-funds-increase.php';

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 *
	 * @param int $user_id
	 * @param string $current_funds
	 * @param string $new_funds
	 */
	public function trigger( $user_id, $current_funds = '', $new_funds = '' ) {
		if ( $user_id ) {
			$this->object        = new WP_User( $user_id );
			$this->user_email    = stripslashes( $this->object->user_email );
			$this->recipient     = $this->user_email;
			$this->current_funds = $current_funds;
			$this->new_funds     = $new_funds;
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html,
			array(
				'email_heading' => $this->get_heading(),
				'current_funds' => $this->current_funds,
				'new_funds'     => $this->new_funds,
				'home_url'      => home_url(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this
			), '', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain,
			array(
				'email_heading' => $this->get_heading(),
				'current_funds' => $this->current_funds,
				'new_funds'     => $this->new_funds,
				'home_url'      => home_url(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this
			), '', $this->template_base );
		return ob_get_clean();
	}
}
