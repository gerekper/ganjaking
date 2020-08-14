<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( "WC_Email" ) ) {
	require_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
}

if ( ! class_exists( "YWQA_Email_Notify_Answer" ) ) {
	/**
	 * Notify the user that a new answer were submitted on a question he asked
	 *
	 * @since 0.1
	 * @extends \WC_Email
	 */
	class YWQA_Email_Notify_Answer extends WC_Email {

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {
			// set ID, this simply needs to be a unique name
			$this->id = 'ywqa-email-notify-answer';

			// this is the title in WooCommerce Email settings
			$this->title = esc_html__( "YITH Q&A - New answer notification", 'yith-woocommerce-questions-and-answers' );

			// this is the description in WooCommerce email settings
			$this->description = esc_html__( 'When an answer is submitted, sent a notification to the user who submitted the question', 'yith-woocommerce-questions-and-answers' );

			// these are the default heading and subject lines that can be overridden using the settings
			$this->heading = esc_html__( 'You have a new answer for [{product_title}]', 'yith-woocommerce-questions-and-answers' );
			$this->subject = esc_html__( '[{site_title}] You have a new answer for [{product_title}]', 'yith-woocommerce-questions-and-answers' );

			// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
			$this->template_html  = 'emails/ywqa-notify-answer.php';
			$this->template_plain = 'emails/plain/ywqa-notify-answer.php';

			// Trigger on specific action call
			add_action( 'ywqa-email-notify-answer_notification', array( $this, 'trigger' ) );

			parent::__construct();

			// Other settings
			$this->recipient = $this->get_option( 'recipient', '' );
		}

		/**
		 * Send the email
		 *
		 * @param array $args
		 *
		 * @return bool|void
		 */
		public function trigger( $args ) {

			if ( ! $this->is_enabled() ) {
				return;
			}

			$this->object = $args;

			$recipient_email = $args['recipient_email'];


			/** @var YWQA_Answer $answer */
			$answer        = $this->object['answer'];
			$product       = wc_get_product( $answer->product_id );
			if( ! $product ) {
				return;
			}
			$product_title = $product->get_title();

			$this->find['product_title']    = '[{product_title}]';
			$this->replace['product_title'] = $product_title;

			$this->send(
				$recipient_email,
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments() );
		}

		/**
		 * get_content_html function.
		 *
		 * @since 0.1
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'answer'        => $this->object['answer'],
					'email_heading' => $this->get_heading(),
					'email_type'    => $this->email_type,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				),
				'',
				YITH_YWQA_TEMPLATES_DIR );
		}


		/**
		 * Get content plain.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'answer'        => $this->object['answer'],
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => true,
					'plain_text'    => true,
					'email'         => $this
				) );
		}

		/**
		 * Initialize Settings Form Fields
		 *
		 * @since 0.1
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => esc_html__( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => esc_html__( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => esc_html__( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'email_type' => array(
					'title'       => esc_html__( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => esc_html__( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true
				)

			);
		}
	} // end \YWQA_Email_Notify_Answer class
}

return new YWQA_Email_Notify_Answer();