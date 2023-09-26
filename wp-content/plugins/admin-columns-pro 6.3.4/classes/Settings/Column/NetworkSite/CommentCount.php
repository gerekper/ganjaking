<?php

namespace ACP\Settings\Column\NetworkSite;

use AC\Settings\Column;

class CommentCount extends Column\CommentCount {

	/**
	 * @param int $blog_id
	 * @param int $original_value
	 *
	 * @return string
	 */
	public function format( $blog_id, $original_value ) {
		$status = $this->get_comment_status();

		switch_to_blog( $blog_id );
		$count = (object) get_comment_count();

		restore_current_blog();

		if ( 'total_comments' === $status ) {
			$value = $this->comments_bubble( $blog_id, $count );
		} else {
			$value = ac_helper()->html->link( $this->get_comments_link( $blog_id, $this->get_comment_status() ), $count->$status );
		}

		return $value;
	}

	/**
	 * @param      $blog_id
	 * @param null $comment_status
	 *
	 * @return string
	 */
	private function get_comments_link( $blog_id, $comment_status = null ) {
		return add_query_arg( [ 'comment_status' => $comment_status ], get_admin_url( $blog_id, 'edit-comments.php' ) );
	}

	/**
	 * Display a comment count bubble
	 *
	 * @param int    $blog_id
	 * @param object $comments
	 *
	 * @return string
	 * @see WP_List_Table::comments_bubble()
	 */
	protected function comments_bubble( $blog_id, $comments ) {
		$approved_comments = $comments->approved;
		$pending_comments = $comments->awaiting_moderation;

		$approved_comments_number = number_format_i18n( $approved_comments );
		$pending_comments_number = number_format_i18n( $pending_comments );

		$approved_only_phrase = sprintf( _n( '%s comment', '%s comments', $approved_comments ), $approved_comments_number );
		$approved_phrase = sprintf( _n( '%s approved comment', '%s approved comments', $approved_comments ), $approved_comments_number );
		$pending_phrase = sprintf( _n( '%s pending comment', '%s pending comments', $pending_comments ), $pending_comments_number );

		ob_start();

		echo '<div class="ac-comment-bubble">';

		// No comments at all.
		if ( ! $approved_comments && ! $pending_comments ) {
			printf( '<span aria-hidden="true">—</span><span class="screen-reader-text">%s</span>',
				__( 'No comments' )
			);
			// Approved comments have different display depending on some conditions.
		} elseif ( $approved_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-approved"><span class="comment-count-approved" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( $this->get_comments_link( $blog_id, 'approved' ) ),
				$approved_comments_number,
				$pending_comments ? $approved_phrase : $approved_only_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-no-comments"><span class="comment-count comment-count-no-comments" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$approved_comments_number,
				$pending_comments ? __( 'No approved comments' ) : __( 'No comments' )
			);
		}

		if ( $pending_comments ) {
			printf( '<a href="%s" class="post-com-count post-com-count-pending"><span class="comment-count-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></a>',
				esc_url( $this->get_comments_link( $blog_id, 'moderated' ) ),
				$pending_comments_number,
				$pending_phrase
			);
		} else {
			printf( '<span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">%s</span><span class="screen-reader-text">%s</span></span>',
				$pending_comments_number,
				$approved_comments ? __( 'No pending comments' ) : __( 'No comments' )
			);
		}

		echo '</div>';

		return ob_get_clean();
	}

}