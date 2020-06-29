<?php
/**
 * Renew needs action email
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

if ( ! class_exists( 'YITH_WCStripe_Renew_Needs_Action_Email' ) ) {

	/**
	 * Renew needs action email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCStripe_Renew_Needs_Action_Email extends WC_Email {

		/**
		 * @var $order_id \WC_Order Failed renew order
		 */
		public $order;

		/**
		 * @var $token \WC_Payment_Token_CC Token that will be used as default for renew
		 */
		public $token;

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCStripe_Renew_Needs_Action_Email
		 * @since 1.10.0
		 */
		public function __construct() {
			$this->id             = 'renew_needs_action';
			$this->title          = 'YITH WooCommerce Stripe - ' . __( 'Payment confirmation email', 'yith-woocommerce-stripe' );
			$this->description    = __( 'This email is sent to customers that have an off session payment pending confirmation', 'yith-woocommerce-stripe' );
			$this->customer_email = true;

			$this->heading = __( 'Confirm your {order_total} payment', 'yith-woocommerce-stripe' );
			$this->subject = __( 'Confirm your {order_total} payment', 'yith-woocommerce-stripe' );

			$this->heading_html  = $this->get_option( 'heading_html', "
<p>
	Please, confirm your payment to <a href='{site_url}'>{site_title}</a>. Your bank requires this security measure for your {card_type} card ending in {card_last4}
</p>" );
			$this->heading_plain = $this->get_option( 'heading_plain', 'Please, confirm your payment to {site_title}. Your bank requires this security measure for your {card_type} card ending in {card_last4}' );
			$this->footer_html   = $this->get_option( 'footer_html', "
<h3>Why to you need to confirm this payment?</h3>
<p>
	Your bank sometimes requires an additional step to make sure an online transaction was authorized. 
	Your bank uses 3D Secure to set an higher security standard and protect you from fraud.
</p>
<p>
	Because of European regulation to protect consumers, many online payments now require two-factor authentication.
	You bank ultimately decides when authentication is required to confirm a payment, but you may notice this step when
	you start paying a service or when the cost changes.
</p>
<p>
	<small>If you have any question, contact us at <a href='mailto:{contact_email}'>{contact_email}</a></small>
</p>" );
			$this->footer_plain  = $this->get_option( 'footer_plain', "
=== Why to you need to confirm this payment? ===\n\n
Your bank sometimes requires an additional step to make sure an online transaction was authorized. 
Your bank uses 3D Secure to set an higher security standard and protect you from fraud.\n\n
Because of European regulation to protect consumers, many online payments now require two-factor authentication.
You bank ultimately decides when authentication is required to confirm a payment, but you may notice this step when
you start paying a service or when the cost changes.\n\n
If you have any question, contact us at {contact_email}\n\n" );

			$this->template_html  = 'emails/renew-needs-action-email.php';
			$this->template_plain = 'emails/plain/renew-needs-action-email.php';

			// Triggers for this email
			add_action( 'yith_wcstripe_renew_intent_requires_action_notification', array( $this, 'trigger' ), 10, 1 );

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
		public function trigger( $order_id ) {
			$this->order = wc_get_order( $order_id );

			if ( $this->order && $user_id = $this->order->get_user_id() ) {
				$found  = false;
				$tokens = WC_Payment_Tokens::get_tokens( array(
					'user_id'    => $user_id,
					'gateway_id' => YITH_WCStripe::$gateway_id
				) );

				if ( ! empty( $tokens ) ) {
					foreach ( $tokens as $token ) {
						if ( $token->is_default() ) {
							$found = true;
							break;
						}
					}
				}

				if ( $found ) {
					$this->token = $token;
				}
			}

			if ( ! $this->is_enabled() || ! $this->get_recipient() || ! $this->order || ! $this->token ) {
				return;
			}

			$content = $this->get_content();

			$this->send( $this->get_recipient(), $this->get_subject(), $content, $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Checks if this email is enabled and will be sent.
		 *
		 * @return bool
		 */
		public function is_enabled() {
			return parent::is_enabled();
		}

		/**
		 * Get valid recipients.
		 *
		 * @return string
		 */
		public function get_recipient() {
			$this->recipient = $this->order ? $this->order->get_billing_email() : false;

			return parent::get_recipient();
		}

		/**
		 * Get the email content in HTML format.
		 *
		 * @return string
		 */
		public function get_content_html() {
			$order    = $this->order;
			$userdata = $order->get_user();
			$username = $userdata->first_name ? $userdata->first_name : $userdata->display_name;
			$bg_color = get_option( 'woocommerce_email_base_color' );
			$pay_url  = $this->order->needs_payment() ? $this->order->get_checkout_payment_url() : $this->order->get_view_order_url();

			if ( apply_filters( 'yith_wcstripe_pay_renew_url_enabled', defined( 'YITH_YWSBS_VERSION' ) && version_compare( YITH_YWSBS_VERSION, '1.6.1', '>=' ) ) ) {
				$pay_url = apply_filters( 'yith_wcstripe_pay_renew_url', wp_nonce_url( $this->order->get_checkout_payment_url(), 'ywsbs_manual_renew', 'ywsbs_manual_renew' ) );
			}

			$this->placeholders = array_merge(
				$this->placeholders,
				array(
					'{site_url}'      => get_home_url(),
					'{username}'      => $username,
					'{card_type}'     => ucfirst( $this->token->get_card_type() ),
					'{pay_renew_url}' => $pay_url,
					'{card_last4}'    => $this->token->get_last4(),
					'{order_id}'      => $order->get_id(),
					'{order_total}'   => wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ),
					'{billing_email}' => $this->order->get_billing_email(),
					'{contact_email}' => apply_filters( 'yith_wcstripe_contact_email', get_option( 'woocommerce_email_from_address', '' ) ),
				)
			);

			$this->placeholders = array_merge(
				$this->placeholders,
				array(
					'{opening_text}' => $this->format_string( $this->heading_html ),
					'{closing_text}' => $this->format_string( $this->footer_html )
				)
			);

			ob_start();
			yith_wcstripe_get_template( $this->template_html, array(
				'order'         => $order,
				'username'      => $username,
				'pay_renew_url' => $pay_url,
				'pay_renew_bg'  => $bg_color,
				'pay_renew_fg'  => wc_light_or_dark( $bg_color, '#202020', '#ffffff' ),
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this
			) );

			return $this->format_string( ob_get_clean() );
		}

		/**
		 * Get the email content in plain text format.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			$order    = $this->order;
			$userdata = $order->get_user();
			$username = $userdata->first_name ? $userdata->first_name : $userdata->display_name;
			$pay_url  = $this->order->needs_payment() ? $this->order->get_checkout_payment_url() : $this->order->get_view_order_url();

			if ( apply_filters( 'yith_wcstripe_pay_renew_url_enabled', defined( 'YITH_YWSBS_VERSION' ) && version_compare( YITH_YWSBS_VERSION, '1.6.1', '>=' ) ) ) {
				$pay_url = apply_filters( 'yith_wcstripe_pay_renew_url', wp_nonce_url( $this->order->get_checkout_payment_url(), 'ywsbs_manual_renew', 'ywsbs_manual_renew' ) );
			}

			$this->placeholders = array_merge(
				$this->placeholders,
				array(
					'{site_url}'      => get_home_url(),
					'{username}'      => $username,
					'{card_type}'     => ucfirst( $this->token->get_card_type() ),
					'{pay_renew_url}' => $pay_url,
					'{card_last4}'    => $this->token->get_last4(),
					'{order_id}'      => $order->get_id(),
					'{order_total}'   => $order->get_total(),
					'{billing_email}' => wc_price( $order->get_billing_email(), array( 'currency' => $order->get_currency() ) ),
					'{contact_email}' => apply_filters( 'yith_wcstripe_contact_email', get_option( 'woocommerce_email_from_address', '' ) ),
				)
			);

			$this->placeholders = array_merge(
				$this->placeholders,
				array(
					'{opening_text}' => $this->format_string( $this->heading_plain ),
					'{closing_text}' => $this->format_string( $this->footer_plain )
				)
			);

			ob_start();
			yith_wcstripe_get_template( $this->template_plain, array(
				'order'         => $order,
				'username'      => $username,
				'pay_renew_url' => $pay_url,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this
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
					'enabled'       => array(
						'title'   => __( 'Enable/Disable', 'woocommerce' ),
						'type'    => 'checkbox',
						'label'   => __( 'Enable this email notification', 'woocommerce' ),
						'default' => 'no',
					),
					'subject'       => array(
						'title'       => __( 'Subject', 'woocommerce' ),
						'type'        => 'text',
						'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
						'placeholder' => '',
						'default'     => ''
					),
					'heading'       => array(
						'title'       => __( 'Email Heading', 'woocommerce' ),
						'type'        => 'text',
						'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
						'placeholder' => '',
						'default'     => ''
					),
					'heading_html'  => array(
						'title'       => __( 'Opening text HTML', 'woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'Enter the text that you want to show before CTA button. You can use the following placeholders: <code>{site_title}, {site_url}, {card_type}, {pay_renew_url}, {card_last4}, {order_id}, {order_total}, {billing_email}, {contact_email}, {opening_text}, {closing_text}</code>.', 'woocommerce' ),
						'placeholder' => '',
						'default'     => $this->heading_html
					),
					'heading_plain' => array(
						'title'       => __( 'Opening text plain', 'woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'Enter the text that you want to show before CTA button (plain text version). You can use the following placeholders: <code>{site_title}, {site_url}, {card_type}, {pay_renew_url}, {card_last4}, {order_id}, {order_total}, {billing_email}, {contact_email}, {opening_text}, {closing_text}</code>.', 'woocommerce' ),
						'placeholder' => '',
						'default'     => $this->heading_plain
					),
					'footer_html'   => array(
						'title'       => __( 'Closing text HTML', 'woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'Enter the text that you want to show after order summary table. You can use the following placeholders: <code>{site_title}, {site_url}, {card_type}, {pay_renew_url}, {card_last4}, {order_id}, {order_total}, {billing_email}, {contact_email}, {opening_text}, {closing_text}</code>.', 'woocommerce' ),
						'placeholder' => '',
						'default'     => $this->footer_html
					),
					'footer_plain'  => array(
						'title'       => __( 'Closing text plain', 'woocommerce' ),
						'type'        => 'textarea',
						'description' => __( 'Enter the text that you want to show after order summary table (plain text version). You can use the following placeholders: <code>{site_title}, {site_url}, {card_type}, {pay_renew_url}, {card_last4}, {order_id}, {order_total}, {billing_email}, {contact_email}, {opening_text}, {closing_text}</code>.', 'woocommerce' ),
						'placeholder' => '',
						'default'     => $this->footer_plain
					),
					'email_type'    => array(
						'title'       => __( 'Email type', 'woocommerce' ),
						'type'        => 'select',
						'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
						'default'     => 'html',
						'class'       => 'email_type wc-enhanced-select',
						'options'     => $this->get_email_type_options()
					),
				)
			);
		}
	}
}

return new YITH_WCStripe_Renew_Needs_Action_Email();