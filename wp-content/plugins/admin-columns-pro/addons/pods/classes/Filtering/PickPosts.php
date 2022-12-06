<?php

namespace ACA\Pods\Filtering;

use ACA\Pods\Filtering;
use ACP\Filtering\Helper;

class PickPosts extends Filtering {

	public function get_filtering_data() {
		return [
			'options'      => ( new Helper() )->get_post_titles( $this->get_meta_values() ),
			'empty_option' => true,
		];
	}

}