<?php
declare( strict_types=1 );

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\AbstractModel;

class BeforeMoreTag extends AbstractModel {

	public function get_sorting_vars() {
		add_filter( 'posts_orderby', [ $this, 'posts_orderby_callback' ] );

		return [
			'suppress_filters' => false,
		];
	}

	public function posts_orderby_callback() {
		remove_filter( 'posts_orderby', [ $this, __FUNCTION__ ] );

		global $wpdb;

		$field = "$wpdb->posts.`post_content`";
		$order = $this->get_order();

		return "
			CASE WHEN $field LIKE '%--more--%' THEN 0 
				ELSE 1
			END,
			$field $order
		";
	}

}