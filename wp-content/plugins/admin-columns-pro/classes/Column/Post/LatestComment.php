<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.2
 */
class LatestComment extends AC\Column
	implements Export\Exportable, Sorting\Sortable {

	public function __construct() {
		$this->set_type( 'column-latest_comment' )
		     ->set_label( __( 'Latest Comment', 'codepress-admin-columns' ) );
	}

	public function is_valid() {
		return post_type_supports( $this->get_post_type(), 'comments' );
	}

	public function get_value( $id ) {
		$value = parent::get_value( $id );

		if ( $value && 'date' !== $this->get_comment_display_as() ) {
			$value .= $this->get_comment_date_value( $id );
		}

		return $value;
	}

	/**
	 * @param int $post_id
	 *
	 * @return string|null
	 */
	private function get_comment_date_value( $post_id ) {
		$comment_id = $this->get_raw_value( $post_id );

		if ( ! $comment_id ) {
			return null;
		}

		$comment = get_comment( $comment_id );
		$label = $comment->comment_date;
		$edit_link = get_edit_comment_link( $comment );

		if ( $edit_link ) {
			$label = sprintf( '<a href="%s">%s</a>', $edit_link, $label );
		}

		return sprintf( '<br><small><em>%s</em></small>', $label );
	}

	public function get_raw_value( $post_id ) {
		$comments = get_comments( [
			'number'  => 1,
			'fields'  => 'ids',
			'post_id' => $post_id,
		] );

		if ( empty( $comments ) ) {
			return false;
		}

		return $comments[0];
	}

	private function get_comment_display_as() {
		$this->get_setting( AC\Settings\Column\Comment::NAME )->get_value();
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Comment( $this ) );
	}

	public function sorting() {
		return new Sorting\Model\Post\LatestComment();
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}