<?php

namespace ACP\Type\License;

use LogicException;

final class Status {

	const STATUS_ACTIVE = 'active';
	const STATUS_CANCELLED = 'cancelled';
	const STATUS_EXPIRED = 'expired';

	/**
	 * @var string
	 */
	private $status;

	public function __construct( $status ) {
		if ( ! self::is_valid( $status ) ) {
			throw new LogicException( 'Invalid status.' );
		}

		$this->status = $status;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->status;
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return $this->status === self::STATUS_ACTIVE;
	}

	/**
	 * @return bool
	 */
	public function is_cancelled() {
		return $this->status === self::STATUS_CANCELLED;
	}

	public static function is_valid( $status ) {
		return in_array( $status, [ self::STATUS_ACTIVE, self::STATUS_CANCELLED, self::STATUS_EXPIRED ], true );
	}

}