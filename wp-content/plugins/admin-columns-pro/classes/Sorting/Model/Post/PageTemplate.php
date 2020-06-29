<?php

namespace ACP\Sorting\Model\Post;

class PageTemplate extends MetaMapping {

	public function __construct( $post_type, $meta_key ) {
		parent::__construct( $meta_key, $this->get_sorted_fields( $post_type ) );
	}

	private function get_sorted_fields( $post_type ) {
		$templates = get_page_templates( null, $post_type );

		if ( ! $templates ) {
			return [];
		}

		$templates = array_flip( $templates );
		natcasesort( $templates );

		return array_keys( $templates );
	}

}