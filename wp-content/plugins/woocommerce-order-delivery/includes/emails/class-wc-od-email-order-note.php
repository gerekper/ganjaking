<?php
/**
 * Class WC_OD_Email_Order_Note file
 *
 * @package WC_OD\Emails
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Email_Order_Note' ) ) {

	/**
	 * Order Note Email.
	 *
	 * An email sent to the admin when a note is added to an order.
	 *
	 * @class   WC_OD_Email_Order_Note
	 * @extends WC_Email
	 */
	abstract class WC_OD_Email_Order_Note extends WC_Email {

		/**
		 * The order note.
		 *
		 * @var string
		 */
		public $note;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			$this->template_base = WC_OD_PATH . 'templates/';

			// Set placeholders.
			$this->setPlaceholder( 'site_title', $this->get_blogname() );
			$this->setPlaceholder( 'order_date', '' );
			$this->setPlaceholder( 'order_number', '' );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @since 1.4.0
		 *
		 * @param string   $note             The order note.
		 * @param WC_Order $order            Order object.
		 * @param bool     $is_customer_note Is a customer note?.
		 */
		public function trigger( $note, $order, $is_customer_note ) {
			// Ignore customer notes.
			if ( $is_customer_note ) {
				return;
			}

			// Added in WC 3.1.
			if ( method_exists( $this, 'setup_locale' ) ) {
				$this->setup_locale();
			}

			if ( $note && is_a( $order, 'WC_Order' ) ) {
				$this->object = $order;
				$this->note   = $note;
				$this->setPlaceholder( 'order_date', wc_od_localize_date( $this->object->get_date_created() ) );
				$this->setPlaceholder( 'order_number', $this->object->get_order_number() );
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			// Added in WC 3.1.
			if ( method_exists( $this, 'restore_locale' ) ) {
				$this->restore_locale();
			}
		}

		/**
		 * Sets a placeholder value.
		 *
		 * Adds backward compatibility with older WC versions.
		 *
		 * @param string $key   The placeholder key.
		 * @param mixed  $value The placeholder value.
		 */
		public function setPlaceholder( $key, $value ) {
			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				$this->find[ $key ]    = "{{$key}}";
				$this->replace[ $key ] = $value;
			} else {
				$this->placeholders[ "{{$key}}" ] = $value;
			}
		}

		/**
		 * Gets the content arguments.
		 *
		 * @since 1.4.0
		 *
		 * @param string $type Optional. The content type [html, plain].
		 *
		 * @return array
		 */
		public function get_content_args( $type = 'html' ) {
			return array(
				'order'         => $this->object,
				'note'          => $this->note,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => ( 'plain' === $type ),
				'email'         => $this,
			);
		}

		/**
		 * Get content html.
		 *
		 * @since 1.4.0
		 *
		 * @return string
		 */
		public function get_content_html() {
			ob_start();
			wc_od_get_template( $this->template_html, $this->get_content_args() );

			return ob_get_clean();
		}

		/**
		 * Get content plain.
		 *
		 * @since 1.4.0
		 *
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();
			wc_od_get_template( $this->template_plain, $this->get_content_args( 'plain' ) );

			return ob_get_clean();
		}

		/**
		 * Initialise settings form fields.
		 *
		 * @since 1.4.0
		 */
		public function init_form_fields() {
			parent::init_form_fields();

			$fields              = array_slice( $this->form_fields, 0, 1 );
			$fields['recipient'] = array(
				'title'       => __( 'Recipient(s)', 'woocommerce-order-delivery' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce-order-delivery' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'placeholder' => '',
				'default'     => '',
				'desc_tip'    => true,
			);

			$fields = array_merge( $fields, array_slice( $this->form_fields, 1 ) );

			$this->form_fields = $fields;
		}
	}

}
