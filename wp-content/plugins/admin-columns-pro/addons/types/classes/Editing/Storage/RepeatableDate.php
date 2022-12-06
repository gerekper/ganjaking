<?php

namespace ACA\Types\Editing\Storage;

class RepeatableDate extends Repeater {

	public function get( $id ) {
		return array_map( [ $this, 'time_to_date' ], parent::get( $id ) );
	}

	public function update( int $id, $data ): bool {
		$data = is_array( $data ) ? array_map( 'strtotime', $data ) : false;

		return parent::update( $id, $data );
	}

	private function time_to_date( $timestamp ) {
		return date( 'Y-m-d', $timestamp );
	}

}