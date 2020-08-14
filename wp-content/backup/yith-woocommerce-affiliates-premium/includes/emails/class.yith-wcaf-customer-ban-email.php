<?php
/**
 * Affiliate banned Email
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

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Customer_Ban_Email' ) ) {
	/**
	 * New affiliate email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Customer_Ban_Email extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCAF_Customer_Ban_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id             = 'affiliate_ban';
			$this->title          = __( 'Affiliate\'s ban message', 'yith-woocommerce-affiliates' );
			$this->description    = __( 'This email is sent to affiliates whenever their account is banned by an admin', 'yith-woocommerce-affiliates' );
			$this->customer_email = true;

			$this->heading = __( 'Your account was disabled', 'yith-woocommerce-affiliates' );
			$this->subject = __( 'Your {site_title} affiliate account was disabled', 'yith-woocommerce-affiliates' );

			$this->content_html = $this->get_option( 'content_html', '<p>your affiliate account on {site_title} has been disabled because we found out that your behaviour does not comply with our fair-play standards.</p>
<p>Please, have a look at our {tos_link} for further information, and don\'t hesitate to contact us if you have any doubt</p>' );
			$this->content_text = $this->get_option( 'content_text', 'your affiliate account on {site_title} has been disabled because we found out that your behaviour does not comply with our fair-play standards.
Please, have a look at our {tos_plain_link} for further information, and don\'t hesitate to contact us if you have any doubt' );

			$this->template_html  = 'emails/customer-ban.php';
			$this->template_plain = 'emails/plain/customer-ban.php';

			// Triggers for this email
			add_action( 'yith_wcaf_affiliate_banned_notification', array( $this, 'trigger' ), 10, 1 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Method triggered to send email
		 *
		 * @param $affiliate_id int New affiliate id
		 *
		 * @return void
		 */
		public function trigger( $affiliate_id ) {
			$this->object = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$user_ban_message   = get_user_meta( $this->object['user_id'], '_yith_wcaf_ban_message', true );
			$global_ban_message = get_option( 'yith_wcaf_ban_global_message', '' );

			$this->additional_notes = ! empty( $user_ban_message ) ? $user_ban_message : $global_ban_message;

			// set replaces
			$this->set_replaces();

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Check if mail is enabled
		 *
		 * @return bool Whether email notification is enabled or not
		 * @since 1.0.0
		 */
		public function is_enabled() {
			$notify_affiliates = get_option( 'yith_wcaf_referral_registration_notify_affiliates_ban' );

			return $notify_affiliates == 'yes';
		}

		/**
		 * Retrieve recipient address
		 *
		 * @return string Email address
		 * @since 1.0.0
		 */
		public function get_recipient() {
			if ( ! $this->object ) {
				return false;
			}

			$user_id = $this->object['user_id'];
			$user    = get_user_by( 'id', $user_id );

			if ( ! $user || is_wp_error( $user ) ) {
				return false;
			}

			return $user->user_email;
		}

		/**
		 * Set custom replace value for this email
		 *
		 * @return void
		 */
		public function set_replaces() {
			$user = get_user_by( 'ID', $this->object['user_id'] );

			$tos_url        = get_option( 'yith_wcaf_referral_registration_terms_anchor_url', '' );
			$tos_label      = get_option( 'yith_wcaf_referral_registration_terms_anchor_text', '' );
			$tos_link       = '';
			$tos_plain_link = '';

			if ( $tos_url ) {
				$tos_label = $tos_label ? $tos_label : __( 'Terms & Conditions', 'yith-woocommerce-affiliates' );

				$tos_link       = sprintf( '<a href="%s" target="_blank">%s</a>', $tos_url, $tos_label );
				$tos_plain_link = sprintf( '%s [%s]', $tos_label, $tos_url );
			}

			$placeholders = array(
				'{display_name}'   => ! empty( $user->first_name ) ? sprintf( '%s %s', $user->first_name, $user->last_name ) : $user->user_login,
				'{ban_message}'    => $this->additional_notes,
				'{tos_link}'       => $tos_link,
				'{tos_plain_link}' => $tos_plain_link
			);

			$this->placeholders = array_merge(
				$this->placeholders,
				$placeholders
			);

			// add formatted content text, using placeholders that we just add
			$this->placeholders = array_merge( $this->placeholders, array(
				'{content_html}' => $this->format_string( $this->content_html ),
				'{content_text}' => $this->format_string( $this->content_text )
			) );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since 1.0.0
		 */
		public function get_content_html() {
			$user = get_user_by( 'ID', $this->object['user_id'] );

			ob_start();
			yith_wcaf_get_template( $this->template_html, array(
				'affiliate'        => $this->object,
				'additional_notes' => $this->additional_notes,
				'user'             => $user,
				'display_name'     => ! empty( $user->first_name ) ? sprintf( '%s %s', $user->first_name, $user->last_name ) : $user->user_login,
				'email_heading'    => $this->get_heading(),
				'email'            => $this,
				'sent_to_admin'    => false,
				'plain_text'       => false
			) );

			return $this->format_string( ob_get_clean() );
		}

		/**
		 * Get plain text content of the mail
		 *
		 * @return string Plain text content of the mail
		 * @since 1.0.0
		 */
		public function get_content_plain() {
			$user = get_user_by( 'ID', $this->object['user_id'] );

			ob_start();
			yith_wcaf_get_template( $this->template_plain, array(
				'affiliate'        => $this->object,
				'additional_notes' => $this->additional_notes,
				'user'             => $user,
				'display_name'     => ! empty( $user->first_name ) ? sprintf( '%s %s', $user->first_name, $user->last_name ) : $user->user_login,
				'email_heading'    => $this->get_heading(),
				'email'            => $this,
				'sent_to_admin'    => false,
				'plain_text'       => true
			) );

			return $this->format_string( ob_get_clean() );
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'subject'      => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'      => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'content_html' => array(
					'title'       => __( 'Content HTML', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send within your email (HTML version). You can use the following placeholders: <code>{site_title}, {display_name}, {ban_message}, {tos_link}</code>.', 'woocommerce' ),
					'placeholder' => '',
					'default'     => $this->content_html
				),
				'content_text' => array(
					'title'       => __( 'Content text', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send within your email (plain text version). You can use the following placeholders: <code>{site_title}, {display_name}, {ban_message}, {tos_plain_link}</code>.', 'woocommerce' ),
					'placeholder' => '',
					'default'     => $this->content_text
				),
				'email_type'   => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options()
				)
			);
		}
	}
}

return new YITH_WCAF_Customer_Ban_Email();