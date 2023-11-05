<?php

namespace Smush\Core\Modules\Background;

class Background_Process_Status {
	const PROCESSING = 'in_processing';
	const CANCELLED = 'is_cancelled';
	const COMPLETED = 'is_completed';
	const DEAD = 'is_dead';
	const TOTAL_ITEMS = 'total_items';
	const PROCESSED_ITEMS = 'processed_items';
	const FAILED_ITEMS = 'failed_items';

	private $identifier;
	/**
	 * @var Background_Utils
	 */
	private $utils;

	public function __construct( $identifier ) {
		$this->identifier = $identifier;
		$this->utils      = new Background_Utils();
	}

	public function get_data() {
		$option_value = $this->utils->get_site_option(
			$this->get_option_id(),
			array()
		);

		return wp_parse_args(
			$option_value,
			array(
				self::PROCESSING      => false,
				self::CANCELLED       => false,
				self::COMPLETED       => false,
				self::TOTAL_ITEMS     => 0,
				self::PROCESSED_ITEMS => 0,
				self::FAILED_ITEMS    => 0,
			)
		);
	}

	public function to_array() {
		return $this->get_data();
	}

	private function set_data( $updated ) {
		$data = $this->get_data();

		update_site_option( $this->get_option_id(), array_merge( $data, $updated ) );
	}

	private function get_value( $key ) {
		$data = $this->get_data();

		return isset( $data[ $key ] )
			? $data[ $key ]
			: false;
	}

	private function set_value( $key, $value ) {
		$this->mutex( function () use ( $key, $value ) {
			$updated_data = array_merge(
				$this->get_data(),
				array( $key => $value )
			);
			update_site_option( $this->get_option_id(), $updated_data );
		} );
	}

	private function get_option_id() {
		return $this->identifier . '_status';
	}

	public function is_in_processing() {
		return $this->get_value( self::PROCESSING );
	}

	public function set_in_processing( $in_processing ) {
		$this->set_value( self::PROCESSING, $in_processing );
	}

	public function get_total_items() {
		return $this->get_value( self::TOTAL_ITEMS );
	}

	public function set_total_items( $total_items ) {
		$this->set_value( self::TOTAL_ITEMS, $total_items );
	}

	public function get_processed_items() {
		return $this->get_value( self::PROCESSED_ITEMS );
	}

	public function set_processed_items( $processed_items ) {
		$this->set_value( self::PROCESSED_ITEMS, $processed_items );
	}

	public function get_failed_items() {
		return $this->get_value( self::FAILED_ITEMS );
	}

	public function set_failed_items( $failed_items ) {
		$this->set_value( self::PROCESSED_ITEMS, $failed_items );
	}

	public function is_cancelled() {
		return $this->get_value( self::CANCELLED );
	}

	public function set_is_cancelled( $is_cancelled ) {
		$this->set_value( self::CANCELLED, $is_cancelled );
	}

	public function is_dead() {
		return $this->get_value( self::DEAD );
	}

	public function is_completed() {
		return $this->get_value( self::COMPLETED );
	}

	public function set_is_completed( $is_completed ) {
		$this->set_value( self::COMPLETED, $is_completed );
	}

	private function mutex( $operation ) {
		$mutex = new Mutex( $this->get_option_id() );
		$mutex->execute( $operation );
	}

	public function start( $total_items ) {
		$this->mutex( function () use ( $total_items ) {
			$this->set_data( array(
				self::PROCESSING      => true,
				self::CANCELLED       => false,
				self::DEAD            => false,
				self::COMPLETED       => false,
				self::TOTAL_ITEMS     => $total_items,
				self::PROCESSED_ITEMS => 0,
				self::FAILED_ITEMS    => 0,
			) );
		} );
	}

	public function complete() {
		$this->mutex( function () {
			$this->set_data( array(
				self::PROCESSING => false,
				self::CANCELLED  => false,
				self::DEAD       => false,
				self::COMPLETED  => true,
			) );
		} );
	}

	public function cancel() {
		$this->mutex( function () {
			$this->set_data( array(
				self::PROCESSING => false,
				self::CANCELLED  => true,
				self::DEAD       => false,
				self::COMPLETED  => false,
			) );
		} );
	}

	public function mark_as_dead() {
		$this->mutex( function () {
			$this->set_data( array(
				self::PROCESSING => false,
				self::CANCELLED  => false,
				self::DEAD       => true,
				self::COMPLETED  => false,
			) );
		} );
	}

	public function task_successful() {
		$this->mutex( function () {
			$this->set_data( array(
				self::PROCESSED_ITEMS => $this->get_processed_items() + 1,
			) );
		} );
	}

	public function task_failed() {
		$this->mutex( function () {
			$this->set_data( array(
				self::PROCESSED_ITEMS => $this->get_processed_items() + 1,
				self::FAILED_ITEMS    => $this->get_failed_items() + 1,
			) );
		} );
	}
}