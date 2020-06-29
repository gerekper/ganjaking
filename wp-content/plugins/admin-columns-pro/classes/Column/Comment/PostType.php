<?php

namespace ACP\Column\Comment;

use AC;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.2
 */
class PostType extends AC\Column
	implements Filtering\Filterable, Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-post_type' )
		     ->set_label( __( 'Post Type', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$post_type_object = get_post_type_object( $this->get_raw_value( $id ) );

		if ( ! $post_type_object ) {
			return false;
		}

		return $post_type_object->labels->singular_name;
	}

	public function get_raw_value( $id ) {
		return get_post_type( get_comment( $id )->comment_post_ID );
	}

	public function filtering() {
		return new Filtering\Model\Comment\PostType( $this );
	}

	public function search() {
		return new Search\Comparison\Comment\PostType();
	}

}