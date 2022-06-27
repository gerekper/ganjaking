<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

class FileMetaVideo extends AC\Column\Media\FileMetaVideo
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\FileMeta( $this->get_media_setting()->get_media_meta_keys() );
	}

}