<?php

namespace WPMailSMTP\Pro\Emails\Logs\Tracking\Events;

/**
 * Email tracking event interface.
 *
 * @since 2.9.0
 */
interface EventInterface {

	/**
	 * Get the event type.
	 *
	 * @since 2.9.0
	 *
	 * @return string
	 */
	public static function get_type();

	/**
	 * Whether the tracking event is enabled or not.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * Persist event data to DB.
	 *
	 * @since 2.9.0
	 *
	 * @return int|false Event ID or false if saving failed.
	 */
	public function persist();
}
