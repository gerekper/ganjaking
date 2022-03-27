<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events;

use WPMailSMTP\WP;
use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;

/**
 * Email tracking event class.
 *
 * @since 2.9.0
 */
abstract class AbstractEvent implements EventInterface {

	/**
	 * The event ID.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	private $id = 0;

	/**
	 * Connected Email Log ID.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	private $email_log_id;

	/**
	 * The event created date.
	 *
	 * @since 2.9.0
	 *
	 * @var \DateTime
	 */
	private $date_created;

	/**
	 * The event related object.
	 *
	 * @since 2.9.0
	 *
	 * @var int
	 */
	private $object_id = 0;

	/**
	 * AbstractEvent constructor.
	 *
	 * @since 2.9.0
	 *
	 * @param int $email_log_id Email Log ID.
	 */
	public function __construct( $email_log_id ) {

		$this->email_log_id = $email_log_id;
	}

	/**
	 * Get the event ID.
	 *
	 * @since 2.9.0
	 *
	 * @return int Event ID.
	 */
	public function get_id() {

		return $this->id;
	}

	/**
	 * Get the Email Log ID.
	 *
	 * @since 2.9.0
	 *
	 * @return int Email Log ID.
	 */
	public function get_email_log_id() {

		return $this->email_log_id;
	}

	/**
	 * Get the event date/time when event was created.
	 *
	 * @since 2.9.0
	 *
	 * @return \DateTime Event created date/time (return current date/time if property is empty).
	 */
	public function get_date_created() {

		if ( $this->date_created instanceof \DateTime ) {
			return $this->date_created;
		}

		$timezone = new \DateTimeZone( 'UTC' );
		$date     = false;

		if( ! empty( $this->date_created ) ) {
			$date = \DateTime::createFromFormat( WP::datetime_mysql_format(), $this->date_created, $timezone );
		}

		if ( $date === false ) {
			$date = new \DateTime( 'now', $timezone );
		}

		$this->date_created = $date;

		return $this->date_created;
	}

	/**
	 * Get the event related object ID (e.g link ID in click link event).
	 *
	 * @since 2.9.0
	 *
	 * @return int Object ID.
	 */
	public function get_object_id() {

		return $this->object_id;
	}

	/**
	 * Set the event ID.
	 *
	 * @since 2.9.0
	 *
	 * @param int $id Event ID.
	 */
	public function set_id( $id ) {

		$this->id = $id;

		return $this;
	}

	/**
	 * Set the Email Log ID.
	 *
	 * @since 2.9.0
	 *
	 * @param int $email_log_id Email Log ID.
	 */
	public function set_email_log_id( $email_log_id ) {

		$this->email_log_id = $email_log_id;

		return $this;
	}

	/**
	 * Set the event date/time when event was created.
	 *
	 * @since 2.9.0
	 *
	 * @param string $date_created Event created date/time in mysql format: Y-m-d H:i:s.
	 */
	public function set_date_created( $date_created ) {

		// Validate the date. Time is ignored.
		$mm = substr( $date_created, 5, 2 );
		$jj = substr( $date_created, 8, 2 );
		$aa = substr( $date_created, 0, 4 );

		$valid_date = wp_checkdate( $mm, $jj, $aa, $date_created );
		$timezone   = new \DateTimeZone( 'UTC' );

		if ( $valid_date ) {
			$date_created = \DateTime::createFromFormat( WP::datetime_mysql_format(), $date_created, $timezone );
		} else {
			$date_created = new \DateTime( 'now', $timezone );
		}

		$this->date_created = $date_created;

		return $this;
	}

	/**
	 * Set the event related object ID (e.g link ID in click link event).
	 *
	 * @since 2.9.0
	 *
	 * @param int $object_id Object ID.
	 */
	public function set_object_id( $object_id ) {

		$this->object_id = $object_id;

		return $this;
	}

	/**
	 * Whether the tracking event is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool Active by default.
	 */
	public function is_active() {

		return true;
	}

	/**
	 * Persist event data to DB.
	 *
	 * @since 2.9.0
	 *
	 * @return int|false Event ID or false if saving failed.
	 */
	public function persist() {

		global $wpdb;

		$data = [
			'email_log_id' => intval( $this->get_email_log_id() ),
			'event_type'   => sanitize_key( static::get_type() ),
			'date_created' => $this->get_date_created()->format( WP::datetime_mysql_format() ),
			'object_id'    => $this->get_object_id() ? intval( $this->get_object_id() ) : null,
		];

		$result = $wpdb->insert( Tracking::get_events_table_name(), $data, [ '%d', '%s', '%s', '%d' ] );

		if ( $result !== false ) {
			$this->set_id( $wpdb->insert_id );
		}

		return $this->get_id() > 0 ? $this->get_id() : false;
	}

	/**
	 * Whether the event was already triggered or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function was_event_already_triggered() {

		global $wpdb;

		$table = Tracking::get_events_table_name();

		$result = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE event_type = %s AND email_log_id = %d LIMIT 1", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				sanitize_key( static::get_type() ),
				intval( $this->get_email_log_id() )
			)
		);

		return $result === 1;
	}
}
