<?php

namespace ACP\Filtering\Model\Comment;

use ACP;
use ACP\Filtering\Model;

class PostType extends Model {

	/**
	 * PostType constructor.
	 *
	 * @param ACP\Column\Comment\PostType $column
	 */
	public function __construct( ACP\Column\Comment\PostType $column ) {
		parent::__construct( $column );

		$this->column = $column;
	}

	public function get_filtering_vars( $vars ) {
		add_filter( 'comments_clauses', [ $this, 'filter_on_post_type' ] );

		return $vars;
	}

	public function filter_on_post_type( $comments_clauses ) {
		global $wpdb;

		$comments_clauses['join'] .= " JOIN wp_posts as pst ON {$wpdb->comments}.comment_post_ID = pst.ID";
		$comments_clauses['where'] .= $wpdb->prepare( " AND pst.post_type = %s", $this->get_filter_value() );

		return $comments_clauses;
	}

	public function get_filtering_data() {
		return [
			'options' => $this->get_available_post_types(),
		];
	}

	/**
	 * @return array
	 */
	private function get_available_post_types() {
		$options = [];

		foreach ( get_post_types( [], 'object' ) as $post_type ) {
			if ( post_type_supports( $post_type->name, 'comments' ) ) {
				$options[ $post_type->name ] = $post_type->labels->singular_name;
			}
		}

		return $options;
	}

}