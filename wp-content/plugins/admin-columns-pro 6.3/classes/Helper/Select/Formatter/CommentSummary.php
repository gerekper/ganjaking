<?php

namespace ACP\Helper\Select\Formatter;

use AC;
use DateTime;
use WP_Comment;

class CommentSummary extends AC\Helper\Select\Formatter {

	/**
	 * @param WP_Comment $comment
	 * @param bool       $is_duplicate
	 *
	 * @return string
	 */
	public function get_label( $comment, $is_duplicate = false ) {
		$date = new DateTime( $comment->comment_date );

		$value = array_filter( [
			$comment->comment_author_email,
			$date->format( 'M j, Y H:i' ),
		] );

		return $comment->comment_ID . ' - ' . implode( ' / ', $value );
	}

}