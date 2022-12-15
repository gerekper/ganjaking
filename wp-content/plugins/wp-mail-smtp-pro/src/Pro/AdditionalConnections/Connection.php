<?php

namespace WPMailSMTP\Pro\AdditionalConnections;

use WPMailSMTP\AbstractConnection;

/**
 * Class Connection.
 *
 * Additional connection implementation.
 *
 * @since 3.7.0
 */
class Connection extends AbstractConnection {

	/**
	 * The connection ID.
	 *
	 * @since 3.7.0
	 *
	 * @var string
	 */
	private $connection_id;

	/**
	 * Connection Options object.
	 *
	 * @since 3.7.0
	 *
	 * @var ConnectionOptions
	 */
	private $options = null;

	/**
	 * Constructor.
	 *
	 * @since 3.7.0
	 *
	 * @param string $connection_id The connection ID.
	 */
	public function __construct( $connection_id ) {

		$this->connection_id = $connection_id;
	}

	/**
	 * Get the connection identifier.
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_id() {

		return $this->connection_id;
	}

	/**
	 * Get the connection name.
	 *
	 * @since 3.7.0
	 *
	 * @return string
	 */
	public function get_name() {

		return $this->get_options()->get( 'connection', 'name' );
	}

	/**
	 * Get connection Options object.
	 *
	 * @since 3.7.0
	 *
	 * @return ConnectionOptions
	 */
	public function get_options() {

		if ( is_null( $this->options ) ) {
			$this->options = new ConnectionOptions( $this->connection_id );
		}

		return $this->options;
	}
}

