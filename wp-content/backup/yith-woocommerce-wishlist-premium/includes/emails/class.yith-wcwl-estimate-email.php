<?php
/**
 * Quote email class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWL_Estimate_Email' ) ) {
	/**
	 * WooCommerce Wishlist
	 *
	 * @since 1.0.0
	 */
	class YITH_WCWL_Estimate_Email extends WC_Email {

		/**
		 * True when the email notification is sent manually only.
		 *
		 * @var bool
		 */
		protected $manual = true;

		/**
		 * Whether to send emails to requester too or not
		 *
		 * @var string
		 */
		protected $enable_cc = false;

		/**
		 * Current wishlist, to be used to compose email
		 *
		 * @var \YITH_WCWL_Wishlist
		 */
		public $wishlist = array();

		/**
		 * Address that should be used as Reply-to and CC for the email
		 *
		 * @var string
		 */
		public $reply_email = array();

		/**
		 * Additional notes to append to request email
		 *
		 * @var string
		 */
		public $additional_notes = '';

		/**
		 * Additional informations send through POST (posted data)
		 *
		 * @var array
		 */
		public $additional_data = array();

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function __construct() {
			$this->id          = 'estimate_mail';
			$this->title       = __( 'Wishlist "Ask for an estimate" email', 'yith-woocommerce-wishlist' );
			$this->description = __( 'This email is sent to users who click the "ask for an estimate" button, only if this feature has been enabled in the wishlist option panel', 'yith-woocommerce-wishlist' );

			$this->heading = __( 'Price estimate request', 'yith-woocommerce-wishlist' );
			$this->subject = __( '[Price estimate request]', 'yith-woocommerce-wishlist' );

			$this->template_html  = 'emails/ask-estimate.php';
			$this->template_plain = 'emails/plain/ask-estimate.php';

			// Triggers for this email.
			add_action( 'send_estimate_mail_notification', array( $this, 'trigger' ), 15, 4 );

			// Call parent constructor.
			parent::__construct();

			// Other settings.
			$this->recipient = $this->get_option( 'recipient' );

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
			}

			$this->enable_cc = $this->get_option( 'enable_cc' );
			$this->enable_cc = 'yes' == $this->enable_cc;
		}

		/**
		 * Method triggered to send email
		 *
		 * @param int    $wishlist_id      Id of wishlist.
		 * @param string $additional_notes Additional notes added by customer.
		 * @param string $reply_email      Email address of the requester (only for unauthenticated users).
		 * @param array  $additional_data  Array of posted data.
		 *
		 * @return void
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function trigger( $wishlist_id, $additional_notes, $reply_email, $additional_data ) {
			$this->wishlist    = yith_wcwl_get_wishlist( ! empty( $wishlist_id ) ? $wishlist_id : false );
			$this->reply_email = ! empty( $reply_email ) ? $reply_email : $this->wishlist->get_user_email();

			$this->additional_notes = $additional_notes;
			$this->additional_data  = apply_filters( 'yith_wcwl_estimate_additional_data', $additional_data, $_POST, $this ); // WPCS: input var ok, CSRF ok.

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get_headers function
		 *
		 * @access public
		 * @return string
		 */
		public function get_headers() {
			$headers = '';

			if ( ! empty( $this->reply_email ) ) {
				$headers = "Reply-to: {$this->reply_email}\r\n";

				if ( $this->enable_cc ) {
					$headers .= "Cc: {$this->reply_email}\r\n";
				}
			}

			$headers .= "Content-Type: {$this->get_content_type()}\r\n";

			return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function get_content_html() {
			$formatted_name = $this->wishlist->get_user_formatted_name();

			ob_start();
			wc_get_template(
				$this->template_html,
				array(
					'email'               => $this,
					'wishlist_data'       => $this->wishlist,
					'user_formatted_name' => $formatted_name ? $formatted_name : $this->reply_email,
					'additional_notes'    => $this->additional_notes,
					'additional_data'     => $this->additional_data,
					'email_heading'       => $this->get_heading(),
					'sent_to_admin'       => true,
					'plain_text'          => false,
				)
			);

			return ob_get_clean();
		}

		/**
		 * Get plain text content of the mail
		 *
		 * @return string Plain text content of the mail
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function get_content_plain() {
			$formatted_name = $this->wishlist->get_user_formatted_name();

			ob_start();
			wc_get_template(
				$this->template_plain,
				array(
					'email'               => $this,
					'wishlist_data'       => $this->wishlist,
					'user_formatted_name' => $formatted_name ? $formatted_name : $this->reply_email,
					'additional_notes'    => $this->additional_notes,
					'additional_data'     => $this->additional_data,
					'email_heading'       => $this->get_heading(),
					'sent_to_admin'       => true,
					'plain_text'          => true,
				)
			);

			return ob_get_clean();
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since  1.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.com>
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'subject'    => array(
					'title'       => __( 'Subject', 'yith-woocommerce-wishlist' ),
					'type'        => 'text',
					// translators: 1. Default subject.
					'description' => sprintf( __( 'This field lets you modify the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-woocommerce-wishlist' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
				),
				'recipient'  => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-wishlist' ),
					'type'        => 'text',
					// translators: 1. Default recipients.
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>', 'yith-woocommerce-wishlist' ), esc_attr( get_option( 'admin_email' ) ) ),
					'placeholder' => '',
					'default'     => '',
				),
				'enable_cc'  => array(
					'title'       => __( 'Send CC copy', 'yith-woocommerce-wishlist' ),
					'type'        => 'checkbox',
					'description' => __( 'Send a copy to the user', 'yith-woocommerce-wishlist' ),
					'default'     => 'no',
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-wishlist' ),
					'type'        => 'text',
					// translators: 1. Default email heading.
					'description' => sprintf( __( 'This field lets you modify the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-woocommerce-wishlist' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'yith-woocommerce-wishlist' ),
					'type'        => 'select',
					'description' => __( 'Choose which type of email to send.', 'yith-woocommerce-wishlist' ),
					'default'     => 'html',
					'class'       => 'email_type',
					'options'     => array(
						'plain'     => __( 'Plain text', 'yith-woocommerce-wishlist' ),
						'html'      => __( 'HTML', 'yith-woocommerce-wishlist' ),
						'multipart' => __( 'Multipart', 'yith-woocommerce-wishlist' ),
					),
				),
			);
		}
	}
}


// returns instance of the mail on file include.
return new YITH_WCWL_Estimate_Email();
