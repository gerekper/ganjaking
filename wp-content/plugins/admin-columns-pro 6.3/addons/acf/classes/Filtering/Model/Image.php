<?php

namespace ACA\ACF\Filtering\Model;

use ACP;

class Image extends ACP\Filtering\Model\Meta {

	public function get_filtering_data() {
		$helper = new ACP\Filtering\Helper();

		return [
			'options'      => $helper->get_post_titles( $this->get_meta_values() ),
			'empty_option' => true,
		];
	}

}