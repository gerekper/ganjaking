<?php
/**
 * Class YITH_WELRP_Customer_Authentication_Code file.
 *
 * @package WooCommerce\Emails
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WELRP_Customer_Authentication_Code', false ) ) :

	/**
	 * Customer Reset Password.
	 *
	 * An email sent to the customer when they reset their password.
	 *
	 * @class       YITH_WELRP_Customer_Authentication_Code
	 * @version     3.5.0
	 * @package     WooCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class YITH_WELRP_Customer_Authentication_Code extends WC_Email {

		/**
		 * User ID.
		 *
		 * @var integer
		 */
		public $user_id;

		/**
		 * User login name.
		 *
		 * @var string
		 */
		public $user_login;

		/**
		 * User email.
		 *
		 * @var string
		 */
		public $user_email;

		/**
		 * Reset key.
		 *
		 * @var string
		 */
		public $authentication_code;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id             = 'yith_welrp_customer_authentication_code';
			$this->customer_email = true;

			$this->title       = __( 'Reset password authentication code', 'yith-easy-login-register-popup-for-woocommerce' );
			$this->description = __( 'Customer "reset password authentication code" emails are sent when customers lose their passwords and ask to set a new one.', 'yith-easy-login-register-popup-for-woocommerce' );

            $this->template_base  = YITH_WELRP_TEMPLATE_PATH . '/emails/';
			$this->template_html  = 'customer-authentication-code.php';
			$this->template_plain = 'plain/customer-authentication-code.php';

			// Trigger.
			add_action( 'send_yith_welrp_customer_authentication_code_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Password reset request for {site_title}', 'woocommerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Password reset request', 'woocommerce' );
		}

		/**
		 * Trigger.
		 *
		 * @param WP_User $user.
		 * @param string $authentication_code Authentication code.
		 */
		public function trigger( $user, $authentication_code = '' ) {

			$this->setup_locale();

			if ( $user && $authentication_code ) {
				$this->object               = $user;
				$this->user_login           = $this->object->user_login;
				$this->authentication_code  = $authentication_code;
				$this->user_email           = stripslashes( $this->object->user_email );
				$this->recipient            = $this->user_email;
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				[
					'email_heading'         => $this->get_heading(),
					'user_login'            => $this->user_login,
					'authentication_code'   => $this->authentication_code,
					'blogname'              => $this->get_blogname(),
					'additional_content'    => $this->get_additional_content(),
					'sent_to_admin'         => false,
					'plain_text'            => false,
					'email'                 => $this,
				],
                WC()->template_path() . '/yith-welrp/emails',
                $this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				[
					'email_heading'         => $this->get_heading(),
					'user_login'            => $this->user_login,
                    'authentication_code'   => $this->authentication_code,
					'blogname'              => $this->get_blogname(),
					'additional_content'    => $this->get_additional_content(),
					'sent_to_admin'         => false,
					'plain_text'            => true,
					'email'                 => $this,
				],
                WC()->template_path() . '/yith-welrp/emails/plain',
                $this->template_base
			);
		}
	}

endif;

return new YITH_WELRP_Customer_Authentication_Code();
