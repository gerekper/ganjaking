<?php

namespace ACP\Column\Post;

use AC;

/**
 * @since 4.0.12
 */
class Revisions extends AC\Column
	implements AC\Column\AjaxValue {

	public function __construct() {
		$this->set_type( 'column-revisions' );
		$this->set_label( __( 'Revisions', 'codepress-admin-columns' ) );
	}

	public function get_value( $post_id ) {
		$value = $this->get_raw_value( $post_id );

		if ( ! $value ) {
			return $this->get_empty_char();
		}

		return ac_helper()->html->get_ajax_toggle_box_link( $post_id, sprintf( _n( '%s revision', '%s revisions', $value, 'codepress-admin-columns' ), $value ), $this->get_name() );
	}

	public function get_raw_value( $post_id ) {
		$revisions = wp_get_post_revisions( $post_id );

		return count( $revisions );
	}

	public function get_ajax_value( $post_id ) {
		$result = [];

		foreach ( wp_get_post_revisions( $post_id ) as $revision ) {
			$result[] = '<div class="acp-row-revision">' . wp_post_revision_title_expanded( $revision ) . '</div>';
		}

		return implode( '', $result );
	}

	public function is_valid() {
		return post_type_supports( $this->get_post_type(), 'revisions' );
	}

}