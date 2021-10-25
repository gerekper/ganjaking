<?php

namespace ACP\Search;

use AC\Preferences;
use ACP\Search\Segments\Segment;

final class Segments {

	const ERROR_DUPLICATE_NAME = 'Segment with this name already exists.';
	const ERROR_NAME_NOT_FOUND = 'No segment found with this name.';
	const ERROR_SAVING = 'Could not save segments.';

	/**
	 * @var Preferences
	 */
	private $preferences;

	/**
	 * @var Segment[]
	 */
	private $segments = [];

	/**
	 * @var array
	 */
	private $errors = [];

	/**
	 * @param Preferences $preferences
	 */
	public function __construct( Preferences $preferences ) {
		$this->preferences = $preferences;

		$this->set_segments();
	}

	/**
	 * @return array
	 */
	public function get_errors() {
		return $this->errors;
	}

	/**
	 * @return string
	 */
	public function get_first_error() {
		$errors = $this->get_errors();

		return array_shift( $errors );
	}

	/**
	 * @return bool
	 */
	public function has_errors() {
		return count( $this->errors ) > 0;
	}

	/**
	 * @param string $context
	 * @param string $message
	 */
	private function add_error( $context, $message ) {
		$this->errors[ $context ] = $message;
	}

	/**
	 * @return void
	 */
	private function set_segments() {
		if ( ! $this->preferences->get( 'segments' ) ) {
			return;
		}

		foreach ( $this->preferences->get( 'segments' ) as $segment ) {
			$data = unserialize( $segment['data'] );

			if ( ! is_array( $data ) ) {
				continue;
			}

			$this->segments[ $segment['name'] ] = new Segment(
				$segment['name'],
				$data
			);
		}
	}

	/**
	 * @return Segment[]
	 */
	public function get_segments() {
		return $this->segments;
	}

	/**
	 * @param $name
	 *
	 * @return false|Segment
	 */
	public function get_segment( $name ) {
		return isset( $this->segments[ $name ] )
			? $this->segments[ $name ]
			: false;
	}

	/**
	 * @param Segment $segment
	 *
	 * @return $this
	 */
	public function add_segment( Segment $segment ) {
		if ( $this->get_segment( $segment->get_name() ) ) {
			$this->add_error( 'Adding segment', self::ERROR_DUPLICATE_NAME );
		}

		$this->segments[ $segment->get_name() ] = $segment;

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function remove_segment( $name ) {
		if ( ! $this->get_segment( $name ) ) {
			$this->add_error( 'Removing segment', self::ERROR_NAME_NOT_FOUND );
		}

		unset( $this->segments[ $name ] );

		return $this;
	}

	/**
	 * @param string  $name
	 * @param Segment $segment
	 *
	 * @return $this
	 */
	public function update_segment( $name, Segment $segment ) {
		if ( $name !== $segment->get_name() && $this->get_segment( $segment->get_name() ) ) {
			$this->add_error( 'Updating segment', self::ERROR_DUPLICATE_NAME );
		}

		if ( ! $this->remove_segment( $name ) ) {
			$this->add_error( 'Updating segment', self::ERROR_NAME_NOT_FOUND );
		}

		$this->add_segment( $segment );

		return $this;
	}

	/**
	 * @return bool
	 */
	public function save() {
		if ( $this->has_errors() ) {
			return false;
		}

		$data = [];

		// Sort natural by name
		uksort( $this->segments, 'strnatcmp' );

		foreach ( $this->segments as $segment ) {
			$data[] = [
				'name' => $segment->get_name(),
				'data' => serialize( $segment->get_data() ),
			];
		}

		$result = $this->preferences->set( 'segments', $data );

		if ( ! $result ) {
			$this->add_error( 'Saving segments', 'Could not save segment' );

			return false;
		}

		return true;
	}

}