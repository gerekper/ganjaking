<?php

namespace ACP\Export\Model;

use ACP\Export\Model;

/**
 * Exportability model for outputting a post's title based on its ID
 * @since 4.1
 */
class PostTitleFromPostId extends Model {

	public function get_value( $id ) {
		return get_the_title( $this->get_column()->get_raw_value( $id ) );
	}

}