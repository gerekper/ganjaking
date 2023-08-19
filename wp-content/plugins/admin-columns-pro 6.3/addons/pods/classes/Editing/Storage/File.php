<?php

namespace ACA\Pods\Editing\Storage;

class File extends Field {

	public function __construct( $pod, $field_name, $meta_type ) {
		parent::__construct( $pod, $field_name, new Read\DbRaw( $this->field_name, $meta_type ) );
	}

	public function update( int $id, $data ): bool {
		$value = [];

		if ( ! empty( $data ) ) {
			foreach ( (array) $data as $attachment_id ) {
				$value[ $attachment_id ] = [
					'id'    => $attachment_id,
					'title' => get_the_title( $attachment_id ),
				];
			}
		}

		return parent::update( $id, $value );
	}

}