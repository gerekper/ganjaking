<?php
declare( strict_types=1 );

namespace ACP\Helper\Select\Post\LabelFormatter;

use ACP\Helper\Select\Post\LabelFormatter;
use WP_Post;

class PostTitle implements LabelFormatter {

	public function format_label( WP_Post $post ): string {
		$post_title = $this->get_post_title( $post ) ?: __( 'no title', 'codepress-admin-columns' );

		return $this->apply_filters( $post_title, $post );
	}

	private function get_post_title( WP_Post $post ): string {
		if ( 'attachment' === $post->post_type ) {
			return ac_helper()->image->get_file_name( $post->ID ) ?: $post->post_title;
		}

		return $post->post_title;
	}

	public function format_label_unique( WP_Post $post ): string {
		return sprintf( '%s (%s)', $this->format_label( $post ), $post->ID );
	}

	private function apply_filters( string $label, WP_Post $post ): string {
		return (string) apply_filters( 'acp/select/formatter/post_title', $label, $post );
	}

}