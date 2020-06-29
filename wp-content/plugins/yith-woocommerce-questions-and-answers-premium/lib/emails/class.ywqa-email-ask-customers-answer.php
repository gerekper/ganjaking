<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( "WC_Email" ) ) {
	require_once( WC()->plugin_path() . '/includes/emails/class-wc-email.php' );
}

if ( ! class_exists( "YWQA_Email_Ask_Customers_Answer" ) ) {
	/**
	 * Create and send an email asking for a feedback about a question over a product that the customer purchased
	 *
	 * @since 0.1
	 * @extends \WC_Email
	 */
	class YWQA_Email_Ask_Customers_Answer extends WC_Email {

		/**
		 * Set email defaults
		 *
		 * @since 0.1
		 */
		public function __construct() {
			$this->id = 'ywqa-email-ask-customers-answer';

			// this is the title in WooCommerce Email settings
			$this->title = esc_html__( "YITH Q&A - ask answer to customers", 'yith-woocommerce-questions-and-answers' );

			// this is the description in WooCommerce email settings
			$this->description = esc_html__( 'When a new question is submitted for a product, ask a feedback to the customers that purchased the same product', 'yith-woocommerce-questions-and-answers' );

			// these are the default heading and subject lines that can be overridden using the settings
			$this->heading = esc_html__( 'Give your feedback', 'yith-woocommerce-questions-and-answers' );

			$this->subject = esc_html__( '[{site_title}] A user need your feedback for [{product_title}]', 'yith-woocommerce-questions-and-answers' );

			// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
			$this->template_html  = 'emails/ywqa-ask-customers.php';
			$this->template_plain = 'emails/plain/ywqa-ask-customers.php';

			// Triggers for this email
			add_action( 'ywqa-email-ask-customers-answer_notification', array( $this, 'trigger' ) );

			parent::__construct();

			// Other settings
			$this->recipient = $this->get_option( 'recipient' );
		}

		/**
		 * Send the email
		 *
		 * @param mixed $obj
		 *
		 * @return bool|void
		 */
		public function trigger( $args ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			$this->object = $args;

			/** @var YWQA_Question $question */
			$question      = $this->object['question'];
			$product       = wc_get_product( $question->product_id );
			if( ! $product ) {
				return;
			}
			$product_title = $product->get_title();
			
			$this->find['product_title']    = '[{product_title}]';
			$this->replace['product_title'] = $product_title;

			$this->send(
				$args['recipient'],
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
					'question'                    => $this->object['question'],
					'unsubscribe_product_url'     => $this->object['unsubscribe_product_url'],
					'unsubscribe_all_product_url' => $this->object['unsubscribe_all_product_url'],
					'email_heading'               => $this->get_heading(),
					'email_type'                  => $this->email_type,
					'sent_to_admin'               => false,
					'plain_text'                  => false,
					'email'                       => $this,
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
			return wc_get_template_html( $this->template_plain, array(
				'question'                    => $this->object['question'],
				'unsubscribe_product_url'     => $this->object['unsubscribe_product_url'],
				'unsubscribe_all_product_url' => $this->object['unsubscribe_all_product_url'],
				'email_heading'               => $this->get_heading(),
				'sent_to_admin'               => true,
				'plain_text'                  => true,
				'email'                       => $this
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
	} // end \YWQA_Email_Ask_Customers_Answer class
}

return new YWQA_Email_Ask_Customers_Answer();