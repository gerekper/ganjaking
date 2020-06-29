<?php

namespace ACP\Filtering\Model\Post;

use ACP\Filtering\Model;

class Ancestors extends Model {

	public function get_filtering_vars( $vars ) {
		switch ( $this->get_filter_value() ) {
			case 'cpac_empty':
				$vars['post_parent'] = 0;

				break;
			case 'cpac_nonempty':
				$vars['post_parent__not_in'] = [ 0 ];

				break;
		}

		return $vars;
	}

	public function get_filtering_data() {
		return [
			'empty_option' => $this->get_empty_labels(),
		];
	}

}