<?php

namespace ACA\JetEngine\Field\Type;

use ACA\JetEngine\Field\Field;
use ACA\JetEngine\Field\Multiple;
use ACA\JetEngine\Field\MultipleTrait;
use ACA\JetEngine\Field\RelatedPostTypes;

class Posts extends Field implements Multiple, RelatedPostTypes {

	use MultipleTrait;

	const TYPE = 'posts';

	public function get_related_post_types() {
		return isset( $this->settings['search_post_type'] ) && is_array( $this->settings['search_post_type'] )
			? $this->settings['search_post_type']
			: null;
	}

}