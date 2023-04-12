<?php
/**
 * Email: Account funds increase.
 *
 * An email sent to the customer when account funds are manually increased.
 *
 * @package WC_Account_Funds/Emails
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Email_Account_Funds_Increase.
 */
class WC_Account_Funds_Email_Account_Funds_Increase extends WC_Email {

	/**
	 * User email.
	 *
	 * @var string
	 */
	public $user_email;

	/**
	 * User's previous funds.
	 *
	 * @var float
	 */
	public $previous_funds;

	/**
	 * User's new funds.
	 *
	 * @var float
	 */
	public $new_funds;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id             = 'wc_account_funds_increase';
		$this->customer_email = true;
		$this->template_base  = WC_ACCOUNT_FUNDS_PATH . 'templates/';
		$this->template_html  = 'emails/customer-account-funds-increase.php';
		$this->template_plain = 'emails/plain/customer-account-funds-increase.php';
		$this->placeholders   = array(
			'{funds_amount}' => '',
		);

		$funds_name = wc_get_account_funds_name();

		/* translators: %s: funds name */
		$this->title = sprintf( _x( '%s increase', 'email title', 'woocommerce-account-funds' ), $funds_name );

		$this->description = sprintf(
			/* translators: %s: funds name */
			_x( 'This email is sent to the customer when the %s amount is manually increased.', 'email description', 'woocommerce-account-funds' ),
			$funds_name
		);

		// Triggers.
		add_action( 'wc_account_funds_customer_funds_increased_notification', array( $this, 'trigger' ), 10, 3 );

		parent::__construct();
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( 'current_funds' === $key ) {
			_doing_it_wrong( 'WC_Account_Funds_Email_Account_Funds_Increase->current_funds', 'This property is deprecated and will be removed in future versions.', '2.8.0' );
			return $this->previous_funds;
		}
	}

	/**
	 * Gets the default email subject.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_default_subject() {
		return $this->get_title();
	}

	/**
	 * Gets the default email heading.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_default_heading() {
		return sprintf(
			/* translators: %s: funds name */
			_x( '%s increased', 'email heading', 'woocommerce-account-funds' ),
			wc_get_account_funds_name()
		);
	}

	/**
	 * Gets the default email message.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_default_message() {
		return sprintf(
			/* translators: %s: funds name */
			_x( 'Your %s amount has increased to:', 'email text', 'woocommerce-account-funds' ),
			wc_get_account_funds_name()
		);
	}

	/**
	 * Gets the email message.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_message() {
		return $this->format_string( $this->get_option( 'message', $this->get_default_message() ) );
	}

	/**
	 * Initializes form fields.
	 *
	 * @since 2.8.0
	 */
	public function init_form_fields() {
		parent::init_form_fields();

		$additional_fields = array(
			'message' => array(
				'title'       => _x( 'Message', 'email field title', 'woocommerce-account-funds' ),
				'type'        => 'textarea',
				'desc_tip'    => true,
				'description' => $this->get_placeholder_text(),
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => $this->get_default_message(),
				'default'     => '',
			),
		);

		// Add the new fields after the 'Email heading' field.
		$position = 1 + array_search( 'heading', array_keys( $this->form_fields ), true );

		$this->form_fields = array_merge(
			array_slice( $this->form_fields, 0, $position ),
			$additional_fields,
			array_slice( $this->form_fields, $position )
		);
	}

	/**
	 * Triggers the sending of this email.
	 *
	 * @since 2.0.0
	 *
	 * @param int   $user_id        User ID.
	 * @param float $previous_funds The previous funds amount.
	 * @param float $new_funds      The new funds amount.
	 */
	public function trigger( $user_id, $previous_funds = 0, $new_funds = 0 ) {
		$this->setup_locale();

		if ( $user_id ) {
			$this->object     = new WP_User( $user_id );
			$this->user_email = stripslashes( $this->object->user_email );
			$this->recipient  = $this->user_email;
		}

		$this->previous_funds = $previous_funds;
		$this->new_funds      = $new_funds;

		$this->placeholders['{funds_amount}'] = wp_strip_all_tags( wc_price( $this->new_funds ) );

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Gets content HTML.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_content_html() {
		return $this->get_content_template();
	}

	/**
	 * Gets content plain.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return $this->get_content_template( 'plain' );
	}

	/**
	 * Gets the template content.
	 *
	 * @since 2.8.0
	 *
	 * @param string $type Optional. The content type [html, plain].
	 * @return string
	 */
	protected function get_content_template( $type = 'html' ) {
		return wc_get_template_html(
			( 'plain' === $type ? $this->template_plain : $this->template_html ),
			$this->get_content_args( $type ),
			'',
			$this->template_base
		);
	}

	/**
	 * Gets the content arguments.
	 *
	 * @since 2.8.0
	 *
	 * @param string $type Optional. The content type [html, plain].
	 * @return array
	 */
	protected function get_content_args( $type = 'html' ) {
		return array(
			'email_heading'      => $this->get_heading(),
			'message'            => $this->get_message(),
			'additional_content' => $this->get_additional_content(),
			'sent_to_admin'      => false,
			'plain_text'         => ( 'plain' === $type ),
			'email'              => $this,
			'funds_amount'       => $this->new_funds,
			'new_funds'          => $this->new_funds, // Backward compatibility.
			'current_funds'      => $this->previous_funds, // Backward compatibility.
			'home_url'           => home_url(), // Backward compatibility.
		);
	}

	/**
	 * Gets the placeholder text.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	protected function get_placeholder_text() {
		return wc_account_funds_get_placeholder_text( array_keys( $this->placeholders ) );
	}
}

return new WC_Account_Funds_Email_Account_Funds_Increase();
