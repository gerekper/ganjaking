<?php

namespace ACP\Editing\BulkDelete\RequestHandler;

use ACP\Editing\BulkDelete\RequestHandler;
use RuntimeException;
use WP_Comment;

class Comment extends RequestHandler {

	protected function delete( $id, array $args = [] ): void {
		$comment = get_comment( (int) $id );

		if ( ! $comment instanceof WP_Comment ) {
			throw new RuntimeException( __( 'Comment does not exists.', 'codepress-admin-columns' ) );
		}

		if ( ! current_user_can( 'moderate_comments' ) || ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			throw new RuntimeException( __( 'You have no permission to delete this item.', 'codepress-admin-columns' ) );
		}

		$force_delete = 'true' === ( $args['force_delete'] ?? null );

		$result = wp_delete_comment( $comment->comment_ID, $force_delete );

		if ( false === $result ) {
			throw new RuntimeException( __( 'Comment does not exists.', 'codepress-admin-columns' ) );
		}
	}

}