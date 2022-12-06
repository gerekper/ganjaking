<?php

namespace ACA\BP\Editing\Storage;

use ACP;

class Group implements ACP\Editing\Storage {

	/**
	 * @var string
	 */
	private $field;

	public function __construct( $field ) {
		$this->field = $field;
	}

	public function update( int $id, $data ): bool {
		$group = groups_get_group( $id );

		if ( property_exists( $group, $this->field ) ) {
			$group->{$this->field} = $data;
			$group->save();
		}

		return $group->save();
	}

	public function get( int $id ) {
		$group = groups_get_group( $id );

		return property_exists( $group, $this->field )
			? $group->{$this->field}
			: null;
	}

}