<?php

/**
 * Booking Notifications
 *
 * An email sent manually for bookings.
 *
 * @class   WC_Email_Booking_Notification
 * @extends WC_Email
 */
class WC_Email_Booking_Notification extends WC_Email {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id             = 'booking_notification';
		$this->title          = __( 'Booking Notification', 'woocommerce-bookings' );
		$this->description    = __( 'Booking notification emails are sent manually from WooCommerce > Bookings > Send Notification.', 'woocommerce-bookings' );

		$this->heading        = ''; // Controlled via form
		$this->subject        = ''; // Controlled via form
		$this->customer_email = true;
		$this->template_html  = 'emails/customer-booking-notification.php';
		$this->template_plain = 'emails/plain/customer-booking-notification.php';

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
	public function trigger( $booking_id, $notification_subject, $notification_message, $attachments = array() ) {
		if ( $booking_id ) {
			$this->object    = get_wc_booking( $booking_id );

			if ( ! is_object( $this->object ) || ! $this->object->get_order() ) {
				return;
			}

			foreach ( array( '{product_title}', '{order_date}', '{order_number}', '{customer_name}', '{customer_first_name}', '{customer_last_name}' ) as $key ) {
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
					$billing_first_name = $this->object->get_order()->billing_first_name;
					$billing_last_name = $this->object->get_order()->billing_last_name;
					$billing_email = $this->object->get_order()->billing_email;
					$order_date = $this->object->get_order()->order_date;
				} else {
					$billing_first_name = $this->object->get_order()->get_billing_first_name();
					$billing_last_name = $this->object->get_order()->get_billing_last_name();
					$billing_email = $this->object->get_order()->get_billing_email();
					$order_date = $this->object->get_order()->get_date_created() ? $this->object->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
				}

				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $order_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = $this->object->get_order()->get_order_number();

				$this->find[]    = '{customer_name}';
				$this->replace[] = $billing_first_name . ' ' . $billing_last_name;

				$this->find[]    = '{customer_first_name}';
				$this->replace[] = $billing_first_name;

				$this->find[]    = '{customer_last_name}';
				$this->replace[] = $billing_last_name;

				$this->find[]    = '{booking_id}';
				$this->replace[] = $booking_id;

				$this->recipient = $billing_email;
			} else {
				$this->find[]    = '{order_date}';
				$this->replace[] = date_i18n( wc_bookings_date_format(), strtotime( $this->object->booking_date ) );

				$this->find[]    = '{order_number}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );

				$this->find[]    = '{customer_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );

				$this->find[]    = '{customer_first_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );

				$this->find[]    = '{customer_last_name}';
				$this->replace[] = __( 'N/A', 'woocommerce-bookings' );

				$this->find[]    = '{booking_id}';
				$this->replace[] = $booking_id;

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

		$this->heading              = str_replace( $this->find, $this->replace, $notification_subject );
		$this->subject              = str_replace( $this->find, $this->replace, $notification_subject );
		$this->notification_message = str_replace( $this->find, $this->replace, $notification_message );
		$attachments                = apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $attachments );
	}

	/**
	 * Reset tags for find/replace in notification message.
	 *
	 * @return void
	 */
	public function reset_tags() {
		$tags = array(
			'product_title',
			'order_data',
			'order_number',
			'customer_name',
			'customer_first_name',
			'customer_last_name',
		);

		foreach ( $tags as $tag ) {
			$key = array_search( '{' . $tag . '}', $this->find );
			if ( false !== $key ) {
				unset( $this->find[ $key ] );
				unset( $this->replace[ $key ] );
			}
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
			'booking'              => $this->object,
			'email_heading'        => $this->get_heading(),
			'notification_message' => $this->notification_message,
			'email'                => $this,
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
			'booking'              => $this->object,
			'email_heading'        => $this->get_heading(),
			'notification_message' => $this->notification_message,
			'email'                => $this,
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
			'email_type' => array(
				'title'       => __( 'Email type', 'woocommerce-bookings' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'woocommerce-bookings' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'woocommerce-bookings' ),
					'html'      => __( 'HTML', 'woocommerce-bookings' ),
					'multipart' => __( 'Multipart', 'woocommerce-bookings' ),
				),
			),
		);
	}
}
