<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

/**
 * Class Identity represents a SES identity (email or domain).
 *
 * @since 2.4.0
 */
class Identity {

	/**
	 * The email identity type.
	 *
	 * @since 2.4.0
	 */
	const EMAIL_TYPE = 'email';

	/**
	 * The domain identity type.
	 *
	 * @since 2.4.0
	 */
	const DOMAIN_TYPE = 'domain';

	/**
	 * The value of the identity (email address or domain name)
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	protected $value = '';

	/**
	 * The type of identity.
	 *
	 * Defaults to self::DOMAIN_TYPE.
	 *
	 * @since 2.4.0
	 *
	 * @var string (self::DOMAIN_TYPE | self::EMAIL_TYPE)
	 */
	protected $type = self::DOMAIN_TYPE;

	/**
	 * The status of the identity.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * The DNS TXT token.
	 *
	 * Used by a domain type identity only.
	 *
	 * @since 2.4.0
	 *
	 * @var string|null
	 */
	protected $txt_token = null;

	/**
	 * Create an object.
	 *
	 * @since 2.4.0
	 *
	 * @param string      $value     The identity value (email address or domain name).
	 * @param string      $type      The identity type ("email" or "domain").
	 * @param string      $status    The identity status.
	 * @param string|null $txt_token The domain DNS TXT record token.
	 */
	public function __construct( $value, $type, $status, $txt_token = null ) {

		$this->value     = $value;
		$this->type      = $type;
		$this->status    = $status;
		$this->txt_token = $txt_token;
	}

	/**
	 * Get the identity value (email address or domain name).
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_value() {

		return $this->value;
	}

	/**
	 * Get the identity type (email or domain).
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->type;
	}

	/**
	 * Get the identity status.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_status() {

		return $this->status;
	}

	/**
	 * Get the domain identity DNS txt token.
	 *
	 * @since 2.4.0
	 *
	 * @return string|null
	 */
	public function get_txt_token() {

		if ( $this->type !== self::DOMAIN_TYPE ) {
			return null;
		}

		return $this->txt_token;
	}

	/**
	 * Check if this identity is a domain.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_domain() {

		return $this->get_type() === self::DOMAIN_TYPE;
	}

	/**
	 * Check if this identity is a email.
	 *
	 * @since 2.4.0
	 *
	 * @return bool
	 */
	public function is_email() {

		return $this->get_type() === self::EMAIL_TYPE;
	}

	/**
	 * Get the identity action links.
	 *
	 * HTML output.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public function get_action_links() {

		$delete_link_title = $this->is_domain() ?
			esc_html__( 'Delete this domain', 'wp-mail-smtp-pro' ) :
			esc_html__( 'Delete this email', 'wp-mail-smtp-pro' );

		ob_start();
		?>

		<?php if ( $this->is_domain() && $this->get_status() === 'Pending' ) : ?>
			<a href="#" title="<?php esc_attr_e( 'View DNS', 'wp-mail-smtp-pro' ); ?>"
			   data-txt-record="<?php echo esc_attr( $this->get_txt_token() ); ?>"
			   data-domain="<?php echo esc_attr( $this->get_value() ); ?>"
			   class="js-wp-mail-smtp-providers-<?php echo esc_attr( Options::SLUG ); ?>-domain-dns-record">
				<?php esc_html_e( 'View DNS', 'wp-mail-smtp-pro' ); ?>
			</a> |
		<?php elseif ( $this->is_email() && $this->get_status() === 'Pending' ) : ?>
			<a href="#" title="<?php esc_attr_e( 'Resend a verification email to this address', 'wp-mail-smtp-pro' ); ?>"
			   data-email="<?php echo esc_attr( $this->get_value() ); ?>"
			   data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_mail_smtp_pro_' . Options::SLUG . '_register_identity' ) ); ?>"
			   class="js-wp-mail-smtp-providers-<?php echo esc_attr( Options::SLUG ); ?>-email-resend">
				<?php esc_html_e( 'Resend', 'wp-mail-smtp-pro' ); ?>
			</a> |
		<?php endif; ?>

		<a href="#" title="<?php echo esc_attr( $delete_link_title ); ?>"
		   data-identity="<?php echo esc_attr( $this->get_value() ); ?>"
		   data-type="<?php echo esc_attr( $this->get_type() ); ?>"
		   data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_mail_smtp_pro_' . Options::SLUG . '_identity_delete' ) ); ?>"
		   class="js-wp-mail-smtp-providers-<?php echo esc_attr( Options::SLUG ); ?>-identity-delete">
			<?php esc_html_e( 'Delete', 'wp-mail-smtp-pro' ); ?>
		</a>

		<?php
		return ob_get_clean();
	}

	/**
	 * Get all Indetity data.
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_all() {

		return [
			'value'     => $this->get_value(),
			'type'      => $this->get_type(),
			'status'    => $this->get_status(),
			'txt_token' => $this->get_txt_token(),
			'actions'   => [
				'view_dns' => $this->is_domain() && $this->get_status() === 'Pending',
				'resend'   => $this->is_email() && $this->get_status() === 'Pending',
				'delete'   => true,
			],
		];
	}
}
