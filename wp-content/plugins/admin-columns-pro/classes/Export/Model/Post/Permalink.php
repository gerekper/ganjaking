<?php

namespace ACP\Export\Model\Post;

use ACP\Export\Model;

class Permalink extends Model {

	public function get_value( $id ) {
		return urldecode( get_permalink( $id ) );
	}

}