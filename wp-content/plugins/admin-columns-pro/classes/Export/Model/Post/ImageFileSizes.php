<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

/**
 * @since 4.1
 */
class ImageFileSizes extends Model {

	public function get_value( $id ) {
		return ac_helper()->file->get_readable_filesize( array_sum( $this->get_column()->get_raw_value( $id ) ) );
	}

}