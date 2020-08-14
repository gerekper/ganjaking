<?php
/**
 * Affiliate New Coupon Email
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

if ( ! class_exists( 'YITH_WCAF_Customer_New_Coupon_Email' ) ) {
	/**
	 * New affiliate email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Customer_New_Coupon_Email extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCAF_Customer_New_Coupon_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id             = 'affiliate_new_coupon';
			$this->title          = __( 'Affiliate\'s new coupon message', 'yith-woocommerce-affiliates' );
			$this->description    = __( 'This email is sent to affiliates whenever admin creates a coupon for them', 'yith-woocommerce-affiliates' );
			$this->customer_email = true;

			$this->heading = __( 'You have a new coupon', 'yith-woocommerce-affiliates' );
			$this->subject = __( 'We created a new coupon for you to share!', 'yith-woocommerce-affiliates' );

			$this->content_html = $this->get_option( 'content_html', __( '<p>an admin just created a new coupon for you!</p>
<p><b>Coupon code:</b> {coupon_code}</p>
<p>Share it with your users: you will be granted with commissions each time a customer purchases using this coupon code</p>
<p>Please, have a look at your {affiliate_dashboard_link} for further information, and don\'t hesitate to contact us if you have any doubts</p>', 'yith-woocommerce-affiliates' ) );
			$this->content_text = $this->get_option( 'content_text', __( 'an admin created a new coupon for you!
Coupon code: {coupon_code}
Share it with your users: you will be granted with commissions each time a customer purchases using this coupon code
Please, have a look at your {affiliate_dashboard_link} for further information, and don\'t hesitate to contact us if you have any doubts', 'yith-woocommerce-affiliates' ) );

			$this->template_html  = 'emails/customer-new-coupon.php';
			$this->template_plain = 'emails/plain/customer-new-coupon.php';

			// Triggers for this email
			add_action( 'yith_wcaf_affiliate_coupon_saved_notification', array( $this, 'trigger' ), 10, 1 );

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
		public function trigger( $coupon ) {
			$this->object = $coupon;

			if ( ! $this->object ) {
				return;
			}

			$affiliate_id = $this->object->get_meta( 'coupon_referrer', true );
			$affiliate    = YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $affiliate_id );

			if ( ! $affiliate ) {
				return;
			}

			$user = get_userdata( $affiliate['user_id'] );

			if ( ! $user || is_wp_error( $user ) ) {
				return;
			}

			$this->user = $user;

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
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
			$coupon_handling_enabled = get_option( 'yith_wcaf_coupon_enable' );
			$notify_affiliates       = get_option( 'yith_wcaf_coupon_notify_affiliate' );

			return $coupon_handling_enabled == 'yes' && $notify_affiliates == 'yes';
		}

		/**
		 * Retrieve recipient address
		 *
		 * @return string Email address
		 * @since 1.0.0
		 */
		public function get_recipient() {
			return $this->user->user_email;
		}

		/**
		 * Set custom replace value for this email
		 *
		 * @return void
		 */
		public function set_replaces() {
			$show_coupon_section            = get_option( 'yith_wcaf_coupon_show_section', 'yes' );
			$affiliate_dashboard_url        = YITH_WCAF()->get_affiliate_dashboard_url( 'yes' == $show_coupon_section ? 'coupons' : '' );
			$affiliate_dashboard_link       = sprintf( '<a href="%s" target="_blank">%s</a>', $affiliate_dashboard_url, __( 'Affiliate Dashboard', 'yith-woocommerce-affiliates' ) );
			$affiliate_dashboard_plain_link = sprintf( '%s [%s]', __( 'Affiliate Dashboard', 'yith-woocommerce-affiliates' ), $affiliate_dashboard_url );

			$placeholders = array(
				'{display_name}'                   => ! empty( $this->user->first_name ) ? sprintf( '%s %s', $this->user->first_name, $this->user->last_name ) : $this->user->user_login,
				'{coupon_code}'                    => $this->object->get_code(),
				'{affiliate_dashboard_link}'       => $affiliate_dashboard_link,
				'{affiliate_dashboard_plain_link}' => $affiliate_dashboard_plain_link
			);

			// add coupon data
			foreach ( $this->object->get_data() as $key => $value ) {
				// fix timestamps
				if ( $value instanceof WC_DateTime ) {
					$value = $value->date_i18n();
				}

				// skip if value is not a string
				if ( ! is_string( $value ) && ! apply_filters( 'yith_wcaf_new_coupon_email_coupon_meta_value', false, $key, $value ) ) {
					continue;
				}

				// remove initial underscore, if any
				if ( strpos( $key, '_' ) === 0 ) {
					$key = substr( $key, 1 );
				}

				// generate replace key
				$key = "{coupon_{$key}}";

				// add key/value pair to placeholders array
				$placeholders[ $key ] = $value;
			}

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
			ob_start();
			yith_wcaf_get_template( $this->template_html, array(
				'coupon'        => $this->object,
				'user'          => $this->user,
				'display_name'  => ! empty( $this->user->first_name ) ? sprintf( '%s %s', $this->user->first_name, $this->user->last_name ) : $this->user->user_login,
				'email_heading' => $this->get_heading(),
				'email'         => $this,
				'sent_to_admin' => false,
				'plain_text'    => false
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
			ob_start();
			yith_wcaf_get_template( $this->template_plain, array(
				'coupon'        => $this->object,
				'user'          => $this->user,
				'display_name'  => ! empty( $this->user->first_name ) ? sprintf( '%s %s', $this->user->first_name, $this->user->last_name ) : $this->user->user_login,
				'email_heading' => $this->get_heading(),
				'email'         => $this,
				'sent_to_admin' => false,
				'plain_text'    => true
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
					'description' => __( 'Enter the text that you want to send within your email (HTML version). You can use the following placeholders: <code>{site_title}, {display_name}, {coupon_code}, {affiliate_dashboard_link}</code>.', 'woocommerce' ),
					'placeholder' => '',
					'default'     => $this->content_html
				),
				'content_text' => array(
					'title'       => __( 'Content text', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send within your email (plain text version). You can use the following placeholders: <code>{site_title}, {display_name}, {coupon_code}, {affiliate_dashboard_plain_link}</code>.', 'woocommerce' ),
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

return new YITH_WCAF_Customer_New_Coupon_Email();