<?php

namespace WPMailSMTP\Pro\Emails\Logs;

use WPMailSMTP\WP;

/**
 * Class Email represents single email log entry.
 */
class Email {

	/**
	 * This email is not successfully sent.
	 *
	 * @since 1.5.0
	 */
	const STATUS_UNSENT = 0;

	/**
	 * This email is successfully sent.
	 *
	 * @since 1.5.0
	 */
	const STATUS_SENT = 1;

	/**
	 * This email is waiting the API "is sent" verification check.
	 *
	 * @since 2.5.0
	 */
	const STATUS_WAITING = 2;

	/**
	 * This email was delivered (confirmed via API).
	 *
	 * @since 2.5.0
	 */
	const STATUS_DELIVERED = 3;

	/**
	 * @since 1.5.0
	 *
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $subject = '';

	/**
	 * @since 1.5.0
	 *
	 * @var string JSON string of email header TO/CC/BCC/FROM values.
	 */
	protected $people = '';

	/**
	 * @since 1.5.0
	 *
	 * @var string JSON strong of email header.
	 */
	protected $headers = '';

	/**
	 * Error recorded when email was sent.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected $error_text = '';

	/**
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $content_plain = '';

	/**
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $content_html = '';

	/**
	 * @since 1.5.0
	 *
	 * @var int Check statuses constants to refer to the meaning of the status value.
	 */
	protected $status = 0;

	/**
	 * @since 1.5.0
	 *
	 * @var \DateTime
	 */
	protected $date_sent;

	/**
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $mailer = 'smtp';

	/**
	 * @since 1.5.0
	 *
	 * @var 0 Whether this email had attachments or not. 0 is none, number is any.
	 */
	protected $attachments = 0;

	/**
	 * Retrieve a particular email when constructing the object.
	 *
	 * @since 1.5.0
	 *
	 * @param int|object $id_or_row
	 */
	public function __construct( $id_or_row = null ) {

		$this->populate_email( $id_or_row );
	}

	/**
	 * Get and prepare the email data.
	 *
	 * @since 1.5.0
	 *
	 * @param int|object $id_or_row
	 */
	private function populate_email( $id_or_row ) {

		$email = null;

		if ( is_numeric( $id_or_row ) ) {
			// Get by ID.
			$collection = new EmailsCollection( array( 'id' => (int) $id_or_row ) );
			$emails     = $collection->get();

			if ( $emails->valid() ) {
				$email = $emails->current();
			}
		} elseif (
			is_object( $id_or_row ) &&
			isset(
				$id_or_row->id,
				$id_or_row->subject,
				$id_or_row->people,
				$id_or_row->headers,
				$id_or_row->content_plain,
				$id_or_row->content_html,
				$id_or_row->status,
				$id_or_row->date_sent,
				$id_or_row->mailer,
				$id_or_row->attachments
			)
		) {
			$email = $id_or_row;
		}

		if ( $email !== null ) {
			foreach ( get_object_vars( $email ) as $key => $value ) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Email ID as per our DB table.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public function get_id() {

		return (int) $this->id;
	}

	/**
	 * Email subject.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_subject() {

		return esc_html( $this->subject );
	}

	/**
	 * All the people that participated in this email: sent to, from or copied.
	 *
	 * @since 1.5.0
	 *
	 * @param string $type Who exactly to retrieve. Supported values: to, from, cc, bcc. If empty - get everyone.
	 *
	 * @return array|string List of people. Empty array if type is incorrect or everyone are requested.
	 *                      String for some specific type.
	 */
	public function get_people( $type = '' ) {

		$people = WP::is_json( $this->people ) ? json_decode( $this->people ) : array();
		$type   = sanitize_key( $type );

		// Get all.
		if ( empty( $type ) ) {
			$data = $people;
		} elseif ( isset( $people->{$type} ) ) {
			$data = $people->{$type};
		} else {
			$data = array();
		}

		return $data;
	}

	/**
	 * All the headers, JSON string.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_headers() {

		return WP::is_json( $this->headers ) ? $this->headers : '{}';
	}


	/**
	 * Get a specific header by its name if it exists.
	 *
	 * @since 2.5.0
	 *
	 * @param string $name The name of the header to get.
	 *
	 * @return string
	 */
	public function get_header( $name ) {

		if ( empty( $name ) ) {
			return '';
		}

		$headers = json_decode( $this->get_headers(), true );

		foreach ( $headers as $header ) {
			preg_match( '/^' . $name . ': (.*)/', $header, $output );

			if ( ! empty( $output[1] ) ) {
				return $output[1];
			}
		}

		return '';
	}

	/**
	 * Error recorded when email was attempted to be sent.
	 *
	 * @since 2.5.0
	 *
	 * @return string
	 */
	public function get_error_text() {

		return $this->error_text;
	}

	/**
	 * Get the content of the email.
	 *
	 * @since 1.5.0
	 *
	 * @return string Return HTML content by default. Fallback to plain.
	 */
	public function get_content() {

		$html = $this->get_content_html();

		if ( empty( $html ) ) {
			return $this->get_content_plain();
		}

		return $html;
	}

	/**
	 * Get the plain content of the email.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_content_plain() {

		return $this->content_plain;
	}

	/**
	 * Get the HTML content of the email.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_content_html() {

		return $this->content_html;
	}

	/**
	 * 1 means sent, 0 means not sent.
	 *
	 * @since 1.5.0
	 *
	 * @return int
	 */
	public function get_status() {

		return (int) $this->status;
	}

	/**
	 * Get the date/time when this email was sent.
	 *
	 * @since 1.5.0
	 *
	 * @return \DateTime
	 * @throws \Exception Emits exception on incorrect date.
	 */
	public function get_date_sent() {

		$date = \DateTime::createFromFormat( WP::datetime_mysql_format(), $this->date_sent );

		if ( $date === false ) {
			$date = new \DateTime();
		}

		return $date;
	}

	/**
	 * @return string
	 */
	public function get_mailer() {

		return $this->mailer;
	}

	/**
	 * Whether email had attachments on not.
	 *
	 * @since 1.5.0
	 *
	 * @return int 0 when no attachments, number if any.
	 */
	public function get_attachments() {

		return (int) $this->attachments;
	}

	/**
	 * Set the email subject.
	 *
	 * @since 1.5.0
	 * @since 2.2.0 Limit the length of the subject to 191 chars because the `subject` DB column is `varchar(191)`.
	 *
	 * @param string $subject
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_subject( $subject ) {

		$this->subject = substr( wp_kses( $subject, [] ), 0, 191 );

		return $this;
	}

	/**
	 * Set the email people. Contains to/cc/bcc/from.
	 * We require array here.
	 *
	 * @since 1.5.0
	 *
	 * @param array $people
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_people( $people ) {

		if ( ! is_array( $people ) ) {
			$people = array();
		}

		$this->people = wp_json_encode( $people );

		return $this;
	}

	/**
	 * Set the email headers.
	 * We require array here.
	 *
	 * @since 1.5.0
	 *
	 * @param array $headers
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_headers( $headers ) {

		if ( ! is_array( $headers ) ) {
			$headers = array();
		}

		$this->headers = wp_json_encode( $headers );

		return $this;
	}

	/**
	 * Set the email error text.
	 *
	 * @since 2.5.0
	 *
	 * @param string $error_text The email error text to be set.
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_error_text( $error_text ) {

		$this->error_text = sanitize_textarea_field( $error_text );

		return $this;
	}

	/**
	 * Set the email content and try to define whether it has HTML or not.
	 *
	 * @since 1.5.0
	 *
	 * @param string $content
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_content( $content ) {

		// TODO: this check is NOT reliable.
		if ( $content === strip_tags( $content ) ) { // phpcs:ignore
			$this->set_content_plain( wp_strip_all_tags( $content ) );
		} else {
			$this->set_content_html( $content );
		}

		return $this;
	}

	/**
	 * Set the email plain text content.
	 *
	 * @since 1.5.0
	 *
	 * @param string $plain
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_content_plain( $plain ) {

		$this->content_plain = $plain;

		return $this;
	}

	/**
	 * Set the email HTML content.
	 *
	 * @since 1.5.0
	 *
	 * @param string $html
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_content_html( $html ) {

		$this->content_html = $html;

		return $this;
	}

	/**
	 * Set the date when the email was sent.
	 * Date validation is taken from wp_insert_post().
	 * Date should be set in this format: Y-m-d H:i:s.
	 * DateTime object will be set in property, on save it will be converted to string.
	 *
	 * @since 1.5.0
	 *
	 * @param string $date_sent
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 *
	 * @throws \Exception When date is incorrectly generated on PHP side.
	 */
	public function set_date_sent( $date_sent ) {

		// Validate the date. Time is ignored.
		$mm = substr( $date_sent, 5, 2 );
		$jj = substr( $date_sent, 8, 2 );
		$aa = substr( $date_sent, 0, 4 );

		$valid_date = wp_checkdate( $mm, $jj, $aa, $date_sent );

		if ( $valid_date ) {
			$date_sent = \DateTime::createFromFormat( WP::datetime_mysql_format(), $date_sent );
		} else {
			$date_sent = new \DateTime();
		}

		$this->date_sent = $date_sent;

		return $this;
	}

	/**
	 * Set whether the email had attachments.
	 *
	 * @since 1.5.0
	 *
	 * @param int $attachments
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_attachments( $attachments ) {

		$this->attachments = (int) $attachments;

		return $this;
	}

	/**
	 * Set the mailer key that was used to send this email.
	 *
	 * @since 1.5.0
	 *
	 * @param string $mailer
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_mailer( $mailer ) {

		$this->mailer = sanitize_key( $mailer );

		return $this;
	}

	/**
	 * Set the status of the email: was it successfully sent or not?
	 * We do not insist on exact default statuses, as developers may want to change that.
	 * We do insist on an integer.
	 *
	 * @since 1.5.0
	 *
	 * @param int $status
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email
	 */
	public function set_status( $status ) {

		$this->status = (int) $status;

		return $this;
	}

	/**
	 * Save a new or modified email in DB.
	 *
	 * @since 1.5.0
	 *
	 * @return \WPMailSMTP\Pro\Emails\Logs\Email New or updated email class instance.
	 * @throws \Exception When email init fails.
	 */
	public function save() {

		global $wpdb;

		$table = Logs::get_table_name();

		if ( (bool) $this->get_id() ) {
			// Update the existing DB table record.
			$wpdb->update(
				$table,
				array(
					'subject'       => $this->subject,
					'people'        => $this->people,
					'headers'       => $this->headers,
					'error_text'    => $this->error_text,
					'content_plain' => $this->content_plain,
					'content_html'  => $this->content_html,
					'status'        => $this->status,
					'date_sent'     => $this->get_date_sent()->format( WP::datetime_mysql_format() ),
					'mailer'        => $this->mailer,
					'attachments'   => $this->attachments,
				),
				array(
					'id' => $this->get_id(),
				),
				array(
					'%s', // subject.
					'%s', // people.
					'%s', // headers.
					'%s', // error_text.
					'%s', // content_plain.
					'%s', // content_html.
					'%s', // status.
					'%s', // date_sent.
					'%s', // mailer.
					'%d', // attachments.
				),
				array(
					'%d',
				)
			);

			$email_id = $this->get_id();
		} else {
			// Create a new DB table record.
			$wpdb->insert(
				$table,
				array(
					'subject'       => $this->subject,
					'people'        => $this->people,
					'headers'       => $this->headers,
					'error_text'    => $this->error_text,
					'content_plain' => $this->content_plain,
					'content_html'  => $this->content_html,
					'status'        => $this->status,
					'date_sent'     => $this->get_date_sent()->format( WP::datetime_mysql_format() ),
					'mailer'        => $this->mailer,
					'attachments'   => $this->attachments,
				),
				array(
					'%s', // subject.
					'%s', // people.
					'%s', // headers.
					'%s', // error_text.
					'%s', // content_plain.
					'%s', // content_html.
					'%s', // status.
					'%s', // date_sent.
					'%s', // mailer.
					'%d', // attachments.
				)
			);

			$email_id = $wpdb->insert_id;
		}

		try {
			$email = new Email( $email_id );
		} catch ( \Exception $e ) {
			$email = new Email();
		}

		return $email;
	}

	/**
	 * Delete the current email.
	 * Usage:
	 *     $email = new Email( $email_id );
	 *     $email->delete();
	 *
	 * @since 1.5.0
	 *
	 * @return int|false Integer on successful deletion (number of rows), false on failure.
	 */
	public function delete() {

		global $wpdb;

		return $wpdb->delete(
			Logs::get_table_name(),
			array(
				'id' => $this->get_id(),
			),
			array(
				'%d',
			)
		);
	}

	/**
	 * Whether the email instance is a valid entity to work with.
	 *
	 * @since 1.5.0
	 */
	public function is_valid() {

		if (
			empty( $this->id ) ||
			empty( $this->headers ) ||
			empty( $this->date_sent )
		) {
			return false;
		}

		return true;
	}

	/**
	 * Whether the email was successfully sent or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_sent() {

		return self::STATUS_SENT === $this->get_status();
	}

	/**
	 * Whether the email sending has failed.
	 *
	 * @since 2.5.0
	 *
	 * @return bool
	 */
	public function has_failed() {

		return self::STATUS_UNSENT === $this->get_status();
	}

	/**
	 * Whether the email content has HTML or not.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_html() {

		return ! empty( $this->get_content_html() );
	}

	/**
	 * If the email has an error text.
	 *
	 * @since 2.5.0
	 *
	 * @return bool
	 */
	public function has_error() {

		return ! empty( $this->get_error_text() );
	}
}
