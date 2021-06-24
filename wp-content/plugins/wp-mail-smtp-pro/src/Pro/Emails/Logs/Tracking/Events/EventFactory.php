<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events;

use WPMailSMTP\Pro\Emails\Logs\Tracking\Tracking;

/**
 * Email tracking event factory.
 *
 * @since 2.9.0
 */
class EventFactory {

	/**
	 * Get available events FQCN.
	 *
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public static function get_events_class_names() {

		return [
			Injectable\OpenEmailEvent::class,
			Injectable\ClickLinkEvent::class,
		];
	}

	/**
	 * Create new event.
	 *
	 * @since 2.9.0
	 *
	 * @param string $type         Event type.
	 * @param int    $email_log_id Email log ID.
	 *
	 * @return AbstractEvent|false
	 */
	public function create_event( $type, $email_log_id ) {

		$class_name = $this->get_classname_from_event_type( $type );

		return $class_name ? new $class_name( $email_log_id ) : false;
	}

	/**
	 * Get event by event ID.
	 *
	 * @since 2.9.0
	 *
	 * @param int $event_id Email ID.
	 *
	 * @return AbstractEvent|false
	 */
	public function get_event( $event_id ) {

		if ( ! is_numeric( $event_id ) ) {
			return false;
		}

		global $wpdb;

		$table = Tracking::get_events_table_name();

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", intval( $event_id ) ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		if ( ! $row ) {
			return false;
		}

		$class_name = $this->get_classname_from_event_type( $row->event_type );

		if ( ! $class_name ) {
			return false;
		}

		$event = new $class_name( $row->email_log_id );
		$event->set_date_created( $row->date_created );

		if ( ! empty( $row->object_id ) ) {
			$event->set_object_id( $row->object_id );
		}

		return $event;
	}

	/**
	 * Get event FQCN by event type.
	 *
	 * @since 2.9.0
	 *
	 * @param string $type Event type.
	 *
	 * @return string|false
	 */
	public function get_classname_from_event_type( $type ) {

		foreach ( self::get_events_class_names() as $class_name ) {
			if ( $class_name::get_type() === $type ) {
				return $class_name;
			}
		}

		return false;
	}
}
