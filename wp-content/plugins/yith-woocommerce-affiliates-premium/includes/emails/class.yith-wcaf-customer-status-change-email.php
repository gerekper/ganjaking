<?php
/**
 * Affiliate status changed Email
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

if ( ! class_exists( 'YITH_WCAF_Customer_Status_Change_Email' ) ) {
	/**
	 * New affiliate email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Customer_Status_Change_Email extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCAF_Customer_Status_Change_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id             = 'affiliate_status_changed';
			$this->title          = __( 'Affiliate\'s status changed', 'yith-woocommerce-affiliates' );
			$this->description    = __( 'This email is sent to affiliates whenever their status changes', 'yith-woocommerce-affiliates' );
			$this->customer_email = true;

			$this->heading = __( 'Your account was updated', 'yith-woocommerce-affiliates' );
			$this->subject = __( 'Your {site_title} affiliate account was updated', 'yith-woocommerce-affiliates' );

			$this->accept_message_html = $this->get_option( 'accept_message_html', '<p>You can now start sharing your affiliate link that you can generate from your {affiliate_dashboard_link}.</p>
<p>You\'ll earn a {affiliate_rate}% commission on sales of every product coming through your affiliate link.<br> The website administrator can set up product exceptions that are not specified here.</p>
<p>Please, read more about any possible exceptions in our {tos_link}</p>' );
			$this->accept_message_text = $this->get_option( 'accept_message_text', "You can now start sharing your affiliate link that you can generate from your {affiliate_dashboard_plain_link}.
You'll earn a {affiliate_rate}% commission on sales of every product coming through your affiliate link. The website administrator can set up product exceptions that are not specified here.
Please, read more about any possible exceptions in our {tos_plain_link}" );
			$this->reject_message_html = $this->get_option( 'reject_message_html', '<p>Unfortunately, we could not accept your request because {reject_message}</p>' );
			$this->reject_message_text = $this->get_option( 'reject_message_text', "Unfortunately, we could not accept your request because {reject_message}" );

			$this->template_html  = 'emails/customer-status-change.php';
			$this->template_plain = 'emails/plain/customer-status-change.php';

			// Triggers for this email
			add_action( 'yith_wcaf_affiliate_status_updated_notification', array( $this, 'trigger' ), 10, 3 );

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
		public function trigger( $affiliate_id, $new_status = '', $old_status = '' ) {
			$this->object = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

			if ( ! $this->is_enabled() || ! $this->get_recipient() && $new_status ) {
				return;
			}

			$this->status           = $new_status;
			$this->new_status       = YITH_WCAF_Affiliate_Handler()->get_readable_status( $new_status );
			$this->old_status       = YITH_WCAF_Affiliate_Handler()->get_readable_status( $old_status );
			$this->additional_notes = '';

			if ( $new_status == - 1 ) {
				$user_reject_message   = get_user_meta( $this->object['user_id'], '_yith_wcaf_reject_message', true );
				$global_reject_message = get_option( 'yith_wcaf_ban_reject_global_message', '' );

				$this->additional_notes = ! empty( $user_reject_message ) ? $user_reject_message : $global_reject_message;
			}

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
			$notify_affiliates = get_option( 'yith_wcaf_referral_registration_notify_affiliates' );

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

			$affiliate_dashboard_url        = apply_filters( 'yith_wcaf_customer_status_change_dashboard_url', YITH_WCAF()->get_affiliate_dashboard_url( 'generate-link' ), $this );
			$affiliate_dashboard_link       = sprintf( '<a href="%s">%s</a>', $affiliate_dashboard_url, __( 'Affiliate Dashboard', 'yith-woocommerce-affiliates' ) );
			$affiliate_dashboard_plain_link = sprintf( '%s [%s]', __( 'Affiliate Dashboard', 'yith-woocommerce-affiliates' ), $affiliate_dashboard_url );

			$user_rate = YITH_WCAF_Rate_Handler()->get_rate( $this->object );

			$placeholders = array(
				'{display_name}'                   => ! empty( $user->first_name ) ? sprintf( '%s %s', $user->first_name, $user->last_name ) : $user->user_login,
				'{reject_message}'                 => $this->additional_notes,
				'{tos_link}'                       => $tos_link,
				'{tos_plain_link}'                 => $tos_plain_link,
				'{affiliate_dashboard_link}'       => $affiliate_dashboard_link,
				'{affiliate_dashboard_plain_link}' => $affiliate_dashboard_plain_link,
				'{affiliate_rate}'                 => $user_rate,
				'{new_status}'                     => $this->new_status,
				'{old_status}'                     => $this->old_status
			);

			$this->placeholders = array_merge(
				$this->placeholders,
				$placeholders
			);

			// add formatted content text, using placeholders that we just add
			$this->placeholders = array_merge( $this->placeholders, array(
				'{message_html}' => $this->format_string( $this->status == 1 ? $this->accept_message_html : $this->reject_message_html ),
				'{message_text}' => $this->format_string( $this->status == 1 ? $this->accept_message_text : $this->reject_message_text )
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
				'new_status'       => $this->new_status,
				'old_status'       => $this->old_status,
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
				'new_status'       => $this->new_status,
				'old_status'       => $this->old_status,
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
				'subject'             => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'             => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'accept_message_html' => array(
					'title'       => __( 'Accept message HTML', 'yith-woocommerce-affiliates' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send to your affiliate when enabling his/her account (HTML version). You can use the following placeholders: <code>{site_title}, {display_name}, {tos_link}, {affiliate_rate}, {affiliate_dashboard_link}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->accept_message_html
				),
				'accept_message_tex'  => array(
					'title'       => __( 'Accept message text', 'yith-woocommerce-affiliates' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send to your affiliate when enabling his/her account (plain text version). You can use the following placeholders: <code>{site_title}, {display_name}, {tos_plain_link}, {affiliate_rate}, {affiliate_dashboard_plain_link}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->accept_message_text
				),
				'reject_message_html' => array(
					'title'       => __( 'Accept message HTML', 'yith-woocommerce-affiliates' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send to your affiliate when rejecting his/her affiliate request (HTML version). You can use the following placeholders: <code>{site_title}, {display_name}, {tos_link}, {reject_message}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->reject_message_html
				),
				'reject_message_tex'  => array(
					'title'       => __( 'Accept message text', 'yith-woocommerce-affiliates' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send to your affiliate when rejecting his/her affiliate request (plain text version). You can use the following placeholders: <code>{site_title}, {display_name}, {tos_plain_link}, {reject_message}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->reject_message_text
				),
				'email_type'          => array(
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

return new YITH_WCAF_Customer_Status_Change_Email();