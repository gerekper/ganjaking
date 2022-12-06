<?php

namespace ACA\Pods\Editing;

use ACA\Pods\Editing;
use ACA\Pods\Field;

trait DefaultServiceTrait {

	public function editing() {
		if ( ! $this instanceof Field ) {
			return false;
		}

		return new Editing\Service\FieldStorage(
			( new StorageFactory() )->create_by_field( $this ),
			( new ViewFactory() )->create_by_field( $this )
		);
	}

}