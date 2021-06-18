<?php

namespace ACP\Column\Post;

use AC;
use ACP\Editing;

/**
 * @since 5.5.2
 */
class PostType extends AC\Column implements Editing\Editable {

	public function __construct() {
		$this->set_type( 'column-post_type' );
		$this->set_label( __( 'Post Type', 'codepress-admin-columns' ) );
	}

	public function get_value( $id ) {
		return $this->get_post_type_label( $this->get_raw_value( $id ) );
	}

	public function get_raw_value( $id ) {
		return get_post_type( $id );
	}

	private function get_post_type_label( $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		return $post_type_object
			? $post_type_object->label
			: $post_type;
	}

	public function editing() {
		return new Editing\Model\Post\PostType( $this );
	}

}