<?php

namespace ACP\Column\Media;

use AC;
use ACP\Sorting;

class FileMetaAudio extends AC\Column\Media\FileMetaAudio
	implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\Media\FileMeta( $this->get_media_setting()->get_media_meta_keys() );
	}

}