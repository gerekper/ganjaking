<?php

namespace Smush\Core\Modules\Background;

class Mutex {
	/**
	 * @var string
	 */
	private $key;
	/**
	 * TRUE: Don't perform the operation if lock couldn't be acquired
	 * FALSE: Even if lock is not acquired, still perform the operation
	 *
	 * @var bool
	 */
	private $break_on_timeout = false;
	/**
	 * @var int
	 */
	private $timeout = 10;

	public function __construct( $key ) {
		$this->key = $key;
	}

	public function execute( $operation ) {
		$acquired = $this->acquire_lock();
		if ( $acquired || ! $this->break_on_timeout() ) {
			call_user_func( $operation );
		}
		$this->release_lock();
	}

	private function acquire_lock() {
		global $wpdb;

		$lock = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT GET_LOCK(%s,%d) as lock_set',
				array(
					$this->get_key(),
					$this->get_timeout(),
				)
			)
		);

		return 1 === intval( $lock->lock_set );
	}

	private function release_lock() {
		global $wpdb;

		$wpdb->get_row(
			$wpdb->prepare(
				'SELECT RELEASE_LOCK(%s) as lock_released',
				array( $this->get_key() )
			)
		);
	}

	/**
	 * @return bool
	 */
	public function break_on_timeout() {
		return $this->break_on_timeout;
	}

	/**
	 * @param bool $break_on_timeout
	 */
	public function set_break_on_timeout( $break_on_timeout ) {
		$this->break_on_timeout = $break_on_timeout;

		return $this;
	}

	/**
	 * @return int
	 */
	public function get_timeout() {
		return $this->timeout;
	}

	/**
	 * @param int $timeout
	 */
	public function set_timeout( $timeout ) {
		$this->timeout = $timeout;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function set_key( $key ) {
		$this->key = $key;

		return $this;
	}
}
