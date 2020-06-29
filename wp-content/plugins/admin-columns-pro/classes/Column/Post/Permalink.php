<?php

namespace ACP\Column\Post;

use AC;
use ACP\Sorting;

class Permalink extends AC\Column\Post\Permalink
	implements Sorting\Sortable {

	public function sorting() {
		return is_post_type_hierarchical( $this->get_post_type() )
			? new Sorting\Model\Post\Permalink()
			: new Sorting\Model\Post\PostField( 'post_name' );
	}

}