<?php
/**
 * Expiring card reminder email
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCSTRIPE' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCStripe_Expiring_Card_Email' ) ) {

	/**
	 * Expiring card reminder email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Expiring_Card_Email extends WC_Email {

		/**
		 * @var $user_id int Receiver id
		 */
		public $user_id = false;

		/**
		 * @var \WC_Payment_Token_CC Expiring card
		 */
		public $token = null;

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCStripe_Expiring_Card_Email
		 * @since 1.8.1
		 */
		public function __construct() {
			$this->id             = 'expiring_card';
			$this->title          = 'YITH WooCommerce Stripe - ' . __( 'Customer\'s expiring card reminder', 'yith-woocommerce-stripe' );
			$this->description    = __( 'This email is sent to customers that have at least one expiring card in the related period', 'yith-woocommerce-stripe' );
			$this->customer_email = true;

			$this->heading = __( 'Update your card information', 'yith-woocommerce-stripe' );
			$this->subject = __( 'Update your card information', 'yith-woocommerce-stripe' );

			$this->days_before_expiration = $this->get_option( 'days_before_expiration' );
			$this->subscribed_only        = defined( 'YITH_YWSBS_VERSION' ) ? 'yes' == $this->get_option( 'days_before_expiration' ) : false;
			$this->exclusions             = explode( ',', $this->get_option( 'exclusions' ) );

			$this->template_html  = 'emails/expiring-card-email.php';
			$this->template_plain = 'emails/plain/expiring-card-email.php';

			// Triggers for this email
			add_action( 'yith_wcstripe_expiring_card_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Trigger email sending
		 *
		 * @param $user_id        int Id of the owner of expiring card
		 * @param $expiring_token \WC_Payment_Token_CC Expiring card
		 *
		 * @return void
		 */
		public function trigger( $user_id, $expiring_token ) {
			$this->user_id = $user_id;
			$this->token   = $expiring_token;

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get valid recipients.
		 *
		 * @return string
		 */
		public function get_recipient() {
			$user = get_user_by( 'id', $this->user_id );

			if ( ! $user ) {
				return false;
			}

			$this->recipient = $user->user_email;

			return parent::get_recipient();
		}

		/**
		 * Get the email content in HTML format.
		 *
		 * @return string
		 */
		public function get_content_html() {
			$expiration_date      = date( 'Y-m-t', strtotime( "{$this->token->get_expiry_year()}-{$this->token->get_expiry_month()}-01" ) );
			$expiration_timestamp = strtotime( $expiration_date );
			$userdata             = get_userdata( $this->user_id );
			$bg_color             = get_option( 'woocommerce_email_base_color' );

			ob_start();
			yith_wcstripe_get_template( $this->template_html, array(
				'username'             => $userdata->first_name ? $userdata->first_name : $userdata->display_name,
				'card_type'            => ucfirst( $this->token->get_card_type() ),
				'update_card_url'      => apply_filters( 'yith_wcstripe_update_card_url', wc_get_endpoint_url( 'payment-methods', '', wc_get_page_permalink( 'myaccount' ) ) ),
				'update_card_bg'       => $bg_color,
				'update_card_fg'       => wc_light_or_dark( $bg_color, '#202020', '#ffffff' ),
				'last4'                => $this->token->get_last4(),
				'already_expired'      => $expiration_timestamp < time(),
				'expiration_timestamp' => $expiration_timestamp,
				'expiration_date'      => date_i18n( wc_date_format(), $expiration_timestamp ),
				'site_title'           => get_option( 'blogname' ),
				'email_heading'        => $this->get_heading(),
				'sent_to_admin'        => false,
				'plain_text'           => false
			) );

			return $this->format_string( ob_get_clean() );
		}

		/**
		 * Get the email content in plain text format.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			$expiration_date      = date( 'Y-m-t', strtotime( "{$this->token->get_expiry_year()} {$this->token->get_expiry_month()} 01" ) );
			$expiration_timestamp = strtotime( $expiration_date );
			$userdata             = get_userdata( $this->user_id );

			ob_start();
			yith_wcstripe_get_template( $this->template_plain, array(
				'username'             => $userdata->first_name ? $userdata->first_name : $userdata->display_name,
				'card_type'            => $this->token->get_card_type(),
				'update_card_url'      => apply_filters( 'yith_wcstripe_update_card_url', wc_get_endpoint_url( 'payment-methods', '', wc_get_page_permalink( 'myaccount' ) ) ),
				'last4'                => $this->token->get_last4(),
				'already_expired'      => $expiration_timestamp < time(),
				'expiration_timestamp' => $expiration_timestamp,
				'expiration_date'      => date_i18n( wc_date_format(), strtotime( $expiration_date ) ),
				'site_title'           => get_option( 'blogname' ),
				'email_heading'        => $this->get_heading(),
				'sent_to_admin'        => false,
				'plain_text'           => true
			) );

			return $this->format_string( ob_get_clean() );
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since 1.8.1
		 */
		public function init_form_fields() {
			$this->form_fields = array_merge(
				array(
					'enabled'                  => array(
						'title'   => __( 'Enable/Disable', 'woocommerce' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable this email notification', 'woocommerce' ),
						'default' => 'no',
					),
					'subject'                  => array(
						'title'       => __( 'Subject', 'woocommerce' ),
						'type'        => 'text',
						'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
						'placeholder' => '',
						'default'     => ''
					),
					'heading'                  => array(
						'title'       => __( 'Email Heading', 'woocommerce' ),
						'type'        => 'text',
						'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
						'placeholder' => '',
						'default'     => ''
					),
					'months_before_expiration' => array(
						'title'       => __( 'Months before expiration', 'yith-woocommerce-stripe' ),
						'type'        => 'number',
						'description' => __( 'This controls how many months before expiration reminder should be sent.', 'yith-woocommerce-stripe' ),
						'placeholder' => '',
						'default'     => 1
					),
				),
				defined( 'YITH_YWSBS_VERSION' ) ? array(
					'subscribed_only' => array(
						'title'   => __( 'Subscribed users only', 'yith-woocommerce-stripe' ),
						'type'    => 'checkbox',
						'label'   => __( 'Send this notification only to customers that have at least one active subscription', 'yith-woocommerce-stripe' ),
						'default' => 'no',
					),
				) : array(),
				array(
					'exclusions' => array(
						'title'       => __( 'Exclusions', 'woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'A comma separated list of email address that should not receive this notification', 'yith-woocommerce-stripe' ),
						'placeholder' => '',
						'default'     => ''
					),
					'email_type' => array(
						'title'       => __( 'Email type', 'woocommerce' ),
						'type'        => 'select',
						'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
						'default'     => 'html',
						'class'       => 'email_type wc-enhanced-select',
						'options'     => $this->get_email_type_options()
					)
				)
			);
		}
	}
}

return new YITH_WCStripe_Expiring_Card_Email();