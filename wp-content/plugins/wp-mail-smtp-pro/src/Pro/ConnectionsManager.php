<?php

namespace WPMailSMTP\Pro;

use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\ConnectionsManager as ConnectionsManagerLite;
use WPMailSMTP\Pro\AdditionalConnections\AdditionalConnections;

/**
 * Class ConnectionsManager.
 *
 * This class allows to define a primary and backup connection per each email.
 *
 * @since 3.7.0
 */
class ConnectionsManager extends ConnectionsManagerLite {

	/**
	 * Hold all created connections objects.
	 *
	 * @since 3.7.0
	 *
	 * @var ConnectionInterface[]
	 */
	private $connections = [];

	/**
	 * The current connection object that is using for email sending.
	 *
	 * @since 3.7.0
	 *
	 * @var ConnectionInterface
	 */
	private $mail_connection = null;

	/**
	 * The backup connection object that will be used for email sending if the primary connection fails.
	 *
	 * @since 3.7.0
	 *
	 * @var ConnectionInterface
	 */
	private $mail_backup_connection = null;

	/**
	 * Get connection object by ID.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id Connection ID.
	 * @param bool   $default       Whether to return the default connection if the connection by ID was not found or not.
	 *
	 * @return false|ConnectionInterface
	 */
	public function get_connection( $connection_id, $default = true ) {

		if ( $connection_id === 'primary' ) {
			return $this->get_primary_connection();
		}

		// Return already created connection from storage.
		if ( isset( $this->connections[ $connection_id ] ) ) {
			return $this->connections[ $connection_id ];
		}

		// Get additional connection.
		$connection = $this->get_additional_connection( $connection_id );

		if ( $connection ) {
			return $connection;
		}

		return $default ? $this->get_primary_connection() : false;
	}

	/**
	 * Set connection object that will be used for email sending.
	 *
	 * @since 3.7.0
	 *
	 * @param ConnectionInterface $connection The connection object.
	 */
	public function set_mail_connection( ConnectionInterface $connection ) {

		$this->mail_connection = $connection;
	}

	/**
	 * Get connection object that should be used for email sending.
	 *
	 * If connection object is not set, then primary connection will be returned.
	 *
	 * @since 3.7.0
	 *
	 * @param bool $default Whether to return the default mail connection if the connection was not set.
	 *
	 * @return ConnectionInterface
	 */
	public function get_mail_connection( $default = true ) {

		if ( ! empty( $this->mail_connection ) || ! $default ) {
			return $this->mail_connection;
		}

		return $this->get_primary_connection();
	}

	/**
	 * Set a backup connection object.
	 *
	 * This object will be used for email sending if the primary connection fails.
	 *
	 * @since 3.7.0
	 *
	 * @param ConnectionInterface|false $connection The connection object.
	 */
	public function set_mail_backup_connection( $connection ) {

		$this->mail_backup_connection = $connection;
	}

	/**
	 * Get a backup connection.
	 *
	 * This object will be used for email sending if the primary connection fails.
	 *
	 * @since 3.7.0
	 *
	 * @return ConnectionInterface|false|null
	 */
	public function get_mail_backup_connection() {

		return $this->mail_backup_connection;
	}

	/**
	 * Reset all mail related variables.
	 *
	 * All variables are set before each particular email is sent and reset after.
	 *
	 * @since 3.7.0
	 */
	public function reset_mail_connection() {

		$this->mail_connection        = null;
		$this->mail_backup_connection = null;
	}

	/**
	 * Get additional connection object by ID.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id Connection ID.
	 *
	 * @return false|ConnectionInterface
	 */
	private function get_additional_connection( $connection_id ) {

		$additional_connections = new AdditionalConnections();
		$connection             = $additional_connections->get_connection( $connection_id );

		if ( $connection && ! isset( $this->connections[ $connection_id ] ) ) {
			$this->connections[ $connection_id ] = $connection;
		}

		return $connection;
	}
}
