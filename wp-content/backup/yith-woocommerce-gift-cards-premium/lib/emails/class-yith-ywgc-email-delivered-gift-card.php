<?php
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists ( "WC_Email" ) ) {
	require_once ( WC ()->plugin_path () . '/includes/emails/class-wc-email.php' );
}

if ( ! class_exists ( "YITH_YWGC_Email_Delivered_Gift_Card" ) ) {
	/**
	 * Create and send a digital gift card to the specific recipient
	 *
	 * @since 0.1
	 * @extends \WC_Email
	 */
	class YITH_YWGC_Email_Delivered_Gift_Card extends WC_Email {
		/**
		 * An introductional message from the shop owner
		 */
		public $introductory_text;

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {

			// set ID, this simply needs to be a unique name
			$this->id = 'ywgc-email-delivered-gift-card';

			// this is the title in WooCommerce Email settings
			$this->title = __( "YITH Gift Cards - Delivered Gift Card Notification", 'yith-woocommerce-gift-cards' );

			// this is the description in WooCommerce email settings
			$this->description = __( 'Customer notification - the gift card you have bought has been delivered', 'yith-woocommerce-gift-cards' );

			// these are the default heading and subject lines that can be overridden using the settings
			$this->heading = __( 'Your gift card has been delivered', 'yith-woocommerce-gift-cards' );
			$this->subject = __( '[{site_title}] Your gift card has been delivered', 'yith-woocommerce-gift-cards' );

			// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
			$this->template_html  = 'emails/delivered-gift-card.php';

			$this->introductory_text = __( 'Your gift card has been delivered to <code>{recipient_email}</code>.<br>Thanks again for choosing our store!<br>We wish you all the best.', 'yith-woocommerce-gift-cards' );

			// Trigger on specific action call
			add_action ( 'ywgc-email-delivered-gift-card', array( $this, 'trigger' ) );

			parent::__construct ();
			$this->email_type = "html";
		}

		/**
		 * Send the digital gift card to the recipient
		 *
		 * @param YWGC_Gift_Card_Premium|YITH_YWGC_Gift_Card $gift_card the gift card to be sent
		 *
		 * @return bool|void
		 */
		public function trigger( $gift_card ) {

			if ( is_numeric ( $gift_card ) ) {

				$gift_card = new YWGC_Gift_Card_Premium( array( 'ID' => $gift_card ) );
			}

			if ( ! ( $gift_card instanceof YWGC_Gift_Card_Premium ) ) {
				return false;
			}

			if ( ! $gift_card->exists () ) {
				return false;
			}

			$this->object    = $gift_card;
			$the_order       = wc_get_order ( $gift_card->order_id );
			$this->recipient = apply_filters( 'ywgc_email_delivered_gift_card_recipient_email', yit_get_prop ( $the_order, 'billing_email' ) );

            $gifted_product_id = isset($this->object->present_product_id) && !empty($this->object->present_product_id) ? $this->object->present_product_id : $this->object->product_id;

            $product_object = wc_get_product($gifted_product_id);
            $product_name = isset($product_object) && !empty($product_object) ? $product_object->get_name() : '';

			$this->introductory_text = $this->get_option ( 'introductory_text', __( 'Your gift card has been delivered to <code>{recipient_email}</code>.<br>Thanks again for choosing our store!<br>We wish you all the best.', 'yith-woocommerce-gift-cards' ) );

			$this->introductory_text = str_replace (
				array( "{sender}", "{recipient_email}", "{product_name}", '{site_title}' ),
				array( $this->object->sender_name, $this->object->recipient, $product_name, get_bloginfo() ),
				$this->introductory_text
			);

			$result = $this->send ( $this->get_recipient (),
				$this->get_subject (),
				$this->get_content (),
				$this->get_headers (),
				$this->get_attachments () );


			return $result;
		}

		/**
		 * get_content_html function.
		 *
		 * @since 0.1
		 * @return string
		 */
		public function get_content_html() {
			ob_start ();
			wc_get_template ( $this->template_html, array(
				'gift_card'         => $this->object,
				'introductory_text' => $this->introductory_text,
				'email_heading'     => $this->get_heading (),
				'email_type'        => $this->email_type,
				'sent_to_admin'     => false,
				'plain_text'        => false,
				'email'             => $this,
			),
				'',
				YITH_YWGC_TEMPLATES_DIR );

			return ob_get_clean ();
		}


		/**
		 * Initialize Settings Form Fields
		 *
		 * @since 0.1
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'           => array(
					'title'   => esc_html__( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'           => array(
					'title'       => esc_html__( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf ( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
				),
				'heading'           => array(
					'title'       => esc_html__( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf ( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),
				'introductory_text' => array(
					'title'       => esc_html__( 'Introductive message', 'yith-woocommerce-gift-cards' ),
					'type'        => 'textarea',
					'description' => sprintf ( esc_html__( 'Defaults to <code>%s</code>', 'woocommerce' ), $this->introductory_text ),
					'placeholder' => '',
					'default'     => '',
				),
			);
		}
	} // end \YITH_YWGC_Email_Delivered_Gift_Card class
}

return new YITH_YWGC_Email_Delivered_Gift_Card();
