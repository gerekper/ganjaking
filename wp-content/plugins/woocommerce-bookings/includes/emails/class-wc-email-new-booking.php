<?php

/**
 * New Booking Email
 *
 * An email sent to the admin when a new booking is created.
 *
 * @class       WC_Email_New_Booking
 * @extends     WC_Email
 */
class WC_Email_New_Booking extends WC_Email {
	/**
	 * Subject for pending confirmation emails.
	 *
	 * @var string
	 */
	public $subject_confirmation = '';

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id                   = 'new_booking';
		$this->title                = __( 'New Booking', 'woocommerce-bookings' );
		$this->description          = __( 'New booking emails are sent to the admin when a new booking is created and paid. This email is also received when a Pending confirmation booking is created.', 'woocommerce-bookings' );

		$this->heading              = __( 'New booking', 'woocommerce-bookings' );
		$this->heading_confirmation = __( 'Confirm booking', 'woocommerce-bookings' );
		$this->subject              = __( '[{blogname}] New booking for {product_title} (Order {order_number}) - {order_date}', 'woocommerce-bookings' );
		$this->subject_confirmation = __( '[{blogname}] A new booking for {product_title} (Order {order_number}) is awaiting your approval - {order_date}', 'woocommerce-bookings' );

		$this->template_html    = 'emails/admin-new-booking.php';
		$this->template_plain   = 'emails/plain/admin-new-booking.php';

		// Triggers for this email
		add_action( 'woocommerce_booking_in-cart_to_paid_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_booking_in-cart_to_pending-confirmation_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_booking_unpaid_to_paid_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_booking_unpaid_to_pending-confirmation_notification', array( $this, 'queue_notification' ) );
		add_action( 'woocommerce_booking_confirmed_to_paid_notification', array( $this, 'queue_notification' ) );

		// the following action is initiated via WC core.
		// It is added to WC core's list in WC_Booking_Email_Manager::bookings_email_actions.
		add_action( 'woocommerce_admin_new_booking_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->template_base = WC_BOOKINGS_TEMPLATE_PATH;
		$this->recipient     = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * When bookings are created, orders and other parts may not exist yet. e.g. during order creation on checkout.
	 *
	 * This ensures emails are sent last, once all other logic is complete.
	 */
	public function queue_notification( $booking_id ) {
		wp_schedule_single_event( time(), 'woocommerce_admin_new_booking', array( 'booking_id' => $booking_id ) );
	}

	/**
	 * trigger function.
	 */
	public function trigger( $booking_id ) {
		if ( $booking_id ) {
			$this->object = get_wc_booking( $booking_id );

			if ( ! is_object( $this->object ) || ! $this->object->get_order() ) {
				return;
			}

			if ( $this->object->has_status( 'in-cart' ) ) {
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
					$order_date = $this->object->get_order()->order_date;
				} else {
					$order_date = $this->object->get_order()->get_date_created() ? $this->object->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
				}
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $this->object->booking_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );
			}

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
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
			'email'         => $this,
		), 'woocommerce-bookings/', $this->template_base );
		return ob_get_clean();
	}

	/**
	 * Return the function for this email type.
	 *
	 * @version 1.10.10 Set the subject and then return it.
	 *
	 * @return string
	 */
	public function get_subject() {

		if ( wc_booking_order_requires_confirmation( $this->object->get_order() ) && $this->object->get_status() == 'pending-confirmation' ) {
			$subject = $this->get_option( 'subject_confirmation', $this->subject_confirmation );
		} else {
			$subject = $this->get_option( 'subject', $this->subject );
		}

		return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $subject ), $this->object );

	}

	/**
	 * get_heading function.
	 *
	 * @return string
	 */
	public function get_heading() {
		if ( wc_booking_order_requires_confirmation( $this->object->get_order() ) && $this->object->get_status() == 'pending-confirmation' ) {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading_confirmation ), $this->object );
		} else {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
		}
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
				'title'         => __( 'Enable/Disable', 'woocommerce-bookings' ),
				'type'          => 'checkbox',
				'label'         => __( 'Enable this email notification', 'woocommerce-bookings' ),
				'default'       => 'yes',
			),
			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'woocommerce-bookings' ),
				'type'          => 'text',
				/* translators: %s: admin email */
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'woocommerce-bookings' ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder'   => '',
				'default'       => '',
			),
			'subject' => array(
				'title'         => __( 'Subject', 'woocommerce-bookings' ),
				'type'          => 'text',
				/* translators: %s: subject */
				'description'   => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-bookings' ), $this->subject ),
				'placeholder'   => '',
				'default'       => '',
			),
			'subject_confirmation' => array(
				'title'         => __( 'Subject (Pending confirmation)', 'woocommerce-bookings' ),
				'type'          => 'text',
				/* translators: %s: subject confirmation */
				'description'   => sprintf( __( 'This controls the email subject line for Pending confirmation bookings. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce-bookings' ), $this->subject_confirmation ),
				'placeholder'   => '',
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email Heading', 'woocommerce-bookings' ),
				'type'          => 'text',
				/* translators: %s: heading */
				'description'   => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-bookings' ), $this->heading ),
				'placeholder'   => '',
				'default'       => '',
			),
			'heading_confirmation' => array(
				'title'         => __( 'Email Heading (Pending confirmation)', 'woocommerce-bookings' ),
				'type'          => 'text',
				/* translators: %s: heading confirmation */
				'description'   => sprintf( __( 'This controls the main heading contained within the email notification for Pending confirmation bookings. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce-bookings' ), $this->heading_confirmation ),
				'placeholder'   => '',
				'default'       => '',
			),
			'email_type' => array(
				'title'         => __( 'Email type', 'woocommerce-bookings' ),
				'type'          => 'select',
				'description'   => __( 'Choose which format of email to send.', 'woocommerce-bookings' ),
				'default'       => 'html',
				'class'         => 'email_type',
				'options'       => array(
					'plain'         => __( 'Plain text', 'woocommerce-bookings' ),
					'html'          => __( 'HTML', 'woocommerce-bookings' ),
					'multipart'     => __( 'Multipart', 'woocommerce-bookings' ),
				),
			),
		);
	}
}
