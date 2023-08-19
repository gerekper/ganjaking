<?php

namespace ACP\Column\Media;

use AC;
use ACP\Filtering;
use ACP\Search;

/**
 * @since 4.3.8
 */
class PostType extends AC\Column
	implements Filtering\Filterable, Search\Searchable {

	public function __construct() {
		$this->set_type( 'column-post_type' )
		     ->set_label( __( 'Post Type', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		$post_type = $this->get_raw_value( $id );

		if ( ! $post_type ) {
			return $this->get_empty_char();
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			return $this->get_empty_char();
		}

		return $post_type_object->labels->singular_name;
	}

	public function get_raw_value( $id ) {
		$parent = wp_get_post_parent_id( $id );

		if ( ! $parent ) {
			return false;
		}

		return get_post_type( $parent );
	}

	public function filtering() {
		return new Filtering\Model\Media\PostType( $this );
	}

	public function search() {
		return new Search\Comparison\Media\PostType();
	}

}