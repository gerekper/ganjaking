<?php

/**
 * Booking is confirmed
 *
 * An email sent to the user when a booking is confirmed.
 *
 * @class   WC_Email_Booking_Pending_Confirmation
 * @extends WC_Email
 */
class WC_Email_Booking_Pending_Confirmation extends WC_Email {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id             = 'booking_pending_confirmation';
		$this->title          = __( 'Booking Pending Confirmation', 'woocommerce-bookings' );
		$this->description    = __( 'Booking pending confirmation emails are sent when the status of a booking goes to pending confirmation.', 'woocommerce-bookings' );
		$this->heading        = __( 'Booking Pending Confirmation', 'woocommerce-bookings' );
		$this->subject        = __( '[{blogname}] Your booking of {product_title} has been received, and it\'s pending confirmation (Order {order_number}) - {order_date}', 'woocommerce-bookings' );
		$this->customer_email = true;
		$this->template_html  = 'emails/customer-booking-pending-confirmation.php';
		$this->template_plain = 'emails/plain/customer-booking-pending-confirmation.php';

		// Triggers for this email
		add_action( 'woocommerce_booking_pending-confirmation_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->template_base = WC_BOOKINGS_TEMPLATE_PATH;
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	public function trigger( $booking_id ) {
		if ( $booking_id ) {
			$this->object = get_wc_booking( $booking_id );

			if ( ! is_object( $this->object ) ) {
				return;
			}

			foreach ( array( '{product_title}', '{order_date}', '{order_number}' ) as $key ) {
				$key = array_search( $key, $this->find );
				if ( false !== $key ) {
					unset( $this->find[ $key ] );
					unset( $this->replace[ $key ] );
				}
			}

			if ( $this->object->get_product() ) {
				$this->find[]    = '{product_title}';
				$this->replace[] = $this->object->get_product()->get_title();
			}

			if ( $this->object->get_order() ) {
				if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
					$billing_email = $this->object->get_order()->billing_email;
					$order_date = $this->object->get_order()->order_date;
				} else {
					$billing_email = $this->object->get_order()->get_billing_email();
					$order_date = $this->object->get_order()->get_date_created() ? $this->object->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
				}

				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();

				$this->recipient = $billing_email;
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $this->object->booking_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );

				$customer_id = $this->object->customer_id;
				$customer    = $customer_id ? get_user_by( 'id', $customer_id ) : false;

				if ( $customer_id && $customer ) {
					$this->recipient = $customer->user_email;
				}
			}
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'booking'       => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'         => $this,
		), 'woocommerce-bookings/', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'booking'       => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'         => $this,
		), 'woocommerce-bookings/', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-bookings' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'woocommerce-bookings' ),
				'default' => 'yes',
			),
			'subject' => array(
				'title'       => __( 'Subject', 'woocommerce-bookings' ),
				'type'        => 'text',
				/* translators: %s: subject */
				'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-bookings' ), $this->subject ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading' => array(
				'title'       => __( 'Email Heading', 'woocommerce-bookings' ),
				'type'        => 'text',
				/* translators: %s: heading */
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-bookings' ), $this->heading ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-bookings' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-bookings' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'      => __( 'Plain text', 'woocommerce-bookings' ),
					'html'       => __( 'HTML', 'woocommerce-bookings' ),
					'multipart'  => __( 'Multipart', 'woocommerce-bookings' ),
				),
			),
		);
	}
}
