<?php

namespace ACA\Types\Editing\Storage;

use AC;
use ACA\Types;
use ACP\Editing\Storage;

abstract class Relationship implements Storage {

	/**
	 * @var Types\Column\Post\Relationship
	 */
	private $column;

	/**
	 * @var string
	 */
	protected $relationship;

	abstract protected function connect_post( $source_id, $connect_id );

	abstract protected function disconnect_post( $source_id, $connect_id );

	public function __construct( Types\Column\Post\Relationship $column, $relationship ) {
		$this->column = $column;
		$this->relationship = $relationship;
	}

	private function get_ids( int $id ) {
		$post_ids = $this->column->get_raw_value( $id );

		return $post_ids instanceof AC\Collection
			? $post_ids->all()
			: (array) $post_ids;
	}

	public function get( int $id ) {
		$post_ids = $this->get_ids( $id );
		$value = [];

		if ( $post_ids ) {
			foreach ( $post_ids as $_id ) {
				$value[ $_id ] = get_post_field( 'post_title', $_id );
			}
		}

		return $value;
	}

	public function update( int $id, $data ): bool {
		$old_ids = $this->get_ids( $id );

		if ( ! $data ) {
			$data = [];
		}

		foreach ( $old_ids as $post_id ) {
			if ( ! in_array( $post_id, $data ) ) {
				$this->disconnect_post( $id, $post_id );
			}
		}

		foreach ( $data as $post_id ) {
			if ( in_array( $post_id, $old_ids ) ) {
				continue;
			}

			$this->connect_post( $id, $post_id );
		}

		return true;
	}

}