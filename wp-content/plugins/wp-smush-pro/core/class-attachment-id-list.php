<?php

namespace Smush\Core;

use Smush\Core\Modules\Background\Mutex;

class Attachment_Id_List {
	private $option_id;

	private $ids;
	/**
	 * @var Array_Utils
	 */
	private $array_utils;

	public function __construct( $option_id ) {
		$this->option_id   = $option_id;
		$this->array_utils = new Array_Utils();
	}

	private function set_ids( $ids ) {
		$this->ids = $ids;
	}

	public function get_ids() {
		if ( is_null( $this->ids ) ) {
			$this->ids = $this->fetch_ids();
		}

		return $this->ids;
	}

	private function fetch_ids() {
		wp_cache_delete( $this->option_id, 'options' );

		return $this->string_to_array( (string) get_option( $this->option_id ) );
	}

	public function has_id( $id ) {
		return in_array( $id, $this->get_ids() );
	}

	public function add_id( $attachment_id ) {
		$this->mutex( function () use ( $attachment_id ) {
			$ids = $this->fetch_ids();
			if ( ! in_array( $attachment_id, $ids ) ) {
				$ids[] = $attachment_id;
			}
			$this->_update_ids( $ids );
		} );
	}

	public function add_ids( $attachment_ids ) {
		$this->mutex( function () use ( $attachment_ids ) {
			$new_ids = array_merge( $this->fetch_ids(), $attachment_ids );
			$new_ids = $this->array_utils->fast_array_unique( $new_ids );
			$this->_update_ids( $new_ids );
		} );
	}

	public function remove_id( $attachment_id ) {
		$this->mutex( function () use ( $attachment_id ) {
			$ids   = $this->fetch_ids();
			$index = array_search( $attachment_id, $ids );
			if ( $index !== false ) {
				unset( $ids[ $index ] );
			}
			$this->_update_ids( $ids );
		} );
	}

	public function update_ids( $ids ) {
		$this->mutex( function () use ( $ids ) {
			$this->_update_ids( $ids );
		} );
	}

	public function delete_ids() {
		delete_option( $this->option_id );
		$this->set_ids( array() );
	}

	private function string_to_array( $string ) {
		return empty( $string )
			? array()
			: explode( ',', $string );
	}

	private function array_to_string( $array ) {
		$array = empty( $array ) || ! is_array( $array )
			? array()
			: $array;

		return join( ',', $array );
	}

	private function mutex( $operation ) {
		$option_id = $this->option_id;
		( new Mutex( "{$option_id}_mutex" ) )->execute( $operation );
	}

	private function _update_ids( $ids ) {
		update_option(
			$this->option_id,
			$this->array_to_string( $ids ),
			false
		);
		$this->set_ids( $ids );
	}

	public function get_count() {
		return count( $this->get_ids() );
	}
}