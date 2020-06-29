<?php

namespace ACP\Sorting\Model\Post;

class Sticky extends Featured {

	protected function get_featured_ids() {
		return (array) get_option( 'sticky_posts' );
	}

}