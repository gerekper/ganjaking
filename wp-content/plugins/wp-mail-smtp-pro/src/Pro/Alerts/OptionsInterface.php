<?php

namespace WPMailSMTP\Pro\Alerts;

/**
 * Interface OptionsInterface.
 *
 * @since 3.5.0
 */
interface OptionsInterface {

	/**
	 * Get the mailer provider slug.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_slug();

	/**
	 * Get the mailer provider title (or name).
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_title();

	/**
	 * Get the mailer provider description.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Output the mailer provider options.
	 *
	 * @since 3.5.0
	 */
	public function display_options();

	/**
	 * Get single connection options.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $connection Connection settings.
	 * @param string $i          Connection index.
	 *
	 * @return string
	 */
	public function get_connection_options( $connection, $i );
}
