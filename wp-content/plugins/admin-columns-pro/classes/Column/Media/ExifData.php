<?php

namespace ACP\Column\Media;

use AC;
use ACP\Export;
use ACP\Sorting;

class ExifData extends AC\Column\Media\ExifData
	implements Sorting\Sortable, Export\Exportable {

	public function sorting() {
		return new Sorting\Model\Media\Exif( $this->get_setting( AC\Settings\Column\ExifData::NAME )->get_value() );
	}

	public function export() {
		return new Export\Model\Value( $this );
	}

}