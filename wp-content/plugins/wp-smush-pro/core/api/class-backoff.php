<?php

namespace Smush\Core\Api;

class Backoff {
	private $max_attempts = 5;

	private $wait = 1;

	private $use_jitter = true;

	private $decider;

	public function __construct() {
		$this->set_decider( $this->get_default_decider() );
	}

	public function run( $callback ) {
		$attempt = 0;
		$try = true;
		$result = null;
		$max_attempts = $this->get_max_attempts();

		while ( $try ) {
			$this->wait( $attempt );
			$result = call_user_func( $callback );

			$attempt ++;

			if ( $attempt >= $max_attempts ) {
				$try = false;
			} else {
				$try = call_user_func( $this->get_decider(), $result );
			}
		}

		return $result;
	}

	private function wait( $attempt ) {
		if ( $attempt == 0 ) {
			return;
		}

		usleep( $this->get_wait_time( $attempt ) * 1000 );
	}

	/**
	 * @return mixed
	 */
	private function get_max_attempts() {
		return $this->max_attempts;
	}

	/**
	 * @param mixed $max_attempts
	 *
	 * @return Backoff
	 */
	public function set_max_attempts( $max_attempts ) {
		$this->max_attempts = max( (int) $max_attempts, 0 );

		return $this;
	}

	/**
	 * @return mixed
	 */
	private function get_wait_time( $attempt ) {
		$wait_time = $attempt == 1
			? $this->wait
			: pow( 2, $attempt ) * $this->wait;

		return $this->jitter( (int) $wait_time );
	}

	/**
	 * @return mixed
	 */
	private function get_initial_wait() {
		return $this->wait;
	}

	/**
	 * @param mixed $wait
	 *
	 * @return Backoff
	 */
	public function set_wait( $wait ) {
		$this->wait = $wait;

		return $this;
	}

	/**
	 * @return mixed
	 */
	private function get_decider() {
		return $this->decider;
	}

	/**
	 * @param mixed $decider
	 *
	 * @return Backoff
	 */
	public function set_decider( $decider ) {
		$this->decider = $decider;

		return $this;
	}

	private function get_default_decider() {
		return function ( $result ) {
			return is_wp_error( $result );
		};
	}

	private function set_jitter( $useJitter ) {
		$this->use_jitter = $useJitter;
	}

	public function enable_jitter() {
		$this->set_jitter( true );

		return $this;
	}

	public function disable_jitter() {
		$this->set_jitter( false );

		return $this;
	}

	private function jitter_enabled() {
		return $this->use_jitter;
	}

	private function jitter( $wait_time ) {
		if ( ! $this->jitter_enabled() ) {
			return $wait_time;
		}

		$jitter_percentage = mt_rand( 1, 20 );
		$add_or_subtract = array_rand( array(
			- 1 => - 1,
			+ 1 => + 1,
		) );
		$jitter = ( $wait_time * $jitter_percentage / 100 ) * $add_or_subtract;

		return $wait_time + $jitter;
	}
}
