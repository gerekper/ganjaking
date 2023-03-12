<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://woocommerce.com/
 * @since      1.0.0
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/emails
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Referral program share emails.
 *
 * @package    Coupon_Referral_Program
 * @subpackage Coupon_Referral_Program/emails
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Coupon_Referral_Share_Via_Email' ) ) {
	/**
	 * This is referral share email class.
	 */
	class Coupon_Referral_Program_Share_Via_Emails extends WC_Email {
		/**
		 *  Referral Link.
		 *
		 * @var string $refferal_link
		 */
		public $refferal_link;
		/**
		 *  User Name.
		 *
		 * @var string $user_name
		 */
		public $user_name;
		/**
		 *  Coupon Amount.
		 *
		 * @var string $coupon_amount
		 */
		public $coupon_amount = 0;
			/** Constructor */
		public function __construct() {
			$this->id             = 'crp_share_via_email';
			$this->title          = __( 'Referral Link', 'coupon-referral-program' );
			$this->customer_email = true;
			$this->description    = __( 'This is the referral link which is used to register on the site and get the discount coupon.', 'coupon-referral-program' );
			$this->template_html  = 'crp-share-via-email-template.php';
			$this->template_plain = 'plain/crp-share-via-email-template.php';
			$this->template_base  = COUPON_REFERRAL_PROGRAM_DIR_PATH . 'emails/templates/';
			$this->placeholders   = array(
				'{site_title}'    => $this->get_blogname(),
				'{refferal_link}' => '',
				'{user_name}'     => '',
				'{refferal_code}' => '',
			);

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
			return __( 'Refferal Link {site_title}', 'coupon-referral-program' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Congratulation! You have received the Referral Link', 'coupon-referral-program' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int   $user_id .
		 * @param mixed $refferal_link .
		 * @param mixed $recipient_email .
		 * @param mixed $refferal_code .
		 */
		public function trigger( $user_id, $refferal_link, $recipient_email, $refferal_code ) {
			if ( $user_id ) {
				$this->setup_locale();
				$user = new WP_User( $user_id );
				if ( is_a( $user, 'WP_User' ) ) {
					$this->object                          = $user;
					$this->refferal_link                   = $refferal_link;
					$this->refferal_code                   = $refferal_code;
					$this->user_name                       = $user->display_name;
					$this->recipient                       = $recipient_email;
					$this->placeholders['{refferal_link}'] = $refferal_link;
					$this->placeholders['{user_name}']     = $user->display_name;
					$this->placeholders['{refferal_code}'] = $refferal_code;
					if ( $this->is_enabled() && $this->get_recipient() ) {
						$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
					}
				}
				$this->restore_locale();
			}

		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'user'               => $this->object,
					'refferal_link'      => $this->refferal_link,
					'user_name'          => $this->user_name,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => false,
					'plain_text'         => false,
					'email'              => $this,
					'additional_content' => $this->get_additional_content(),
					'refferal_code'      => $this->refferal_code,
				),
				'',
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
				array(
					'user'               => $this->object,
					'refferal_link'      => $this->refferal_link,
					'user_name'          => $this->user_name,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => false,
					'plain_text'         => true,
					'email'              => $this,
					'additional_content' => $this->get_additional_content(),
					'refferal_code'      => $this->refferal_code,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'coupon-referral-program' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'coupon-referral-program' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'coupon-referral-program' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'coupon-referral-program' ), '<code>{site_title}, {refferal_link}, {refferal_code}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'coupon-referral-program' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'coupon-referral-program' ), '<code>{site_title}, {refferal_link}, {refferal_code}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'additional_content' => array(
					'title'       => esc_html__( 'Custom Email', 'coupon-referral-program' ),
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'If N/A then default email will send. Available placeholders: %s', 'coupon-referral-program' ), '<code>{site_title}, {refferal_link}, {user_name}, {refferal_code}</code>' ),
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => esc_html__( 'N/A', 'coupon-referral-program' ),
					'type'        => 'textarea',
					'default'     => $this->get_default_additional_content(),
					'desc_tip'    => true,
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'coupon-referral-program' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'coupon-referral-program' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}

	}

}

return new Coupon_Referral_Program_Share_Via_Emails();
