<?php

namespace ACA\EC\Filtering;

use ACP;

class RelatedPost extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$values = $this->get_meta_values();

		return [
			'empty_option' => true,
			'options'      => ( new ACP\Filtering\Helper() )->get_post_titles( $values ),
		];
	}

}