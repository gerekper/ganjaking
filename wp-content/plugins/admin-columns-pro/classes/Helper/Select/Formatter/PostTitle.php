<?php

namespace ACP\Helper\Select\Formatter;

use AC;
use ACP\Helper\Select\Value;
use WP_Post;

class PostTitle extends AC\Helper\Select\Formatter {

	public function __construct( AC\Helper\Select\Entities $entities, AC\Helper\Select\Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		parent::__construct( $entities, $value );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_label( $post ) {
		$label = $post->post_title;

		if ( 'attachment' === $post->post_type ) {
			$label = ac_helper()->image->get_file_name( $post->ID );
		}

		if ( ! $label ) {
			$label = sprintf( __( '#%d (no title)' ), $post->ID );
		}

		return (string) apply_filters( 'acp/select/formatter/post_title', $label, $post );
	}

}