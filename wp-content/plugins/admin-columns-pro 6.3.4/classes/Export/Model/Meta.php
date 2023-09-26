<?php

namespace ACP\Export\Model;

use AC\MetaType;
use ACP\Export\Service;

class Meta implements Service {

	protected $meta_type;

	protected $meta_key;

	public function __construct( MetaType $meta_type, string $meta_key ) {
		$this->meta_type = $meta_type;
		$this->meta_key = $meta_key;
	}

	public function get_value( $id ) {
		$value = get_metadata(
			(string) $this->meta_type,
			(int) $id,
			$this->meta_key,
			true
		);

		return is_scalar( $value )
			? (string) $value
			: '';
	}

}