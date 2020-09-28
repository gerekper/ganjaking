<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Wishlists_Mail_Email_Verification' ) ) :

	/**
	 * Wish List Email Confrimation.
	 *
	 * An email sent to a wishlist owner to verify email.
	 *
	 * @class       WC_Wishlists_Email_Verification
	 * @version     1.0.0
	 * @extends     WC_Email
	 */
	class WC_Wishlists_Mail_Email_Verification extends WC_Email {

		public $email_confirmation_hash;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'wc_wishlist_email_verfication';
			$this->title          = __( 'Confirm Wishlist Email', 'wc_wishlist' );
			$this->description    = __( 'Email sent to customers who create a list and need to verify their email address', 'wc_wishlist' );
			$this->customer_email = true;
			$this->template_html  = 'emails/wishlist-email-verification.php';
			$this->template_plain = 'emails/plain/wishlist-email-verification.php';

			$this->placeholders = array(
				'{list_title}' => '',
				'{list_date}'  => '',
			);

			$this->template_base = WC_Wishlists_Plugin::plugin_path() . '/templates/';


			// Call parent constructor
			parent::__construct();

		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Response Required:  Confirm your email address for your Wish List', 'wc_wishlist' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Verification Required', 'wc_wishlist' );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int $order_id The order ID.
		 * @param WC_Order $order Order object.
		 */
		public function trigger( $email_address, $wishlist_id, $confirmation_code ) {
			$this->setup_locale();

			$this->recipient = $email_address;
			$this->email_confirmation_hash = $confirmation_code;

			$this->object                       = new WC_Wishlists_Wishlist( $wishlist_id );
			$this->placeholders['{list_title}'] = get_the_title( $this->object->post->ID );
			$this->placeholders['{list_date}}'] = $this->object->post->post_date;

			if ( $this->is_enabled() && $this->get_recipient() ) {
				return $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'wishlist'                => $this->object,
				'email_confirmation_hash' => $this->email_confirmation_hash,
				'email_heading'           => $this->get_heading(),
				'sent_to_admin'           => false,
				'plain_text'              => false,
				'email'                   => $this,
			), '', WC_Wishlists_Plugin::plugin_path() . '/templates/' );
		}

		/**
		 * Get content plain.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'wishlist'      => $this->object,
				'email_confirmation_hash' => $this->email_confirmation_hash,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			), '', WC_Wishlists_Plugin::plugin_path() . '/templates/' );
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'woocommerce' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;
